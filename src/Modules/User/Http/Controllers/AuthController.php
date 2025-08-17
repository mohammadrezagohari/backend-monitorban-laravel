<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Modules\User\DTO\UserDTO;
use Modules\User\Models\User;
use OpenApi\Attributes as OA;
use Str;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;
use Illuminate\Support\Facades\Cache;

#[OA\Tag(
    name: "Authentication",
    description: "Endpoints for user authentication"
)]
#[OA\Info(
    title: "Authentication",
    version: "1.0.0",
    description: "Endpoints for user authentication"
)]
class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/auth/register",
        summary: "Register a new user account",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "User registration details",
            content: new OA\JsonContent(
                required: ["username", "email", "password"],
                properties: [
                    new OA\Property(property: "username", type: "string", example: "gohari"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john.doe@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: HTTPResponse::HTTP_CREATED, description: "Registration successful"),
            new OA\Response(response: HTTPResponse::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error"),
        ]
    )]
    public function register(Request $request)
    {
        $request->validate([
            'usernam' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('user'); // نقش پیش‌فرض

        return response()->json(['message' => 'ثبت‌نام موفق بود'], HTTPResponse::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/auth/login",
        summary: "Log in an existing user",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "User login credentials",
            content: new OA\JsonContent(
                required: ["username", "password"],
                properties: [
                    new OA\Property(property: "username", type: "string", example: "gohari"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string"),
                        new OA\Property(property: "user", ref: UserDTO::class),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Invalid credentials"),
        ]
    )]
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'اطلاعات وارد شده صحیح نیست'], 401);
        }

        return response()->json([
            'token' => $token,
            'user' => Auth::guard('api')->user()
        ], HTTPResponse::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/auth/verify-otp",
        summary: "Verify the one-time password (OTP)",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Verify OTP for authentication",
            content: new OA\JsonContent(
                required: ["mobile", "otp"],
                properties: [
                    new OA\Property(property: "mobile", type: "string", example: "09123456789"),
                    new OA\Property(property: "otp", type: "integer", example: 123456),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "OTP verified successfully"),
            new OA\Response(response: 400, description: "Invalid or expired OTP"),
            new OA\Response(response: 404, description: "User not found"),
        ]
    )]
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'mobile' => 'required|string|exists:users,mobile',
            'otp' => 'required|integer',
        ]);


        $user = User::where('mobile', $validated['mobile'])->first();

        $cachedOtp = Cache::get('otp_' . $validated['mobile']);

        if (!$cachedOtp || $cachedOtp != $validated['otp']) {
            return response()->json(['message' => 'Invalid or expired OTP'], HTTPResponse::HTTP_BAD_REQUEST);
        }

        // ساخت JWT Access Token
        $accessToken = auth('api')->login($user);

        // ساخت Refresh Token (نمونه: رشته تصادفی)
        $refreshToken = Str::random(64);

        // ذخیره Refresh Token با مدت طولانی‌تر (مثلاً ۷ روز)
        Cache::put('refresh_' . $refreshToken, $user->id, now()->addDays(7));

        return response()->json([
            'message' => 'کد یکبار مصرف تایید شد',
            'access_token' => $accessToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60, // ثانیه
            'refresh_token' => $refreshToken,
        ], HTTPResponse::HTTP_OK);
    }



    #[OA\Post(
        path: "/api/auth/request-otp",
        summary: "Request a one-time password (OTP)",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Request OTP for authentication",
            content: new OA\JsonContent(
                required: ["mobile"],
                properties: [
                    new OA\Property(property: "mobile", type: "string", example: "09371801145"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "OTP generated successfully"),
            new OA\Response(response: 404, description: "User not found"),
        ]
    )]
    public function requestOtp(Request $request)
    {
        $validated = $request->validate([
            'mobile' => 'required|string',
        ]);

        $user = User::where('mobile', $validated['mobile'])->first();

        if (!$user) {
            $user = User::firstOrCreate(
                ['mobile' => $validated['mobile']],
                ['password' => Hash::make(Str::random(16))]
            );
        }

        $otp = random_int(100000, 999999);

        Cache::put('otp_' . $validated['mobile'], $otp, now()->addMinutes(2));

        return response()->json(['message' => 'کد یکبار مصرف تولید شد', 'otp' => $otp], HTTPResponse::HTTP_OK);
    }


    #[OA\Post(
        path: "/api/auth/refresh-token",
        summary: "Refresh the access token using a refresh token",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Refresh token details",
            content: new OA\JsonContent(
                required: ["refresh_token"],
                properties: [
                    new OA\Property(property: "refresh_token", type: "string", description: "The refresh token used to obtain a new access token.")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "New access token generated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "access_token", type: "string"),
                        new OA\Property(property: "token_type", type: "string", example: "bearer"),
                        new OA\Property(property: "expires_in", type: "integer", description: "Time in seconds until the token expires")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Invalid or expired refresh token"),
            new OA\Response(response: 404, description: "User not found"),
        ]
    )]
    public function refreshToken(Request $request)
    {
        $validated = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        $userId = Cache::get('refresh_' . $validated['refresh_token']);

        if (!$userId) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 400);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $newAccessToken = auth('api')->login($user);

        return response()->json([
            'access_token' => $newAccessToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }


}

<?php

namespace Modules\User\Http\Controllers;

use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\User\Models\User;
use Modules\User\SwaggerDTO\Login\LoginRequestDTO;
use Modules\User\DTO\LoginValidationDTO;
use Modules\User\SwaggerDTO\User\UserRequestDTO;
use Modules\User\SwaggerDTO\User\UserResponseDTO;
use OpenApi\Attributes as OA;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Str;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;
use Illuminate\Support\Facades\Cache;

#[OA\Tag(
    name: "Authentication",
    description: "Endpoints for user authentication"
)]
#[OA\Info(
    version: "1.0.0",
    description: "Monitorban REST API documentation",
    title: "Monitorban API"
)]
#[OA\Server(
    url: "/",
    description: "Current host"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    description: "Enter JWT token as: Bearer <token>",
    bearerFormat: "JWT",
    scheme: "bearer"
)]
class AuthController extends Controller
{

    #[OA\Post(
        path: "/api/v1/auth/register",
        summary: "Register a new user account",
        requestBody: new OA\RequestBody(
            description: "User registration payload",
            required: true,
            content: new OA\JsonContent(ref: UserRequestDTO::class),
        ),
        // accepts: ['application/json'],
        tags: ["Authentication"],
        responses: [
            new OA\Response(response: 201, description: "Registration successful"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]

    public function register(Request $request)
    {
        $validData = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:11|unique:users',
            // 'password_confirmation' => 'required|string|same:password'
        ]);

        $result = DB::transaction(function () use ($validData) {
            $validData['password'] = Hash::make($validData['password']);

            $userDTO = UserRequestDTO::validateAndCreate($validData);
            $user = new User($userDTO->all());
            $user->save();

            if (Role::where('name', '=', 'user')->count() == 0) {
                $role = Role::create(['name' => 'user']);
                $permission = Permission::create(['name' => 'view dashboard']);
                $role->givePermissionTo($permission);
            }

            $user->assignRole(['user']); // نقش پیش‌فرض
            return compact('user');
            // Log::info('User registered successfully', ['token' => $token]);
        });

        $credentials = [
            'mobile' => $validData['mobile'],
            'password' => $validData['password'],
        ];

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'اطلاعات وارد شده صحیح نیست'], 401);
        }

        return response()->json(['message' => 'ثبت‌ نام موفق بود', 'user' => $result['user'], 'token' =>$token], HTTPResponse::HTTP_CREATED);

    }

    #[OA\Response(
        response: 200,
        description: "Login successful",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "token", type: "string"),
                new OA\Property(property: "user", ref: LoginRequestDTO::class),
            ]
        )
    )]
    #[OA\Post(
        path: "/api/v1/auth/login",
        summary: "login user account",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "User Login payload",
            content: new OA\JsonContent(ref: LoginRequestDTO::class),
        ),
        responses: [
            new OA\Response(response: 201, description: "Login successful"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function login(Request $request)
    {
        $validData = $request->validate([
            'password' => 'required|string|min:8',
            'mobile' => 'required|string|min:8|max:20|exists:users,mobile',
        ]);

        $loginDTO = LoginValidationDTO::validateAndCreate($validData);

        $credentials = [
            'mobile' => $loginDTO->mobile,
            'password' => $loginDTO->password,
        ];

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'اطلاعات وارد شده صحیح نیست'], 401);
        }

        return response()->json([
            'token' => $token,
            'user' => UserResponseDTO::from(Auth::guard('api')->user())
        ], HTTPResponse::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/v1/auth/verify-otp",
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
        path: "/api/v1/auth/request-otp",
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
        path: "/api/v1/auth/refresh-token",
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

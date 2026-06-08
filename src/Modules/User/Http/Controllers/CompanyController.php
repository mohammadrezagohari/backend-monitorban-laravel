<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Modules\User\Services\CompanyService;

class CompanyController extends Controller
{
    public function __construct(private CompanyService $companies)
    {
    }

    public function index(Request $request)
    {
        return ApiResponse::paginated(
            $this->companies->paginate(ApiResponse::perPage($request->query('per_page')))
        );
    }

    public function store(Request $request)
    {
        return response()->json(['status' => 'success', 'data' => $this->companies->create($request)], 201);
    }

    public function show(Company $company)
    {
        return response()->json(['status' => 'success', 'data' => $company->load('users')]);
    }

    public function update(Request $request, Company $company)
    {
        return response()->json(['status' => 'success', 'data' => $this->companies->update($request, $company)]);
    }

    public function destroy(Company $company)
    {
        $this->companies->delete($company);

        return response()->json(null, 204);
    }

    public function attachUser(Request $request, Company $company)
    {
        return response()->json(['status' => 'success', 'data' => $this->companies->attachUser($request, $company)]);
    }
}

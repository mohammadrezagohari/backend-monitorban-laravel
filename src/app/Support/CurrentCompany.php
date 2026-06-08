<?php

namespace App\Support;

use App\Models\Company;
use Illuminate\Http\Request;
use Modules\User\Models\User;

class CurrentCompany
{
    public static function id(Request $request): int
    {
        $user = $request->user();
        $requestedCompanyId = $request->integer('company_id') ?: null;

        if (! $user instanceof User) {
            abort(401, 'Unauthenticated.');
        }

        $query = $user->companies();

        if ($requestedCompanyId) {
            $company = $query->whereKey($requestedCompanyId)->first();

            if (! $company) {
                abort(403, 'You do not have access to this company.');
            }

            return $company->id;
        }

        $company = $query->first();

        if (! $company) {
            abort(403, 'User is not assigned to any company.');
        }

        return $company->id;
    }

    public static function queryForUser(Request $request)
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(401, 'Unauthenticated.');
        }

        return Company::query()->whereIn('id', $user->companies()->select('companies.id'));
    }
}

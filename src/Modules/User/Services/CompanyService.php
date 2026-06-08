<?php

namespace Modules\User\Services;

use App\Models\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\User\Models\User;

class CompanyService
{
    public function __construct(private CompanyRepositoryInterface $companies)
    {
    }

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->companies->paginate($this->companies->query()->latest(), $perPage);
    }

    public function create(Request $request): Company
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:companies,slug'],
            'is_active' => ['boolean'],
        ]);

        $data['slug'] ??= Str::slug($data['name']);

        return $this->companies->create($data);
    }

    public function update(Request $request, Company $company): Company
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', 'alpha_dash', Rule::unique('companies', 'slug')->ignore($company->id)],
            'is_active' => ['boolean'],
        ]);

        return $this->companies->update($company, $data);
    }

    public function delete(Company $company): void
    {
        $this->companies->delete($company);
    }

    public function attachUser(Request $request, Company $company): Company
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'is_owner' => ['boolean'],
        ]);

        User::findOrFail($data['user_id']);

        $company->users()->syncWithoutDetaching([
            $data['user_id'] => ['is_owner' => $data['is_owner'] ?? false],
        ]);

        return $company->load('users');
    }
}

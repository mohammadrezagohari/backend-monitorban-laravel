<?php

namespace Modules\Sensor\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Modules\Sensor\Models\Unit;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $units = Unit::query()
            ->latest()
            ->paginate(ApiResponse::perPage($request->query('per_page')));

        return ApiResponse::paginated($units);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['required', 'string', 'max:50'],
            'dimension' => ['nullable', 'string', 'max:100'],
            'is_canonical' => ['boolean'],
        ]);

        return response()->json(['status' => 'success', 'data' => Unit::create($data)], 201);
    }

    public function show(Unit $unit)
    {
        return response()->json(['status' => 'success', 'data' => $unit]);
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'symbol' => ['sometimes', 'required', 'string', 'max:50'],
            'dimension' => ['nullable', 'string', 'max:100'],
            'is_canonical' => ['boolean'],
        ]);

        $unit->update($data);

        return response()->json(['status' => 'success', 'data' => $unit]);
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return response()->json(null, 204);
    }
}

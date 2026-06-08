<?php

namespace Modules\Room\Services;

use App\Support\CurrentCompany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Room\Models\ServerRoom;
use Modules\Room\Repositories\Contracts\ServerRoomRepositoryInterface;

class RoomService
{
    public function __construct(private ServerRoomRepositoryInterface $rooms)
    {
    }

    public function paginateForRequest(Request $request, int $perPage): LengthAwarePaginator
    {
        $query = $this->rooms->query()
            ->where('company_id', CurrentCompany::id($request))
            ->when(! $request->user()->hasAnyRole(['super-admin', 'admin']), fn ($query) => $query->whereIn('id', $request->user()->scopedResourceIds(ServerRoom::class)))
            ->latest();

        return $this->rooms->paginate($query, $perPage);
    }

    public function create(Request $request): ServerRoom
    {
        $companyId = CurrentCompany::id($request);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('server_rooms')->where('company_id', $companyId),
            ],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        return $this->rooms->create($data + ['company_id' => $companyId]);
    }

    public function findAccessible(Request $request, int $id, array $relations = []): ServerRoom
    {
        return $this->rooms->query()
            ->where('company_id', CurrentCompany::id($request))
            ->when(! $request->user()->hasAnyRole(['super-admin', 'admin']), fn ($query) => $query->whereIn('id', $request->user()->scopedResourceIds(ServerRoom::class)))
            ->with($relations)
            ->findOrFail($id);
    }

    public function update(Request $request, int $id): ServerRoom
    {
        $companyId = CurrentCompany::id($request);
        $room = $this->findAccessible($request, $id);

        $data = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('server_rooms')->where('company_id', $companyId)->ignore($room->id),
            ],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        return $this->rooms->update($room, $data);
    }

    public function delete(Request $request, int $id): void
    {
        $this->rooms->delete($this->findAccessible($request, $id));
    }
}

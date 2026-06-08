<?php

namespace Modules\Room\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use Modules\Room\Models\ServerRoom;
use Modules\Room\Repositories\Contracts\ServerRoomRepositoryInterface;

class ServerRoomRepository extends BaseRepository implements ServerRoomRepositoryInterface
{
    public function __construct(ServerRoom $serverRoom)
    {
        parent::__construct($serverRoom);
    }
}

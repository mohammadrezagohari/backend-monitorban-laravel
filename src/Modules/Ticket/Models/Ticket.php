<?php

namespace Modules\Ticket\Models;

use App\Models\Concerns\TracksLastChange;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Ticket\Enums\TicketStatusEnum;
use Modules\User\Models\User;

// use Modules\Ticket\Database\Factories\TicketFactory;

class Ticket extends Model
{
    use HasFactory, TracksLastChange;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'user_id',
        'subject',
        'recipient',
        'message',
        'status',
        'last_change_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'status' => TicketStatusEnum::class,
    ];

    // protected static function newFactory(): TicketFactory
    // {
    //     // return TicketFactory::new();
    // }
}

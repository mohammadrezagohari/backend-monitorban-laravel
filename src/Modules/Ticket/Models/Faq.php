<?php

namespace Modules\Ticket\Models;

use App\Models\Concerns\TracksLastChange;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

// use Modules\Ticket\Database\Factories\FaqFactory;

class Faq extends Model
{
    use HasFactory, TracksLastChange;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['question', 'answer', 'sort_order', 'is_active', 'last_change_by'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_change_by');
    }
    // protected static function newFactory(): FaqFactory
    // {
    //     // return FaqFactory::new();
    // }
}

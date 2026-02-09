<?php

namespace Modules\Ticket\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Modules\Ticket\Database\Factories\FaqFactory;

class Faq extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['question', 'answer', 'sort_order', 'is_active'];

    // protected static function newFactory(): FaqFactory
    // {
    //     // return FaqFactory::new();
    // }
}

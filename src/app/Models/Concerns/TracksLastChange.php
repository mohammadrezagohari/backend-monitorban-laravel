<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait TracksLastChange
{
    protected static function bootTracksLastChange(): void
    {
        static::creating(function (Model $model): void {
            $userId = auth()->id();

            if ($userId && ! $model->getAttribute('last_change_by')) {
                $model->setAttribute('last_change_by', $userId);
            }
        });

        static::updating(function (Model $model): void {
            $userId = auth()->id();

            if ($userId) {
                $model->setAttribute('last_change_by', $userId);
            }
        });
    }
}

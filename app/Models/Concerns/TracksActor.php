<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Auth;

trait TracksActor
{
    protected static function bootTracksActor(): void
    {
        static::creating(function ($model) {
            if (! $model->created_by) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }
}

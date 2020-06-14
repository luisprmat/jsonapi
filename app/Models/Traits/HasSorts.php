<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

trait HasSorts
{
    public function scopeApplySorts(Builder $query, $sort)
    {
        if(! property_exists($this, 'allowedSorts')) {
            abort(500, 'Please set the public property $allowedSorts inside ' . get_class($this));
        }

        if (is_null($sort)) {
            return;
        }

        $sortFields = Str::of($sort)->explode(',');

        foreach ($sortFields as $sortField) {
            $direction = 'asc';

            if(Str::of($sortField)->startsWith('-')) {
                $direction = 'desc';
                $sortField = Str::of($sortField)->substr(1);
            }

            if( ! collect($this->allowedSorts)->contains($sortField) ) {
                abort(400, "Invalid Query Parameter, '{$sortField}' is not allowed");
            }

            $query->orderBy($sortField, $direction);
        }
    }
}

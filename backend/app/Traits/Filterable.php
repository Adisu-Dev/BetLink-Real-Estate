<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') continue;

            $method = 'filter' . str($key)->studly();

            if (method_exists($this, $method)) {
                $this->$method($query, $value);
            }
        }

        return $query;
    }
}

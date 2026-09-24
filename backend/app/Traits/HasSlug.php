<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    // Auto-generate slugs during model creation and updates
    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model): void {
            $source = static::slugSource();
            if (empty($model->slug) && !empty($model->{$source})) {
                $model->slug = static::generateUniqueSlug((string) $model->{$source});
            }
        });

        static::updating(function (Model $model): void {
            $source = static::slugSource();
            if ($model->isDirty($source) && empty($model->slug) && !empty($model->{$source})) {
                $model->slug = static::generateUniqueSlug((string) $model->{$source}, $model->getKey());
            }
        });
    }

    protected static function slugSource(): string
    {
        return 'title';
    }

    public static function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $count = 1;

        while (true) {
            $query = static::where('slug', $slug);
            if ($ignoreId !== null) {
                $query->whereKeyNot($ignoreId);
            }
            if (!$query->exists()) {
                break;
            }
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }
}

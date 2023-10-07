<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'id' => 'string',
    ];

    public static function booted(): void
    {
        static::saving(function (Brand $brand) {
            $brand->slug = Str::slug($brand->name);
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

<?php

namespace App\Casts;

use Money\Money;
use Money\Currency;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MoneyCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): Money
    {
        return new Money((string) $value, new Currency('BRL'));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof Money) {
            return $value->getAmount();
        }

        return $value;
    }
}

<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

/**
 * Like "exists:table,id", but goes through the Eloquent model so the
 * owner scope applies. A user can't submit the ID of someone else's
 * lease, unit or tenant (Livewire properties can be changed in the browser).
 */
class OwnedRecord implements ValidationRule
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(private string $model) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->model::query()->whereKey($value)->exists()) {
            $fail('The selected :attribute is invalid.');
        }
    }
}

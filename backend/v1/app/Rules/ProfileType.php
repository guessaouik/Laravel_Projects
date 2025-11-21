<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProfileType implements ValidationRule
{
    private const TYPES = [
        "hospital", "clinic", "lab", "mic", "pharmacy", "doctor", "patient"
    ];
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!in_array($value, static::TYPES)){
            $fail("'$value' is no a valid type");
        }
    }
}

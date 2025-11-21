<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProviderHasAccess implements ValidationRule
{
    protected string $type;
    public function __construct(string $type){
        $this->type = $type;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (str_contains($attribute, "equipment") && $this->type !== "pharmacy"){
            $fail("'{$this->type}' doesn't have equipments.");
        }
        if (str_contains($attribute, "test") && $this->type !== "lab"){
            $fail("'{$this->type}' doesn't have tests.");
        }
        if (str_contains($attribute, "technology") && $this->type !== "mic"){
            $fail("'{$this->type}' doesn't have technologies.");
        }
    }
}

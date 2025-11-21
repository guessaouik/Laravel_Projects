<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateTypePermission implements ValidationRule
{
    public static array $defaultAcceptedTypes;
    public array $types;
    public function __construct(...$types){
        $this->types = $types;
    }

    private static function isValid(array $validator, array|string $validated) : null|string{
        if (is_array($validated)){
            foreach ($validated as $type){
                if (!in_array(strtolower($type), $validator)){
                    return $type;
                }
            }
            return null;
        }
        if (!in_array(strtolower($validated), $validator)){
            return $validated;
        }
        return null;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->types === []){
            $result = static::isValid(static::$defaultAcceptedTypes, $value);
            if ($result !== null){
                $fail("'$result' is not a valid type.");
            }
            return;
        }

        $result = static::isValid($this->types, $value);
        if ($result !== null){
            $fail("'$result' is not a valid type.");
        }
    }
}

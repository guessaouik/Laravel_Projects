<?php

namespace App\Http\Controllers\Helpers;
use Illuminate\Support\Str;

class StringOperator{

    public static function camelCase(string $str) : string{
        $words = explode("_", $str);
        return $words[0] . implode("", array_map(fn($word) => ucfirst(strtolower($word)), array_slice($words, 1)));
    }

    public static function snakeCase(string $str) : string{
        $words = Str::ucsplit($str);
        return implode("_", array_map(fn($word) => strtolower($word), $words));
    }

    public static function arrayValuesToCamelCase(array $values) : array{
        $result = [];
        foreach ($values as $value){
            $result[] = static::camelCase($value);
        } 
        return $result;
    }

    public static function arrayValuesToSnakeCase(array $values) : array{
        $result = [];
        foreach ($values as $value){
            $result[] = static::snakeCase($value);
        }
        return $result;
    }

    public static function arrayKeysToSnakeCase(array $values) : array{
        $result = [];
        foreach ($values as $key => $value){
            $result[static::snakeCase($key)] = $value;
        }
        return $result;
    }

    public static function arrayKeysToCamelCase(array $values) : array{
        $result = [];
        foreach($values as $key => $value){
            $result[static::camelCase($key)] = $value;
        }
        return $result;
    }

}
<?php


namespace App\Traits;

trait IsPerson{

    protected function fillPersonFillable(array &$fillable){
        $fillable = array_merge($fillable, array_filter([
            'firstname', 'lastname', 'address', 'photo', 'socials', "email", "phone", "password"
        ], fn($element) => !in_array($element, $fillable)));
    }
}
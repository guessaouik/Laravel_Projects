<?php


namespace App\Traits;

trait IsProvider{

    protected function fillProviderFillable(array &$fillable){
        $fillable = array_merge($fillable, array_filter([
            'longitude', 'latitude', 'address',
            'socials', 'about', 'status', 'email', "phone", "password"
        ], fn($element) => !in_array($element, $fillable)));
    }
}
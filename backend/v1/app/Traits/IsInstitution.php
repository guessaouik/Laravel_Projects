<?php


namespace App\Traits;

trait IsInstitution{

    protected function fillInstitutionFillable(array &$fillable){
        $fillable = array_merge($fillable, array_filter(['name', "photos"], fn($element) => !in_array($element, $fillable)));
    }
}
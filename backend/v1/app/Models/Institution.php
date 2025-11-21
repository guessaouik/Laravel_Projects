<?php

namespace App\Models;

use App\Traits\HasPrivileges;
use App\Traits\IsInstitution;

abstract class Institution extends Provider 
{
    use IsInstitution, HasPrivileges;


    public function __construct()
    {
        $this->collectionMorph_Methods = array_merge($this->collectionMorph_Methods, ["privileges"]);
        parent::__construct();
        $this->fillInstitutionFillable($this->fillable);
    }
}

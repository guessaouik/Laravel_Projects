<?php

namespace App\Models;

use Error;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MorphModel extends Model
{
    /**
     * array of method names that return MorphModel
     *
     * @var array
     */
    protected array $modelMorph_Methods = [];

    /**
     * array of method names that return MorphCollection
     *
     * @var array
     */
    protected array $collectionMorph_Methods = [];
    use HasFactory;

    public function __get($key)
    {
        if (in_array($key, $this->collectionMorph_Methods)){
            return $this->{$key}()->execute();
        }
        if (in_array($key, $this->modelMorph_Methods)){
            return $this->{$key}()->model;
        }
        return parent::__get($key);
    }


}

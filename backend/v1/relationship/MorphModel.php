<?php 

namespace Relationship;

use Error;
use Illuminate\Database\Eloquent\Model;

class MorphModel{
    public $pivot;
    private ?Model $model;

    public function __construct(?Model $model, $pivot)
    {
        $this->model = $model;
        $this->pivot = $pivot;
    }

    public function __get($name)
    {
        if ($name === "pivot"){
            return $this->pivot;
        }
        if ($name === "model"){
            return $this->model;
        }
    }

}
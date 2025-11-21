<?php

namespace App\Http\Controllers\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Sort{

    private array $conditions = [];
    private Collection $models;

    public function __construct(Collection $models, array $conditions = [])
    {
        if ($conditions !== []){
            $this->conditions = $conditions;
        }
        $this->models = $models->values();
    }

    public function addCondition(string $condition, bool $asc) : Sort{
        $this->conditions[] = [$condition, $asc];
        return $this;
    }

    public function setConditions(array $conditions) : Sort{
        $this->conditions = $conditions;
        return $this;
    }


    private function getValueFromModel(Model $model, array $condition){
        return ATTRIBUTE_REQUEST_KEY[$condition[0]]($model);
    }

    private function isBefore(int $i1, int $i2, int $conditionIndex = 0){
        if ($conditionIndex == count($this->conditions)){
            return true;
        }
        $condition = $this->conditions[$conditionIndex];
        $model1 = $this->models->get($i1);
        $model2 = $this->models->get($i2);
        $value1 = $this->getValueFromModel($model1, $condition); 
        $value2 = $this->getValueFromModel($model2, $condition);
        if ($value1 === $value2){
            return $this->isBefore($model1, $model2, $conditionIndex + 1);
        }
        if ($condition[1] === true){
            return $value1 < $value2;
        } else if ($condition[1] === false){
            return $value2 < $value1;
        }
    }

    private function swapModels(int $i1, int $i2){
        $extra = $this->models->get($i1);
        $this->models->put($i1, $this->models->get($i2));
        $this->models->put($i2, $extra);
    } 
    
    public function sort(){
        if ($this->conditions === []){
            return $this->models;
        }

        // simple boring bubble sort
        for ($i = $this->models->count() - 1; $i >= 0; $i--){
            for ($j = 0; $j < $i; $j++){
                if (!$this->isBefore($j, $j + 1)){
                    $this->swapModels($j, $j + 1);
                }
            }
        }

        return $this->models;
    }
}
<?php

namespace App\Http\Controllers\Helpers;

use Illuminate\Http\Request;

class View{

    private array $validationParams;
    private string $class;
    private string $primaryRequestKey;
    private string $resourceClass;

    public function __construct(string $class, string $primaryRequestKey, string $resourceClass, array $validationParams)
    {
        $this->validationParams = $validationParams;
        $this->class = $class;
        $this->primaryRequestKey = $primaryRequestKey;
        $this->resourceClass = $resourceClass;     
    }
    
    public function setValidationParams(array $validationParams){
        $this->validationParams = $validationParams;
    }

    public function view(Request $request){
        $request->validate($this->validationParams);

        $model = $this->class::find($request->{$this->primaryRequestKey});
        $model->views++;
        $model->save();

        $resource = $this->resourceClass;
        return new $resource($model);
    }
}
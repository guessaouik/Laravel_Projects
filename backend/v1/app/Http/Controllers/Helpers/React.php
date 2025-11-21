<?php

namespace App\Http\Controllers\Helpers;

use App\Rules\ValidateTypePermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class React{

    private array $validationParams;
    private string $reactableClass;
    private string $reactablePrimaryInRequest;
    private string $reactionTable;

    public function __construct(string $reactableClass, string $reactablePrimaryInRequest, string $reactionTable, array $validationParams = [])
    {
        ValidateTypePermission::$defaultAcceptedTypes = [
            "patient", "doctor", "mic", "hospital", "clinic", "pharmacy", "lab"
        ];
        $this->validationParams = [
            "type" => ["required", new ValidateTypePermission],
            "id" => "required",
            $reactablePrimaryInRequest => "required",
        ];
        $this->validationParams = array_merge($this->validationParams, $validationParams);
        $this->reactableClass = $reactableClass;
        $this->reactablePrimaryInRequest = $reactablePrimaryInRequest;
        $this->reactionTable =$reactionTable;
    }

    /**
     * re-initializes the validation parameters except type and id
     *
     * @param array $validationParams
     * @return void
     */
    public  function setValidationParams(array $validationParams){
        foreach (array_keys($this->validationParams) as $key){
            if ($key !== "type" && $key !== "id"){
                unset($this->validationParams[$key]);
            }
        }
        $this->validationParams = $validationParams;
    }

    public function react(Request $request){
        $request->validate($this->validationParams);

        $model = $this->reactableClass::find($request->{$this->reactablePrimaryInRequest});

        $query = DB::table($this->reactionTable)
        ->where($model->getPrimaryKey(), "=", $model->getKey())
        ->where("profile_type", "=", TYPE_ALIAS[$request->type])
        ->where("profile_id", "=", $request->id);

        if (!$query->get()->empty()){
            return;
        }
       
        $query->delete();
        DB::table($this->reactionTable)->insert([
            $model->getPrimaryKey() => $model->getKey(), 
            "profile_type" => TYPE_ALIAS[$request->type],
            "profile_id" => $request->id,
        ]);

        $model->likes++;

        $model->save();
    }

    public function unreact(Request $request){
        $request->validate($this->validationParams);

        $model = $this->reactableClass::find($request->{$this->reactablePrimaryInRequest});

        $query = 
        DB::table($this->reactionTable)
        ->where($model->getPrimaryKey(), "=", $model->getKey())
        ->where("profile_type", "=", TYPE_ALIAS[$request->type])
        ->where("profile_id", "=", $request->id);

        if ($query->get()->empty()){
            return;
        }

        $query->delete();

        $model->likes--;

        $model->save();
    }

    public function getReaction(Request $request){
        $request->validate($this->validationParams);

        $record = DB::table($this->reactionTable)
        ->where((new $this->reactableClass())->getPrimaryKey(), "=", $request->articleId)
        ->where("profile_type", "=", TYPE_ALIAS[$request->type])
        ->where("profile_id", "=", $request->id)
        ->first();
        return ["liked" => $record === null ? false : true];
    }
}
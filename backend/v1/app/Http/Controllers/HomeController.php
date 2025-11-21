<?php

namespace App\Http\Controllers;

use App\Http\Resources\Home\ArticleResource;
use App\Http\Resources\Home\DoctorResource;
use App\Http\Resources\Home\InstitutionResource;
use App\Models\Article;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    private const HOME_TYPE_METHOD = [
        "doctor" => "topDoctors",
        "article" => "latestArticles",
    ];

    public function topDoctors(){
        return DoctorResource::collection(Doctor::orderByDesc("rating")->limit(HOME_RESULTS_NUMBER)->get());
    }

    public function topInstitutions(string $type){
        if (!isset(TYPE_MODEL[$type])){
            return response()->json([
                "error" => "'$type' is not a valid type",
            ], 404);
        }
        $model = TYPE_MODEL[$type];
        return InstitutionResource::collection($model::orderByDesc("rating")->limit(HOME_RESULTS_NUMBER)->get());
    }

    public function latestArticles(){
        return ArticleResource::collection(
            Article::orderByDesc("updated_at")->orderBy("likes")->limit(HOME_RESULTS_NUMBER)->get()
        );
    }

    public function getHomeItems(string $type){
        if (isset(static::HOME_TYPE_METHOD[$type])){
            $methodName = static::HOME_TYPE_METHOD[$type];
            return call_user_func_array([$this, $methodName], []);
        }
        return $this->topInstitutions($type);
    }
}

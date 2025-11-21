<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\Photo;
use App\Http\Controllers\Helpers\React;
use App\Http\Controllers\Helpers\Search;
use App\Http\Controllers\Helpers\Sort;
use App\Http\Controllers\Helpers\View;
use App\Http\Helpers\Paginate;
use App\Http\Resources\Article\ArticleResource;
use App\Http\Resources\Article\PersonalArticleResource;
use App\Http\Resources\Article\ProfileArticleResource;
use App\Models\Article;
use App\Models\Specialty;
use App\Rules\ProfileType;
use App\Rules\ValidateTypePermission;
use FileUpload\FileUploadHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ArticleController extends Controller
{
    private React $react;
    public function __construct()
    {
        $this->react = new React(Article::class, "articleId", "article_ratings"); 
    }

    #region private methods
    private function setValidatorArray(array $toAdd = []){
        ValidateTypePermission::$defaultAcceptedTypes = array_merge($toAdd, [
            "hospital", "clinic", "doctor", "pharmacy", "lab", "mic"
        ]);
    }

    private function getArticle(Request $request){
        return (new View(
            Article::class,
            "articleId",
            ArticleResource::class,
            [
                "type" => ["required", new ProfileType],
                "id" => "required",
                "articleId" => "required"
            ]
        ))->view($request);
    }

    private function getProviderArticles(Request $request){
        $this->setValidatorArray();
        $request->validate([
            "type" => ["required", new ValidateTypePermission],
            "id" => "required"
        ]);
        
        $model = call_user_func_array([TYPE_MODEL[$request->type], "find"], [$request->id]);
        $articles = $model->articles;

        if (MODEL_ALIAS[$model::class] !== TYPE_ALIAS[$request->type] && $model->getKey() !== $request->id){
            foreach ($articles as &$article){
                $record = 
                DB::table("article_ratings")
                ->where("article_id", "=", $article->id)
                ->where("profile_type", "=", TYPE_ALIAS[$request->type])
                ->where("profile_id", "=", $request->id)
                ->first();
                $article->rating = $record === null ? false : true;
            }
        }

        return ProfileArticleResource::collection(Paginate::paginate($articles, $request->perPage ?? DEFAULT_PER_PAGE));
    }

    private function searchProviderArticles(Request $request){
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
        ]);
        $pattern = $request->pattern ?? "";
        $model = call_user_func_array([TYPE_MODEL[$request->type], "find"], [$request->id]);
        $articles = $model->articles;
        return Search::searchInCollection($articles, $pattern, $request->perPage ?? DEFAULT_PER_PAGE, ["title", "content"], ProfileArticleResource::class);
    }

    private function searchAllArticles(Request $request){
        return Search::searchInCollection(Article::all(), $request->pattern ?? "", $request->perPage ?? DEFAULT_PER_PAGE, ["title", "content"], ArticleResource::class);
    }

    #endregion

    #region public methods
 
    public function show(Request $request){
        return ArticleResource::collection(Paginate::paginate((new Sort(Article::all(), $request->sort ?? [["time", true]]))->sort(), $request->perPage ?? DEFAULT_PER_PAGE));
    }

    public function get(Request $request){
        if ($request->articleId === null){
            return $this->getProviderArticles($request);
        } else {
            return $this->getArticle($request);
        }
    }

    public function search(Request $request){
        if ($request->type !== null){
            return $this->searchProviderArticles($request);
        } else {
            return $this->searchAllArticles($request);
        }
    }

    public function create(Request $request){
        $this->setValidatorArray();
        $request->validate([
            "title" => "required",
            "content" => "required",
            "id" => "required",
            "type" => ["required", new ValidateTypePermission],
        ]);
        

        $article = Article::create([
            "photo" => $request->photo === null ? null : Photo::saveArticle($request, "photo"), // FileUploadHandler::getFilePath()
            "title" => $request->title,
            "content" => $request->content,
        ]);

        $values = [];
        $specialties = $request->specialtyIds ?? [];
        foreach ($specialties as $id){
            $values[] = ["specialty_id" => $id, "component_type" => "a", "component_id" => $article->getKey()];
        }
        DB::table("component_specialty")->insert($values);

        DB::table("article_provider")->insert([
            "article_id" => $article->getKey(),
            "provider_type" => TYPE_ALIAS[$request->type],
            "provider_id" => $request->id
        ]);

        return new ArticleResource($article);
    }

    public function update(Request $request){
        $request->validate(["articleId" => "required"]);
        $specialtyIds = $request->specialtyIds ?? [];
        $article = Article::find($request->id);
        $article->photo = $request->photo === null ? $article->photo : Photo::saveArticle($request, "photo");
        $article->title = $request->title ?? $article->title;
        $article->content = $request->content ?? $article->content;
        $article->save();

        if ($specialtyIds !== []){
            foreach ($specialtyIds as $id){
                $record =
                    DB::table("component_specialty")
                    ->where("specialty_id", "=", $id)
                    ->where("component_type", "=", "a")
                    ->where("component_id", "=", $article->getKey())
                    ->first();
                if ($record === null){
                    DB::table("component_specialty")->insert([
                        "specialty_id" => $id,
                        "component_type" => "a",
                        "component_id" => $article->getKey(),
                    ]);
                }
            }
        }
        return new ArticleResource($article);
    }

    public function delete(Request $request){
        $request->validate([
            "ids" => "required"
        ]);
        Article::whereIn("article_id", $request->ids)->delete();
        return ["message" => "success"];
    }

    public function getRating(Request $request){
        return $this->react->getReaction($request);
    }

    public function changeRating(Request $request){
        $liked = $request->value ?? false;
        if ($liked !== false){
            $this->react->react($request);
            return;
        }

        $this->react->unreact($request);
    }

    public function getSpecialtyArticles(Request $request){
        return ArticleResource::collection(Specialty::find($request->specialtyId)->articles);
    }
    #endregion
}

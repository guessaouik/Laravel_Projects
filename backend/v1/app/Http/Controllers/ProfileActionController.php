<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\Search;
use App\Http\Helpers\Paginate;
use App\Http\Resources\Profile\SavedItems\SavedArticleResource;
use App\Http\Resources\Profile\SavedItems\SavedPostResource;
use App\Http\Resources\Profile\SavedItems\SavedProfileResource;
use App\Models\Article;
use App\Models\Post;
use App\Rules\ProfileType;
use App\Rules\ValidateTypePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileActionController extends Controller
{

    public function saveArticles(Request $request){
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
            "articleIds" => "required",
        ]);

        $ids = is_array($request->articleIds) ? $request->articleIds : [$request->articleIds];
        foreach ($ids as $id){
            DB::table("saved_articles")->updateOrInsert([
                "article_id" => $id,
                "saver_type" => TYPE_ALIAS[$request->type],
                "saver_id" => $request->id,
            ]);
        }

        return ["message" => "saved successfully."];
    }

    public function savePosts(Request $request){
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
            "postIds" => "required",
        ]);

        $ids = is_array($request->postIds) ? $request->postIds : [$request->postIds];
        foreach ($ids as $id){
            if (Post::find($id)->parent_id != null){
                return response(["error" => "cannot save post comment."], 404);
            }
            DB::table("saved_posts")->updateOrInsert([
                "post_id" => $id,
                "saver_type" => TYPE_ALIAS[$request->type],
                "saver_id" => $request->id,
            ]);
        }

        return ["message" => "saved successfully."];
    }

    public function saveProfiles(Request $request){
        ValidateTypePermission::$defaultAcceptedTypes = [
            "hospital", "clinic", "doctor", "pharmacy", "lab", "mic",
        ];
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
            "profileTypes" => ["required", new ValidateTypePermission],
            "profileIds" => "required",
        ]);

        $profileTypes = is_array($request->profileTypes) ? $request->profileTypes : [$request->profileTypes];
        $profileIds = $request->profileIds;
        $profileIds = is_array($profileIds) ? $profileIds : [$profileIds];

        if (count($profileIds) !== count($profileTypes)){
            return response(["error" => "types and ids don't match."], 404);
        }
        for ($i = 0; $i < count($profileIds); $i++){
            DB::table("saved_profiles")->updateOrInsert([
                "saver_type" => TYPE_ALIAS[$request->type],
                "saver_id" => $request->id,
                "saved_type" => TYPE_ALIAS[$profileTypes[$i]],
                "saved_id" => $profileIds[$i],
            ]); 
        }
        return ["message" => "saved successfully"];
    }

    public function searchSavedItems(Request $request, string $resource, string $attName, array $columns = [], ?string $pattern = null){
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
        ]);

        $model = call_user_func_array([TYPE_MODEL[$request->type], "find"], [$request->id]);
        if ($pattern === null){
            return $resource::collection(Paginate::paginate($model->{$attName}, $request->perPage ?? DEFAULT_PER_PAGE));
        } else {
            $pattern = $request->pattern ?? "";
            Search::searchInCollection($model->{$attName}, $pattern, $request->perPage ?? DEFAULT_PER_PAGE, $columns, $resource);
        }
    }

    public function getSavedArticles(Request $request){
        return $this->searchSavedItems($request, SavedArticleResource::class, "savedArticles");
    }

    public function getSavedPosts(Request $request){
        return $this->searchSavedItems($request, SavedPostResource::class, "savedPosts");
    }

    public function getSavedProfiles(Request $request){
        return $this->searchSavedItems($request, SavedProfileResource::class, "savedProfiles");
    }

    public function searchSavedArticles(Request $request){
        return $this->searchSavedItems($request, SavedArticleResource::class, "savedArticles", ["title", "content"], "");
    }

    public function searchSavedPosts(Request $request){
        return $this->searchSavedItems($request, SavedPostResource::class, "savedPosts", ["title", "content"], "");
    }

    public function searchSavedProfiles(Request $request){
        return $this->searchSavedItems($request, SavedProfileResource::class, "savedProfiles", ["name"], "");
    }

    public function unsaveArticles(Request $request){
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
            "articleIds" => "required",
        ]);

        $ids = is_array($request->articleIds) ? $request->articleIds : [$request->articleIds];
        DB::table("saved_articles")
        ->whereIn("article_id", $ids)
        ->where("saver_type", "=", TYPE_ALIAS[$request->type])
        ->where("saver_id", "=", $request->id)
        ->delete();
        return ["message" => "deleted successfully."];
    }

    public function unsavePosts(Request $request){
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
            "postIds" => "required",
        ]);

        $ids = is_array($request->postIds) ? $request->postIds : [$request->postIds];
        DB::table("saved_posts")
        ->whereIn("post_id", $ids)
        ->where("saver_type", "=", TYPE_ALIAS[$request->type])
        ->where("saver_id", "=", $request->id)
        ->delete();
        return ["message" => "deleted successfully."];
    }

    public function unsaveProfiles(Request $request){
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
            "profileTypes" => "required",
            "profileIds" => "required",
        ]);

        $types = $request->profileTypes;
        $types = is_array($types) ? $types : [$types];
        $ids = $request->profileIds;
        $ids = is_array($ids) ? $ids : [$ids];
        for ($i = 0; $i < count($ids); $i++){
            DB::table("saved_profiles")
            ->where("saved_type", "=", TYPE_ALIAS[$types[$i]])
            ->where("saved_id", "=", $ids[$i])
            ->where("saver_type", "=", TYPE_ALIAS[$request->type])
            ->where("saver_id", "=", $request->id)
            ->delete();
        }
        return ["message" => "deleted successfully."];
    }

}

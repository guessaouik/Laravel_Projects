<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\React;
use App\Http\Controllers\Helpers\Search;
use App\Http\Controllers\Helpers\View;
use App\Http\Helpers\Paginate;
use App\Http\Resources\Review\ReviewResource;
use App\Models\Review;
use App\Rules\ProfileType;
use App\Rules\ValidateTypePermission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    private React $react;

    private static function addValue($collection, Request $request){
        if ($collection instanceof Model){
            $collection = new Collection($collection);
        }

        foreach ($collection as &$model){
            $record = 
            DB::table("review_ratings")
            ->where("profile_id", "=", $request->id)
            ->where("profile_type", "=", TYPE_ALIAS[$request->type])
            ->where("review_id", "=", $model->getKey())
            ->first();
            $model->value = $record === null ? false : true;
        }
        return $collection;
    }

    public function __construct(){
        $this->react = new React(Review::class, "reviewId", "review_ratings");
    }

    public function changeRating(Request $request){
        $liked = $request->value ?? false;
        if ($liked !== false){
            $this->react->react($request);
            return;
        }

        $this->react->unreact($request);
    }

    public function getRating(Request $request){
        return $this->react->getReaction($request);
    }

    public function getReview(Request $request){
        return (new View(
            Review::class,
            "reviewId",
            ReviewResource::class,
            [
                "type" => ["required", new ProfileType],
                "id" => "required",
                "reviewId" => "required"
            ]
        ))->view($request);
    }

    public function create(Request $request){
        ValidateTypePermission::$defaultAcceptedTypes = [
            "hospital", "clinic", "doctor", "pharmacy", "lab", "mic",
        ];

        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
            "reviewedType" => ["required", new ValidateTypePermission],
            "reviewedId" => "required",
            "rating" => "required",
        ]);

        $review = Review::create([
            "content" => $request->content ?? "",
            "rating" => $request->rating,
        ]);

        DB::table("reviewed_reviewer")->insert([
            "review_id" => $review->getKey(),
            "reviewed_type" => TYPE_ALIAS[$request->reviewedType],
            "reviewed_id" => $request->reviewedId,
            "reviewer_type" => TYPE_ALIAS[$request->type],
            "reviewer_id" => $request->id,
        ]);

        $reviewed = call_user_func_array([TYPE_MODEL[$request->reviewedType], "find"], [$request->reviewedId]);
        $reviewed->rating = round(($reviewed->rating + $review->rating) / 2, 1);
        $reviewed->save();
    }

    public function update(Request $request){
        $request->validate([
            "reviewId" => "required",
        ]);

        $review = Review::find($request->reviewId);
        if ($request->rating === null){
            $review->content = $request->content ?? "";
            $review->save();
            return;
        }
        
        if ($review->rating === $request->rating){
            $review->content = $request->content ?? "";
            $review->save();
            return;
        }

        $reviewed = $review->reviewed;
        $reviewed->rating += round(($request->rating - $review->rating) / $reviewed->reviewers->count(), 1);
        $review->rating = $request->rating;
        $review->content = $request->content ?? "";
        $review->save();
        $reviewed->save();
    }

    public function delete(Request $request){
        $request->validate([
            "reviewId" => "required",
        ]);

        $review = Review::find($request->reviewId);
        $reviewed = $review->reviewed;
        $count = $reviewed->count();

        $reviewed->rating = ($review->rating - ($review->rating / $count)) * ($count / ($count - 1));
        $reviewed->save();
        $review->delete();
    }

    public function getCreatedReviews(Request $request){
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
        ]);

        $model = call_user_func_array([TYPE_MODEL[$request->type], "find"], [$request->id]);

        return ReviewResource::collection(Paginate::paginate(static::addValue($model->createdReviews, $request), $request->perPage ?? DEFAULT_PER_PAGE));
    }

    public function getReviews(Request $request){
        ValidateTypePermission::$defaultAcceptedTypes = [
            "hospital", "clinic", "doctor", "pharmacy", "lab", "mic",
        ];
        $request->validate([
            "type" => ["required", new ValidateTypePermission],
            "id" => "required",
        ]);

        $model = call_user_func_array([TYPE_MODEL[$request->type], "find"], [$request->id]);
        return ReviewResource::collection(Paginate::paginate(static::addValue($model->reviews, $request), $request->perPage ?? DEFAULT_PER_PAGE));
    }

    public function searchCreatedReviews(Request $request){
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
        ]);


        $pattern = $request->pattern ?? "";
        $model = call_user_func_array([TYPE_MODEL[$request->type], "find"], [$request->id]);
        return Search::searchInCollection(
            $model->createdReviews,
            $pattern,
            $request->perPage ?? DEFAULT_PER_PAGE,
            [
                [
                    fn($model) => $model->name ?? $model->firstname . " " . $model->lastname,
                    fn() => "name",
                ],
                "content",
            ],
            ReviewResource::class
        );
    }

    public function searchReviews(Request $request){
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
        ]);


        $pattern = $request->pattern ?? "";
        $model = call_user_func_array([TYPE_MODEL[$request->type], "find"], [$request->id]);
        return Search::searchInCollection(
            $model->reviews,
            $pattern,
            $request->perPage ?? DEFAULT_PER_PAGE,
            [
                [
                    fn($model) => $model->name ?? $model->firstname . " " . $model->lastname,
                    fn() => "name",
                ],
                "content",
            ],
            ReviewResource::class
        );
    }
}

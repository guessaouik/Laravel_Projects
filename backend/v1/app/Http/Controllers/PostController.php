<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Post;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

use App\Http\Resources\CommunityResources\PostResource;
use App\Http\Resources\CommunityResources\PostCollection;

use App\Services\PostQuery;

use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $filter = new PostQuery();
        $queryItems = $filter->transform($request); //This is the format of the filtering in PostQuery : [['column', 'operator', 'value']]


        //when user enter community, the URL will be /post?parent_id=0&page=1

        if (count($queryItems) == 0) {
            return new PostCollection(Post::paginate(15));
        } else {
            return new PostCollection(Post::where($queryItems)->paginate(15));
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $dummy = new Post();
        $posts = Post::create($request->only($dummy->getFillable())) ;
        
        
        $pProfile = [
            "post_id" => $posts->post_id,
            "profile_type" => $request->type, // front
            "profile_id" => $request->profile_id // front
        ];

        // SPECIALTY IS THE SAME BUT IT IS AN ARRAY OF ARRAYS BECAUSE THERE IS MANY SPECIALTY IDs FOR ONE POST
        
        $specialty_ids = $request->specialty_id;
        $specialties = null;
        foreach($specialty_ids as $specialty_id){
            $specialties[] = [
                "specialty_id" => $specialty_id,//
                "component_type" => "po",
                "component_id" => $posts->post_id//
            ];
        }
        DB::table("component_specialty")->insert($specialties);

        DB::table("post_profile")->insert($pProfile);
        
        // updating the replies counter on all parent posts
        $parent_id = $posts->parent_id;
        while ($parent_id != 0) {
            $parent_post = Post::find($parent_id);
            $parent_post->replies++;
            $parent_post->save();
            $posts = $parent_post;
            $parent_id = $posts->parent_id;
        }
        
        return new PostResource($posts); // dont forget the accept Application/json in headers
    }

    /**
     * Display the specified resource.
     */
    public function show($input)
    {
        $post = Post::find((int) $input);
        return new PostResource($post);
    }

    
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->all()); // dont forget the accept Application/json in the request 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
    }

    public function react(Request $request)
    {
        $row_post = Post::find($request->post_id);

        $row_rating = DB::table("post_ratings")->where("post_id", "=" , $request->post_id)
                                               ->where("profile_type", "=" , $request->profile_type)
                                               ->where("profile_id", "=" , $request->profile_id)->get();

        if (count($row_rating) == 0) { 
            $row_post->likes++;
            
            DB::table("post_ratings")->insert([
                "post_id" => $request->post_id,
                "profile_type" => $request->profile_type, // front
                "profile_id" => $request->profile_id // front
            ]);
        }else {
            $row_post->likes--;

            DB::table("post_ratings")->where("post_id", "=" , $request->post_id)
                                    ->where("profile_type", "=" , $request->profile_type)
                                    ->where("profile_id", "=" , $request->profile_id)
                                    ->delete();
        };

        $row_post->save();
    }

    public function save_post (Request $request){ // verify the UML
        $row_save = DB::table("saved_posts")->where("post_id", "=" , $request->post_id)
                                            ->where("saver_type", "=" , $request->profile_type)
                                            ->where("saver_id", "=" , $request->profile_id)->get(); 

        if (count($row_save) == 0) {                
            DB::table("saved_posts")->insert([
                "post_id" => $request->post_id,
                "saver_type" => $request->profile_type, // front
                "saver_id" => $request->profile_id // front
            ]);
        }else {
            DB::table("saved_posts")->where("post_id", "=" , $request->post_id)
                                    ->where("saver_type", "=" , $request->profile_type)
                                    ->where("saver_id", "=" , $request->profile_id)
                                    ->delete();
        };
    }
    

    public function save_profile (Request $request){
        $row_save = DB::table("saved_profiles")->where("saver_type", "=" , $request->saver_type)
                                               ->where("saver_id", "=" , $request->saver_id)
                                               ->where("saved_type", "=" , $request->saved_type)
                                               ->where("saved_id", "=" , $request->saved_id)->get();

        
        if (count($row_save) == 0) {  
            // follow profile              
            DB::table("saved_profiles")->insert([
                "saver_type" => $request->saver_type, // front
                "saver_id" => $request->saver_id, // front
                "saved_type" => $request->saved_type,
                "saved_id" => $request->saved_id
            ]);
        }else {
            // unfollow profile
            DB::table("saved_profiles")->where("saver_type", "=" , $request->saver_type)
                                       ->where("saver_id", "=" , $request->saver_id)
                                       ->where("saved_type", "=", $request->saved_type)
                                       ->where("saved_id", "=",  $request->saved_id)
                                       ->delete();
        };
    }
}

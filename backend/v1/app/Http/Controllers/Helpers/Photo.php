<?php

namespace App\Http\Controllers\Helpers;

use Illuminate\Http\Request;

class Photo{

    public static function getPhotoAbsolutePath(string $path){
        return asset("storage/" . $path);
    }

    public static function save(Request $request, string $attribute, string $type) : string{
        return $request->file($attribute)->store($type, "public");
    }

    public static function saveProfile(Request $request, string $attribute) : string{
        return static::save($request, $attribute, "profile");
    }

    public static function savePost(Request $request, string $attribute) : string{
        return static::save($request, $attribute, "post");
    }

    public static function saveArticle(Request $request, string $attribute) : string{
        return static::save($request, $attribute, "article");
    }
}
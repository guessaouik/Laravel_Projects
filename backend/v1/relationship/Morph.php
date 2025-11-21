<?php

namespace Relationship;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * class defining methods used to retrieve data in morph relationships
 */
class Morph
{
    #region properties
    private static array $modelAlias = [
        "App\\Models\\Hospital" => "h",
        "App\\Models\\Pharmacy" => "ph",
        "App\\Models\\Patient" => "pa",
        "App\\Models\\Lab" => "l",
        'App\\Models\\MIC' => "m",
        "App\\Models\\Clinic" => 'c',
        'App\\Models\\Doctor' => 'd',
        'App\\Models\\Post' => 'p',
        'App\\Models\\Article' => 'a',
        'App\\Models\\MedicalOffice' => 'o'
    ];
    #endregion

    #region private methods

    private static function getModelAlias(string $model){
        return static::$modelAlias[$model];
    }
    #endregion

    #region simple morph relationships

    /**
     * returns the model in a one to one relationship with $owner through a morph pivot
     *
     * @param Model $owner 
     * @param string $pivotTableName the table defining the relationship
     * @param string $foreign name of column referencing owner's primary key
     * @param string $relatedType name of column containing the related tables types
     * @param string $relatedId name of column containing the related tables ids
     * @return MorphModel
     */
    public static function morphsOne(
        Model $owner,
        string $pivotTableName,
        string $foreign,
        string $relatedType,
        string $relatedId
    ): MorphModel {

        $pivotRow = DB::table($pivotTableName)->where($foreign, "=", $owner->getKey())->first();
        $relatedModelName = array_search($pivotRow->{$relatedType}, static::$modelAlias);
        return new MorphModel(call_user_func_array([$relatedModelName, "find"], [$pivotRow->{$relatedId}]), $pivotRow);
    }

    /**
     * returns the model in a one to one relationship with $owner through a morph pivot
     *
     * @param Model $owner
     * @param string $related the qualified class name for the related model (which doesn't have type column)
     * @param string $pivotTableName the table defining the relationship
     * @param string $type name of column containing the type of the owner
     * @param string $id name of column containing the id of the owner
     * @param string $relatedForeign name of column containing the related id
     * @return MorphModel
     */
    public static function hasOneThroughMorph(
        Model $owner,
        string $relatedClass,
        string $pivotTableName,
        string $type,
        string $id,
        string $relatedForeign
    ): MorphModel {
        $pivotRow = DB::table($pivotTableName)
            ->where($type, "=", static::getModelAlias($owner::class))
            ->where($id, "=", $owner->getKey())->first();
        if ($pivotRow === null){
            return new MorphModel(null, null);
        }
        //throw new Error(implode(", ", array_map(fn($key, $value) => "$key : $value", array_keys(get_object_vars($pivotRow)), get_object_vars($pivotRow))));
        return new MorphModel(call_user_func_array([$relatedClass, "find"], [$pivotRow->{$relatedForeign}]), $pivotRow);
    }

    /**
     * returns the models in a one to many relationship with $owner through a morph pivot
     *
     * @param Model $owner 
     * @param string $pivotTableName the table defining the relationship
     * @param string $foreign name of column referencing owner's primary key
     * @param string $relatedType name of column containing the related tables types
     * @param string $relatedId name of column containing the related tables ids
     * @return MorphCollection 
     */
    public static function morphsMany(
        Model $owner,
        string $pivotTableName,
        string $foreign,
        string $relatedType,
        string $relatedId
    ): MorphCollection {
        // get related models in pivot
        $pivotQuery = DB::table($pivotTableName)->where($foreign, "=", $owner->getKey());
        return new MorphCollection($pivotQuery, $relatedId, null, static::$modelAlias, $relatedType);
    }

    /**
     * returns the model in a one to one relationship with $owner through a morph pivot
     *
     * @param Model $owner 
     * @param string $related the qualified class name for the related model (which doesn't have type column)
     * @param string $pivotTableName the table defining the relationship
     * @param string $type name of column containing the type of the owner
     * @param string $id name of column containing the id of the owner
     * @param string $relatedForeign name of column containing the related id
     * @return MorphCollection
     */
    public static function hasManyThroughMorph(
        Model $owner,
        string $relatedClass,
        string $pivotTableName,
        string $type,
        string $id,
        string $relatedForeign
    ): MorphCollection {
        $pivotQuery = DB::table($pivotTableName)
            ->where($type, "=", static::getModelAlias($owner::class))
            ->where($id, "=", $owner->getKey());
        return new MorphCollection($pivotQuery, $relatedForeign, $relatedClass);
    }
    #endregion

    #region complex morph relationships 

    /**
     * returns the related model that is in a one to one relationship with owner, through
     * a morph pivot that joins many tables to many others
     *
     * @param Model $owner model that has one
     * @param string $pivotTableName the name of the morph pivot
     * @param string $type name of column containing owner type
     * @param string $id name of column containing owner id
     * @param string $relatedType name of column containing related types
     * @param string $relatedId name of column containing related ids
     * @param string $ownerAlias the alias of the owner in the morph pivot
     * @param array $relatedAliases the [model => alias] array for the related tables
     * @return MorphModel
     */
    public static function hasOneThroughManyMorphs(
        Model $owner,
        string $pivotTableName,
        string $type,
        string $id,
        string $relatedType,
        string $relatedId,
        string $ownerAlias,
        array &$relatedModelAliases
    ): MorphModel {
        $pivotRow = DB::table($pivotTableName)
            ->where($type, "=", $ownerAlias)
            ->where($id, "=", $owner->getKey())->first();
        $relatedModelName = array_search($pivotRow->{$relatedType}, $relatedModelAliases);
        return new MorphModel(call_user_func_array([$relatedModelName, "find"], [$pivotRow->{$relatedId}]), $pivotRow);
    }

    /**
     * returns the related models that are in a one to many relationship with owner, through
     * a morph pivot that joins many tables to many others
     *
     * @param Model $owner model that has many
     * @param string $pivotTableName the name of the morph pivot
     * @param string $type name of column containing owner type
     * @param string $id name of column containing owner id
     * @param string $relatedType name of column containing related types
     * @param string $relatedId name of column containing related ids
     * @param string $ownerAlias the alias of the owner in the morph pivot
     * @param array $relatedAliases the [model => alias] array for the related tables
     * @return MorphCollection
     */
    public static function hasManyThroughManyMorphs(
        Model $owner,
        string $pivotTableName,
        string $type,
        string $id,
        string $relatedType,
        string $relatedId,
        string $ownerAlias,
        array &$relatedModelAliases
    ): MorphCollection {
        $pivotQuery = DB::table($pivotTableName)
            ->where($type, "=", $ownerAlias)
            ->where($id, "=", $owner->getKey());
        return new MorphCollection($pivotQuery, $relatedId, null, $relatedModelAliases, $relatedType);
    }

    #endregion

}

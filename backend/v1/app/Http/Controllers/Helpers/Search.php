<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Paginate;
use App\Http\Resources\General\SearchCollection;
use App\Http\Resources\General\SearchResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;

class Search extends Controller
{
    /**
     * matches model values to pattern, the values to match are gotten using $columns, adds matched column and the indexes at which the pattern was found to the model. 
     *
     * @param Model $model the model to check
     * @param array $columns can be array of strings, in which case the columns are used as model attributes; or they can be an array of arrays containing two closures, the first accepts a model and returns a string value, the second returns the name to store as the column name.
     * @param string $pattern
     * @return void
     */
    private static function matchesPattern(Model &$model, array $columns, string $pattern){
        $matches = [];
        $indexes = [];
        foreach ($columns as $column){
            if (is_array($column)){
                $valueGetter = $column[0];
                $columnNameGetter = $column[1];
                $index = strpos($valueGetter($model), $pattern);
                if ($index !== false){
                    $matches[] = $columnNameGetter;
                    $indexes[] = $index;
                }
                continue;
            }
            $index = strpos($model->{$column}, $pattern);
            if ($index !== false){
                $matches[] = $column;
                $indexes[] = $index;
            }
        }
        if ($matches !== []){
            $model->matches = $matches;
            $model->indexes = $indexes;
            return true;
        }
        return false;
    }

    /**
     * searches in collection for models which have column (from $columns) values matching pattern,
     * returns a api-compatible paginated resource collection
     *
     * @param Collection|SupportCollection $collection
     * @param string $pattern pattern to look up can be empty string
     * @param integer $perPage the number of records per pagination page
     * @param array $columns the column to match pattern to
     * @param string $modelResource the resource that formats model attributes, eg value: ArticleResource::class
     * @return void
     */
    public static function searchInCollection (
            Collection|SupportCollection $collection,
            string $pattern,
            int $perPage, 
            array $columns,
            string $modelResource
        ){  

        $filteredModels = new Collection();

        foreach ($collection as &$model){
            if (static::matchesPattern($model, $columns, $pattern)){
                $filteredModels->add($model);
            }
        }

        SearchCollection::$pattern = $pattern;
        SearchResource::$resourceClass = $modelResource;
        return new SearchCollection(Paginate::paginate($filteredModels, $perPage));
    }
}

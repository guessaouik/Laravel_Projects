<?php

namespace Relationship;

use BadMethodCallException;
use Error;
use ErrorException;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

class MorphCollection{

    private static array $tableAlias = [
        "hospitals" => "h",
        "pharmacies" => "ph",
        "patients" => "pa",
        "labs" => "l",
        'mics' => "m",
        "clinics" => 'c',
        'doctors' => 'd',
        'posts' => 'p',
        'articles' => 'a',
        'offices' => 'o'
    ];

    private Builder $pivotQuery;
    private ?string $relatedType;
    private string $relatedId;
    private ?string $relatedClass;
    private array $modelAliases;

    /**
     * MorphCollection Constructor
     *
     * @param Builder $pivotQuery a query in the morph table
     * @param string $relatedId the name of the column containing related ids
     * @param string|null $relatedClass fully qualified class name of related table, leave null if the related table is not singular
     * @param array $modelAliases simple class names (without their namespaces) and their aliases
     * @param string|null $relatedType the name of the column containing the related types
     */
    public function __construct(Builder $pivotQuery, string $relatedId, ?string $relatedClass = null, array &$modelAliases = [], string $relatedType = null)
    {
        $this->pivotQuery = $pivotQuery;
        $this->relatedType = $relatedType;
        $this->relatedId = $relatedId;
        $this->relatedClass = $relatedClass;
        $this->modelAliases = $modelAliases;
    }
    
    public function __get($name)
    {
        if ($name === 'pivots'){
            return $this->pivotQuery->get();
        }
        if (array_key_exists($name, static::$tableAlias)){
            return $this->{$name}()->execute();
        }
        throw new Error("property '$name' doesn't exist.");
    }

    /**
     * used to narrow down queries
     * throws error if:
     * - singular table. (BadMethodCallException)
     * - the methods doesn't exist. (BadMethodCallException)
     * - the table doesn't exist in 'multiple' methods. (Exception)
     *
     * @param [type] $name
     * @param [type] $arguments
     * @return MorphCollection|Builder|string|Collection
     */
    public function __call($name, $arguments) : MorphCollection | Builder | string | Collection
    {

        if ($this->relatedType === null){
            throw new BadMethodCallException("method is not available for singular table.");
        }

        if ($name === "get"){
            return $this->execute();
        }

        if ($name === "multiple"){
            $newQuery = $this->pivotQuery->where(function ($query) use ($arguments){
                $tables = $arguments[0] ?? [];
                $isFirst = true;
                foreach ($tables as $table){
                    try{
                        if ($isFirst){
                            call_user_func_array([$query, "where"], [$this->relatedType, "=", static::$tableAlias[$table]]);
                            $isFirst = false;
                            continue;
                        }
                        call_user_func_array([$query, "orWhere"], [$this->relatedType, "=", static::$tableAlias[$table]]);
                    } catch (ErrorException){
                        throw new Exception("table '$table' doesn't exist.");
                    }
                }
            });

            return new MorphCollection(
                $newQuery,
                $this->relatedId,
                $this->relatedClass,
                $this->modelAliases,
                $this->relatedType
            );
        }

        try{
            $query = call_user_func_array([$this->pivotQuery, $name], $arguments);
            return new MorphCollection(
                $query,
                $this->relatedId,
                $this->relatedClass,
                $this->modelAliases,
                $this->relatedType
            );
        } catch (BadMethodCallException){
            if (!array_key_exists($name, static::$tableAlias)){
                throw new BadMethodCallException("method '$name' doesn't exist.");
            }
        }
        $newQuery = $this->pivotQuery->where($this->relatedType, "=", static::$tableAlias[$name]);
        return new MorphCollection(
            $newQuery,
            $this->relatedId,
            $this->relatedClass,
            $this->modelAliases,
            $this->relatedType
        );
    }

    /**
     * uses the query to get collection of result models
     *
     * @return Collection
     */
    public function execute() : Collection{
        $pivotRows = $this->pivotQuery->get();
        $result = [];
        $relatedClass = $this->relatedClass;
        foreach ($pivotRows as $pivotRow){
            if ($this->relatedClass === null){
                $relatedClass = array_search($pivotRow->{$this->relatedType}, $this->modelAliases);
            }
            $result[] = call_user_func_array([$relatedClass, "find"], [$pivotRow->{$this->relatedId}]);
        }
        return new Collection($result);
    }

}
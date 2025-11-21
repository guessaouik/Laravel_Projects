<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\Profile;
use App\Http\Helpers\Paginate;
use App\Http\Resources\Search\DoctorResource;
use App\Http\Resources\Search\InstitutionResource;
use App\Http\Resources\Search\SearchCollection;
use App\Models\State;
use BadMethodCallException;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use TypeError;

class SearchController extends Controller
{
    #region properties
    private Collection $queryResults;
    #endregion

    #region constants
    private const GENERAL_PROVIDERS = [
        "hospitals", "mics", "pharmacies", "labs", "doctors", "clinics"
    ];
    private const SERVICES_PROVIDERS = ["hospitals", "clinics"];
    private const TREATMENTS_PROVIDERS = ["hospitals", "pharmacies", "doctors", "clinics"];
    private const DISEASES_PROVIDERS = ["hospitals", "pharmacies", "doctors", "clinics"];
    private const EQUIPMENTS_PROVIDERS = ["pharmacies"];
    private const TESTS_PROVIDERS = ["labs"];
    private const TECHNOLOGIES_PROVIDERS = ["mics"];
    private const PRIVILEGES_PROVIDERS = ["hospitals", "pharmacies", "clinics", "labs", "mics"];
    private const SPECIALTIES_PROVIDERS = ["doctors", "hospitals", "clinics"];

    private const FILTERS = [
        "treatments", "diseases", "equipments", "tests", "technologies", "privileges",
        "available", "services", "cities", "states", "specialties", "rating"
    ];

    // array of filters that are general
    private const GENERAL_FILTERS = ["cities", "states", "available", "rating"];

    private const PROVIDERS_MODELS = [
        "hospitals"     => "App\\Models\\Hospital",
        "doctors"       => "App\\Models\\Doctor",
        "clinics"       => "App\\Models\\Clinic",
        "labs"          => "App\\Models\\Lab",
        "pharmacies"    => "App\\Models\\Pharmacy",
        "mics"          => "App\\Models\\MIC"
    ];

    private const FILTERS_MODELS = [
        "cities"        => "App\\Models\\City",
        "equipments"    => "App\\Models\\MedicalEquipment",
        "tests"         => "App\\Models\\Test",
        "technologies"  => "App\\Models\\Technology",
        "diseases"      => "App\\Models\\Disease",
        "privileges"    => "App\\Models\\Privilege",
        "treatments"    => "App\\Models\\Treatment",
        "services"      => "App\\Models\\Service",
        "states"        => "App\\Models\\State",
        "specialties"   => "App\\Models\\Specialty"
    ];

    private const FILTERS_METHODS = [
        "cities" => "providers",
        "equipments" => "pharmacies",
        "tests" => "labs",
        "technologies" => "mics",
        "diseases" => "providers",
        "privileges" => "profiles",
        "treatments" => "providers",
        "services" => "providers",
        "specialties" => "components"
    ];
    #endregion
 
    #region helpers
    /**
     * puts the element of a collection in an array, use instead of toArray method to avoid
     * executing the toArray method on the collection items
     *
     * @param Collection $collection
     * @return array
     */
    public static function collectionToArray(Collection $collection) : array{
        $array = [];
        foreach ($collection as $item){
            $array[] = $item;
        }
        return $array;
    }

    /**
     * get the unique models in an array, two models are the same if they are of the same
     * type and have the same id
     *
     * @param array $models
     * @return array
     */
    public static function uniqueModels(array $models) : array{
        $array = [];
        $keys = [];
        foreach ($models as &$model){
            $class = explode("\\", get_class($model));
            $key = $class[count($class) - 1] . $model->getKey();
            $keys[] = $key;
            $array[$key] = $model;
        }
        sort($keys);
        $uniques = [];
        for ($i = 0; $i < count($models); $i++){
            if ($i === 0){
                $uniques[] = $array[$keys[0]];
                continue;
            }
            if ($keys[$i] !== $keys[$i - 1]){
                $uniques[] = $array[$keys[$i]];
            }
        }
        return $uniques;
    }

    private static function getProviderTypesFromFilters(array $filters) : array{
        static::validateFilters($filters);
        $providerTypesFromFilters = static::GENERAL_PROVIDERS;
        foreach($filters as $filter){
            if (in_array($filter, static::GENERAL_FILTERS)){
                continue;
            }
            $providerTypesFromFilters = array_intersect(
                $providerTypesFromFilters,
                constant(static::class. "::" . strtoupper($filter) . "_PROVIDERS")
            );
        }
        return $providerTypesFromFilters;
    }
        
    /**
     * validates filters
     * throws exception if:
     *  -the filter is not valid
     *
     * @param array $filters
     * @return void
     */
    private static function validateFilters(array $filters){
        foreach ($filters as $filter){
            if (!in_array($filter, static::FILTERS) && !in_array($filter, static::GENERAL_FILTERS)){
                throw new Exception("'$filter' is not a valid filter.");
            }
        }
    }

    /**
     * checks the validity of provided provider types
     * throws exception if a provider type is not valid
     *
     * @param array $providerTypes
     * @param array $filters
     * @return array
     */
    private static function validateProviderTypes(array $providerTypes, array $filters){
        $providerTypesFromFilters = static::getProviderTypesFromFilters($filters);

        foreach ($providerTypes as $type){
            if (!in_array($type, $providerTypesFromFilters)){
                throw new Exception("provider '$type' is not valid for the specified query.");
            }
        }
    }

    /**
     * gets all of the providers that have a relationship with a value, in the
     * sense that they either provide, sell or have that value.
     *
     * @param string $class used to get the id of a specific value from its appropriate table
     * @param string $valueColumn the name of the column containing the value
     * @param array $values the values to look up
     * @param array $providerTypes the types of providers needed
     * @return array
     */
    private function getFilterProviders(string $class, string $valueColumn, array $values, array $providerTypes) : SearchController{
        if ($class === "App\\Models\\State"){
            $cities = [];
            foreach ($values as $value){
                $matchingRecords = $class::where($valueColumn, "=", $value)->get();
                foreach ($matchingRecords as $record){
                    $cities = array_merge($cities, $record->cities->pluck("name")->toArray());
                }
            }
            $class = static::FILTERS_MODELS["cities"];
            $values = $cities;
            $valueColumn = "name";
        }

        $methodName = static::FILTERS_METHODS[array_search($class, static::FILTERS_MODELS)];
        $result = [];
        foreach ($values as $value){
            $matchingRecords = $class::where($valueColumn, "=", $value)->get();
            foreach ($matchingRecords as $record){
                try {
                    $result = array_merge($result, 
                        static::collectionToArray(
                            $record
                            ->{$methodName}()
                            ->multiple($providerTypes)
                            ->get()
                        )
                    );
                } catch (BadMethodCallException){
                    $result = array_merge(
                        $result,
                        static::collectionToArray(
                            $record
                            ->{$methodName}
                        )
                    );
                }
            }
        }
        $this->queryResults = (new Collection(static::uniqueModels($result)))->values();
        return $this;
    }

    private function filterProvidersByValues(string $methodName, string $valueColumn, array $values) : SearchController{
        $filterResults = [];
        foreach ($this->queryResults as $model){
            $modelValuesCollection = $model->{$methodName};
            if ($modelValuesCollection === null){
                continue;
            }
            $providerMatchingValues = $modelValuesCollection
                ->filter(
                    fn($filter) => in_array($filter->{$valueColumn}, $values)
                )->toArray();
            if ($providerMatchingValues !== []){
                $filterResults[] = $model;
            }
        }
        $this->queryResults = new Collection($filterResults);
        return $this;
    }

    public function toCollection(){
        return new Collection($this->queryResults);
    }

    #endregion

    #region private methods
    private function cities(array $cities) : SearchController{
        $filterResults = [];
        foreach ($this->queryResults as $model){
            if ($model->city === null){
                continue;
            }
            if (in_array($model->city->name, $cities)){
                $filterResults[] = $model;
            }
        }
        $this->queryResults = new Collection($filterResults);
        return $this;
    }

    private function states(array $states) : SearchController{
        $allCities = [];
        foreach ($states as $state){
            $allCities = array_merge($allCities, State::where("name", "=", $state)->first()->cities->pluck("name")->toArray());
        }
        return $this->cities($allCities);
    }

    private function treatments(array $treatments) : SearchController{
        return $this->filterProvidersByValues("treatments", "name", $treatments);
    }

    private function diseases(array $diseases) : SearchController{
        return $this->filterProvidersByValues("diseases", "name", $diseases);
    }

    private function specialties(array $specialties) : SearchController{
        return $this->filterProvidersByValues("specialties", "name", $specialties);
    }

    private function services(array $services) : SearchController{
        return $this->filterProvidersByValues("services", "name", $services);
    }

    private function equipments(array $equipments) : SearchController{
        return $this->filterProvidersByValues("equipments", "name", $equipments);
    }

    private function tests(array $tests) : SearchController{
        return $this->filterProvidersByValues("tests", "name", $tests);
    }

    private function technologies(array $technologies) : SearchController{
        return $this->filterProvidersByValues("technologies", "name", $technologies);
    }

    private function privileges(array $privileges) : SearchController{
        return $this->filterProvidersByValues("privileges", "name", $privileges);
    }

    private function available(string|array $dateTime) : SearchController{
        if (is_array($dateTime)){
            $dateTime = $dateTime[0];
        }
        if ($dateTime == ""){
            $dateTime = date("d-m-Y H:i");
        }
        $availableProviders = $this->queryResults->filter(function ($provider) use ($dateTime){
            return (new Profile($provider))->isAvailable($dateTime);
        });
        $this->queryResults = $availableProviders;
        return $this;
    }

    private function rating(array|float $ratingInterval) : SearchController{
        $ratingInterval = is_array($ratingInterval) ? $ratingInterval : [$ratingInterval, 5];
        $this->queryResults = $this->queryResults->filter(
            fn($model) => $model->rating >= $ratingInterval[0] && $model->rating <= $ratingInterval[1]
        );
        return $this;
    }

    private function getProvidersByRating(array $providerTypes, array|float $ratingInterval) : SearchController{
        $ratingInterval = is_array($ratingInterval) ? $ratingInterval : [$ratingInterval, 5];
        $result = [];
        foreach ($providerTypes as $type){
            $modelClass = static::PROVIDERS_MODELS[$type];
            $result = array_merge($result, static::collectionToArray(
                $modelClass::whereBetween("rating", $ratingInterval)->get()
            ));
        }
        $this->queryResults = new Collection($result);
        return $this;
    }

    private function nameInResults(string $pattern) : SearchController{
        $matchingProviders = $this->queryResults->filter(function ($provider) use ($pattern){
            $name = property_exists($provider, "name") ?
            $provider->name :
            $provider->firstname . " " . $provider->lastname;
            return str_contains($name, $pattern);
        });
        $this->queryResults = $matchingProviders;
        return $this;
    }

    private function name(string $pattern, array $providerTypes){
        $matchingProviders = [];
        foreach ($providerTypes as $type){
            if ($type !== "doctors"){
                $query = call_user_func_array([static::PROVIDERS_MODELS[$type], "where"], ["name", "like", "%$pattern%"]);
            } else {
                // get the full names of all doctors with their respective id
                $fullNames = call_user_func_array(
                    [static::PROVIDERS_MODELS[$type], "select"],
                    ["doctor_id", DB::raw("concat(firstname, ' ', lastname) as fullname")]
                );
                // select ids where full name matches pattern
                $matchingIds = "select doctor_id from (" .
                    $fullNames->toSql() .
                    ") as ids where fullname like '%$pattern%'";
                // query the doctors table for the ids
                $query = call_user_func_array(
                    [static::PROVIDERS_MODELS[$type], "whereRaw"],
                    ["doctor_id in (" . $matchingIds . ")"]
                );
            }
            $matchingProviders = array_merge($matchingProviders, static::collectionToArray($query->get()));
        }
        $this->queryResults = new Collection($matchingProviders);
        return $this;
    }
    #endregion

    #region public methods
    public function search(Request $request){
        $queryInfo = $request->all();

        $perPage = $queryInfo["perPage"] ?? DEFAULT_PER_PAGE;
        if (isset($queryInfo["perPage"])){
            unset($queryInfo["perPage"]);
        }

        $providerTypes = $queryInfo["providers"] ?? [];
        $excludes = ["providers", "name", "page"];
        // getting filters
        $filters = array_values(array_filter(
            array_keys($queryInfo),
            fn($key) => !in_array($key, $excludes) && $queryInfo[$key] !== null
        ));

        if ($providerTypes === []){
            $providerTypes = static::getProviderTypesFromFilters($filters);
        } else { static::validateProviderTypes($providerTypes, $filters); }


        $startingIndex = 0;
        if (isset($queryInfo["name"])){
            $providers = $this->name($queryInfo["name"], $providerTypes);
        } else if ($filters !== [] && $filters[0] !== "available"){
            $startingIndex = 1;
            if ($filters[0] === "rating"){
                $providers = $this->getProvidersByRating($providerTypes, $queryInfo[$filters[0]]);
            } else {
                $values = $queryInfo[$filters[0]];
                $providers = $this->getFilterProviders(
                    static::FILTERS_MODELS[$filters[0]],
                    "name",
                    is_array($values) ? $values : [$values],
                    $providerTypes
                );
            }
        } else {
            $providers = $this->name("", $providerTypes);
        }
        for ($i = $startingIndex; $i < count($filters); $i++){
            $values = $queryInfo[$filters[$i]];
            $providers = $providers->{$filters[$i]}(is_array($values) ? $values : [$values]);
        }

        $result = [];
        foreach ($this->toCollection() as $model){
            if ($model instanceof \App\Models\Doctor){
                $result[] = new DoctorResource($model);
                continue;
            }
            $result[] = new InstitutionResource($model);
        }
        return new SearchCollection(Paginate::paginate(new Collection($result), $perPage));

    }

    private static function getStateMatchingCities(Request $request) : array{
        $statePattern = $request->state ?? "";
        $cityPattern = $request->city ?? "";
        $stateIds = call_user_func_array(["App\\Models\\State", "where"], ["name", "like", "%$statePattern%"])->pluck("state_id")->toArray();
        $nameQuery = call_user_func_array(["App\\Models\\City", "where"], ["name", "like", "%$cityPattern%"]);
        $cities = call_user_func_array([$nameQuery, "whereIn"], ["state_id", $stateIds])->pluck("name")->toArray();
        return $cities;
    }
    
    public function filterSearch(Request $request) : Collection{
        $filterInfo = $request->all();
        if (array_key_exists("city", $filterInfo)){
            return new Collection(["values" => static::getStateMatchingCities($request)]);
        }
        $filter = array_keys($filterInfo)[0];
        $pattern = $filterInfo[$filter];
        if ($filter !== "equipment"){
            $class = "App\\Models\\" . ucfirst(strtolower($filter));
        } else {
            $class = "App\\Models\\MedicalEquipment";
        }
        try{
            $values = call_user_func_array([$class, "where"], ["name", "like", "%$pattern%"])->pluck("name")->toArray();
            return new Collection(["values" => $values]);
        } catch (TypeError $e){
            throw new Exception("'$filter' is not a filter.");
        }
    }

    public function filters(Request $request){
        $providers = $request->providers;
        if ($providers === null){
            $providers = [];
        } else { $providers = is_array($providers) ? $providers : [$providers]; }
        $filters = [];
        foreach (static::FILTERS as $filter){
            if (in_array($filter, static::GENERAL_FILTERS)){
                $filters[] = $filter;
                continue;
            }
            $providerTypesFromFilter = constant(static::class . "::" . strtoupper($filter) . "_PROVIDERS");
            if (array_values($providers) == array_values(array_intersect($providers, $providerTypesFromFilter))){
                $filters[] = $filter;
            }
        }
        return new Collection(["filters" => $filters]);
    }
    #endregion

}

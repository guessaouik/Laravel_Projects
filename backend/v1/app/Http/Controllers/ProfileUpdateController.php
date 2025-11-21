<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\Photo;
use App\Http\Controllers\Helpers\Profile;
use App\Http\Controllers\Helpers\StringOperator;
use App\Models\Schedule;
use App\Models\User;
use App\Rules\ProfileType;
use DateTime;
use Error;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileUpdateController extends Controller
{

    #region constants
    private const TYPE_METHODS = [
        "hospital"  => ["photos", "socials", "city", "services", "treatments", "diseases", "specialties", "privileges", "doctors"],
        "clinic"    => ["photos", "socials", "city", "schedule", "services", "treatments", "diseases", "specialties", "privileges", "doctors"],
        "doctor"    => ["photos", "socials", "city", "schedule", "specialties", "diseases", "treatments", "institutions"],
        "pharmacy"  => ["photos", "socials", "city", "privileges", "diseases", "treatments", "equipments"],
        "mic"       => ["photos", "socials", "city", "privileges", "schedule", "technologies"],
        "lab"       => ["photos", "socials", "city", "privileges", "schedule", "tests"],
        "patient"   => ["photos", "socials", "privileges"],
    ];

    private const TYPE_FILTERS = [
        "hospital"  => ["city", "services", "treatments", "diseases", "specialties", "privileges"],
        "clinic"    => ["city", "schedule", "services", "treatments", "diseases", "specialties", "privileges"],
        "doctor"    => ["city", "schedule", "specialties", "diseases", "treatments", "institutions"],
        "pharmacy"  => ["city", "privileges", "diseases", "treatments", "equipments"],
        "mic"       => ["city", "privileges", "schedule", "technologies"],
        "lab"       => ["city", "privileges", "schedule", "tests"],
        "patient"   => ["privileges"],       
    ];

    private const FILTER_MORPH_PIVOT = [
        "city" => ["city_provider", "provider"],
        "services" => ["provider_service", "provider"],
        "treatments" => ["provider_treatment", "provider"],
        "specialties" => ["component_specialty", "component"],
        "diseases" => ["disease_provider", "provider"],
        "schedule" => ["provider_schedule", "provider"],
        "privileges" => ["privilege_profile", "profile"],
    ];

    #endregion

    #region properties
    private ?Model $model;
    #endregion

    private function privileges(Request $request){
        $privilegeIds = $request->privilegeIds ?? [];

        foreach ($privilegeIds as $id){
            DB::table("privilege_profile")->updateOrInsert([
                "privilege_id" => $id,
                "profile_type" => TYPE_ALIAS[$request->type],
                "profile_id" => $this->model->getKey(),
            ]);
        }

        return $this;
    }

    private function specialties(Request $request){
        $specialtyIds = $request->specialtyIds ?? [];

        foreach ($specialtyIds as $id){
            DB::table("component_specialty")->updateOrInsert([
                "specialty_id" => $id,
                "component_type" => TYPE_ALIAS[$request->type],
                "component_id" => $this->model->getKey(),
            ]);
        }

        return $this;
    }

    private function treatments(Request $request){
        $treatmentIds = $request->treatmentIds ?? [];

        foreach ($treatmentIds as $id){
            DB::table("provider_treatment")->updateOrInsert([
                "treatment_id" => $id,
                "provider_type" => TYPE_ALIAS[$request->type],
                "provider_id" => $this->model->getKey(),
            ]);
        }

        return $this;
    }

    private function diseases(Request $request){
        $diseaseIds = $request->diseaseIds ?? [];

        foreach ($diseaseIds as $id){
            DB::table("disease_provider")->updateOrInsert([
                "disease_id" => $id,
                "provider_type" => TYPE_ALIAS[$request->type],
                "provider_id" => $this->model->getKey()
            ]);
        }

        return $this;
    }

    private function city(Request $request){
        if ($request->cityId){
            DB::table("city_provider")->updateOrInsert([
                "city_id" => $request->cityId,
                "provider_type" => TYPE_ALIAS[$request->type],
                "provider_id" => $this->model->getKey()
            ]);
        }
        return $this;
    }

    private function services(Request $request){
        $serviceIds = $request->serviceIds ?? [];

        foreach ($serviceIds as $id){
            DB::table("provider_service")->updateOrInsert([
                "service_id" => $id,
                "provider_type" => TYPE_ALIAS[$request->type],
                "provider_id" => $this->model->getKey()
            ]);
        }

        return $this;
    }

    private function equipments(Request $request){
        $equipmentIds = $request->equipmentIds ?? [];

        foreach ($equipmentIds as $id){
            DB::table("equipment_pharmacy")->updateOrInsert([
                "equipment_id" => $id,
                "pharmacy_id" => $this->model->getKey()
            ]);
        }

        return $this;
    }

    private function technologies(Request $request){
        $technologyIds = $request->technologyIds ?? [];

        foreach ($technologyIds as $id){
            DB::table("mic_technology")->updateOrInsert([
                "technology_id" => $id,
                "mic_id" => $this->model->getKey()
            ]);
        }

        return $this;
    }

    private function tests(Request $request){
        $testIds = $request->testIds ?? [];

        foreach ($testIds as $id){
            DB::table("lab_test")->updateOrInsert([
                "test_id" => $id,
                "lab_id" => $this->model->getKey()
            ]);
        }

        return $this;
    }

    private function institutions(Request $request){
        $institutions = $request->institutions ?? [];

        foreach ($institutions as $institution){
            DB::table("doctor_institution")->updateOrInsert([
                "doctor_id" => $this->model->getKey(),
                "institution_type" => $institution[0],
                "institution_id" => $institution[1]
            ]);
        }

        return $this;
    }

    private static function formatIntervals(array $intervals){
        foreach ($intervals as &$interval){
            foreach ($interval as &$part){
                $part = (new DateTime($part))->format("H:i");
            }
        }
        return $intervals;
    }

    private static function sortIntervals(array $intervals){
        $intervals = static::formatIntervals($intervals);
        foreach ($intervals as &$interval){
            sort($interval);
        }
        for ($i = count($intervals) - 1; $i > 0; $i--){
            for ($j = 0; $j < $i; $j++){
                if ($intervals[$j + 1][0] < $intervals[$j][0]){
                    $x = $intervals[$j + 1];
                    $intervals[$j + 1] = $intervals[$j];
                    $intervals[$j] = $x;
                }
            }
        }
        return $intervals;
    }

    private static function intervalUnion(array $intervals){
        $intervals = static::sortIntervals($intervals);

        $unifiedIntervals = [];
        foreach ($intervals as $interval1){
            $hasCommonElements = false;
            foreach ($unifiedIntervals as &$interval2){
                if ($interval1[0] < $interval2[0] && $interval2[0] <= $interval1[1]){
                    $interval2[0] = $interval1[0];
                    $hasCommonElements = true;
                }
                if ($interval2[1] < $interval1[1] && $interval1[0] <= $interval2[1]){
                    $interval2[1] = $interval1[1];
                    $hasCommonElements = true;
                }
                if ($hasCommonElements){
                    break;
                }
            }
            if (!$hasCommonElements){
                $unifiedIntervals[] = $interval1;
            }
        }

        return $unifiedIntervals;
    }

    private function schedule(Request $request){
        $schedule = $request->schedule ?? [];
        if ($schedule === []){
            return;
        }
        foreach ($schedule as $day => $daySchedule){
            $schedule[$day] = static::intervalUnion($daySchedule ?? []);
        }

        $days = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
        $toInsert = [];
        foreach ($days as $day){
            if (!isset($schedule[$day])){
                $toInsert[$day] = null;
                continue;
            }
            $formattedIntervals = [];
            if ($schedule[$day] !== null){
                foreach ($schedule[$day] as $interval){
                    $formattedIntervals[] = (new DateTime($interval[0]))->format("H:i") . "-" . (new DateTime($interval[1]))->format("H:i");
                }
            }
            $toInsert[$day] = $formattedIntervals === [] ? null : implode(";", $formattedIntervals);
        }

        if ($request->scheduleId !== null){
            Schedule::where("schedule_id", "=", $request->scheduleId)->update($toInsert);
            return;
        }

        $schedule = Schedule::create($toInsert);

        DB::table("provider_schedule")->updateOrInsert([
            "schedule_id" => $schedule->getKey(),
            "provider_type" => TYPE_ALIAS[$request->type],
            "provider_id" => $this->model->getKey()
        ]);
        return $this;
    }

    private function socials(Request $request){
        if ($request->socials !== null){
            $model = TYPE_MODEL[$request->type];
            $model = call_user_func_array([$model, "find"], [$request->id]);
            $model->socials = implode(";", $request->socials);
            $model->save();
        }
        return $this;
    }

    private function doctors(Request $request){
        $doctorIds = $request->doctorIds ?? [];

        foreach ($doctorIds as $id){
            DB::table("doctor_institution")->updateOrInsert([
                "doctor_id" => $id,
                "institution_type" => TYPE_ALIAS[$request->type],
                "institution_id" => $request->id,
            ]);
        }

        return $this;
    }

    private function photos(Request $request){
        if ($request->type === "doctor" || $request->type === "patient"){
            if ($request->photo === null){
                return $this;
            }
            $this->model->photo = Photo::saveProfile($request, "photo");
            $this->model->save();
            return $this;
        }

        if ($request->photos === null){
            return $this;
        }
        $this->model->photos = PROFILE_PHOTO_INDICATOR . ":" . Photo::saveProfile($request, "photo");
        $this->model->save();
        return $this;
        /*
        if (is_string($request->photos)){
            $this->model->photos = PROFILE_PHOTO_INDICATOR . ":" . Photo::saveProfile($request, "photo");
            $this->model->save();
            return $this;
        }

        $otherPhotos = $request->photos["other"] ?? [];
        $photos = [PROFILE_PHOTO_INDICATOR . ":" . Photo::saveProfile($request, "photo")];
        foreach ($otherPhotos as $photo){
            $photos[] = Photo::saveProfile($request, "photo"); 
        }
        $this->model->photos = implode(";", $photos);
        $this->model->save();
        return $this;
        */
    }

    private static function hashPassword(array $values){
        foreach ($values as $key => &$value){
            if ($key === "password"){
                $value = Hash::make($value);
            }
        }
        return $values;
    }

    private function updateAttributes(Request $request){
        $modelClass = TYPE_MODEL[$request->type];
        $dummy = new $modelClass();
        $attributes = array_diff($dummy->getFillable(), ["socials"]);
        $arr = StringOperator::arrayKeysToSnakeCase($request->only(StringOperator::arrayValuesToCamelCase($attributes)));
        if ($this->model === null){
            $model = call_user_func_array([$modelClass, "create"], [static::hashPassword(StringOperator::arrayKeysToSnakeCase($request->only(StringOperator::arrayValuesToCamelCase($attributes))))]);
            $model->save();
            $this->model = $model;
            // create new user
            $user = User::create([
                "email" => $model->email,
                "password" => $model->password,
                // add type and id columns
            ]);
            DB::table("profile_user")->insert([
                "user_id" => $user->getKey(),
                "profile_type" => TYPE_ALIAS[$request->type],
                "profile_id" => $model->getKey(),
            ]);
            return $this;
        }

        call_user_func_array([$modelClass, "where"], [$dummy->getKeyName(), "=", $request->id])->update(static::hashPassword(StringOperator::arrayKeysToSnakeCase($request->only(StringOperator::arrayValuesToCamelCase($attributes)))));
        if ($request->exists("email")){
            $user = $this->model->user;
            if ($user !== null){
                $user->email = $request->email;
                $user->save();
            }
        }
        return $this;
    }

    public function updateProfile(Request $request){
        $request->validate([
            "type" => ["required", new ProfileType],
        ]);
        $this->model = null;
        if ($request->id !== null){
            $this->model = call_user_func_array([TYPE_MODEL[$request->type], "find"], [$request->id]);
        }
        $this->updateAttributes($request);

        foreach (static::TYPE_METHODS[$request->type] as $method){
            call_user_func_array([$this, $method], [$request]);
        }
    }

    private function deleteFromTable(Request $request, string $tableName, string $typeColumn, string $idColumn){
        DB::table($tableName)
        ->where($typeColumn, "=", TYPE_ALIAS[$request->type])
        ->where($idColumn, "=", $request->id)
        ->delete();
    }

    public function delete(Request $request){
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
        ]);

        $model = call_user_func_array([TYPE_MODEL[$request->type], "find"], [$request->id]);
        $model->user->delete();
        foreach (static::TYPE_FILTERS[$request->type] as $filter){
            if (isset(static::FILTER_MORPH_PIVOT[$filter])){
                $filterInfo = static::FILTER_MORPH_PIVOT[$filter];
                $this->deleteFromTable($request, $filterInfo[0], $filterInfo[1] . "_type", $filterInfo[1] . "_id");
            }
        }

        // delete reviews
        $this->deleteFromTable($request, "reviewed_reviewer", "reviewer_type", "reviewer_id");

        // delete review ratings
        $this->deleteFromTable($request, "review_ratings", "profile_type", "profile_id");

        // delete articles 
        $this->deleteFromTable($request, "article_provider", "provider_type", "provider_id");

        // delete article ratings
        $this->deleteFromTable($request, "article_ratings", "profile_type", "profiled_id");

        // delete posts 
        $this->deleteFromTable($request, "post_profile", "profile_type", "profile_id");

        // delete post ratings
        $this->deleteFromTable($request, "post_ratings", "profile_type", "profile_id");

        $model->delete();

        return ["message" => "deleted successfully."];
    }
    
}

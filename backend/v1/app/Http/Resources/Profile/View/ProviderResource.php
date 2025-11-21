<?php

namespace App\Http\Resources\Profile\View;

use App\Http\Controllers\Helpers\Profile;
use Error;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
{

    public static function addValueColumn(string $column, Model $model, array &$values){
        if ($model->{$column} === null){
            $values[$column] = null;
            return;
        }
        $values[$column] = $model->{$column}->pluck("name")->toArray();
    }

    public static function addValueColumns(array $columns, Model $model, array &$values){
        foreach ($columns as $column){
            static::addValueColumn($column, $model, $values);
        }
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profile = new Profile($this->resource);
        $city = $this->city;
        return [
            "id" => $this->resource->getKey(),
            "type" => array_search($this->resource::class, TYPE_MODEL),
            "email" => $this->email,
            "phone" => $this->phone,
            "name" => $this->name,
            "longitude" => $this->longitude,
            "latitude" => $this->latitude,
            "address" => $this->address,
            "socials" => $profile->getSocialsArray(),
            "about" => $this->about,
            "views" => $this->views,
            "rating" => $this->rating,
            "status" => $profile->isAvailable(),
            "city" => $city === null ? null : $city->name,
            "state" => $city === null ? null : ($city->state === null ? null : $city->state->name),
            "schedule" => $profile->getDisplaySchedule($request->offset ?? SCHEDULE_DAYS_DEFAULT_OFFSET, $request->number ?? SCHEDULE_DAYS_DEFAULT_NUMBER),
        ];
    }
}

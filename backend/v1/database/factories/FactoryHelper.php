<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FactoryHelper extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    #region constants
    private const DIR_SAMPLES = [
        'opt', 'man', 'this', 'is', 'so', 'interesting','lampp', 'you',
        'should', 'change', 'the', 'values', 'later', 'though'
    ];

    private const MEME_ACCOUNTS = [
        'get_a_life', 'no_bitches?', 'giga_chad', 'rslhali_m3a_lil',
        'noice', 'dr. mbyadayin', 'man_coding_sucks', '*outro_music_intensifies*',
        "*chuckles*i'm in danger"
    ];

    private const TLD = [
        '.com', '.dz', '.uk', '.org', '.co', 'edu', '.gov'
    ];

    public const WEBSITES = [
        'www.instagram',
        'www.facebook',
        'www.reddit',
        'www.youtube',
        'www.medcare',
        'www.telegram',
        'www.meta'
    ];

    public const QUARTERS = [
        "00", "15", "30", "45"
    ];
    #endregion

    private static function getAccountName(bool $meme = true){
        $obj = new FactoryHelper();
        return $obj->faker->boolean(5) ? static::MEME_ACCOUNTS[array_rand(static::MEME_ACCOUNTS)] : $obj->faker->name();
    }

    public static function getRandomLinks(int $numberOfLinks = 1){
        $links = [];
        for ($i = 0; $i < $numberOfLinks; $i++){
            $website = static::WEBSITES[array_rand(static::WEBSITES)];
            $tld = static::TLD[array_rand(static::TLD)];
            $account = static::getAccountName();
            $links[] = $website . $tld . '/' . $account;
        }
        return implode(', ', $links);
    }

    public static function getRandomPaths(int $numOfPaths = 1) : string{
        $paths = [];
        $keys = array_rand(static::DIR_SAMPLES, rand(1, count(static::DIR_SAMPLES)));
        $keys = is_array($keys) ? $keys : [$keys];
        for ($i = 0; $i < $numOfPaths; $i++){
            $paths[] = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array_map(
                fn ($key) => static::DIR_SAMPLES[$key],
                $keys
            )) . DIRECTORY_SEPARATOR;
        }
        return implode(";", $paths);
        
    }

    /**
     * values for (longitude, latitude, address, photo(s), socials, about, status)
     *
     * @param boolean $isPlural is it photo or photos, and changes the number of provided links accordingly
     * @return array
     */
    public static function provider(string $type = "", bool $isPlural = true) : array{
        $obj = new FactoryHelper();
        $maxNumInRand = 10;
        return [
            'email' => $obj->faker->email(),
            'phone' => $obj->faker->boolean() ? $obj->faker->phoneNumber() : null,
            'password' => Hash::make($obj->faker->password(8)),
            'longitude' => $obj->faker->randomFloat(2, 1, 100),
            'latitude' => $obj->faker->randomFloat(2, 1, 100),
            'address' => $obj->faker->address(),
            'photo' . ($isPlural ? 's' : '') => $type === "" ? null : static::getProfilePhoto($type),
            'socials' => null,
            "about" => "this is a(n) " . strtolower($type) . " a provider in medhub.",
            "rating" => rand(1, 50) / 10,
            'status' => $obj->faker->boolean()
        ];
    }

    /**
     * values for (firstname, lastname, address, photo, socials)
     *
     * @return array
     */
    public static function person(string $type = "") : array{
        $obj = new FactoryHelper();
        $maxNumInRand = 10;
        return [
            'email' => $obj->faker->email(),
            'phone' => $obj->faker->boolean() ? $obj->faker->phoneNumber() : null,
            'password' => Hash::make($obj->faker->password()),
            'firstname' => static::getFirstName(),
            'lastname' => static::getLastName(),
            'address' => $obj->faker->address(),
            'photo' => $type !== "" ? static::getProfilePhoto($type) : "",
            'socials' => static::getRandomLinks(rand(1, $maxNumInRand))
        ];
    }



    public static function getProfilePhoto(string $type){
        $photosPath = dirname(__DIR__).DIRECTORY_SEPARATOR."photos". DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
        $photoFiles = array_filter(scandir($photosPath), fn($path) => $path !== "." && $path !== "..");
        $path = "public/images/" . $type . "/" . $photoFiles[array_rand($photoFiles)];
        return $type === "Doctor" ? $path : PROFILE_PHOTO_INDICATOR . ":" . $path;
    }

    private static array $firstNames = [];

    private static array $lastNames = [];

    private static function loadNames(){
        if (count(static::$firstNames) !== 0){
            return;
        }
        
        $file = fopen(dirname(__DIR__) . DIRECTORY_SEPARATOR . "photos" . DIRECTORY_SEPARATOR . "Filters" . DIRECTORY_SEPARATOR . "Doctor.txt", "r");
        $fullNames = fgetcsv($file);
        $i = 0;
        foreach ($fullNames as $fullName){
            [static::$firstNames[$i], static::$lastNames[$i]] = explode(" ", $fullName);
            $i++;
        }
    }

    public static function getInstitutionName(){
        $file = fopen(dirname(__DIR__). "photos/Filters/Institution.txt", "r");
        $names = fgetcsv($file);
        return $names[array_rand($names)];
    }

    public static function getFirstName(){
        static::loadNames();
        return static::$firstNames[array_rand(static::$firstNames)];
    }

    public static function getLastName(){
        static::loadNames();
        return static::$lastNames[array_rand(static::$lastNames)];
    }

    /**
     * values for (name, longitude, latitude, address, photo, socials, about, status)
     *
     * @return array
     */
    public static function institution(string $type = "") : array{
        $obj = new FactoryHelper();
        return array_merge(['name' => $obj->faker->company()], static::provider($type));
    }
/*
    private static function getTimeInterval() : string{
        $hour1 = rand(00, 23);
        $hour2 = rand($hour1, 23);
        $quarter1 = static::QUARTERS[array_rand(static::QUARTERS)];
        if ($hour1 == $hour2){
            $availableQuarters = array_slice(static::QUARTERS, array_search($quarter1, static::QUARTERS));
            array_shift($availableQuarters);
            if ($availableQuarters === []){
                if ($hour2 === 23){
                    $hour1--;
                } else {
                    $hour2++;
                }
                $quarter2 = static::QUARTERS[array_rand(static::QUARTERS)];
            } else {
                $quarter2 = $availableQuarters[array_rand($availableQuarters)];
            }
        } else $quarter2 = static::QUARTERS[array_rand(static::QUARTERS)];
        return "$hour1:$quarter1-$hour2:$quarter2";
    }

    private static function getDaySchedule(int $maxNumIntervals) : string{
        $numIntervals = rand(1, $maxNumIntervals);
        $intervals = [];
        for ($i = 0; $i < $numIntervals; $i++){
            $intervals[] = static::getTimeInterval();
        }
        return implode(";", $intervals);
    }
*/
    private static function getTime() : string{
        $hour = rand(8, 22);
        $hour = ($hour < 10 ? "0" : "") . $hour;
        return $hour . ":" . static::QUARTERS[array_rand(static::QUARTERS)];
    }

    public static function getDaySchedule(int $maxNumIntervals){
        $numTimes = rand(1, $maxNumIntervals) * 2;
        $times = [];
        for ($i = 0; $i < $numTimes; $i++){
            do {
                $time = static::getTime();
            } while(in_array($time, $times));
            $times[] = $time;
        }
        sort($times, SORT_STRING);
        $addDash = true;
        $result = "";
        foreach ($times as $time){
            $result .= $time . ($addDash ? "-" : ";");
            $addDash = !$addDash;
        }
        return substr($result, 0, -1);
    }

    public static function getWeekSchedule(int $maxNumIntervals = 5) : array{
        return [
            "saturday" => fake()->boolean() ? null : static::getDaySchedule($maxNumIntervals),
            "sunday" => fake()->boolean() ? null : static::getDaySchedule($maxNumIntervals),
            "monday" => fake()->boolean() ? null : static::getDaySchedule($maxNumIntervals),
            "tuesday" => fake()->boolean() ? null : static::getDaySchedule($maxNumIntervals),
            "wednesday" => fake()->boolean() ? null : static::getDaySchedule($maxNumIntervals),
            "thursday" => fake()->boolean() ? null : static::getDaySchedule($maxNumIntervals),
            "friday" => fake()->boolean() ? null : static::getDaySchedule($maxNumIntervals)
        ];
    }
}

echo FactoryHelper::getDaySchedule(5) . PHP_EOL;
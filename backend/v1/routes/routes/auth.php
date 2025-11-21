<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Patient;
use App\Models\Hospital;
use App\Models\MIC;
use App\Models\Lab;
use App\Models\Pharmacy;
use App\Models\Clinic;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\MailResetPasswordRequest;
use Illuminate\Validation\Rules\Password;
use App\Models\Doctor;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SocialLoginController;
use App\Events\NewMessageSent;
use App\Http\Controllers\Helpers\Photo;
use App\Http\Requests\GetMessageRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\GetChatRequest;
use App\Http\Requests\StoreChatRequest;
use App\Models\Specialty;
use App\Models\State;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->json(['message' => 'CSRF cookie set']);});  */
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
    Route::get('auth/{provider}/redirdct' , [AuthSocialLoginController::class , 'Redirect'])->name('auth.socialite.redirect');
    Route::get('auth/{provider}/callback' , [AuthSocialLoginController::class , 'Callback'])->name('auth.socialite.callback');
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    Route::post('FS-register', function (Request $request) {
        $request->validate([
            'email' => 'required|string|email|unique:users',
            'type' =>'required' ,
            
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            return response()->json([
                'message' => 'Already exist account with this mail'
            ], 404);
        }
        $user = new User();
        $user->email = $request->email ; 
        $user->password = Hash::make($request->password); 
        $code =  rand(0000, 9999);
        $user->code_verification = str_repeat("0", 4 - strlen("" . $code)) . $code;
        $user->type = $request->type;
        $emailData = array(
            'heading' => 'EMAIL CONFIRMATION',
            'name' => 'CLIENT',
            'email' => $request->email,
            'code' => $user->code_verification
        );
        Mail::to($emailData['email'])->send(new MailResetPasswordRequest($emailData));
        if ($user->save()){
            $user = User::find($user->getKey());
            return response()->json([
                'message' => 'We have sent a verification code to your provider email to confirm code' , 
                'CODE' => $user->toArray()["code_verification"]
        ], 200); 
        }
        else {
            return response()->json([
                'message' => 'Bad Request' 
            ], 400);
        }

        
    });
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    Route::post('SS-register', function (Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'code_verification' =>'required' , 
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                "message" => "invalid email."
            ], 404);
        }

        if ($user->code_verification === $request->code_verification){
            return ["message" => "right code."];
        }
        return response()->json([
            'message' => 'wrong code.', 
        ], 404); 
    });
        
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    Route::post('/register' , function (Request $request){
            $request->validate([
                'email' => 'required|string|max:190|email',
                'type' =>'required'
                
            ]);
            $profileId = 0;
            $user = User::where('email', "=", $request->email)->first();
            if ($user === null) {
                return response()->json([
                    'message' => 'Fendi Fix the request please'
                ], 404);
            }

            if($user->type == 'PATIENT'){
                $request->validate([
                    'email' => 'required|string|max:190|email|unique:patients',
                    //'image' => 'image|mimes:jpeg,jpg,png,svg' ,
                    'firstname' =>'required|string|max:20' , 
                    'lastname' =>'required|string|max:20' , 
                    'gender' =>'required|max:1',
                    'bloodtype' =>'required|max:3',
                    'phone' =>'required|max:10' , 
                    'dateofbirth' =>'required|max:10' ,
                ]);
                   
                $patient = new Patient();
                $patient->email = $user->email ; 
                $patient->firstname = $request->firstname ; 
                $patient->lastname = $request->lastname ; 
                $patient->phone = $request->phone ; 
                if($request->hasFile('image')){
                    $filename=Photo::saveProfile($request, "image");
                }else{
                    $filename=Null;
                }
                $patient->photo = $filename ; 
                $patient->socials = $request->socials ; 
                $patient->gender = $request->gender ; 
                $patient->blood_type = $request->bloodtype ; 
                $patient->password = $user->password;
                $patient->address = $request->address;
    
    
          //  $result = $request->file('photo')->storeOnCloudinary();    
            if ($patient->save()) {
                $profileId = $patient->getKey();
            } else {
                return response()->json([
                    'message' => 'Some error occurred, please try again'
                ], 500);
            }
        
        } else if($user->type == 'DOCTOR'){
                $request->validate([
                    'email' => 'required|string|max:190|email|unique:doctors',
                    'image' => 'image|mimes:jpeg,jpg,png,svg' ,
                    'phone' =>'required|max:10' ,
                    'firstname' =>'required|string|max:20' , 
                    'lastname' =>'required|string|max:20' , 
                ]);
                $doctor = new Doctor();
                $doctor->email = $user->email ; 
                $doctor->firstname = $request->firstname ; 
                $doctor->lastname = $request->lastname ; 
                $doctor->phone = $request->phone ; 
                if($request->hasFile('image')){
                    $filename=Photo::saveProfile($request, "image");
                }else{
                    $filename=Null;
                }
                $doctor->photo = $filename ;        
                $doctor->password = $user->password;

          //  $result = $request->file('photo')->storeOnCloudinary();    
          //  $doctor->photo = $result->getPath(); 
            if ($doctor->save()) {
                $profileId = $doctor->getKey();
            } else {
                return response()->json([
                    'message' => 'Some error occurred, please try again'
                ], 500);
            }
        
            }
            else if($user->type == 'HOSPITAL'){
                $request->validate([
                    'email' => 'required|string|max:190|email|unique:hospitals',
                    //'image' => 'image|mimes:jpeg,jpg,png,svg' ,
                    'phone' =>'required|max:10',
                    'name' =>'required|string|max:20' , 
                    ]);
                $hospital = new Hospital();
                $hospital->email = $user->email ; 
                $hospital->name = $request->name ; 
                $hospital->phone = $request->phone ; 
                if($request->hasFile('image')){
                    $filename=Photo::saveProfile($request, "image");
                }else{
                    $filename=Null;
                }
                $hospital->photos = PROFILE_PHOTO_INDICATOR . ":" . $filename ; 
                $hospital->password = $user->password;
                $hospital->address = $request->address;
    
          //  $result = $request->file('photo')->storeOnCloudinary();    
          //  $hospital->photo = $result->getPath(); 
            if ($hospital->save()) {
                $profileId = $hospital->getKey();
                
            } else {
                return response()->json([
                    'message' => 'Some error occurred, please try again'
                ], 500);
            }
        
            }
            else if($user->type == 'LAB'){
                $request->validate([
                    'email' => 'required|string|max:190|email|unique:labs',
                    //'image' => 'image|mimes:jpeg,jpg,png,svg' ,
                    'phone' =>'required|max:10' , 'image' => 'image|mimes:jpeg,jpg,png,svg' ,
                    'name' =>'required|string|max:20' , 
                    'address' => 'required' , 
                    ]);
                $lab = new Lab();
                $lab->email = $user->email ; 
                $lab->name = $request->name ; 
                $lab->phone = $request->phone ; 
                if($request->hasFile('image')){
                    $filename=Photo::saveProfile($request, "image");
                }else{
                    $filename=Null;
                }
                $lab->photos = PROFILE_PHOTO_INDICATOR . ":" . $filename ;  
                $lab->password = $user->password;
                $lab->address = $request->address;
    
          //  $result = $request->file('photo')->storeOnCloudinary();    
          //  $lab->photo = $result->getPath(); 
            if ($lab->save()) {
                $profileId = $lab->getKey();
            } else {
                return response()->json([
                    'message' => 'Some error occurred, please try again'
                ], 500);
            }
        
            }
            else if($user->type == 'MIC'){
                $request->validate([
                    'email' => 'required|string|max:190|email|unique:m_i_c_s',
                    //'image' => 'image|mimes:jpeg,jpg,png,svg' ,
                    'phone' =>'required|max:10' , 'image' => 'image|mimes:jpeg,jpg,png,svg' ,
                    'name' =>'required|string|max:20' , 
                    'address' => 'required' , 
                    ]);
                $mic = new MIC();
                $mic->email = $user->email ; 
                $mic->name = $request->name ; 
                $mic->phone = $request->phone ; 
                if($request->hasFile('image')){
                    $filename=Photo::saveProfile($request, "image");
                }else{
                    $filename=Null;
                }
                $mic->photos = PROFILE_PHOTO_INDICATOR . ":" . $filename ;  
                $mic->password = $user->password;
                $mic->address = $request->address;
    
          //  $result = $request->file('photo')->storeOnCloudinary();    
          //  $mic->photo = $result->getPath(); 
            if ($mic->save()) {
                $profileId = $mic->getKey();
                
            } else {
                return response()->json([
                    'message' => 'Some error occurred, please try again'
                ], 500);
            }}
            else if($user->type == 'PHARMACY'){
                $request->validate([
                    'email' => 'required|string|max:190|email|unique:pharmacies',
                    //'image' => 'image|mimes:jpeg,jpg,png,svg' ,
                    'phone' =>'required|max:10' , 'image' => 'image|mimes:jpeg,jpg,png,svg' ,
                    'name' =>'required|string|max:20' , 
                    'address' => 'required' , 
                ]);
                $pharmacy = new Pharmacy();
                $pharmacy->email = $user->email ; 
                $pharmacy->name = $request->name ; 
                $pharmacy->phone = $request->phone ; 
                if($request->hasFile('image')){
                    $filename=Photo::saveProfile($request, "image");
                }else{
                    $filename=Null;
                }
                $pharmacy->photos = PROFILE_PHOTO_INDICATOR . ":" . $filename ; 
                $pharmacy->password = $user->password;
                $pharmacy->address = $request->address;
    
          //  $result = $request->file('photo')->storeOnCloudinary();    
          //  $pharmacy->photo = $result->getPath(); 
            if ($pharmacy->save()) {
                $profileId = $pharmacy->getKey();
                }}

                else if($user->type == 'CLINIC'){
                    $request->validate([
                        'email' => 'required|string|max:190|email|unique:clinics',
                        //'image' => 'image|mimes:jpeg,jpg,png,svg' ,
                        'phone' =>'required|max:10' , 'image' => 'image|mimes:jpeg,jpg,png,svg' ,
                        'name' =>'required|string|max:20' , 
                        'address' => 'required' , 
                        ]);
                    $clinic = new Clinic();
                    $clinic->email = $user->email ; 
                    $clinic->name = $request->name ; 
                    $clinic->phone = $request->phone ; 
                    if($request->hasFile('image')){
                        $filename=Photo::saveProfile($request, "image");
                    }else{
                        $filename=Null;
                    }
                    $clinic->photos = PROFILE_PHOTO_INDICATOR . ":" . $filename ;   
                                      $clinic->password = $user->password;
                    $clinic->address = $request->address;
        
              //  $result = $request->file('photo')->storeOnCloudinary();    
              //  $pharmacy->photo = $result->getPath(); 
                if ($clinic->save()) {
                    $profileId = $clinic->getKey();
            } else {
                return response()->json([
                    'message' => 'Some error occurred, please try again'
                ], 500);
            }
            }
          else{ return response()->json([
            'message' => "'{$request->type}' is not a valid type."
        ], 404);}


        DB::table("profile_user")->insert([
            "user_id" => $user->getKey(),
            "profile_type" => TYPE_ALIAS[strtolower($user->type)],
            "profile_id" => $profileId,
        ]);

        return ["message" => "registration successful, please try to log in."];
    });

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    Route::post('/login', function (Request $request) {
        
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
    if(!$user){ return response()->json([
'message' => 'please register first'
], 401);}

 else if ($user && Hash::check($request->password, $user->password)) { 
    
    if($user->type == 'PATIENT'){
        $profile = Patient::where('email', $request->email)->first();
    }
    else if($user->type == 'DOCTOR'){
        $profile = Doctor::where('email', $request->email)->first();
    }
    else if($user->type == 'HOSPITAL'){
        $profile = Hospital::where('email', $request->email)->first();
    }
    else if($user->type == 'LAB'){
        $profile = Lab::where('email', $request->email)->first();
    }
    else if($user->type == 'MIC'){
        $profile = Mic::where('email', $request->email)->first();
    }
    else if($user->type == 'PHARMACY'){
        $profile = Pharmacy::where('email', $request->email)->first();
    }
    else if($user->type == 'CLINIC'){
        $profile =Clinic::where('email', $request->email)->first();
    } 
    $profile->type = strtolower($user->type);
    return response()->json([
        'type' => $user->type,
        'user' => $profile,
        'access_token' => $user->createToken($request->email)->plainTextToken,
        'token_type' => 'Bearer',
    ], 200);
    } 
else {
    return response()->json([
    'message' => 'Wrong password',
], 404);}}
);
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Route::post('reset-password-request', function (Request $request){
    $request->validate([
        'email' => 'required|string|email'
    ]);

    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return response()->json([
            'message' => 'there are no account with this mail'
        ], 404);
    }

    $code = rand(0000, 9999);
    $user->code_verification = $code;

    if ($user->save()) {
        $emailData = array(
            'heading' => 'Reset Password Request',
            'name' => $user->firstname.''.$user->lastname,
            'email' => $user->email,
            'code' => $user->code_verification
        );
        $emailData = array(
            'heading' => 'EMAIL CONFIRMATION',
            'name' => 'CLIENT',
            'email' => $request->email,
            'code' => $user->code_verification
        );
        Mail::to($emailData['email'])->send(new MailResetPasswordRequest($emailData));
        return response()->json([
            'message' => 'We have sent a verification code to your provided email to reser your password' , 
            'CODE' => $code , 
        ], 200);
    } else {
        return response()->json([
            'message' => 'Some error occurred, please try again'
        ], 500);
    }
});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Route::post('FS-reset-password', function (Request $request){
    $request->validate([
        'email' => 'required|string|email',
        'code_verification' => 'required', ]);

    $user = User::where('email', $request->email)->where('code_verification', $request->code_verification)->first();

    if (!$user) {
        return response()->json([
            'message' => 'Invalid code'
        ], 404);
    }


    if ($user->save()) {
        return response()->json([
            'message' => 'Correct Code ! , try to login now ..'
        ], 200);
    } else {
        return response()->json([
            'message' => 'Some error occurred, please try again'
        ], 500);
    }

});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::post('reset-password', function (Request $request){
    $request->validate([
        'email' => 'required|string|email',
      'new_password' => [
            'required',
            'max:150',
            'confirmed',
            Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()
        ],
    ]);

    $user = User::where('email', $request->email)->where('verification_code', $request->verification_code)->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found/Invalid code'
        ], 404);
    }

    $user->password = $request->new_password;
    $user->verification_code = NULL;

    if ($user->save()) {
        return response()->json([
            'message' => 'Password updated successfully!'
        ], 200);
    } else {
        return response()->json([
            'message' => 'Some error occurred, please try again'
        ], 500);
    }

});
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Route::group(['middleware' => 'auth:sanctum'], function() {
Route::get('profile' , function (Request $request){
    $user = User::where('id', $request->id)->first();

    if ($user) {
        if($user->type == 'PATIENT'){
            return response()->json([
                'user' => $patient = Patient::where('email', $request->email)->first()
            ], 200);
    
        }
        else if($user->type == 'DOCTOR'){
            return response()->json([
                    'user' => $doctor = Doctor::where('email', $request->email)->first()
                ], 200);
    
        }
        else if($user->type == 'HOSPITAL'){
                return response()->json([
                    'user' => $hospital = Hospital::where('email', $request->email)->first()
                   
                ], 200);
    
        }
        else if($user->type == 'LAB'){
                return response()->json([
                    'user' => $lab = Lab::where('email', $request->email)->first()
                ], 200);
    
        }
        else if($user->type == 'MIC'){
            
           
                return response()->json([
                    'user' => $mic = Mic::where('email', $request->email)->first()
                   
                ], 200);
    }
        else if($user->type == 'PHARMACY'){
            

           
                return response()->json([
                    'user' => $pharmacy = Pharmacy::where('email', $request->email)->first()
                   
                ], 200);
    }
    else if($user->type == 'CLINIC'){
            

           
        return response()->json([
            'user' => $clinic = Clinic::where('email', $request->email)->first()
           
        ], 200);
}
    } else {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

});


Route::post('update-password' ,  function (Request $request){
    $request->validate([
        'current_password' => 'required|string',
        'new_password' => [
            'required',
            'max:150',
            'confirmed',
            Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()
        ],
    ]);

    $user = $request->user();

    $user = User::where('email', $request->email)->where('password', $request->current_password)->first();
      if(!$user){return response()->json([
            'message' => 'Current password is wrong'
        ], 401);
    }

    $user->password = bcrypt($request->new_password);
    if ($user->save()) {
        return response()->json([
            'message' => 'Password changed succesfully!'
        ], 200);
    } else {
        return response()->json([
            'message' => 'Some error occurred, please try again'
        ], 500);
    }});

Route::post('update-profile' , function (Request $request) {

      
        $user = User::where('id', $request->id)->first();
        
        
        
                if($user->type == 'PATIENT'){
                    
                       
                    $patient = new Patient();
                    $patient = Patient::where('email', $request->email)->first();
                    $patient->email = $request->email ; 
                    $patient->firstname = $request->firstname ; 
                    $patient->lastname = $request->lastname ; 
                    $patient->phone = $request->phone ; 
                    $patient->photo = $request->photo ; 
                    $patient->socials = $request->socials ; 
                    $patient->gender = $request->gender ; 
                    $patient->bloodtype = $request->bloodtype ; 
                    $patient->password = $user->password;
                    $patient->address = $request->address;
                   
                    return response()->json([
                        'user' => $patient,
                        
                    ], 200);
            
                }
        
                
                 
                else if($user->type == 'DOCTOR'){
                    $doctor = new Doctor();
                    $doctor =   Doctor::where('email', $request->email)->first();
                    $doctor->email = $user->email ; 
                    $doctor->firstname = $request->firstname ; 
                    $doctor->lastname = $request->lastname ; 
                    $doctor->phone = $request->phone ; 
                    $doctor->photo = $request->photo ; 
                    $doctor->password = $request->password;
                        return response()->json([
                            'user' => $doctor,
                            
                        ], 200);
            
                }
                else if($user->type == 'HOSPITAL'){
                    $hospital = new Hospital();
                    $hospital =   Hospital::where('email', $request->email)->first();
                    $hospital = new Doctor();
                 
                    $hospital->email = $user->email ; 
                    $hospital->name = $request->name ; 
                    $hospital->phone = $request->phone ; 
                    $hospital->photo = $request->photo ; 
                    $hospital->password = $request->password;
                    $hospital->address = $request->address;
        
                        return response()->json([
                            'user' => $hospital,
                            
                        ], 200);
        
                   
                   
            
                }
                else if($user->type == 'LAB'){
                    $lab = new Lab();
                    $lab =   Lab::where('email', $request->email)->first();
                    $lab = new Lab();
                    $lab->email = $user->email ; 
                    $lab->name = $request->name ; 
                    $lab->phone = $request->phone ; 
                    $lab->photo = $request->photo ; 
                    $lab->password = $request->password;
                    $lab->address = $request->address;
        
                        return response()->json([
                            'user' => $lab,
                            
                        ], 200);
            
                }
                else if($user->type == 'MIC'){
                    $mic = new Mic();
                    $mic =   Mic::where('email', $request->email)->first();
                    $mic->email = $user->email ; 
                    $mic->name = $request->name ; 
                    $mic->phone = $request->phone ; 
                    $mic->photo = $request->photo ; 
                    $mic->password = $request->password;
                    $mic->address = $request->address;
                   
                        return response()->json([
                            'user' => $mic,
                            
                        ], 200);
            }
                else if($user->type == 'PHARMACY'){
                    $pharmacy = new Pharmacy();
                    $pharmacy =   Pharmacy::where('email', $request->email)->first();
                    $pharmacy->email = $user->email ; 
                    $pharmacy->name = $request->name ; 
                    $pharmacy->phone = $request->phone ; 
                    $oldPhoto = $user->photo;
                    if ($request->hasFile('photo')) {
                        $request->validate([
                            'photo' => 'image|mimes:jpeg,png,jpg|max:5120',
                        ]);
            
                        $path = $request->file('photo')->store('profile');
                        $pharmacy->photo = $path;
                    }
                    $pharmacy->password = $user->password;
                    $pharmacy->address = $request->address;
                   
                        return response()->json([
                            'user' => $pharmacy,
                          
                        ], 200);}
                        else if($user->type == 'CLINIC'){
                            $clinic = new Clinic();
                            $clinic =   Clinic::where('email', $request->email)->first();
                            $clinic->email = $user->email ; 
                            $clinic->firstname = $request->firstname ; 
                            $clinic->lastname = $request->lastname ; 
                            $clinic->phone = $request->phone ; 
                            $clinic->photo = $request->photo ; 
                            $clinic->password = $request->password;
                                return response()->json([
                                    'user' => $clinic,
                                    
                                ], 200);
            }
});

Route::post('logout' , function (Request $request){

        if ($request->user()->tokens()->delete()) {
            return response()->json([
                'message' => 'Logout successfully!'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Some error occurred, please try again'
            ], 500);
        
    } 
});

Route::apiResource('chat', 'ChatController')->only(['index', 'store', 'show']);
Route::apiResource('chat_message', 'ChatMessageController')->only(['index','store']);
Route::apiResource('user', 'UserController')->only(['index']);
Broadcast::routes(['middleware' => ['auth:sanctum']]);
});
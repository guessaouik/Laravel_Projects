<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function Redirect($provider) {
        return Socialite::driver($provider)->redirect();
 
     } 
 
 
 
 
 
     public function Callback($provider) {
        $user =  Socialite::driver($provider)->redirect();
        $user->token->delete();
        $user1 = User::where(['email' => $user->email])->first() ;
       if (!$user1) {
 
        $user1 = new User();
        $user1->email = $request->email ; 
        $user1->password = 'Password' ; 
        $code = rand(0000, 9999);
        $user1->code_verification = $code;
        $user1->type = 'PATIENT';

       
        
 
         return response()->json([
             'user' => $user,
             'access_token' => $user->token,
            
         ], 200);
       }
       else {
        return response()->json([
        'message' => 'Login succeffly' ,],200);
      
     } 
 
    }}

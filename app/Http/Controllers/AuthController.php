<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'bio' => 'required|max:100',
            'username' => 'required|unique:users,username|min:3|regex:/^[a-zA-Z0-9._]+$/',
            'password' => 'required|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'invalid fields',
                'errors'  => $validator->errors()          
            ],422);
        }

          $user = User::create([
            'full_name' => $request -> full_name,
            'bio' => $request->bio,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'is_private' => $request->is_private ? true : false,
        ]);

        $token = $user->createToken('auth.token')->plainTextToken;

        return response()->json([
            'message' => 'Register Success',
            'token' => $token,
            'user' => $user
        ]);      
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Wrong username or password'
            ]);
        }

        $token = $user->createToken('login_token')->plainTextToken;

        return response()->json([
            'message' => 'login success',
            'acces_token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout success'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ]);
        }
    }
}

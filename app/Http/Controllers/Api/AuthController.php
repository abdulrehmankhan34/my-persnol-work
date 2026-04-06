<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    public function register(Request $request){
         $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6'
    ]);
       $user = User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>Hash::make($request->password)
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'user'=>$user,
        'token'=>$token
    ]);
    }
    public function login(Request $request)
{
     // Validation
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }
    $user = User::where('email',$request->email)->first();

    if(!$user || !Hash::check($request->password,$user->password)){
        return response()->json([
            'message'=>'Invalid credentials'
        ],401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'user'=>$user,
        'token'=>$token
    ]);
}
public function logout(Request $request)
{
    $request->user()->tokens()->delete();

    return response()->json([
        'message'=>'Logged out'
    ]);
}
}

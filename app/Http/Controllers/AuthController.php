<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\AuthUpdateRequest;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(AuthRegisterRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        return response()->json(['message' => 'User registered successfully', 'data' => $user]);
    }

    public function login(AuthLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json(['token' => $token]);
    }

    public function profile()
    {
        return response()->json(auth('api')->user());
    }

    public function update(AuthUpdateRequest $request)
    {
        $validatedData = $request->all();

        $user = auth('api')->user();

        if (isset($validatedData['name']) && !empty(isset($validatedData['name']))) $user->name = $validatedData['name'];
        if (isset($validatedData['email']) && !empty(isset($validatedData['email']))) $user->email = $validatedData['email'];
        if (isset($validatedData['phone']) && !empty(isset($validatedData['phone']))) $user->phone = $validatedData['phone'];
        if (isset($validatedData['address']) && !empty(isset($validatedData['address']))) $user->address = $validatedData['address'];
        if (isset($validatedData['role']) && !empty(isset($validatedData['role']))) $user->role = $validatedData['role'];
        if (isset($validatedData['password']) && !empty(isset($validatedData['password']))) $user->password = $validatedData['password'];

        $user->save();

        return response()->json([ 'message' => 'User updated successfully', 'data' => $user ]);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            auth()->logout(true);
            
            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Exception | JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }
    }
}

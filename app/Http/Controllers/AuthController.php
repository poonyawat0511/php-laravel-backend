<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'in:public,staff'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'public',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse([
            'user' => $user,
            'token' => $token
        ], 'User registered successfully', 201);
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('อีเมลหรือรหัสผ่านไม่ถูกต้อง', null, 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse([
            'user' => $user,
            'token' => $token
        ], 'Login successful', 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $r)
    {
        $data = $r->validate(['first_name' => 'required|string|max:100', 'last_name' => 'required|string|max:100', 'email' => 'required|email|unique:users', 'phone' => 'nullable|string|max:30', 'password' => 'required|string|min:8|confirmed']);
        $user = User::create([...$data, 'name' => $data['first_name'].' '.$data['last_name'], 'role' => 'CLIENT', 'status' => 'ACTIVE']);
        NotificationPreference::create(['user_id' => $user->id]);

        return response()->json(['success' => true, 'data' => ['user' => $user, 'token' => $user->createToken('api')->plainTextToken]], 201);
    }

    public function login(Request $r)
    {
        $data = $r->validate(['email' => 'required|email', 'password' => 'required|string']);
        $user = User::where('email', $data['email'])->first();
        abort_unless($user && $user->status === 'ACTIVE' && Hash::check($data['password'], $user->password), 422, 'Invalid credentials.');

        return ['success' => true, 'data' => ['user' => $user, 'token' => $user->createToken('api')->plainTextToken]];
    }

    public function logout(Request $r)
    {
        $r->user()->currentAccessToken()->delete();

        return ['success' => true, 'message' => 'Logged out'];
    }
}

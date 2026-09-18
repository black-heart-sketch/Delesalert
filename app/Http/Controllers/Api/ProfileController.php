<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);
        $firstName = $data['first_name'] ?? $user->first_name;
        $lastName = $data['last_name'] ?? $user->last_name;

        $user->update($data + ['name' => trim($firstName.' '.$lastName)]);

        return response()->json(['success' => true, 'data' => $user->fresh()]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return response()->json(['success' => true, 'message' => 'Password updated.']);
    }

    public function destroy(Request $request): Response
    {
        $request->validate(['password' => ['required', 'string']]);
        abort_unless(Hash::check($request->string('password')->toString(), $request->user()->password), 422, 'Invalid password.');

        $user = $request->user();
        $user->tokens()->delete();
        $user->update(['status' => 'DEACTIVATED']);

        return response()->noContent();
    }
}

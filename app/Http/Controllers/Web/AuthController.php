<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([...$credentials, 'status' => 'ACTIVE'], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The provided credentials do not match an active account.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function demoLogin(Request $request): RedirectResponse
    {
        abort_unless(config('app.demo_login_enabled'), 404);

        $data = $request->validate(['role' => ['required', 'in:CLIENT,PROVIDER,ADMIN']]);
        $user = User::query()->where('role', $data['role'])->where('status', 'ACTIVE')->firstOrFail();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'You are signed in to the '.$user->role.' demo account.');
    }

    public function register(): View
    {
        return view('auth.register');
    }

    public function createAccount(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            ...$data,
            'name' => $data['first_name'].' '.$data['last_name'],
            'password' => Hash::make($data['password']),
            'role' => 'CLIENT',
            'status' => 'ACTIVE',
        ]);

        NotificationPreference::create(['user_id' => $user->id]);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Welcome to DelestAlert. Start by saving a location.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

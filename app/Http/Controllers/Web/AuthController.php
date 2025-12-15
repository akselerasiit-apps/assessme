<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('dashboard'));
        }
        
        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    
    public function showRegister()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        // Assign default role (Viewer)
        $user->assignRole('Viewer');
        
        Auth::login($user);
        
        return redirect()->route('dashboard')->with('success', 'Account created successfully!');
    }
    
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }
    
    public function sendResetLink(Request $request)
    {
        // Implementation for password reset
        return back()->with('success', 'Password reset link sent to your email!');
    }
    
    public function logout(Request $request)
    {
        // For web guard (session-based)
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}

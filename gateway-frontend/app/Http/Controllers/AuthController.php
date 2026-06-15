<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    private $authServiceUrl;

    public function __construct()
    {
        $this->authServiceUrl = config('services.auth.url', 'http://auth-service:8000/api');
    }

    /**
     * Show registration form.
     */
    public function showRegister()
    {
        return view('register');
    }

    /**
     * Handle registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:Admin,User',
        ]);

        $response = Http::post("{$this->authServiceUrl}/register", [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
        ]);

        if ($response->failed()) {
            $errors = $response->json('errors') ?? ['email' => [$response->json('message') ?? 'Registration failed']];
            return back()->withErrors($errors)->withInput();
        }

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    /**
     * Show login form.
     */
    public function showLogin(Request $request)
    {
        if ($request->cookie('token')) {
            return redirect()->route('dashboard');
        }
        return view('login');
    }

    /**
     * Handle login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $response = Http::post("{$this->authServiceUrl}/login", [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->failed()) {
            return back()->withErrors([
                'email' => $response->json('message') ?? 'Invalid credentials'
            ])->withInput();
        }

        $data = $response->json();
        $token = $data['access_token'];

        // Store JWT token securely in HttpOnly cookie
        $cookie = cookie('token', $token, 60, null, null, false, true); // 60 minutes, HttpOnly

        return redirect()->route('dashboard')->withCookie($cookie);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $token = $request->cookie('token');

        if ($token) {
            Http::withToken($token)->post("{$this->authServiceUrl}/logout");
        }

        // Forget cookie
        $cookie = cookie::forget('token');

        return redirect()->route('login')->withCookie($cookie);
    }
}

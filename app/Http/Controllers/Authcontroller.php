<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
public function showLogin()
    {
return view('login');
    }
public function login(Request $request)
    {
$request->validate([
'email'    => 'required|email',
'password' => 'required',
        ]);
$credentials = $request->only('email', 'password');
$remember    = $request->boolean('remember');
if (Auth::attempt($credentials, $remember)) {
$request->session()->regenerate();
return redirect()->route('dashboard');
        }
return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Mali ang email o password.']);
    }
public function showRegister()
    {
return view('register');
    }
public function register(Request $request)
    {
$request->validate([
'name'     => 'required|string|max:255',
'username' => 'required|string|max:255|unique:users,username',
'email'    => 'required|email|unique:users',
'password' => 'required|min:8|confirmed',
        ]);
$user = User::create([
'name'     => $request->name,
'username' => $request->username,
'email'    => $request->email,
'password' => Hash::make($request->password),
        ]);
return redirect()->route('login')
                         ->with('success', 'Account created!');
    }
public function logout(Request $request)
    {
Auth::logout();
$request->session()->invalidate();
$request->session()->regenerateToken();
return redirect()->route('login');
    }
}
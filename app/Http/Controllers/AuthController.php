<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nip' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'nip' => 'NIP atau password salah.',
        ])->onlyInput('nip');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showChangePassword()
    {
        return view('auth.change_password');
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:3', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], Auth::user()->password)) {
            return back()->withErrors([
                'current_password' => 'Password lama tidak sesuai.',
            ])->withInput($request->except('current_password'));
        }

        Auth::user()->update([
            'password' => $data['new_password'],
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}

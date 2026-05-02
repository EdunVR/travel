<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Investor;

class InvestorLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('investor.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find investor by email
        $investor = Investor::where('email', $request->email)->first();

        // Check if investor exists and phone matches password
        if ($investor && $investor->phone === $request->password) {
            Auth::guard('investor')->login($investor, $request->remember);
            
            return redirect()->intended(route('investor.dashboard'))
                            ->with('success', 'Login berhasil!');
        }

        return back()->withInput($request->only('email', 'remember'))
                    ->withErrors([
                        'email' => 'Email atau nomor telepon tidak valid.',
                    ]);
    }

    public function logout()
    {
        Auth::guard('investor')->logout();
        return redirect()->route('investor.login');
    }
}
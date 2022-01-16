<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->two_factor_code === null) {
            return redirect()->home();
        }

        if ($request->input('two_factor_code') === $user->two_factor_code) {
            $user->resetTwoFactorCode();
            return redirect()->route('dashboard');
        }

        return view('auth.twoFactor');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'two_factor_code' => 'integer|required',
        ]);

        $user = auth()->user();

        if ($request->input('two_factor_code') == $user->two_factor_code) {
            $user->resetTwoFactorCode();
            return redirect()->route('dashboard');
        }

        if ($request->input('two_factor_code')) {
            Auth::logout();
            return redirect()->route('login');
        }

        return redirect()->back()
            ->withErrors(['two_factor_code' =>
                'The two factor code you have entered does not match']);
    }
}

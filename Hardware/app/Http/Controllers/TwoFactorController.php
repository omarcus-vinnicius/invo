<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

class TwoFactorController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $code = rand(100000, 999999);
        $user->two_factor_code = $code;
        $user->save();

        Mail::raw("your  two factor code is $code", function ($message) use ($user) {
            $message->to($user->email)->subject('Two-Factor Code');

        });

        return view('auth.two-factor');
    }


    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);
        $user = auth()->user();

        if ($request->code == $user->two_factor_code) {
            session(['two_factor_authenticated' => true]);
            return redirect()->intended('/home');
        }

        return redirect('/two-factor')->withErrors(['code' => 'The provided code is  incorrect']);
    }
}

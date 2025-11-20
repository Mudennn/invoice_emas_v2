<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    public function forget_password()
    {
        return view('auth.passwords.email');
    }

    public function temp_password(Request $request)
    {
        $temppass = Str::random(12);

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $request->email)
            ->where('status', 0)
            ->first();

        if ($user) {
            $user_update = User::where('id', $user->id)
                ->update([
                    'password' => Hash::make($temppass),
                ]);

            //$temporaryemail = 'teo@futuretech.com.my';

            if ($user_update) {
                request('email');
                ////Mail::to($request->email)->send(new TemporaryPassword($temppass));
                Mail::to($request->email)->send(new ForgetPasswordMail($temppass, request('email')));
                //Mail::to($temporaryemail)->send(new TemporaryPassword($temppass, request('email')));
            }

            if ($user_update) {
                Session::flash('message', [
                    'type' => 'success',
                    'content' => '<ul>
                                    <li>You’ll receive a password reset link shortly.</li>
                                    <li>Please check your inbox.</li>
                                </ul>'
                ]);
            }
        } else {
            // Flash error message
            Session::flash('message', [
                'type' => 'error',
                'content' => '<ul>
                                <li>Email address you entered is not in our system.</li>
                                <li>Please try again.</li>
                            </ul>'
            ]);
        }

        return view('auth.passwords.email');

        //return redirect()->route('homepage.main.index');
    }

    public function change_password()
    {
        return view('auth.passwords.reset');
    }

    public function save_password(Request $request)
    {
        $user = Auth::user();
        // Validate the incoming request data
        $validatedData = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Update the user's password
        $user = User::where('id', $user->id)
            ->update([
                'password' => Hash::make($validatedData['password']),
            ]);

        // if ($user) {
        //     Alert::toast('Changes have been updated.', 'success');
        // } else {
        //     Alert::toast('An error has occurred.', 'error');
        // }

        // Redirect back with a success message
        return redirect()->route('dashboard');
    }
}
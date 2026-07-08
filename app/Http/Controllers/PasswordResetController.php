<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class PasswordResetController extends Controller
{
    /** Formulaire "Mot de passe oublié" */
    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    /** Envoyer le lien de réinitialisation */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email'    => 'L\'adresse email n\'est pas valide.',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /** Formulaire de réinitialisation du mot de passe */
    public function showReset(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /** Traiter la réinitialisation */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->mixedCase()->numbers()],
        ], [
            'email.required'    => 'L\'adresse email est obligatoire.',
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.confirmed'=> 'Les mots de passe ne correspondent pas.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password'       => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * AuthController — Gestion de l'authentification utilisateur
 * 
 * Inscription, connexion et déconnexion.
 * Les mots de passe sont automatiquement hashés par Laravel (bcrypt).
 */
class AuthController extends Controller
{
    // ========================
    // FORMULAIRE D'INSCRIPTION
    // ========================

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validation avec règles strictes
        $validated = $request->validate([
            'name'     => ['required', 'string', 'min:2', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'name.required'      => 'Votre nom est obligatoire.',
            'email.required'     => 'L\'adresse email est obligatoire.',
            'email.email'        => 'L\'adresse email n\'est pas valide.',
            'email.unique'       => 'Cette adresse email est déjà utilisée.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        // Créer l'utilisateur (le password est hashé automatiquement via le cast)
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'user', // Par défaut : rôle client
        ]);

        // Connexion automatique après inscription
        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Bienvenue ' . $user->name . ' ! Votre compte a été créé avec succès.');
    }

    // ========================
    // FORMULAIRE DE CONNEXION
    // ========================

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'L\'adresse email est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $remember = $request->boolean('remember');

        // Tentative de connexion
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate(); // Protection contre la fixation de session

            $user = Auth::user();

            // Redirection selon le rôle
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Bienvenue, ' . $user->name . ' !');
            }

            return redirect()->intended(route('home'))
                ->with('success', 'Bienvenue, ' . $user->name . ' !');
        }

        // Échec : on retourne avec une erreur générique (sécurité)
        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ])->onlyInput('email');
    }

    // ========================
    // DÉCONNEXION
    // ========================

    public function logout(Request $request)
    {
        Auth::logout();

        // Invalider la session et regénérer le token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}

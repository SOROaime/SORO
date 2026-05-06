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
 *
 * Création d'un compte admin :
 *   - L'utilisateur coche "Compte administrateur" sur la page d'inscription
 *   - Il doit saisir la clé secrète définie dans ADMIN_REGISTRATION_KEY (.env)
 *   - Si la clé est correcte → rôle "admin" attribué
 *   - Si la clé est absente ou incorrecte → erreur de validation, rôle "user" refusé
 */
class AuthController extends Controller
{
    // ============================================================
    // FORMULAIRE D'INSCRIPTION
    // ============================================================

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // ---- Règles de validation de base ----
        $rules = [
            'name'     => ['required', 'string', 'min:2', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];

        $messages = [
            'name.required'      => 'Votre nom est obligatoire.',
            'email.required'     => 'L\'adresse email est obligatoire.',
            'email.email'        => 'L\'adresse email n\'est pas valide.',
            'email.unique'       => 'Cette adresse email est déjà utilisée.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
        ];

        // ---- Si la case "compte admin" est cochée, valider la clé secrète ----
        $wantsAdmin = $request->boolean('is_admin');

        if ($wantsAdmin) {
            $rules['admin_key'] = ['required', 'string'];
            $messages['admin_key.required'] = 'La clé secrète est obligatoire pour créer un compte administrateur.';
        }

        $validated = $request->validate($rules, $messages);

        // ---- Déterminer le rôle final ----
        $role = 'user'; // Rôle par défaut

        if ($wantsAdmin) {
            $secretKey = config('app.admin_registration_key');

            // Comparaison sécurisée (timing-safe) pour éviter les attaques temporelles
            if (! hash_equals((string) $secretKey, (string) $request->input('admin_key'))) {
                return back()
                    ->withInput($request->except('password', 'password_confirmation', 'admin_key'))
                    ->withErrors([
                        'admin_key' => 'La clé secrète saisie est incorrecte. Accès refusé.',
                    ]);
            }

            $role = 'admin';
        }

        // ---- Créer l'utilisateur ----
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $role,
        ]);

        // Connexion automatique après inscription
        Auth::login($user);

        // Message de bienvenue adapté au rôle
        $message = $role === 'admin'
            ? 'Bienvenue ' . $user->name . ' ! Votre compte administrateur a été créé avec succès.'
            : 'Bienvenue ' . $user->name . ' ! Votre compte a été créé avec succès.';

        // Redirection selon le rôle
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', $message);
        }

        return redirect()->route('home')->with('success', $message);
    }

    // ============================================================
    // FORMULAIRE DE CONNEXION
    // ============================================================

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

        // Échec : erreur générique (ne pas révéler si c'est l'email ou le mdp)
        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ])->onlyInput('email');
    }

    // ============================================================
    // DÉCONNEXION
    // ============================================================

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

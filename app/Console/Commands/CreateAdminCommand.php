<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Commande Artisan pour créer un compte administrateur.
 *
 * Usage :
 *   php artisan make:admin
 *   php artisan make:admin --name="Jean" --email="jean@shop.com" --password="Admin123!"
 *   php artisan make:admin --promote=jean@shop.com   (promouvoir un user existant)
 */
class CreateAdminCommand extends Command
{
    protected $signature = 'make:admin
                            {--name=      : Nom complet de l\'administrateur}
                            {--email=     : Adresse email}
                            {--password=  : Mot de passe (min. 8 caractères)}
                            {--promote=   : Promouvoir un utilisateur existant en admin (email)}';

    protected $description = 'Créer un nouveau compte administrateur ou promouvoir un utilisateur existant';

    public function handle(): int
    {
        $this->newLine();
        $this->line('  <fg=yellow>╔══════════════════════════════════════╗</>');
        $this->line('  <fg=yellow>║   Création d\'un compte Admin         ║</>');
        $this->line('  <fg=yellow>╚══════════════════════════════════════╝</>');
        $this->newLine();

        // ── Option : promouvoir un utilisateur existant ──────────────────
        if ($promoteEmail = $this->option('promote')) {
            return $this->promoteUser($promoteEmail);
        }

        // ── Créer un nouveau compte admin ────────────────────────────────
        return $this->createAdmin();
    }

    private function createAdmin(): int
    {
        // Nom
        $name = $this->option('name')
            ?? $this->ask('  Nom complet de l\'administrateur');

        // Email
        $email = $this->option('email')
            ?? $this->ask('  Adresse email');

        // Vérifier que l'email n'existe pas déjà
        if (User::where('email', $email)->exists()) {
            $existing = User::where('email', $email)->first();

            if ($existing->role === 'admin') {
                $this->error("  ✗ Ce compte est déjà administrateur : {$email}");
                return self::FAILURE;
            }

            // Proposer de promouvoir
            if ($this->confirm("  Ce compte existe déjà (rôle: {$existing->role}). Le promouvoir en admin ?")) {
                return $this->promoteUser($email);
            }

            return self::FAILURE;
        }

        // Mot de passe
        $password = $this->option('password')
            ?? $this->secret('  Mot de passe (min. 8 car., majuscule + chiffre)');

        // Validation
        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name'     => ['required', 'string', 'min:2'],
                'email'    => ['required', 'email', 'unique:users'],
                'password' => ['required', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error("  ✗ {$error}");
            }
            return self::FAILURE;
        }

        // Créer l'admin
        $admin = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'admin',
        ]);

        $this->newLine();
        $this->line('  <fg=green>✅ Compte administrateur créé avec succès !</>');
        $this->newLine();
        $this->table(
            ['Champ', 'Valeur'],
            [
                ['Nom',    $admin->name],
                ['Email',  $admin->email],
                ['Rôle',   '🔴 ADMIN'],
                ['Créé le', $admin->created_at->format('d/m/Y à H:i')],
            ]
        );
        $this->newLine();
        $this->line("  → Connexion : <fg=cyan>http://localhost:8000/connexion</>");
        $this->newLine();

        return self::SUCCESS;
    }

    private function promoteUser(string $email): int
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("  ✗ Aucun utilisateur trouvé avec l'email : {$email}");
            return self::FAILURE;
        }

        if ($user->role === 'admin') {
            $this->warn("  ⚠  {$user->name} est déjà administrateur.");
            return self::SUCCESS;
        }

        $user->update(['role' => 'admin']);

        $this->newLine();
        $this->line("  <fg=green>✅ {$user->name} ({$email}) a été promu administrateur !</>");
        $this->newLine();

        return self::SUCCESS;
    }
}

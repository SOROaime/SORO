<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DatabaseSeeder — Données de test pour ShopLaravel
 * 
 * Crée :
 * - 1 compte administrateur
 * - 2 comptes utilisateurs clients
 * - 12 produits variés dans 4 catégories
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ========================
        // UTILISATEURS
        // ========================

        // Compte admin (accès tableau de bord)
        User::firstOrCreate(
            ['email' => 'admin@shop.com'],
            [
                'name'     => 'Administrateur',
                'password' => Hash::make('Admin123!'),
                'role'     => 'admin',
            ]
        );

        // Comptes clients de test
        User::firstOrCreate(
            ['email' => 'alice@example.com'],
            [
                'name'     => 'Alice Martin',
                'password' => Hash::make('User123!'),
                'role'     => 'user',
                'address'  => '15 rue de Rivoli',
            ]
        );

        User::firstOrCreate(
            ['email' => 'bob@example.com'],
            [
                'name'     => 'Bob Dupont',
                'password' => Hash::make('User123!'),
                'role'     => 'user',
                'address'  => '8 avenue Montaigne',
            ]
        );

        // ========================
        // PRODUITS
        // ========================

        $products = [
            // --- Électronique ---
            [
                'name'        => 'iPhone 15 Pro',
                'description' => 'Le dernier iPhone d\'Apple avec puce A17 Pro, appareil photo 48 MP et titane résistant. Une expérience mobile sans compromis.',
                'price'       => 1229.00,
                'stock'       => 25,
                'category'    => 'Électronique',
                'is_active'   => true,
            ],
            [
                'name'        => 'MacBook Air M3',
                'description' => 'Laptop ultra-fin avec la puce Apple M3, 15h d\'autonomie, écran Liquid Retina 13". Parfait pour les professionnels nomades.',
                'price'       => 1299.00,
                'stock'       => 15,
                'category'    => 'Électronique',
                'is_active'   => true,
            ],
            [
                'name'        => 'Samsung 4K QLED 55"',
                'description' => 'Téléviseur QLED 4K avec HDR10+, 120Hz, Smart TV. Couleurs vibrantes et contrastes saisissants pour une immersion totale.',
                'price'       => 799.00,
                'stock'       => 12,
                'category'    => 'Électronique',
                'is_active'   => true,
            ],
            [
                'name'        => 'Sony WH-1000XM5',
                'description' => 'Casque Bluetooth avec réduction de bruit active de référence. 30h d\'autonomie, son Hi-Res, confort premium pour les voyages.',
                'price'       => 349.00,
                'stock'       => 30,
                'category'    => 'Électronique',
                'is_active'   => true,
            ],

            // --- Mode ---
            [
                'name'        => 'Sneakers Nike Air Max',
                'description' => 'Chaussures de sport iconiques avec coussin Air Max visible. Design moderne, confort exceptionnel pour un usage quotidien ou sportif.',
                'price'       => 149.90,
                'stock'       => 50,
                'category'    => 'Mode',
                'is_active'   => true,
            ],
            [
                'name'        => 'Veste en cuir noir',
                'description' => 'Veste en cuir véritable coupe slim. Doublure polyester, fermeture zip, poches multiples. Style intemporel pour toutes les saisons.',
                'price'       => 289.00,
                'stock'       => 20,
                'category'    => 'Mode',
                'is_active'   => true,
            ],
            [
                'name'        => 'Montre Minimalist',
                'description' => 'Montre analogique au design épuré. Boîtier acier inoxydable, bracelet cuir marron, mouvement quartz japonais. Résistante à l\'eau.',
                'price'       => 129.00,
                'stock'       => 35,
                'category'    => 'Mode',
                'is_active'   => true,
            ],

            // --- Maison ---
            [
                'name'        => 'Cafetière Nespresso Expert',
                'description' => 'Machine à café capsules avec contrôle de température précis. 3 tailles de tasse, mode Bluetooth, design premium en inox brossé.',
                'price'       => 199.00,
                'stock'       => 18,
                'category'    => 'Maison',
                'is_active'   => true,
            ],
            [
                'name'        => 'Lampe de bureau LED Smart',
                'description' => 'Lampe LED connectée avec 5 niveaux de luminosité, 3 températures de couleur. USB-C intégré, compatible Alexa et Google Home.',
                'price'       => 79.90,
                'stock'       => 40,
                'category'    => 'Maison',
                'is_active'   => true,
            ],
            [
                'name'        => 'Aspirateur Robot Roomba',
                'description' => 'Robot aspirateur intelligent avec navigation LiDAR. Carte de la maison, programmable via app, compatible avec assistants vocaux.',
                'price'       => 449.00,
                'stock'       => 10,
                'category'    => 'Maison',
                'is_active'   => true,
            ],

            // --- Sport ---
            [
                'name'        => 'Vélo électrique Urban E+',
                'description' => 'Vélo électrique urbain avec moteur 250W, batterie 400Wh (jusqu\'à 80km). Freins hydrauliques, afficheur LCD, cadre aluminium léger.',
                'price'       => 1899.00,
                'stock'       => 8,
                'category'    => 'Sport',
                'is_active'   => true,
            ],
            [
                'name'        => 'Tapis de yoga premium',
                'description' => 'Tapis de yoga antidérapant 6mm en TPE écologique. Surface texturée biface, lavable, livré avec sangle de transport. 183 × 61 cm.',
                'price'       => 59.90,
                'stock'       => 3, // Faible stock volontaire pour la démo
                'category'    => 'Sport',
                'is_active'   => true,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['name' => $product['name']],
                $product
            );
        }

        $this->command->info('✅ Données de test créées avec succès !');
        $this->command->line('');
        $this->command->line('  👤 Admin     : admin@shop.com   / Admin123!');
        $this->command->line('  👤 Client 1  : alice@example.com / User123!');
        $this->command->line('  👤 Client 2  : bob@example.com   / User123!');
    }
}

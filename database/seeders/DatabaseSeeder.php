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
                'image'       => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'MacBook Air M3',
                'description' => 'Laptop ultra-fin avec la puce Apple M3, 15h d\'autonomie, écran Liquid Retina 13". Parfait pour les professionnels nomades.',
                'price'       => 1299.00,
                'stock'       => 15,
                'category'    => 'Électronique',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Samsung 4K QLED 55"',
                'description' => 'Téléviseur QLED 4K avec HDR10+, 120Hz, Smart TV. Couleurs vibrantes et contrastes saisissants pour une immersion totale.',
                'price'       => 799.00,
                'stock'       => 12,
                'category'    => 'Électronique',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Sony WH-1000XM5',
                'description' => 'Casque Bluetooth avec réduction de bruit active de référence. 30h d\'autonomie, son Hi-Res, confort premium pour les voyages.',
                'price'       => 349.00,
                'stock'       => 30,
                'category'    => 'Électronique',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=280&fit=crop&auto=format',
            ],

            // --- Mode ---
            [
                'name'        => 'Sneakers Nike Air Max',
                'description' => 'Chaussures de sport iconiques avec coussin Air Max visible. Design moderne, confort exceptionnel pour un usage quotidien ou sportif.',
                'price'       => 149.90,
                'stock'       => 50,
                'category'    => 'Mode',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Veste en cuir noir',
                'description' => 'Veste en cuir véritable coupe slim. Doublure polyester, fermeture zip, poches multiples. Style intemporel pour toutes les saisons.',
                'price'       => 289.00,
                'stock'       => 20,
                'category'    => 'Mode',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Montre Minimalist',
                'description' => 'Montre analogique au design épuré. Boîtier acier inoxydable, bracelet cuir marron, mouvement quartz japonais. Résistante à l\'eau.',
                'price'       => 129.00,
                'stock'       => 35,
                'category'    => 'Mode',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1523170335258-f87a2f971db5?w=400&h=280&fit=crop&auto=format',
            ],

            // --- Maison ---
            [
                'name'        => 'Cafetière Nespresso Expert',
                'description' => 'Machine à café capsules avec contrôle de température précis. 3 tailles de tasse, mode Bluetooth, design premium en inox brossé.',
                'price'       => 199.00,
                'stock'       => 18,
                'category'    => 'Maison',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Lampe de bureau LED Smart',
                'description' => 'Lampe LED connectée avec 5 niveaux de luminosité, 3 températures de couleur. USB-C intégré, compatible Alexa et Google Home.',
                'price'       => 79.90,
                'stock'       => 40,
                'category'    => 'Maison',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Aspirateur Robot Roomba',
                'description' => 'Robot aspirateur intelligent avec navigation LiDAR. Carte de la maison, programmable via app, compatible avec assistants vocaux.',
                'price'       => 449.00,
                'stock'       => 10,
                'category'    => 'Maison',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=280&fit=crop&auto=format',
            ],

            // --- Sport ---
            [
                'name'        => 'Vélo électrique Urban E+',
                'description' => 'Vélo électrique urbain avec moteur 250W, batterie 400Wh (jusqu\'à 80km). Freins hydrauliques, afficheur LCD, cadre aluminium léger.',
                'price'       => 1899.00,
                'stock'       => 8,
                'category'    => 'Sport',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1558980664-3a031cf67ea8?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Tapis de yoga premium',
                'description' => 'Tapis de yoga antidérapant 6mm en TPE écologique. Surface texturée biface, lavable, livré avec sangle de transport. 183 × 61 cm.',
                'price'       => 59.90,
                'stock'       => 3,
                'category'    => 'Sport',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1601925228816-9b5d8d0a3d0a?w=400&h=280&fit=crop&auto=format',
            ],

            // --- Beauté ---
            [
                'name'        => 'Parfum Chanel N°5',
                'description' => 'Le parfum iconique de Chanel, une fragrance florale aldéhydée intemporelle. Eau de parfum 50ml, notes de rose, jasmin et vétiver.',
                'price'       => 129.00,
                'stock'       => 22,
                'category'    => 'Beauté',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1541643600914-78b084683702?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Palette Maquillage Pro',
                'description' => 'Palette de 20 fards à paupières haute pigmentation. Finitions mates, satinées et pailletées. Longue tenue 24h, sans paraben.',
                'price'       => 45.90,
                'stock'       => 35,
                'category'    => 'Beauté',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=400&h=280&fit=crop&auto=format',
            ],

            // --- Livres ---
            [
                'name'        => 'Atomic Habits — James Clear',
                'description' => 'Le guide définitif pour construire de bonnes habitudes et éliminer les mauvaises. Méthodes prouvées pour changer votre vie, 1% à la fois.',
                'price'       => 18.90,
                'stock'       => 60,
                'category'    => 'Livres',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Le Petit Prince',
                'description' => 'Le chef-d\'œuvre d\'Antoine de Saint-Exupéry. Un conte poétique et philosophique pour les enfants et les adultes. Édition illustrée.',
                'price'       => 9.50,
                'stock'       => 80,
                'category'    => 'Livres',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1629992101753-56d196c8aabb?w=400&h=280&fit=crop&auto=format',
            ],

            // --- Jouets ---
            [
                'name'        => 'LEGO Technic Ferrari',
                'description' => 'Réplique LEGO Technic de la Ferrari 488 GTE. 1 677 pièces, moteur V8 fonctionnel, suspension détaillée. Pour passionnés 18 ans+.',
                'price'       => 179.90,
                'stock'       => 14,
                'category'    => 'Jouets',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Drone DJI Mini 4 Pro',
                'description' => 'Drone compact avec caméra 4K/60fps, stabilisation sur 3 axes, autonomie 34 min. Détection d\'obstacles omnidirectionnelle. Poids < 249g.',
                'price'       => 759.00,
                'stock'       => 9,
                'category'    => 'Jouets',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1473968512647-3e447244af8f?w=400&h=280&fit=crop&auto=format',
            ],

            // --- Alimentaire ---
            [
                'name'        => 'Coffret Chocolats Belges',
                'description' => 'Assortiment de 36 chocolats artisanaux belges. Pralinés, ganaches, truffes et caramels. Boîte cadeau luxe, cacao 70% minimum.',
                'price'       => 34.90,
                'stock'       => 45,
                'category'    => 'Alimentaire',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Huile d\'Olive Extra Vierge',
                'description' => 'Huile d\'olive extra vierge première pression à froid, origine Toscane. Acidité < 0,3%, arômes fruités et poivrés. Bouteille 750ml.',
                'price'       => 22.50,
                'stock'       => 50,
                'category'    => 'Alimentaire',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&h=280&fit=crop&auto=format',
            ],

            // --- Électronique (suite) ---
            [
                'name'        => 'iPad Pro 12,9" M2',
                'description' => 'La tablette la plus puissante d\'Apple avec puce M2, écran Liquid Retina XDR 12,9", compatible Apple Pencil Pro. Stockage 256 Go.',
                'price'       => 1199.00,
                'stock'       => 11,
                'category'    => 'Électronique',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Console PlayStation 5',
                'description' => 'La console next-gen de Sony avec SSD ultra-rapide, ray-tracing, 4K 120fps. Manette DualSense avec retour haptique immersif.',
                'price'       => 549.00,
                'stock'       => 6,
                'category'    => 'Électronique',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=400&h=280&fit=crop&auto=format',
            ],

            // --- Maison (suite) ---
            [
                'name'        => 'Canape 3 places en velours',
                'description' => 'Canapé 3 places en velours côtelé bleu canard. Structure en bois massif, pieds métal doré, garnissage haute densité. 220 × 85 cm.',
                'price'       => 699.00,
                'stock'       => 5,
                'category'    => 'Maison',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&h=280&fit=crop&auto=format',
            ],

            // --- Mode (suite) ---
            [
                'name'        => 'Sac à main en cuir',
                'description' => 'Sac à main en cuir grainé camel. Bandoulière amovible, fermeture zip, compartiment intérieur zippé. Dimensions : 30 × 20 × 12 cm.',
                'price'       => 189.00,
                'stock'       => 18,
                'category'    => 'Mode',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400&h=280&fit=crop&auto=format',
            ],
            [
                'name'        => 'Lunettes de soleil Aviator',
                'description' => 'Lunettes de soleil style aviateur, verres polarisés protection UV400. Monture métal doré, branches ajustables. Étui rigide inclus.',
                'price'       => 79.00,
                'stock'       => 30,
                'category'    => 'Mode',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&h=280&fit=crop&auto=format',
            ],

            // --- Sport (suite) ---
            [
                'name'        => 'Haltères réglables 40 kg',
                'description' => 'Paire d\'haltères réglables de 2,5 à 40 kg par incrément de 2,5 kg. Système de verrouillage sécurisé, poignée antidérapante. Gain de place optimal.',
                'price'       => 249.00,
                'stock'       => 12,
                'category'    => 'Sport',
                'is_active'   => true,
                'image'       => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=400&h=280&fit=crop&auto=format',
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

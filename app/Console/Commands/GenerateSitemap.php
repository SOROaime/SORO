<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    protected $signature   = 'sitemap:generate';
    protected $description = 'Génère le fichier public/sitemap.xml pour le SEO';

    public function handle(): int
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $now     = now()->toAtomString();

        $urls = collect();

        // Pages statiques
        $staticPages = [
            ['loc' => $baseUrl . '/',           'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => $baseUrl . '/produits',   'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => $baseUrl . '/inscription','priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/connexion',  'priority' => '0.5', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $urls->push($page + ['lastmod' => $now]);
        }

        // Pages catégories
        $categories = Product::active()->distinct()->pluck('category')->filter();
        foreach ($categories as $cat) {
            $urls->push([
                'loc'        => $baseUrl . '/produits?category=' . urlencode($cat),
                'priority'   => '0.8',
                'changefreq' => 'weekly',
                'lastmod'    => $now,
            ]);
        }

        // Pages produits
        $products = Product::active()->select('id', 'updated_at')->get();
        foreach ($products as $product) {
            $urls->push([
                'loc'        => $baseUrl . '/produits/' . $product->id,
                'priority'   => '0.7',
                'changefreq' => 'weekly',
                'lastmod'    => $product->updated_at->toAtomString(),
            ]);
        }

        // Générer le XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url['loc']}</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        file_put_contents(public_path('sitemap.xml'), $xml);

        $this->info('Sitemap généré : ' . public_path('sitemap.xml') . ' (' . $urls->count() . ' URLs)');
        return self::SUCCESS;
    }
}

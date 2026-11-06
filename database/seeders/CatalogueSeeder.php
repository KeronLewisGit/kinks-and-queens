<?php

namespace Database\Seeders;

use App\Models\GalleryPhoto;
use App\Models\PriceSet;
use App\Models\Setting;
use App\Models\Texture;
use Illuminate\Database\Seeder;

/**
 * The real catalogue: textures, and the price lists published in the
 *
 * @kinksandqueens Instagram highlights (TT$, 2026). Safe to run again;
 * it never overwrites prices or photos the owner has edited in the admin.
 */
class CatalogueSeeder extends Seeder
{
    public function run(): void
    {
        $textures = [
            [
                'slug' => 'brazilian-kinky-curly', 'name' => 'Brazilian Kinky Curly', 'origin' => 'Brazilian', 'pattern' => 'Kinky Curly', 'script_word' => 'Curly',
                'best_for' => '4A–4B natural hair',
                'tagline' => 'Soft, defined curls without the “wiggy” shine.',
                'description' => 'Created with 4A–4B textures in mind: rich definition, softness and fullness, without the overly shiny look that gives extensions away. Wear it washed and air-dried in its natural state, or style it your way. It takes colour well too. One client lightly bleached hers and the curl pattern held.',
                'highlights' => ['100% human hair', 'Wash and air-dry, no curl wand needed', 'Still defined on day 10 without restyling', 'Takes colour and lightening'],
                'formats' => ['bundles', 'ponytail'], 'bundle_weight' => '98–100 g', 'coil' => 'curl', 'sort' => 1,
                'image' => 'assets/img/kinky-curly-portrait.webp', 'image_alt' => 'Woman wearing shoulder-length Brazilian Kinky Curly extensions with a centre part',
                'prices' => [
                    '12,12,12' => 1910, '14,14,14' => 2030, '16,16,16' => 2200, '18,18,18' => 2425, '20,20,20' => 2590, '22,22,22' => 2850,
                    '24,24,24' => 3120, '26,26,26' => 3610, '28,28,28' => 3790,
                    '12,14,16' => 2045, '14,16,18' => 2222, '16,18,20' => 2400, '18,20,22' => 2625, '20,22,24' => 2830, '22,24,26' => 3195, '24,26,28' => 3500,
                    '20,22,24,26' => 3740, '22,24,26,28' => 4140, '22,22,22,22' => 3485, '24,24,24,24' => 3830, '26,26,26,26' => 4500, '28,28,28,28' => 4745,
                ],
            ],
            [
                'slug' => 'mongolian-afro-coily', 'name' => 'Mongolian Afro Coily', 'origin' => 'Mongolian', 'pattern' => 'Afro Coily', 'script_word' => 'Coily',
                'best_for' => '4C natural hair',
                'tagline' => 'Tight coils with richness, density and definition.',
                'description' => 'Made for queens who love the richness, density and definition of coily hair. It blends with 4C textures to enhance your natural crown, not change it. One client has kept her bundles in rotation for about two years, from everyday looks to a polished updo.',
                'highlights' => ['100% human hair', 'Blends with 4C texture', 'Lasts for years with the right care', 'Your bundles can become clip-ins'],
                'formats' => ['bundles', 'ponytail'], 'bundle_weight' => '95–100 g', 'coil' => 'coil', 'sort' => 2,
                'image' => 'assets/img/afro-coily-updo.webp', 'image_alt' => 'Smiling woman wearing a high Mongolian Afro Coily updo outdoors',
                'prices' => [
                    '14,14,14' => 1680, '16,16,16' => 1750, '18,18,18' => 1800, '20,20,20' => 2040, '22,22,22' => 2260, '24,24,24' => 2380,
                    '26,26,26' => 2560, '28,28,28' => 2770,
                    '12,14,16' => 1700, '14,16,18' => 1745, '16,18,20' => 1870, '18,20,22' => 2030, '20,22,24' => 2220, '22,24,26' => 2400, '24,26,28' => 2560,
                ],
            ],
            [
                'slug' => 'brazilian-textured-straight', 'name' => 'Brazilian Textured Straight', 'origin' => 'Brazilian', 'pattern' => 'Textured Straight', 'script_word' => 'Straight',
                'best_for' => 'Relaxed, silk-pressed and heat-trained hair',
                'tagline' => 'Soft movement, no harsh contrast, no silky shine.',
                'description' => 'It has never been about natural versus relaxed. Textured Straight is made to blend with relaxed and heat-trained hair, with a soft, natural finish and real movement, so you can get ready in minutes. Wear it straight, curl it, or lift the colour like the client who wore three 18″ bundles.',
                'highlights' => ['100% human hair', 'Blends with relaxed and silk-pressed hair', 'Takes curls and colour', 'Also made into custom clip-ins'],
                'formats' => ['bundles', 'clip-ins'], 'bundle_weight' => '98–100 g', 'coil' => 'wave', 'sort' => 3,
                'image' => 'assets/img/straight-coloured-curls.webp', 'image_alt' => 'Brazilian Textured Straight bundles with lifted colour, finished in soft curls',
                'prices' => [
                    '12,12,12' => 2355, '14,14,14' => 2430, '16,16,16' => 2865, '18,18,18' => 3135, '20,20,20' => 3370, '22,22,22' => 3690, '24,24,24' => 3875,
                    '12,14,16' => 2600, '14,16,18' => 2835, '16,18,20' => 2900, '18,20,22' => 3400, '20,22,24' => 3640,
                    '12,12' => 1845, '14,14' => 1880, '16,16' => 2140, '18,18' => 2300, '20,20' => 2455, '22,22' => 2672, '24,24' => 2790,
                ],
            ],
        ];

        foreach ($textures as $data) {
            $prices = $data['prices'];
            unset($data['prices']);
            $texture = Texture::firstOrCreate(['slug' => $data['slug']], $data);
            $sort = 0;
            foreach ($prices as $lengths => $price) {
                PriceSet::firstOrCreate(
                    ['texture_id' => $texture->id, 'lengths' => $lengths],
                    ['bundle_count' => count(explode(',', $lengths)), 'price' => $price, 'sort' => $sort++],
                );
            }
        }

        if (GalleryPhoto::count() === 0) {
            $t = Texture::pluck('id', 'slug');
            $photos = [
                ['kinky-curly-fulani', 'Fulani cornrows at the front with soft Brazilian Kinky Curly at the back', 'Fulani look, cornrows to the front', 'Queen Cam', 'brazilian-kinky-curly'],
                ['afro-coily-queen-cam', 'Woman at a lantern-lit terrace wearing a Mongolian Afro Coily half-up puff', 'Afro Coily, blended with 4C hair', 'Queen Cam', 'mongolian-afro-coily'],
                ['kinky-curly-half-up', 'Close portrait of a client wearing Brazilian Kinky Curly half-up, half-down', 'Half-up, half-down', 'Client Cam', 'brazilian-kinky-curly'],
                ['straight-coloured-curls', 'Brazilian Textured Straight with lifted colour, finished in soft curls', '3 × 18″, colour lifted', 'Client Cam', 'brazilian-textured-straight'],
                ['kinky-curly-bleached', 'Back view of lightly bleached Brazilian Kinky Curly with honey tones', 'Lightly bleached install', 'Texture Shot', 'brazilian-kinky-curly'],
                ['afro-coily-updo', 'Mongolian Afro Coily styled into a high, polished updo', 'Two years in, still in rotation', 'Client Cam', 'mongolian-afro-coily'],
                ['kinky-curly-flip', 'Woman flipping long Brazilian Kinky Curly hair to one side', '7-day hair diary', 'Wear Test', 'brazilian-kinky-curly'],
                ['straight-blend', 'Back view of Brazilian Textured Straight blended with relaxed hair', 'Three tracks, seamless blend', 'Texture Shot', 'brazilian-textured-straight'],
                ['kinky-curly-red-back', 'Back view of voluminous Brazilian Kinky Curly over a red top', 'Day 10, no redefining', 'Wear Test', 'brazilian-kinky-curly'],
                ['clip-ins-afro-coily', 'A custom clip-in set made from Mongolian Afro Coily bundles', 'Custom clip-ins from bundles', 'Behind the scenes', 'mongolian-afro-coily'],
                ['kinky-curly-closeup', 'Close-up of defined Brazilian Kinky Curly curls after washing and air-drying', 'Washed and air-dried', 'Texture Shot', 'brazilian-kinky-curly'],
                ['kinky-curly-red', 'Woman with Brazilian Kinky Curly extensions and a gold cross necklace', 'Day 10 performance check', 'Wear Test', 'brazilian-kinky-curly'],
            ];
            foreach ($photos as $i => [$file, $alt, $caption, $label, $slug]) {
                GalleryPhoto::create([
                    'path' => "assets/img/{$file}.webp", 'alt' => $alt, 'caption' => $caption, 'label' => $label,
                    'texture_id' => $t[$slug] ?? null, 'sort' => $i,
                ]);
            }
        }

        foreach (Setting::DEFAULTS as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}

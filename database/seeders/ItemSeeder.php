<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $tim   = User::where('email', 'test@test.com')->first(); 

        $fietsCat = Category::where('slug', 'fietsen')->value('id');
        $toolCat  = Category::where('slug', 'gereedschap')->value('id');

        if ($admin && $fietsCat) {
            Item::create([
                'user_id'       => $admin->id,
                'category_id'   => $fietsCat,
                'title'         => 'Fiets',
                'description'   => 'Stadsfiets in goede staat, ideaal voor korte ritjes.',
                'condition'     => 'Gebruikt',
                'status'        => 'beschikbaar',
                'location'      => 'Leiden',
                'price_per_day' => 5,
                'cover_image'   => null,
            ]);
        }

        if ($tim && $toolCat) {
            Item::create([
                'user_id'       => $tim->id,
                'category_id'   => $toolCat,
                'title'         => 'Boormachine',
                'description'   => 'Sterke boormachine, handig voor klusjes in huis.',
                'condition'     => 'Zo goed als nieuw',
                'status'        => 'beschikbaar',
                'location'      => 'Den Haag',
                'price_per_day' => 8,
                'cover_image'   => null,
            ]);
        }
    }
}

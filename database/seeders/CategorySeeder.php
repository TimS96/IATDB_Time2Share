<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'name' => 'Fietsen',
            'slug' => 'fietsen',
        ]);

        Category::create([
            'name' => 'Elektronica',
            'slug' => 'elektronica',
        ]);

        Category::create([
            'name' => 'Gereedschap',
            'slug' => 'gereedschap',
        ]);
    }
}

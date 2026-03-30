<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Molitor\Cms\Database\Seeders\CmsSeeder;
use Molitor\Currency\Database\Seeders\CurrencySeeder;
use Molitor\Language\Database\Seeders\LanguageSeeder;
use Molitor\Product\Database\Seeders\ProductSeeder;
use Molitor\User\Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            LanguageSeeder::class,
            CmsSeeder::class,
            CurrencySeeder::class,
            ProductSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Molitor\Cms\Database\Seeders\CmsSeeder;
use Molitor\Language\Database\Seeders\LanguageSeeder;
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
            // User package MUST be first - creates user groups and permissions that other seeders depend on
            UserSeeder::class,

            // Language package - depends on 'admin' user group
            LanguageSeeder::class,

            // CMS package - depends on 'admin' user group
            CmsSeeder::class,
        ]);
    }
}

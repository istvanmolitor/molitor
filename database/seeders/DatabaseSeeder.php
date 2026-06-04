<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use IstvanMolitor\ArticleScraper\database\seeders\ArticleScraperSeeder;
use Molitor\Address\database\seeders\AddressSeeder;
use Molitor\Cms\Database\Seeders\CmsSeeder;
use Molitor\CmsPostRelations\Database\Seeders\CmsPostRelationsSeeder;
use Molitor\Currency\Database\Seeders\CurrencySeeder;
use Molitor\Customer\database\seeders\CustomerSeeder;
use Molitor\Language\Database\Seeders\LanguageSeeder;
use Molitor\Media\Database\Seeders\MediaSeeder;
use Molitor\Order\database\seeders\OrderSeeder;
use Molitor\Product\Database\Seeders\ProductSeeder;
use Molitor\RssWatcher\database\seeders\NewsRssSeeder;
use Molitor\Setting\database\seeders\SettingSeeder;
use Molitor\Stock\database\seeders\StockSeeder;
use Molitor\Theme\database\seeders\ThemeSeeder;
use Molitor\Unas\database\seeders\UnasSeeder;
use Molitor\User\Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = [
            UserSeeder::class,
            LanguageSeeder::class,
            ArticleScraperSeeder::class,
            CmsSeeder::class,
            CmsPostRelationsSeeder::class,
            CurrencySeeder::class,
            CustomerSeeder::class,
            MediaSeeder::class,
            OrderSeeder::class,
            ProductSeeder::class,
            SettingSeeder::class,
            ThemeSeeder::class,
            StockSeeder::class,
            UnasSeeder::class,
            NewsRssSeeder::class,
            AddressSeeder::class,
        ];

        $textMiningSeeder = 'Molitor\\TextMining\\Database\\Seeders\\TextMiningSeeder';
        if (class_exists($textMiningSeeder)) {
            $seeders[] = $textMiningSeeder;
        }

        $this->call($seeders);
    }
}

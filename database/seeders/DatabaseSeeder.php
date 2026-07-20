<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use IstvanMolitor\ArticleScraper\database\seeders\ArticleScraperSeeder;
use Molitor\Address\database\seeders\AddressSeeder;
use Molitor\Cms\Database\Seeders\CmsSeeder;
use Molitor\CmsPostGenerator\Database\Seeders\CmsPostGeneratorSeeder;
use Molitor\CmsPostRelations\Database\Seeders\CmsPostRelationsSeeder;
use Molitor\Currency\Database\Seeders\CurrencySeeder;
use Molitor\Customer\database\seeders\CustomerSeeder;
use Molitor\CustomerProduct\database\seeders\CustomerProductSeeder;
use Molitor\DynamicForm\Database\Seeders\DynamicFormSeeder;
use Molitor\Gallery\Database\Seeders\GallerySeeder;
use Molitor\Language\Database\Seeders\LanguageSeeder;
use Molitor\Media\Database\Seeders\MediaSeeder;
use Molitor\Order\database\seeders\OrderSeeder;
use Molitor\Product\Database\Seeders\ProductSeeder;
use Molitor\RssWatcher\database\seeders\NewsRssSeeder;
use Molitor\Scraper\database\seeders\ScraperSeeder;
use Molitor\Setting\database\seeders\SettingSeeder;
use Molitor\Stock\database\seeders\StockSeeder;
use Molitor\Keyword\Database\Seeders\KeywordSeeder;
use Molitor\Purchase\database\seeders\PurchaseSeeder;
use Molitor\TextMining\Database\Seeders\TextMiningSeeder;
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
            CmsPostGeneratorSeeder::class,
            CmsPostRelationsSeeder::class,
            CurrencySeeder::class,
            CustomerSeeder::class,
            CustomerProductSeeder::class,
            MediaSeeder::class,
            OrderSeeder::class,
            ProductSeeder::class,
            SettingSeeder::class,
            ThemeSeeder::class,
            StockSeeder::class,
            UnasSeeder::class,
            NewsRssSeeder::class,
            KeywordSeeder::class,
            TextMiningSeeder::class,
            AddressSeeder::class,
            ScraperSeeder::class,
            GallerySeeder::class,
            PurchaseSeeder::class,
            DynamicFormSeeder::class,
        ];

        $this->call($seeders);
    }
}

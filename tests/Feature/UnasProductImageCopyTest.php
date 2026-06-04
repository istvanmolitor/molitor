<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Molitor\Language\Models\Language;
use Molitor\Product\Models\ProductUnit;
use Molitor\Unas\Models\UnasProduct;
use Molitor\Unas\Models\UnasProductImage;
use Molitor\Unas\Models\UnasShop;
use Molitor\Unas\Services\ProductCopyService;
use Molitor\Stock\Models\Warehouse;
use Tests\TestCase;

class UnasProductImageCopyTest extends TestCase
{
    use RefreshDatabase;

    public function test_unas_product_image_urls_are_copied_to_product_master(): void
    {
        Language::query()->create([
            'enabled' => true,
            'code' => 'hu',
        ]);

        $warehouse = Warehouse::query()->create([
            'is_primary' => true,
            'name' => 'Kozponti raktar',
        ]);

        $shop = UnasShop::query()->create([
            'enabled' => true,
            'domain' => 'test-shop.hu',
            'name' => 'Test Shop',
            'api_key' => 'secret-key',
            'warehouse_id' => $warehouse->id,
        ]);

        $productUnit = ProductUnit::query()->create([
            'enabled' => true,
            'code' => 'db',
        ]);
        $productUnit->setAttributeTranslation('name', 'Darab', 'hu');
        $productUnit->setAttributeTranslation('short_name', 'db', 'hu');
        $productUnit->save();

        $unasProduct = UnasProduct::query()->create([
            'sku' => 'UNAS-IMG-1',
            'unas_shop_id' => $shop->id,
            'product_unit_id' => $productUnit->id,
            'price' => 1990,
            'stock' => 7,
            'changed' => false,
        ]);
        $unasProduct->setAttributeTranslation('name', 'UNAS Termek', 'hu');
        $unasProduct->save();

        UnasProductImage::query()->create([
            'unas_product_id' => $unasProduct->id,
            'image_url' => 'https://example.com/unas-image-1.jpg',
            'is_main' => true,
            'sort' => 0,
        ]);

        UnasProductImage::query()->create([
            'unas_product_id' => $unasProduct->id,
            'image_url' => 'https://example.com/unas-image-2.jpg',
            'is_main' => false,
            'sort' => 1,
        ]);

        /** @var ProductCopyService $copyService */
        $copyService = $this->app->make(ProductCopyService::class);
        $product = $copyService->copyUnasProduct($unasProduct->fresh('images', 'productUnit'));

        $this->assertDatabaseHas('product_images', [
            'product_id' => $product->id,
            'image_url' => 'https://example.com/unas-image-1.jpg',
            'is_main' => true,
            'sort' => 0,
        ]);

        $this->assertDatabaseHas('product_images', [
            'product_id' => $product->id,
            'image_url' => 'https://example.com/unas-image-2.jpg',
            'is_main' => false,
            'sort' => 1,
        ]);
    }
}

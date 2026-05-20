<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Molitor\Unas\Models\UnasShop;
use Tests\TestCase;

class UnasApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_unas_shops(): void
    {
        Sanctum::actingAs(User::factory()->create());

        UnasShop::query()->create([
            'enabled' => true,
            'domain' => 'test-shop.hu',
            'name' => 'Test Shop',
            'api_key' => 'secret-key',
        ]);

        $response = $this->getJson('/api/unas/shops');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Test Shop');
    }

    public function test_can_create_unas_shop(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/unas/shops', [
            'enabled' => true,
            'domain' => 'new-shop.hu',
            'name' => 'New Shop',
            'api_key' => 'new-key',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'New Shop');

        $this->assertDatabaseHas('unas_shops', [
            'name' => 'New Shop',
            'domain' => 'new-shop.hu',
        ]);
    }
}

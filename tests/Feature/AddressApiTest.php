<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Molitor\Language\Models\Language;
use Tests\TestCase;

class AddressApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Language::query()->create([
            'enabled' => true,
            'code' => 'en',
        ]);
    }

    public function test_can_manage_countries_via_api(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $createResponse = $this->postJson('/api/admin/address/countries', [
            'code' => 'HU',
            'name' => 'Hungary',
            'is_default' => true,
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('data.code', 'HU')
            ->assertJsonPath('data.name', 'Hungary')
            ->assertJsonPath('data.is_default', true);

        $countryId = (int) $createResponse->json('data.id');

        $indexResponse = $this->getJson('/api/admin/address/countries');
        $indexResponse->assertOk()
            ->assertJsonPath('meta.total', 1);

        $updateResponse = $this->putJson("/api/admin/address/countries/{$countryId}", [
            'code' => 'HUN',
            'name' => 'Hungary Updated',
            'is_default' => false,
        ]);

        $updateResponse->assertOk()
            ->assertJsonPath('data.code', 'HUN')
            ->assertJsonPath('data.name', 'Hungary Updated')
            ->assertJsonPath('data.is_default', false);

        $this->deleteJson("/api/admin/address/countries/{$countryId}")
            ->assertOk();

        $this->assertDatabaseMissing('countries', ['id' => $countryId]);
    }
}

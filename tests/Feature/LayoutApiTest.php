<?php

namespace Tests\Feature;

use Tests\TestCase;

class LayoutApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_can_list_layouts(): void
    {
        $response = $this->getJson('/api/cms/layouts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'default' => [
                        'name',
                        'template',
                    ],
                    'full-width',
                ],
            ]);

        $this->assertArrayHasKey('default', $response->json('data'));
        $this->assertEquals('Default Layout', $response->json('data.default.name'));
    }
}

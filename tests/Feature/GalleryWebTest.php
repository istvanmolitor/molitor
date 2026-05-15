<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Molitor\Gallery\Models\Gallery;
use Molitor\Gallery\Models\GalleryImage;
use Molitor\Theme\Services\ThemeRegistry;
use Molitor\Theme\Themes\DefaultTheme;
use Tests\TestCase;

class GalleryWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_page_is_accessible(): void
    {
        $registry = app(ThemeRegistry::class);
        $registry->register(DefaultTheme::class);
        // Beállítjuk az aktív témát kézzel a teszthez
        $reflection = new \ReflectionClass($registry);
        $property = $reflection->getProperty('activeTheme');
        $property->setAccessible(true);
        $property->setValue($registry, app(DefaultTheme::class));

        $gallery = Gallery::create([
            'name' => 'Test Gallery',
            'slug' => 'test-gallery',
            'description' => 'Test Description',
        ]);

        $image1 = GalleryImage::create([
            'gallery_id' => $gallery->id,
            'image_url' => 'https://example.com/image1.jpg',
            'title' => 'Image 1',
            'order' => 1,
        ]);

        $image2 = GalleryImage::create([
            'gallery_id' => $gallery->id,
            'image_url' => 'https://example.com/image2.jpg',
            'title' => 'Image 2',
            'order' => 2,
        ]);

        // Alap galéria nézet
        $response = $this->get(route('gallery.show', $gallery->slug));
        $response->assertStatus(200);
        $response->assertSee('Test Gallery');
        $response->assertSee('https://example.com/image1.jpg'); // Főkép (első)
        $response->assertSee('https://example.com/image2.jpg'); // Miniatűr

        // Konkrét kép nézet
        $response = $this->get(route('gallery.show', [$gallery->slug, $image2->id]));
        $response->assertStatus(200);
        $response->assertSee('https://example.com/image2.jpg'); // Most ez a főkép
        $response->assertSee('aria-label="Előző kép"', false);
        $response->assertDontSee('aria-label="Következő kép"');
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Molitor\Cms\Models\Content;
use Molitor\Cms\Models\Post;
use Tests\TestCase;

class CmsPostRelationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_post_to_post_relation(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $post = $this->createPost();
        $relatedPost = $this->createPost('Kapcsolt poszt');

        $response = $this->postJson('/api/admin/cms-post-relations', [
            'post_id' => $post->id,
            'related_post_id' => $relatedPost->id,
            'sort' => 5,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.post_id', $post->id)
            ->assertJsonPath('data.related_post_id', $relatedPost->id)
            ->assertJsonPath('data.sort', 5);

        $this->assertDatabaseHas('cms_post_relations', [
            'post_id' => $post->id,
            'related_post_id' => $relatedPost->id,
            'sort' => 5,
        ]);
    }

    public function test_cannot_create_duplicate_post_to_post_relation(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $post = $this->createPost();
        $relatedPost = $this->createPost('Masodik poszt');

        $payload = [
            'post_id' => $post->id,
            'related_post_id' => $relatedPost->id,
            'sort' => 0,
        ];

        $this->postJson('/api/admin/cms-post-relations', $payload)->assertCreated();

        $this->postJson('/api/admin/cms-post-relations', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['related_post_id']);
    }

    private function createPost(string $title = 'Teszt poszt'): Post
    {
        $content = Content::query()->create();

        return Post::query()->create([
            'title' => $title,
            'slug' => 'teszt-poszt-'.uniqid(),
            'is_published' => true,
            'lead' => 'Rovid leiras',
            'layout' => 'default',
            'main_image_url' => null,
            'content_id' => $content->id,
            'language_id' => null,
        ]);
    }
}

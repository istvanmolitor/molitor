<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Molitor\Cms\Models\Content;
use Molitor\Cms\Models\Post;
use Molitor\Cms\Models\PostGroup;
use Tests\TestCase;

class CmsPostWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_index_page_is_accessible(): void
    {
        $response = $this->get(route('cms.post.index'));

        $response->assertStatus(200);
    }

    public function test_post_show_page_is_accessible_by_slug(): void
    {
        $content = Content::query()->create();

        $post = Post::query()->create([
            'title' => 'Teszt bejegyzes',
            'slug' => 'teszt-bejegyzes',
            'content_id' => $content->id,
        ]);

        $response = $this->get(route('cms.post.show', $post->slug));

        $response->assertStatus(200);
        $response->assertSee('Teszt bejegyzes');
    }

    public function test_post_group_show_page_is_accessible_by_slug(): void
    {
        $content = Content::query()->create();

        $post = Post::query()->create([
            'title' => 'Csoport bejegyzes',
            'slug' => 'csoport-bejegyzes',
            'content_id' => $content->id,
        ]);

        $postGroup = PostGroup::query()->create([
            'name' => 'Hirek',
            'slug' => 'hirek',
        ]);

        $postGroup->posts()->attach($post->id);

        $response = $this->get(route('cms.post-group.show', $postGroup->slug));

        $response->assertStatus(200);
        $response->assertSee('Hirek');
        $response->assertSee('Csoport bejegyzes');
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Molitor\Cms\Models\Author;
use Molitor\Cms\Models\Content;
use Molitor\Cms\Models\Post;
use Molitor\Cms\Models\PostGroup;
use Tests\TestCase;

class CmsAuthorWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_index_page_is_accessible(): void
    {
        Author::query()->create([
            'name' => 'Teszt Szerző',
            'slug' => 'teszt-szerzo',
        ]);

        $response = $this->get(route('cms.author.index'));

        $response->assertStatus(200);
        $response->assertSee('Teszt Szerző');
    }

    public function test_author_show_page_is_accessible_by_slug(): void
    {
        $author = Author::query()->create([
            'name' => 'Teszt Szerző',
            'slug' => 'teszt-szerzo',
        ]);

        $content = Content::query()->create();
        $post = Post::query()->create([
            'title' => 'Szerző cikke',
            'slug' => 'szerzo-cikke',
            'content_id' => $content->id,
        ]);

        $author->posts()->attach($post->id);

        $response = $this->get(route('cms.author.show', $author->slug));

        $response->assertStatus(200);
        $response->assertSee('Teszt Szerző');
        $response->assertSee('Szerző cikke');
    }

    public function test_post_show_page_displays_clickable_authors_and_groups(): void
    {
        $author = Author::query()->create([
            'name' => 'John Doe',
            'slug' => 'john-doe',
        ]);

        $group = PostGroup::query()->create([
            'name' => 'Tech',
            'slug' => 'tech',
        ]);

        $content = Content::query()->create();
        $post = Post::query()->create([
            'title' => 'Sample Post',
            'slug' => 'sample-post',
            'content_id' => $content->id,
        ]);

        $post->authors()->attach($author->id);
        $post->postGroups()->attach($group->id);

        $response = $this->get(route('cms.post.show', $post->slug));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee(route('cms.author.show', $author->slug));
        $response->assertSee('Tech');
        $response->assertSee(route('cms.post-group.show', $group->slug));
    }
}

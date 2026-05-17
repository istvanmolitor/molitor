<?php

namespace Tests\Feature\Services;

use App\Services\RssWatcherCmsPageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Molitor\Language\Models\Language;
use Molitor\RssWatcher\Models\RssFeed;
use Molitor\RssWatcher\Models\RssFeedItem;
use Tests\TestCase;

class RssWatcherCmsPageServiceTest extends TestCase
{
    use RefreshDatabase;

    private RssWatcherCmsPageService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RssWatcherCmsPageService::class);

        // Create a default language
        Language::query()->create([
            'name' => 'Hungarian',
            'code' => 'hu',
            'is_default' => true,
        ]);
    }

    public function test_creates_page_with_content_and_metadata(): void
    {
        $rssFeed = RssFeed::query()->create([
            'enabled' => true,
            'name' => 'Test Feed',
            'url' => 'https://example.com/rss',
        ]);

        $rssFeedItem = RssFeedItem::query()->create([
            'rss_feed_id' => $rssFeed->id,
            'guid' => 'test-guid-123',
            'title' => 'Test Article',
            'link' => 'https://example.com/article',
            'description' => 'Test description',
            'image' => 'https://example.com/image.jpg',
            'published_at' => now(),
        ]);

        $page = $this->service->createOrUpdateFromRssItem($rssFeedItem);

        $this->assertSame('Test Article', $page->title);
        $this->assertSame('test-article', $page->slug);
        $this->assertTrue($page->is_published);
        $this->assertSame('https://example.com/image.jpg', $page->main_image_url);

        $this->assertDatabaseHas('page_meta', [
            'page_id' => $page->id,
            'name' => 'rss_feed_item_id',
            'meta_data' => (string) $rssFeedItem->id,
        ]);

        $this->assertDatabaseHas('page_meta', [
            'page_id' => $page->id,
            'name' => 'rss_source_link',
            'meta_data' => 'https://example.com/article',
        ]);
    }

    public function test_updates_existing_page_for_same_rss_feed_item(): void
    {
        $rssFeed = RssFeed::query()->create([
            'enabled' => true,
            'name' => 'Test Feed',
            'url' => 'https://example.com/rss',
        ]);

        $rssFeedItem = RssFeedItem::query()->create([
            'rss_feed_id' => $rssFeed->id,
            'guid' => 'test-guid-456',
            'title' => 'Original title',
            'link' => 'https://example.com/original',
            'description' => 'Original description',
            'published_at' => now(),
        ]);

        $firstPage = $this->service->createOrUpdateFromRssItem($rssFeedItem);

        $rssFeedItem->update([
            'title' => 'Updated title',
            'description' => 'Updated description',
            'link' => 'https://example.com/updated',
        ]);

        $updatedPage = $this->service->createOrUpdateFromRssItem($rssFeedItem->fresh());

        $this->assertSame($firstPage->id, $updatedPage->id);
        $this->assertSame('Updated title', $updatedPage->title);
        $this->assertSame('Updated description', $updatedPage->lead);
        $this->assertDatabaseCount('pages', 1);
    }
}

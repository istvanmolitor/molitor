<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Molitor\Cms\Models\Page;
use Molitor\Cms\Services\ContentHandler;
use Molitor\RssWatcher\Events\RssFeedItemChangedEvent;
use Molitor\RssWatcher\Events\RssFeedItemCreatedEvent;
use Molitor\RssWatcher\Models\RssFeed;
use Molitor\RssWatcher\Models\RssFeedItem;
use Tests\TestCase;

class RssWatcherCmsPageListenersTest extends TestCase
{
    use RefreshDatabase;

    public function test_created_event_creates_cms_page(): void
    {
        $feed = RssFeed::query()->create([
            'name' => 'Test feed',
            'url' => 'https://example.com/rss.xml',
            'enabled' => true,
        ]);

        $item = RssFeedItem::query()->create([
            'rss_feed_id' => $feed->id,
            'guid' => 'guid-created-1',
            'title' => 'Created RSS Item',
            'link' => 'https://example.com/item-created',
            'description' => 'Created item description',
            'image' => 'https://example.com/created.jpg',
            'published_at' => now(),
        ]);

        event(new RssFeedItemCreatedEvent($item));

        $page = Page::query()->first();

        $this->assertNotNull($page);
        $this->assertSame('Created RSS Item', $page->title);
        $this->assertTrue($page->is_published);
        $this->assertSame('https://example.com/created.jpg', $page->main_image_url);

        $this->assertDatabaseHas('page_meta', [
            'page_id' => $page->id,
            'name' => 'rss_feed_item_id',
            'meta_data' => (string) $item->id,
        ]);

        $elements = $page->content->contentElements()->orderBy('sort')->get();
        $this->assertCount(2, $elements);

        $contentHandler = app(ContentHandler::class);
        $textData = $contentHandler->getContentData($elements->first());

        $this->assertSame('Created item description', $textData['text'] ?? null);
    }

    public function test_changed_event_updates_existing_cms_page(): void
    {
        $feed = RssFeed::query()->create([
            'name' => 'Update feed',
            'url' => 'https://example.com/update-rss.xml',
            'enabled' => true,
        ]);

        $item = RssFeedItem::query()->create([
            'rss_feed_id' => $feed->id,
            'guid' => 'guid-changed-1',
            'title' => 'Original title',
            'link' => 'https://example.com/item-changed',
            'description' => 'Original description',
            'image' => null,
            'published_at' => now(),
        ]);

        event(new RssFeedItemCreatedEvent($item));

        $originalPage = Page::query()->first();
        $this->assertNotNull($originalPage);

        $item->update([
            'title' => 'Updated title',
            'description' => 'Updated description',
            'image' => 'https://example.com/updated.jpg',
        ]);

        event(new RssFeedItemChangedEvent($item->fresh()));

        $updatedPage = Page::query()->first();

        $this->assertNotNull($updatedPage);
        $this->assertSame($originalPage->id, $updatedPage->id);
        $this->assertSame('Updated title', $updatedPage->title);
        $this->assertSame('https://example.com/updated.jpg', $updatedPage->main_image_url);
        $this->assertSame(1, Page::query()->count());

        $this->assertDatabaseHas('page_meta', [
            'page_id' => $updatedPage->id,
            'name' => 'rss_feed_item_id',
            'meta_data' => (string) $item->id,
        ]);

        $elements = $updatedPage->content->contentElements()->orderBy('sort')->get();
        $contentHandler = app(ContentHandler::class);
        $textData = $contentHandler->getContentData($elements->first());

        $this->assertSame('Updated description', $textData['text'] ?? null);
    }

    public function test_created_events_with_same_title_generate_incremental_slug_without_rss_item_id(): void
    {
        $feed = RssFeed::query()->create([
            'name' => 'Slug feed',
            'url' => 'https://example.com/slug-rss.xml',
            'enabled' => true,
        ]);

        $firstItem = RssFeedItem::query()->create([
            'rss_feed_id' => $feed->id,
            'guid' => 'guid-slug-1',
            'title' => 'Same title',
            'link' => 'https://example.com/slug-1',
            'description' => 'First',
            'image' => null,
            'published_at' => now(),
        ]);

        $secondItem = RssFeedItem::query()->create([
            'rss_feed_id' => $feed->id,
            'guid' => 'guid-slug-2',
            'title' => 'Same title',
            'link' => 'https://example.com/slug-2',
            'description' => 'Second',
            'image' => null,
            'published_at' => now(),
        ]);

        event(new RssFeedItemCreatedEvent($firstItem));
        event(new RssFeedItemCreatedEvent($secondItem));

        $pages = Page::query()
            ->orderBy('id')
            ->get();

        $this->assertCount(2, $pages);
        $this->assertSame('same-title', $pages[0]->slug);
        $this->assertSame('same-title-1', $pages[1]->slug);
    }
}

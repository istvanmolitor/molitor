<?php

namespace Tests\Feature\Services;

use App\Services\RssWatcherCmsPageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Molitor\Cms\Models\PageGroup;
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

    public function test_creates_page_group_based_on_rss_feed_domain(): void
    {
        // Create an RSS feed with a URL
        $rssFeed = RssFeed::query()->create([
            'enabled' => true,
            'name' => 'Test Feed',
            'url' => 'https://example.com/rss',
        ]);

        // Create an RSS feed item
        $rssFeedItem = RssFeedItem::query()->create([
            'rss_feed_id' => $rssFeed->id,
            'guid' => 'test-guid-123',
            'title' => 'Test Article',
            'link' => 'https://example.com/article',
            'description' => 'Test description',
            'published_at' => now(),
        ]);

        // Execute the service method
        $page = $this->service->createOrUpdateFromRssItem($rssFeedItem);

        // Assert that a page group was created with the correct domain
        $this->assertDatabaseHas('page_groups', [
            'name' => 'example.com',
            'slug' => 'example-com',
        ]);

        // Assert that the page is linked to the page group
        $pageGroup = PageGroup::query()->where('slug', 'example-com')->first();
        $this->assertNotNull($pageGroup);
        $this->assertTrue($page->pageGroups->contains($pageGroup));
    }

    public function test_reuses_existing_page_group_for_same_domain(): void
    {
        // Create an existing page group
        $existingPageGroup = PageGroup::query()->create([
            'name' => 'example.com',
            'slug' => 'example-com',
            'layout' => 'default',
        ]);

        // Create an RSS feed with a URL
        $rssFeed = RssFeed::query()->create([
            'enabled' => true,
            'name' => 'Test Feed',
            'url' => 'https://example.com/rss',
        ]);

        // Create an RSS feed item
        $rssFeedItem = RssFeedItem::query()->create([
            'rss_feed_id' => $rssFeed->id,
            'guid' => 'test-guid-456',
            'title' => 'Another Test Article',
            'link' => 'https://example.com/another-article',
            'description' => 'Another test description',
            'published_at' => now(),
        ]);

        // Execute the service method
        $page = $this->service->createOrUpdateFromRssItem($rssFeedItem);

        // Assert that no new page group was created
        $this->assertEquals(1, PageGroup::query()->count());

        // Assert that the page is linked to the existing page group
        $this->assertTrue($page->pageGroups->contains($existingPageGroup));
    }

    public function test_handles_www_prefix_in_domain(): void
    {
        // Create an RSS feed with a URL containing www
        $rssFeed = RssFeed::query()->create([
            'enabled' => true,
            'name' => 'Test Feed',
            'url' => 'https://www.example.com/rss',
        ]);

        // Create an RSS feed item
        $rssFeedItem = RssFeedItem::query()->create([
            'rss_feed_id' => $rssFeed->id,
            'guid' => 'test-guid-789',
            'title' => 'Test Article with WWW',
            'link' => 'https://www.example.com/article',
            'description' => 'Test description',
            'published_at' => now(),
        ]);

        // Execute the service method
        $page = $this->service->createOrUpdateFromRssItem($rssFeedItem);

        // Assert that the page group was created without www prefix
        $this->assertDatabaseHas('page_groups', [
            'name' => 'example.com',
            'slug' => 'example-com',
        ]);

        // Assert that the page is linked to the page group
        $pageGroup = PageGroup::query()->where('slug', 'example-com')->first();
        $this->assertNotNull($pageGroup);
        $this->assertTrue($page->pageGroups->contains($pageGroup));
    }
}

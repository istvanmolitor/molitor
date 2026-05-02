<?php

namespace App\Services;

use Illuminate\Support\Str;
use Molitor\Cms\Models\Content;
use Molitor\Cms\Models\Page;
use Molitor\Cms\Models\PageGroup;
use Molitor\Cms\Models\PageMeta;
use Molitor\Cms\Repositories\PageGroupRepositoryInterface;
use Molitor\Cms\Repositories\PageMetaRepositoryInterface;
use Molitor\Cms\Repositories\PageRepositoryInterface;
use Molitor\Cms\Services\ContentHandler;
use Molitor\Language\Models\Language;
use Molitor\RssWatcher\Models\RssFeedItem;

class RssWatcherCmsPageService
{
    private const RSS_ITEM_ID_META_NAME = 'rss_feed_item_id';

    private const RSS_SOURCE_LINK_META_NAME = 'rss_source_link';

    public function __construct(
        private ContentHandler $contentHandler,
        private PageRepositoryInterface $pageRepository,
        private PageMetaRepositoryInterface $pageMetaRepository,
        private PageGroupRepositoryInterface $pageGroupRepository
    ) {}

    public function createOrUpdateFromRssItem(RssFeedItem $rssFeedItem): Page
    {
        $page = $this->getPageByRssItem($rssFeedItem);

        if (! $page) {
            $page = $this->createPage($rssFeedItem);
        } else {
            $page = $this->updatePage($page, $rssFeedItem);
        }

        $this->savePageMeta($page, $rssFeedItem);
        $this->syncContent($page, $rssFeedItem);
        $this->syncPageGroups($page, $rssFeedItem);

        return $page->fresh(['content.contentElements', 'metaData', 'pageGroups']);
    }

    private function createPage(RssFeedItem $rssFeedItem): Page
    {
        $content = Content::query()->create([]);
        $title = $this->resolveTitle($rssFeedItem);

        return Page::withoutEvents(fn (): Page => Page::query()->create([
            'title' => $title,
            'slug' => $this->pageRepository->generateUniqueSlug($title, 'rss-item'),
            'is_published' => true,
            'lead' => $this->resolveLead($rssFeedItem),
            'layout' => 'article',
            'main_image_url' => $this->resolveMainImageUrl($rssFeedItem),
            'content_id' => $content->id,
            'language_id' => $this->resolveDefaultLanguageId(),
        ]));
    }

    private function updatePage(Page $page, RssFeedItem $rssFeedItem): Page
    {
        Page::withoutEvents(function () use ($page, $rssFeedItem): void {
            $page->update([
                'title' => $this->resolveTitle($rssFeedItem),
                'is_published' => true,
                'lead' => $this->resolveLead($rssFeedItem),
                'layout' => 'article',
                'main_image_url' => $this->resolveMainImageUrl($rssFeedItem),
            ]);
        });

        return $page->fresh();
    }

    private function savePageMeta(Page $page, RssFeedItem $rssFeedItem): void
    {
        PageMeta::query()->updateOrCreate(
            [
                'page_id' => $page->id,
                'name' => self::RSS_ITEM_ID_META_NAME,
            ],
            [
                'meta_data' => (string) $rssFeedItem->id,
            ]
        );

        PageMeta::query()->updateOrCreate(
            [
                'page_id' => $page->id,
                'name' => self::RSS_SOURCE_LINK_META_NAME,
            ],
            [
                'meta_data' => (string) $rssFeedItem->link,
            ]
        );
    }

    private function syncContent(Page $page, RssFeedItem $rssFeedItem): void
    {
        $elements = [];
        $description = trim((string) $rssFeedItem->description);
        $image = trim((string) $rssFeedItem->image);

        if ($description !== '') {
            $elements[] = [
                'type' => 'text',
                'settings' => [
                    'text' => $description,
                    'align' => 'left',
                ],
            ];
        }

        if ($image !== '') {
            $elements[] = [
                'type' => 'image',
                'settings' => [
                    'src' => $image,
                    'alt' => $this->resolveTitle($rssFeedItem),
                    'width' => null,
                    'height' => null,
                    'alignment' => 'center',
                ],
            ];
        }

        if (empty($elements)) {
            $elements[] = [
                'type' => 'text',
                'settings' => [
                    'text' => (string) $rssFeedItem->link,
                    'align' => 'left',
                ],
            ];
        }

        $this->contentHandler->saveContentElements($page->content, $elements);
    }

    private function getPageByRssItem(RssFeedItem $rssFeedItem): ?Page
    {
        $pageMeta = $this->pageMetaRepository->getByValue(
            (string) $rssFeedItem->id,
            self::RSS_ITEM_ID_META_NAME
        );

        return $pageMeta?->page;
    }

    private function resolveDefaultLanguageId(): ?int
    {
        return Language::query()
            ->where('code', 'hu')
            ->value('id');
    }

    private function resolveTitle(RssFeedItem $rssFeedItem): string
    {
        $title = trim((string) $rssFeedItem->title);

        if ($title === '') {
            $title = trim((string) $rssFeedItem->link);
        }

        if ($title === '') {
            $title = 'RSS item';
        }

        return Str::limit($title, 255, '...');
    }

    private function resolveLead(RssFeedItem $rssFeedItem): ?string
    {
        $lead = trim((string) $rssFeedItem->description);

        if ($lead === '') {
            return null;
        }

        return $lead;
    }

    private function resolveMainImageUrl(RssFeedItem $rssFeedItem): ?string
    {
        $imageUrl = trim((string) $rssFeedItem->image);

        if ($imageUrl === '') {
            return null;
        }

        return Str::length($imageUrl) > 255 ? null : $imageUrl;
    }

    private function syncPageGroups(Page $page, RssFeedItem $rssFeedItem): void
    {
        $rssFeedItem->load('feed');

        if (! $rssFeedItem->feed) {
            return;
        }

        $domain = $this->extractDomainFromUrl($rssFeedItem->feed->url);

        if (! $domain) {
            return;
        }

        $pageGroup = $this->getOrCreatePageGroupByDomain($domain);

        $page->pageGroups()->syncWithoutDetaching([$pageGroup->id]);
    }

    private function getOrCreatePageGroupByDomain(string $domain): PageGroup
    {
        $slug = Str::slug(str_replace('.', '-', $domain));
        $pageGroup = $this->pageGroupRepository->getBySlug($slug);

        if ($pageGroup) {
            return $pageGroup;
        }

        return $this->pageGroupRepository->create([
            'name' => $domain,
            'slug' => $slug,
            'layout' => 'default',
        ]);
    }

    private function extractDomainFromUrl(string $url): ?string
    {
        $parsedUrl = parse_url($url);

        if (! isset($parsedUrl['host'])) {
            return null;
        }

        $host = $parsedUrl['host'];

        // Remove 'www.' prefix if present
        return preg_replace('/^www\./i', '', $host);
    }
}

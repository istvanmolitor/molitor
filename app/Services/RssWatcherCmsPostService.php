<?php

namespace App\Services;

use Illuminate\Support\Str;
use Molitor\Cms\Models\Post;
use Molitor\Cms\Models\PostGroup;
use Molitor\Cms\Repositories\ContentRepositoryInterface;
use Molitor\Cms\Repositories\PostGroupRepositoryInterface;
use Molitor\Cms\Repositories\PostMetaRepositoryInterface;
use Molitor\Cms\Repositories\PostRepositoryInterface;
use Molitor\Cms\Repositories\PostTypeRepositoryInterface;
use Molitor\Language\Repositories\LanguageRepositoryInterface;
use Molitor\RssWatcher\Models\RssFeedItem;
use Molitor\Theme\Services\LayoutService;

class RssWatcherCmsPostService
{
    public function __construct(
        protected PostRepositoryInterface $postRepository,
        protected ContentRepositoryInterface $contentRepository,
        protected PostMetaRepositoryInterface $postMetaRepository,
        protected LanguageRepositoryInterface $languageRepository,
        protected PostGroupRepositoryInterface $postGroupRepository,
        protected PostTypeRepositoryInterface $postTypeRepository,
    ) {}

    public function createOrUpdateFromRssItem(RssFeedItem $rssFeedItem): Post
    {
        $layout = app(LayoutService::class)->getDefault();
        $post = null;

        $postMetaRssId = $this->postMetaRepository->getByValue((string) $rssFeedItem->id, 'rss_feed_item_id');
        if ($postMetaRssId?->post) {
            $post = $postMetaRssId->post;
        }

        if ($post === null && $rssFeedItem->link) {
            $postMetaSourceLink = $this->postMetaRepository->getByValue($rssFeedItem->link, 'source_link');
            if ($postMetaSourceLink?->post) {
                $post = $postMetaSourceLink->post;
                $this->postMetaRepository->create([
                    'post_id' => $post->id,
                    'name' => 'rss_feed_item_id',
                    'meta_data' => (string) $rssFeedItem->id,
                ]);
            }
        }

        if ($post === null) {
            $languageId = $this->languageRepository->getDefaultId();
            $postTypeId = $this->postTypeRepository->getDefault()?->id;

            $post = $this->postRepository->create(
                title: $rssFeedItem->title,
                slug: $this->postRepository->generateUniqueSlug($rssFeedItem->title),
                isPublished: true,
                lead: $rssFeedItem->description,
                mainImageUrl: $rssFeedItem->image,
                languageId: $languageId,
                layout: $layout,
                postTypeId: $postTypeId
            );

            $this->postMetaRepository->create([
                'post_id' => $post->id,
                'name' => 'rss_feed_item_id',
                'meta_data' => (string) $rssFeedItem->id,
            ]);
        }

        $this->postRepository->update(
            post: $post,
            title: $rssFeedItem->title,
            lead: $rssFeedItem->description,
            mainImageUrl: $rssFeedItem->image
        );

        $postGroup = $this->getOrCreatePostGroupFromRssItem($rssFeedItem);
        if ($postGroup) {
            $post->postGroups()->syncWithoutDetaching([$postGroup->id]);
        }

        $postMeta = $this->postMetaRepository->getByPostIdAndName($post->id, 'source_link');
        if ($postMeta) {
            $this->postMetaRepository->update($postMeta, ['meta_data' => $rssFeedItem->link]);
        } else {
            $this->postMetaRepository->create([
                'post_id' => $post->id,
                'name' => 'source_link',
                'meta_data' => $rssFeedItem->link,
            ]);
        }

        return $post;
    }

    protected function getOrCreatePostGroupFromRssItem(RssFeedItem $rssFeedItem): ?PostGroup
    {
        $domainName = $this->getDomainNameFromRssFeedItem($rssFeedItem);
        if (! $domainName) {
            return null;
        }

        $slug = Str::slug(str_replace('.', '-', $domainName));
        if ($slug === '') {
            return null;
        }

        $postGroup = $this->postGroupRepository->getBySlug($slug);
        if ($postGroup) {
            return $postGroup;
        }

        return $this->postGroupRepository->create([
            'name' => $domainName,
            'slug' => $slug,
        ]);
    }

    protected function getDomainNameFromRssFeedItem(RssFeedItem $rssFeedItem): ?string
    {
        $url = $rssFeedItem->link ?: $rssFeedItem->feed?->url;
        if (! $url) {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (! is_string($host) || $host === '') {
            return null;
        }

        $host = Str::lower($host);

        return Str::startsWith($host, 'www.') ? Str::after($host, 'www.') : $host;
    }
}

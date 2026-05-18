<?php

namespace App\Services;

use Molitor\Cms\Models\Post;
use Molitor\Cms\Repositories\ContentRepositoryInterface;
use Molitor\Cms\Repositories\PostMetaRepositoryInterface;
use Molitor\Cms\Repositories\PostRepositoryInterface;
use Molitor\Language\Repositories\LanguageRepositoryInterface;
use Molitor\RssWatcher\Models\RssFeedItem;

class RssWatcherCmsPostService
{
    public function __construct(
        protected PostRepositoryInterface $postRepository,
        protected ContentRepositoryInterface $contentRepository,
        protected PostMetaRepositoryInterface $postMetaRepository,
        protected LanguageRepositoryInterface $languageRepository,
    ) {}

    public function createOrUpdateFromRssItem(RssFeedItem $rssFeedItem): Post
    {
        $postMetaRssId = $this->postMetaRepository->getByValue((string) $rssFeedItem->id, 'rss_feed_item_id');

        if ($postMetaRssId) {
            $post = $postMetaRssId->post;
        } else {
            $languageId = $this->languageRepository->getDefaultId();

            $post = $this->postRepository->create(
                title: $rssFeedItem->title,
                slug: $this->postRepository->generateUniqueSlug($rssFeedItem->title),
                isPublished: true,
                lead: $rssFeedItem->description,
                mainImageUrl: $rssFeedItem->image,
                languageId: $languageId
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

        $postMeta = $this->postMetaRepository->getByPostIdAndName($post->id, 'rss_source_link');
        if ($postMeta) {
            $this->postMetaRepository->update($postMeta, ['meta_data' => $rssFeedItem->link]);
        } else {
            $this->postMetaRepository->create([
                'post_id' => $post->id,
                'name' => 'rss_source_link',
                'meta_data' => $rssFeedItem->link,
            ]);
        }

        return $post;
    }
}

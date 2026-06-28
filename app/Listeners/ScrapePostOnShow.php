<?php

namespace App\Listeners;

use IstvanMolitor\ArticleScraper\Services\ArticleToPostService;
use Molitor\ArticleParser\Services\ArticleParserService;
use Molitor\Cms\Events\Post\PostShow;
use Molitor\Cms\Repositories\PostMetaRepositoryInterface;

class ScrapePostOnShow
{
    public function __construct(
        private ArticleParserService $articleParserService,
        private ArticleToPostService $articleToPostService,
        private PostMetaRepositoryInterface $postMetaRepository,
    ) {}

    public function handle(PostShow $event): void
    {
        $post = $event->post;

        if ($this->postMetaRepository->exists($post, 'scraped_at')) {
            return;
        }

        $sourceLinkMeta = $this->postMetaRepository->getByPostIdAndName($post->id, 'source_link');

        if ($sourceLinkMeta === null) {
            return;
        }

        $url = $sourceLinkMeta->meta_data;

        if (! $this->articleParserService->isValidUrl($url)) {
            return;
        }

        $article = $this->articleParserService->getByUrl($url);

        if ($article === null) {
            return;
        }

        $this->articleToPostService->convertArticleToPost(
            article: $article,
            sourceLink: $url,
            publish: true,
        );
    }
}

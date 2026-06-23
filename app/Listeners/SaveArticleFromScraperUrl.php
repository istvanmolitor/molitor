<?php

namespace App\Listeners;

use IstvanMolitor\ArticleScraper\Services\ArticleToPostService;
use Molitor\ArticleParser\Services\ArticleParserService;
use Molitor\Scraper\Events\ScraperUrlUpdateEvent;

class SaveArticleFromScraperUrl
{
    public function __construct(
        private ArticleParserService $articleParserService,
        private ArticleToPostService $articleToPostService,
    ) {}

    public function handle(ScraperUrlUpdateEvent $event): void
    {
        $url = $event->scraperUrl->url;

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

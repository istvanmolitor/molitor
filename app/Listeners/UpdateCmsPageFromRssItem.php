<?php

namespace App\Listeners;

use App\Services\RssWatcherCmsPostService;
use Molitor\RssWatcher\Events\RssFeedItemChanged;
use Molitor\RssWatcher\Events\RssFeedItemChangedEvent;

class UpdateCmsPageFromRssItem
{
    public function __construct(
        private RssWatcherCmsPostService $rssWatcherCmsPostService
    ) {}

    public function onChanged(RssFeedItemChangedEvent|RssFeedItemChanged $event): void
    {
        if ($event instanceof RssFeedItemChangedEvent) {
            $this->rssWatcherCmsPostService->createOrUpdateFromRssItem($event->item);

            return;
        }

        $this->rssWatcherCmsPostService->createOrUpdateFromRssItem($event->rssFeedItem);
    }
}

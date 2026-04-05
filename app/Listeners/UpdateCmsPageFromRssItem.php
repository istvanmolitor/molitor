<?php

namespace App\Listeners;

use App\Services\RssWatcherCmsPageService;
use Molitor\RssWatcher\Events\RssFeedItemChanged;
use Molitor\RssWatcher\Events\RssFeedItemChangedEvent;

class UpdateCmsPageFromRssItem
{
    public function __construct(
        private RssWatcherCmsPageService $rssWatcherCmsPageService
    ) {}

    public function onChanged(RssFeedItemChangedEvent|RssFeedItemChanged $event): void
    {
        if ($event instanceof RssFeedItemChangedEvent) {
            $this->rssWatcherCmsPageService->createOrUpdateFromRssItem($event->item);

            return;
        }

        $this->rssWatcherCmsPageService->createOrUpdateFromRssItem($event->rssFeedItem);
    }
}

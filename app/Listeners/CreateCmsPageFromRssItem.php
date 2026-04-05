<?php

namespace App\Listeners;

use App\Services\RssWatcherCmsPageService;
use Molitor\RssWatcher\Events\RssFeedItemCreated;
use Molitor\RssWatcher\Events\RssFeedItemCreatedEvent;

class CreateCmsPageFromRssItem
{
    public function __construct(
        private RssWatcherCmsPageService $rssWatcherCmsPageService
    ) {}

    public function onCreated(RssFeedItemCreatedEvent|RssFeedItemCreated $event): void
    {
        $this->rssWatcherCmsPageService->createOrUpdateFromRssItem($event->item);
    }
}

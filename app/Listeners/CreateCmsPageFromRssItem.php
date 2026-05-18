<?php

namespace App\Listeners;

use App\Services\RssWatcherCmsPostService;
use Molitor\RssWatcher\Events\RssFeedItemCreated;
use Molitor\RssWatcher\Events\RssFeedItemCreatedEvent;

class CreateCmsPageFromRssItem
{
    public function __construct(
        private RssWatcherCmsPostService $rssWatcherCmsPostService
    ) {}

    public function onCreated(RssFeedItemCreatedEvent|RssFeedItemCreated $event): void
    {
        $this->rssWatcherCmsPostService->createOrUpdateFromRssItem($event->item);
    }
}

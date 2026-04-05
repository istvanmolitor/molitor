<?php

namespace App\Providers;

use App\Listeners\CreateCmsPageFromRssItem;
use App\Listeners\UpdateCmsPageFromRssItem;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Molitor\RssWatcher\Events\RssFeedItemChanged;
use Molitor\RssWatcher\Events\RssFeedItemChangedEvent;
use Molitor\RssWatcher\Events\RssFeedItemCreated;
use Molitor\RssWatcher\Events\RssFeedItemCreatedEvent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(RssFeedItemCreatedEvent::class, [CreateCmsPageFromRssItem::class, 'onCreated']);
        Event::listen(RssFeedItemChangedEvent::class, [UpdateCmsPageFromRssItem::class, 'onChanged']);

        Event::listen(RssFeedItemCreated::class, [CreateCmsPageFromRssItem::class, 'onCreated']);
        Event::listen(RssFeedItemChanged::class, [UpdateCmsPageFromRssItem::class, 'onChanged']);
    }
}

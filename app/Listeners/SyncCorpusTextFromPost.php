<?php

namespace App\Listeners;

use Molitor\Cms\Events\Post\PostCreated;
use Molitor\Cms\Events\Post\PostUpdated;
use Molitor\Cms\Models\Post;
use Molitor\Cms\Services\ContentHandler;
use Molitor\TextMining\Models\CorpusText;
use Molitor\TextMining\Services\TextMiningService;

class SyncCorpusTextFromPost
{
    public function __construct(
        private TextMiningService $textMiningService,
        private ContentHandler $contentHandler
    ) {}

    public function onCreated(PostCreated $event): void
    {
        //$this->syncFromPost($event->post);
    }

    public function onUpdated(PostUpdated $event): void
    {
        //$this->syncFromPost($event->post);
    }

    private function syncFromPost(Post $post): void
    {
        $post->loadMissing('content.contentElements.contentElementType');

        $name = 'cms-post-'.$post->id;
        $text = $this->buildCorpusText($post);

        $corpusText = CorpusText::query()->firstOrNew(['name' => $name]);
        $corpusText->text = $text;
        $corpusText->save();

        $this->textMiningService->updateKeywords($corpusText);
    }

    private function buildCorpusText(Post $post): string
    {
        $parts = [$post->title, $post->lead];

        if ($post->content) {
            foreach ($post->content->contentElements as $contentElement) {
                $settings = $this->contentHandler->getSettings($contentElement);
                $parts = [...$parts, ...$this->extractTextValues($settings)];
            }
        }

        $parts = array_filter(array_map(function (?string $part): string {
            $value = is_string($part) ? strip_tags(html_entity_decode($part, ENT_QUOTES | ENT_HTML5, 'UTF-8')) : '';

            return trim(preg_replace('/\s+/u', ' ', $value) ?? '');
        }, $parts));

        return implode("\n", $parts);
    }

    private function extractTextValues(mixed $value): array
    {
        if (is_string($value)) {
            return [$value];
        }

        if (is_array($value)) {
            $results = [];
            foreach ($value as $item) {
                $results = [...$results, ...$this->extractTextValues($item)];
            }

            return $results;
        }

        return [];
    }
}

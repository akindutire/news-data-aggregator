<?php
namespace App\Services\Concretes\Core;

use App\Models\News;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Log;

class NewsAggregatorService implements \App\Services\Contracts\AggregatorInterface
{

    public function aggregate(): void {
        $sources = config('innoscripta.supported_news_sources', []);

        $closures = array_map(function($source) {
            return function() use ($source) {
                $newsSource = (new \App\Services\Factories\NewsSourceFactory())->make($source);
                if ($newsSource) {
                    return $newsSource->orchestrate();
                }
            };
        }, $sources);

        Log::info("Aggregating news from all sources concurrently @ ".now()->toString());
        dump("Aggregating news from all sources concurrently @ ".now()->toString());
        $allNewsSourceResults = Concurrency::run(
            [
                ...$closures
            ]
        );

        $allNewsSourceResults = array_merge(...$allNewsSourceResults);

        Log::info("Aggregated news from all sources @ ".now()->toString(), ['total_sources' => count($sources)]);
        dump("Aggregated news from all sources  @ ".now()->toString());

        //result is type array of NewsVO
        foreach ($allNewsSourceResults as $result) {
            //Save to news model
            if ( !($result) instanceof \App\Models\ValueObject\NewsVO ) {
                continue;
            }

            News::updateOrCreate([
                'article_remote_key' => $result->remoteId,
            ], [
                'title' => $result->title,
                'url' => $result->url,
                'category' => $result->category,
                'published_at' => $result->publishedAt,
                'source' => $result->source,
                'author' => $result->author,
                'content' => $result->content,
                'description' => $result->description,
                'image_url' => $result->imageUrl,
                'article_remote_key' => $result->remoteId,
                'article_remote_source' => $result->remoteSource,
            ]);
        }
    }
}

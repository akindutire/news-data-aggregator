<?php
namespace App\Services\Concretes\Core;

use App\Models\News;
use App\Services\Contracts\FetchOpts;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Log;

class NewsAggregatorService implements \App\Services\Contracts\AggregatorInterface
{

    public function aggregate(array $sources = []): void {

        if(count($sources) == 0)
            $sources = config('innoscripta.supported_news_sources', []);
        else
            $sources = array_intersect($sources, config('innoscripta.supported_news_sources', []));

        //Compose closures for each source
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

        // Run closure concurrently
        $allNewsSourceResults = Concurrency::run(
            [
                ...$closures
            ]
        );

        // Merge all sources
        $allNewsSourceResults = array_merge(...$allNewsSourceResults);

        Log::info("Aggregated news from all sources @ ".now()->toString(), ['total_sources' => count($sources), 'total_news_fetched' => count($allNewsSourceResults) ]);
        dump("Aggregated news from all sources  @ ".now()->toString()."/".implode(", ", ['total_sources=' .count($sources), 'total_news_fetched='. count($allNewsSourceResults) ]));

        //result is type array of NewsVO
        foreach ($allNewsSourceResults as $result) {

            //Save to news model
            if ( !($result instanceof \App\Models\ValueObject\NewsVO ) ) {
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

    /** Central logic to fetch from news
     * @return Illuminate\Database\Eloquent\Collection<int,App\Models\News>|Illuminate\Pagination\LengthAwarePaginator
    */
    public function fetch(?FetchOpts $opts) {

        $baseQuery = News::query();

        $baseQuery->when(is_array($opts->fields) && count($opts->fields) > 0, function($q) use ($opts) {
            return $q->select($opts->fields);
        });

        $baseQuery->when( !empty($opts->source), function($q) use ($opts) {
            return $q->where('source', $opts->source);
        });

         $baseQuery->when( is_array($opts->source) && count($opts->source) > 0, function($q) use ($opts) {
            return $q->whereIn('source', $opts->source);
        });

        $baseQuery->when( !empty($opts->title), function($q) use ($opts) {
            return $q->where('title', 'LIKE', '%'.$opts->title.'%')->orWhere('content', 'LIKE', '%'.$opts->title.'%');
        });

        $baseQuery->when( is_string($opts->category) && !empty($opts->category), function($q) use ($opts) {
            return $q->where('category','LIKE', '%'.$opts->category.'%');
        });

        $baseQuery->when( is_array($opts->category) && count($opts->category) > 0, function($q) use ($opts) {
            return $q->whereIn('category', $opts->category);
        });

        $baseQuery->when( !empty($opts->fromDate), function($q) use ($opts) {
            return $q->where('published_at', '>=', $opts->fromDate);
        });

        $baseQuery->when( !empty($opts->toDate), function($q) use ($opts) {
            return $q->where('published_at', '<=', $opts->toDate);
        });

        $baseQuery->when( is_array($opts->author) && count($opts->author) > 0, function($q) use ($opts) {
            $authors = $opts->author;
            return $q->where(function($iq) use ($authors) {
                foreach($authors as $author)  {
                    $iq->orWhere('author', 'LIKE', '%'.$author.'%');
                }
            });

        });

        $baseQuery->when( $opts->distinct, function($q) {
            return $q->distinct();
        });

        $baseQuery->when( !empty($opts->orderBy), function($q) use ($opts) {
            return $q->orderBy($opts->orderBy, $opts->orderByDir);
        });

        // Log the evaluated query string
        // Log::debug('Query SQL: ' . $baseQuery->toSql());
        // Log::debug('Query Bindings: ' . json_encode($baseQuery->getBindings()));

        if ($opts->shouldPaginate) {
            return $baseQuery->paginate($opts->perPage??25, page: $opts->page);
        } else {
            return $baseQuery->get();
        }
    }

}

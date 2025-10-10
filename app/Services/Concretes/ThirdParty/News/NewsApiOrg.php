<?php
namespace App\Services\Concretes\ThirdParty\News;

use App\Models\ValueObject\NewsVO;
use App\PossibleNewsSource;
use App\Services\Contracts\OrchestrateProps;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

//api key = 2395a2308cc94540bac470245d498b58

class NewsApiOrg extends \App\Services\Abstracts\AbstractNewsSource {

    private string $baseUrl = "https://newsapi.org/v2";
    private $source = PossibleNewsSource::NEWSAPIORG->value;

    public function getSource(): string
    {
        return $this->source;
    }

    public function featuresToExtract(): array {
        return ['title', 'content', 'author', 'publishedAt', 'url', 'source', 'description'];
    }

    public function fetchByPage($page, $fetchSize) {
        return Http::get($this->baseUrl . "/everything", [
            'domains' => implode(",", ['gizmodo.com','bbc.co.uk','techcrunch.com','thenextweb.com']),
            'from' => now()->subDay()->toDateString(),
            'apiKey' => env('NEWSAPIORG_API_KEY'),
            'pageSize' => $fetchSize,
            'page' => $page,
        ]);
    }

    /**
     * @return NewsVO[]
     */
    public function orchestrate($property=null) : array {

        if (is_null($property) ) {
            $property = new OrchestrateProps;
            $property->pageSize = 50;
        }

        // Pull data
        $dataSet = [];

        // Check orchestration state, get the last page fetched and continue from
        // This step would keep news up to date without extreme overfetching
        $orchState = $this->orchestrationState();
        $page = $orchState?->last_fetched_page??1;

        do{
            $response = $this->fetchByPage($page, $property?->pageSize??100);
            if ($response->json('status') == "error") {
                Log::error("Error fetching news: ".$response->json('message',  'An error occured'));
                break;
            }
            if ($response->ok()) {
                $dataSet = [...$dataSet, ...$response->json('articles', [])];
            } else {
                Log::error("Error fetching news02: ".$response->json('message',  'An error occured'));
                break;
            }

            $totalPages = ceil($response->json('totalResults', 1)/($property?->pageSize??100));
            $page++;

            // Stop news fetch when limit reached or exceeded
            if($property?->maxItems??0 > 0 && count($dataSet) >= $property?->maxItems??0) {
                break;
            }
        } while ($page < $totalPages);

        // Create or Update state
        if(count($dataSet) > 0) {
            $this->updateOrchestrationState($page);
        } else {
            return [];
        }

        // filter/clean data
        $dataSet = $this->extractFeatures($dataSet);


        // adapt cleaned data to system features (news value object)
        return array_map(
            function($item) {
                return (new NewsVO())
                    ->setTitle($item['title'])
                    ->setPublishedAt(new \DateTime($item['publishedAt']))
                    ->setUrl($item['url'])
                    ->setSource($this->getSource())
                    ->setRemoteSource($item['source']['id']??$item['source']['name']??'')
                    ->setRemoteId($item['url'])
                    ->setCategory(null)
                    ->setAuthor($item['author'])
                    ->setContent($item['content'])
                    ->setDescription($item['description'])
                    ->setImageUrl('');
            },
            $dataSet
        );
    }

}

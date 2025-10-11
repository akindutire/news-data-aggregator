<?php
namespace App\Services\Concretes\ThirdParty\News;

use App\Models\ValueObject\NewsVO;
use App\PossibleNewsSource;
use App\Services\Contracts\OrchestrateProps;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class NewYorkTimes extends \App\Services\Abstracts\AbstractNewsSource
{
    private string $baseUrl = "https://api.nytimes.com/svc/news/v3/content/all";
    private $source = PossibleNewsSource::NEWYORKTIMES->value;

    public function getSource(): string
    {
        return $this->source;
    }

    public function featuresToExtract(): array {
        return ['title', 'byline', 'slug_name', 'published_date', 'url', 'source', 'abstract', "subsection", "section", "des_facet"];
    }

    public function fetchByPage($page, $fetchSize) {
        return Http::get($this->baseUrl . "/all.json", [
            'source' => 'nyt',
            'api-key' => env('NEWYORKTIMES_API_KEY'),
            'limit' => $fetchSize,
            'offset' => ($page-1)*$fetchSize,
        ]);
    }

    /**
     * @return NewsVO[]
     */
    public function orchestrate($property=null) : array {

        if (is_null($property) ) {
            $property = new OrchestrateProps;
            $property->pageSize = 100;
        }

        // Pull data
        $dataSet = [];

        // Check orchestration state, get the last page fetched and continue from
        // This step would keep news up to date without extreme overfetching
        $orchState = $this->orchestrationState();
        $page = $orchState?->last_fetched_page??1;

        do{
            try{
                $response = $this->fetchByPage($page, $property?->pageSize??100);

                if ($response->ok()) {
                    $dataSet = [...$dataSet, ...$response->json('results', [])];
                } else {
                    Log::error("Error fetching news02: ".$response->json('message',  'An error occured'));
                    break;
                }
            } catch (Throwable $t) {
                Log::error($t);
                break;
            }

            $totalPages = 5;
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
                    ->setPublishedAt(new \DateTime($item['published_date']))
                    ->setUrl($item['url'])
                    ->setSource($this->getSource())
                    ->setRemoteSource($item['source'])
                    ->setRemoteId($item['slug_name'])
                    ->setCategory($item['section'])
                    ->setAuthor($item['byline'])
                    ->setContent("")
                    ->setDescription($item['abstract'])
                    ->setImageUrl('');
            },
            $dataSet
        );
    }

}

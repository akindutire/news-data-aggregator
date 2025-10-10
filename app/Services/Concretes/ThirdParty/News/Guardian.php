<?php
namespace App\Services\Concretes\ThirdParty\News;

use App\Models\News;
use App\Models\ValueObject\NewsVO;
use App\PossibleNewsSource;
use App\Services\Contracts\OrchestrateProps;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Guardian extends \App\Services\Abstracts\AbstractNewsSource {

    private string $baseUrl = "https://content.guardianapis.com";
    private $source = PossibleNewsSource::GUARDIAN->value;


    protected function getSource(): string {
        return $this->source;
    }

    protected function featuresToExtract(): array {
        return ['webTitle', 'webPublicationDate', 'webUrl', 'sectionName', 'id'];
    }

    public function fetchByPage($page, $fetchSize) {
        return Http::get($this->baseUrl . "/search", [
            'from-date' => now()->subDay()->toDateString(),
            'api-key' => env('GUARDIAN_API_KEY'),
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

            if ($response->ok()){
                $dataSet = [...$dataSet, ...$response->json('response.results', [])];
            } else {
                Log::error("Error fetching news02: ".$response->json('message', 'An error occured'));
                break;
            }

            $totalPages = $response->json('response.pages', 1);
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
                    ->setTitle($item['webTitle'])
                    ->setPublishedAt(new \DateTime($item['webPublicationDate']))
                    ->setUrl($item['webUrl'])
                    ->setSource($this->getSource())
                    ->setRemoteSource("")
                    ->setRemoteId($item['id'])
                    ->setCategory($item['sectionName'])
                    ->setAuthor('Unknown')
                    ->setContent('')
                    ->setDescription('')
                    ->setImageUrl('');
            },
            $dataSet
        );
    }

}

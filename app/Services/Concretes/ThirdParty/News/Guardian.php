<?php
namespace App\Services\Concretes\ThirdParty\News;

use App\Models\News;
use App\Models\ValueObject\NewsVO;
use App\PossibleNewsSource;
use App\Services\Contracts\OrchestrateProps;
use Illuminate\Support\Facades\Http;

class Guardian extends \App\Services\Abstracts\AbstractNewsSource {

    private string $baseUrl = "https://content.guardianapis.com";
    private $source = PossibleNewsSource::GUARDIAN->value;
    private int $pageSize = 50;


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
        // Pull data
        $dataSet = [];

        // Check orchestration state, get the last page fetched and continue from
        // This step would keep news up to date without extreme overfetching
        $orchState = $this->orchestrationState();
        $page = $orchState?->last_fetched_page??1;

        do{
            $response = $this->fetchByPage($page, $property?->pageSize??$this->pageSize);

            if ($response->ok()){
                $dataSet = [...$dataSet, ...$response->json('response.results', [])];
            }

            $totalPages = $response->json('response.pages', 1);
            $page++;

            if($property?->maxItems??0 > 0 && count($dataSet) >= $property?->maxItems??0) {
                break;
            }
        } while ($page < $totalPages);

        // Create or Update state
        $this->updateOrchestrationState($page);

        // filter/clean data
        $dataSet = $this->extractFeatures($dataSet);

        // adapt cleaned data to system features (news value object)
        return array_map(
            function($item) {
                return (new NewsVO())
                    ->setTitle($item['webTitle'])
                    ->setPublishedAt(new \DateTime($item['webPublicationDate']))
                    ->setUrl($item['webUrl'])
                    ->setSource($this->source)
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

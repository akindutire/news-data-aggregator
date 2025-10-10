<?php

namespace Tests\Unit;

use App\PossibleNewsSource;
use App\Services\Contracts\OrchestrateProps;
use App\Services\Factories\NewsSourceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class Guardian extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    public function test_guardian_news_can_fetch_data(): void
    {
        $this->assertContains(PossibleNewsSource::GUARDIAN->value, config('innoscripta.supported_news_sources'));

        $source = (new NewsSourceFactory())->make(PossibleNewsSource::GUARDIAN->value);
        $response = $source->fetchByPage(1,10);
        $data = $response->json('response.results', []);

        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }

    public function test_guardian_news_data_can_adapt_to_its_features(): void
    {
        $this->assertContains(PossibleNewsSource::GUARDIAN->value, config('innoscripta.supported_news_sources'));

        $source = (new NewsSourceFactory())->make(PossibleNewsSource::GUARDIAN->value);
        $response = $source->fetchByPage(1,10);
        $data = $response->json('response.results', []);

        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        $mappedData = $source->extractFeatures($data);

        $this->assertIsArray($mappedData);
        $this->assertNotEmpty($mappedData);

        //test 1 of the items has the right keys
        $item = $mappedData[0];
        $features = $source->getFeaturesToExtract();
        foreach($features as $feature) {
            $this->assertArrayHasKey($feature, $item);
        }
    }

    public function test_guardian_news_data_can_orchestrate_news_fetch(): void
    {
        $this->assertContains(PossibleNewsSource::GUARDIAN->value, config('innoscripta.supported_news_sources'));

        $source = (new NewsSourceFactory())->make(PossibleNewsSource::GUARDIAN->value);

        $props = new OrchestrateProps;
        $props->maxItems = 5;
        $props->pageSize = 5;
        $adaptedData = $source->orchestrate($props);

        $this->assertIsArray($adaptedData);
        $this->assertNotEmpty($adaptedData);

        //test 1 of the items has the right keys
        $item = $adaptedData[0];
        $this->assertInstanceOf(\App\Models\ValueObject\NewsVO::class, $item);

    }

}

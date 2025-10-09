<?php

namespace Tests\Unit;

use Tests\TestCase;

class SupportAggregatorUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_supported_aggregator_sources_is_array(): void
    {
        $this->assertIsArray(config('innoscripta.supported_aggregator_sources'));
        $this->assertNotEmpty(config('innoscripta.supported_aggregator_sources'));
    }
}

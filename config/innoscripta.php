<?php
// Entails the configuration settings for the Innoscripta Data Aggregator project.

use App\PossibleAggregatorSource;

return [
    'app_name' => 'Innoscripta Data Aggregator',
    'version' => '1.0.0',
    'supported_aggregator_sources' => array_filter( PossibleAggregatorSource::cases(), fn($source) => $source->isSupported()),
];

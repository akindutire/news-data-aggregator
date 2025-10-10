<?php
// Entails the configuration settings for the Innoscripta Data Aggregator project.

use App\PossibleNewsSource;

return [
    'app_name' => 'Innoscripta Data Aggregator',
    'version' => '1.0.0',
    'supported_news_sources' => array_map(fn($source) => $source->value,  array_filter( PossibleNewsSource::cases(), fn($source) => $source->isSupported())),
];

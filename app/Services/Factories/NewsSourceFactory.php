<?php
namespace App\Services\Factories;

use App\PossibleNewsSource;
use Illuminate\Support\Facades\App;

class NewsSourceFactory
{
    public static function make(string $sourceType): ?\App\Services\Contracts\NewsSourceInterface
    {
        if (!in_array($sourceType, config('innoscripta.supported_news_sources'))) {
            throw new \InvalidArgumentException("{$sourceType} is not a supported news source at the moment.");
        }
        switch (strtolower($sourceType)) {
            case PossibleNewsSource::NEWYORKTIMES->value:
                return App::make(\App\Services\Concretes\ThirdParty\News\NewYorkTimes::class);
            case PossibleNewsSource::GUARDIAN->value:
                return App::make(\App\Services\Concretes\ThirdParty\News\Guardian::class);
            case PossibleNewsSource::NEWSAPIORG->value:
                return App::make(\App\Services\Concretes\ThirdParty\News\NewsApiOrg::class);
            default:
                throw new \InvalidArgumentException("Internal error: Missing module for source: {$sourceType}, contact support.");
        }
    }
}

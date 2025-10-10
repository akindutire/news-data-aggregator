<?php

namespace App;

enum PossibleNewsSource: string
{
    case NEWSAPI = 'newsapi';
    case GUARDIAN = 'guardian';
    case NEWYORKTIMES = 'newyorktimes';
    case NEWSCRED = 'newscred';
    case OPENNEWS = 'opennews';
    case BBCNEWS = 'bbcnews';
    case NEWSAPIORG = 'newsapiorg';

    public function isSupported(): bool {
        return match($this) {
            self::NEWSAPIORG, self::GUARDIAN => true,
            default => false,
        };
    }
}

<?php
namespace App\Services\Contracts;

interface AggregatorInterface {
    public function aggregate(array $sources=[]);
    public function fetch(?FetchOpts $opts);
}

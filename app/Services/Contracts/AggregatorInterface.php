<?php
namespace App\Services\Contracts;

use stdClass;

interface AggregatorInterface {
    public function aggregate(array $sources=[]);
    public function fetch(?FetchOpts $opts);
}

class FetchOpts extends stdClass {
    public string $source = '';
    public string|array $category = '';
    public bool $distinct = false;
    public array $fields = [];
    public array $orderBy = [];
    public string $orderByDir = "ASC";
    public string $title = '';
    public string $fromDate = '';
    public string $toDate = '';
    public string|array $author = '';
    public bool $shouldPaginate = true;
    public int $perPage = 25;
    public int $page = 1;

}

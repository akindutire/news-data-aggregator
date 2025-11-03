<?php
namespace App\Services\Contracts;

class FetchOpts extends \stdClass {
    public string|array $source = '';
    public string|array $category = '';
    public bool $distinct = false;
    public array $fields = [];
    public string $orderBy = '';
    public string $orderByDir = "ASC";
    public string $title = '';
    public \DateTime $fromDate;
    public \DateTime $toDate;
    public string|array $author = '';
    public bool $shouldPaginate = true;
    public int $perPage = 25;
    public int $page = 1;

    function __set(string $prop, $value) {
        if ($prop == 'orderByDir') {
            if (!in_array($value, ["ASC", "DESC"])) {
                $value = "ASC";
            }
        }

        $this->$prop = $value;
    }
}

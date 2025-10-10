<?php
namespace App\Services\Contracts;

use stdClass;

interface NewsSourceInterface
{
    public function getFeaturesToExtract(): array;
    public function fetchByPage(int $page, int $size);
    public function extractFeatures(array $dataSet): array;
    public function orchestrate(?OchestrateProps $prop): array;
}

class OchestrateProps extends stdClass {
    public int $pageSize = 50;
    public int $maxItems = 0;
}

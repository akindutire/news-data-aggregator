<?php
namespace App\Services\Contracts;

interface NewsSourceInterface
{
    public function getFeaturesToExtract(): array;
    public function fetchByPage(int $page, int $size);
    public function extractFeatures(array $dataSet): array;
    public function orchestrate(?OrchestrateProps $prop=null): array;
}

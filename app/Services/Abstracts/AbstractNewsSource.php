<?php
namespace App\Services\Abstracts;

use App\Services\Contracts\OchestrateProps;

abstract class AbstractNewsSource implements \App\Services\Contracts\NewsSourceInterface
{
    abstract public function fetchByPage(int $page, int $size);
    abstract public function orchestrate(?OchestrateProps $prop): array;

    //Reuseable methods
    protected function featuresToExtract() : array {
        return [];
    }

    public function getFeaturesToExtract() : array {
        return $this->featuresToExtract();
    }

    public function extractFeatures(array $dataSet) : array {
        foreach($dataSet as &$data) {
            $data = array_intersect_key($data, array_flip($this->featuresToExtract()));
        }
        return $dataSet;
    }
}

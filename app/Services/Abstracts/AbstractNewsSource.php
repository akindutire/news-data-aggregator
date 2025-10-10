<?php
namespace App\Services\Abstracts;

use App\Models\NewSourceOrchestrationHistory;
use App\Models\NewSourceOrchestrationState;
use App\Services\Contracts\OrchestrateProps;

abstract class AbstractNewsSource implements \App\Services\Contracts\NewsSourceInterface
{

    abstract public function fetchByPage(int $page, int $size);
    abstract public function orchestrate(?OrchestrateProps $prop=null): array;
    abstract protected function getSource(): string;
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

    protected function orchestrationState() : ?NewSourceOrchestrationState {
        return NewSourceOrchestrationState::where('source', $this->getSource())->where('last_fetched_at', now()->toDateString())->first();
    }

    protected function updateOrchestrationState(int $lastPageFetched) : void {
        $state = $this->orchestrationState();
        if($state) {
            $state->last_fetched_page = $lastPageFetched;
            $state->last_fetched_at = now()->toDateString();
            $state->save();
        } else {
            NewSourceOrchestrationState::create([
                'source' => $this->getSource(),
                'last_fetched_page' => $lastPageFetched,
                'last_fetched_at' => now()->toDateString(),
            ]);
        }
    }

}

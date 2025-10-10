<?php

namespace App\Console\Commands;

use App\Services\Concretes\Core\NewsAggregatorService;
use App\Services\Contracts\AggregatorInterface;
use Illuminate\Console\Command;

class AggregatorCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aggregate:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate News from different sources';

    /**
     * Execute the console command.
     */
    public function handle(AggregatorInterface $newsAggregatorService)
    {
        $newsAggregatorService->aggregate();

    }
}

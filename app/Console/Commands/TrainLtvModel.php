<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SynapCores\SynapCoresMLService;

class TrainLtvModel extends Command
{
    protected $signature = 'synapcores:train';
    protected $description = 'Initializes and runs the SynapCores AutoML regression pipeline.';

    public function handle(SynapCoresMLService $mlService)
    {
        $this->info('Initiating model training on SynapCores engine...');
        $mlService->initializeAndTrainModel();
        $this->info('Model successfully trained.');
    }
}

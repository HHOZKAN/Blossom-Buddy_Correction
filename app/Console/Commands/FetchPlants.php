<?php

namespace App\Console\Commands;

use App\Interfaces\PlantServiceInterface;
use Illuminate\Console\Command;

class FetchPlants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-plants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the first 30 plants from the API and store them in the database';


    protected PlantServiceInterface $plantService;

    public function __construct(PlantServiceInterface $plantService)
    {
        parent::__construct();

        $this->plantService = $plantService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching plants...');
        $this->plantService->fetchAndStorePlants();
        $this->info('Plants fetched and stored successfully.');
    }
}

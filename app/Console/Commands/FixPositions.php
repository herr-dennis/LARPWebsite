<?php

namespace App\Console\Commands;

use App\Models\DonnerfelsStory;
use App\Models\EcklandStory;
use App\Models\TalagradStory;
use Illuminate\Console\Command;

class FixPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'positions:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $countTala = TalagradStory::query()
            ->where('position',">=",0)
            ->update(['position' =>  TalagradStory::query()
                ->raw('id')]);


        $countDonnerfels = DonnerfelsStory::query()
            ->where('position',">=",0)
            ->update(['position' =>  DonnerfelsStory::query()
                ->raw('id')]);


        $countEck = EcklandStory::query()
            ->where('position',">=",0)
            ->update(['position' =>  EcklandStory::query()
                ->raw('id')]);


        $this->info("  $countTala Einträge repariert/ Talagrad");
        $this->info("  $countDonnerfels Einträge repariert/ Donnerfels");
        $this->info(" $countEck Einträge repariert/Eckland");
    }
}

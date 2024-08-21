<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainBrand;
use App\Models\Account;
use App\Http\Controllers\MainBrandController;
use Illuminate\Support\Facades\Log;

class WeeklyFetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Captura os dados de redes sociais semanalmente e gera um relatório.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $controller = new MainBrandController();
        $accounts = Account::all();
        foreach( $accounts as $account ) {
            if(!$account->active)
                continue;

            $brands = $account->mainBrand()->get();
            foreach( $brands as $brand ) {
                //$sRequest = 
                $result = $controller->buildDelta($brand->id);

                if($result->getStatusCode() == 200)
                    Log::info('Sucesso no semanal de '.$brand->name.' de ID '.$brand->id);
                else
                    Log::info('Falha no semanal de '.$brand->name.' de ID '.$brand->id.' '.$result->json());
            }
        }

        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Purchase;
use App\Jobs\SubscriptionWorker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


class subscriptionChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptionChecker:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Abonelik hizmeti kontrolü sağlar';

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


        /* Expire Date sonlanmış ise ve subscription started veya renewed ise kullanıcıyı mock data kontrolü + callback */
        $purchase = Purchase::where('expire_date','<=',Carbon::now())->where('status','success')->get();


        foreach($purchase as $row){

            Log::info("subscriptionChecker ".$row->id);
            SubscriptionWorker::dispatch($row->id);
        }

        return 0;
    }
}

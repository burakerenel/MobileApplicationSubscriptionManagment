<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RenewedEventListener
{


    public function __construct()
    {
    }

    public function handle($event)
    {

        try{
            Http::timeout(30)->post($event->callBack, [
                'appId' => $event->appId,
                'uid' => $event->uid,
                'event' => 'renewed'
            ]);
            Log::info("callBack posted!");
        }
        catch(\Exception $e){
            Log::info($e);
        }

    }
}

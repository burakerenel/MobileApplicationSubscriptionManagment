<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\StartedEvent;
use App\Events\RenewedEvent;
use App\Events\CanceledEvent;
use App\Models\App;

class CallBackWorker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $appId, $uid, $event;

    public function __construct($appId,$uid,$event)
    {
        $this->appId = $appId;
        $this->uid = $uid;
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $app = App::where('id',$this->appId)->first();

        if($this->event == 'started')
            event(new StartedEvent($this->appId,$this->uid,$app->callBack));
        elseif($this->event == 'renewed')
            event(new RenewedEvent($this->appId,$this->uid,$app->callBack));
        elseif($this->event == 'canceled')
            event(new CanceledEvent($this->appId,$this->uid,$app->callBack));

    }
}

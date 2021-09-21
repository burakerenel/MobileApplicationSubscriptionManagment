<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Purchase;
use App\Models\Device;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;


class SubscriptionWorker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;

    public function __construct($id){
        $this->id = $id;
    }

    public function handle()
    {


        $purchase = Purchase::where('id',$this->id)->first();

        $mockData = new \App\Http\Controllers\MockDataController;

        $device = Device::where('uid',$purchase->uid)->where('appId',$purchase->appId)->first();


        $lastChar = substr($purchase->receiptId, -2);
        if (is_numeric($lastChar) && $lastChar % 6 == 0) {
            try{
                if($device->os == 'android')
                    $mockResponse = $mockData->mockGoogle($purchase->receiptId);
                elseif($device->os == 'ios')
                    $mockResponse = $mockData->mockIos($purchase->receiptId);
            }
            catch(\Exception $e){
                Log::info($purchase->receiptId. ' rate-limit error!');
                $this->release(300);
            }
        }
        else{
            if($device->os == 'android')
                $mockResponse = $mockData->mockGoogle($purchase->receiptId);
            elseif($device->os == 'ios')
                $mockResponse = $mockData->mockIos($purchase->receiptId);
        }


        if($mockResponse['status']=='success'){
            $subscription = 'renewed';
            $expire_date = $mockResponse['expire_date'];
            Purchase::where('uid',$device->uid)->where('appId',$device->appId)->where('receiptId',$purchase->receiptId)->update([
                'status' => 'success',
                'expire_date' => $expire_date,
                'subscription' => $subscription
                ]);
            }
        else{
            $subscription = 'canceled';
            $expire_date = Carbon::now()->format('Y-m-d H:i:s');
            Purchase::where('uid',$device->uid)->where('appId',$device->appId)->where('receiptId',$purchase->receiptId)->update([
                'status' => 'error',
                'expire_date' =>$expire_date,
                'subscription' =>  $subscription
            ]);
        }

        CallBackWorker::dispatch($device->appId,$device->uid,$subscription);


        Log::info("SubscriptionWorker runned!");

    }
}

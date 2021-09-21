<?php

namespace App\Http\Controllers;

use App\Jobs\CallBackWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Device;
use App\Models\Purchase;
use Carbon\Carbon;




class PurchaseController extends Controller
{
    public function purchase($accessToken,$receiptId){


        $device = Device::where('accessToken',$accessToken)->first();

        if(!$device)
            return response()->json(array('status'=>false,'message'=>'Device Error!'),401);


        /* Mock Data */

        $mockData = new \App\Http\Controllers\MockDataController;

        /* todo: MockData için servis yazılacak */

        if($device->os == 'android')
            $mockResponse = $mockData->mockGoogle($receiptId);
        elseif($device->os == 'ios')
            $mockResponse = $mockData->mockIos($receiptId);




        $purchase = Purchase::where('uid',$device->uid)->where('appId',$device->appId)->first();

        if(!$purchase && $mockResponse['status']=='success'){

            $subscription = 'started';
            $expire_date = $mockResponse['expire_date'];
            $purchase = new Purchase;
            $purchase->status = 'success';
            $purchase->uid = $device->uid;
            $purchase->appId = $device->appId;
            $purchase->receiptId = $receiptId;
            $purchase->expire_date = $expire_date;
            $device->subscription = $subscription;
            $purchase->save();
        }
        elseif($purchase && $mockResponse['status']=='success'){
            $subscription = 'renewed';
            $expire_date = $mockResponse['expire_date'];

            Purchase::where('uid',$device->uid)->where('appId',$device->appId)->update([
                'status' => 'success',
                'expire_date' => $expire_date,
                'receiptId' => $receiptId,
                'subscription' => $subscription
            ]);
        }
        else{
            $subscription = 'canceled';
            $expire_date = Carbon::now()->format('Y-m-d H:i:s');


            Purchase::where('uid',$device->uid)->where('appId',$device->appId)->update([
                'status' => 'error',
                'expire_date' => $expire_date,
                'receiptId' => $receiptId,
                'subscription' => $subscription
            ]);
        }

        CallBackWorker::dispatch($device->appId,$device->uid,$subscription);

        /* Satın alma işleminin durumu kullanıcıya döndürülüyor. */

        return response()->json(array('status'=>$mockResponse['status']?'success':'error','expire_date'=>$expire_date),200);




    }
}

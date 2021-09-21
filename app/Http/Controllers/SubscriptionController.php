<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Purchase;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function check(Request $request){
        $device = Device::where('accessToken',$request->accessToken)->first();
        if(!$device)
            return response()->json(array('status'=>false,'message'=>'Device Error!'),401);


        $purchase = Purchase::where('uid',$device->uid)->where('appId',$device->appId)->first();
        if($purchase){
            return response()->json(array('status'=>$purchase->status,'expire_date'=>$purchase->expire_date),200);
        }

        return response()->json(array('status'=>'error','expire_date'=>Carbon::now()->format('Y-m-d H:i:s')),200);



    }
}

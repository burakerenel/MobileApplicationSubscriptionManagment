<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class MockDataController extends Controller
{
    public function mockGoogle($receiptId){

        /* Abonelik için örnek olarak 30 gün eklenmiştir. */

        $mockExpireDay = Carbon::now()->addDays(30)->format("Y-m-d H:i:s");

        if (is_numeric(substr($receiptId,-1)) && substr($receiptId,-1) % 2==1){
            return array('status'=>true,'expire_date'=>$mockExpireDay,'message'=>'OK');
        }
        else{
            return array('status'=>false,'message'=>'OK');

        }

    }

    public function mockIos($receiptId){

        /* Abonelik için örnek olarak 30 gün eklenmiştir. */

        $mockExpireDay = Carbon::now()->addDays(30)->format("Y-m-d H:i:s");

        if (is_numeric(substr($receiptId,-1)) && substr($receiptId,-1) % 2==1){
            return array('status'=>true,'expire_date'=>$mockExpireDay,'message'=>'OK');
        }
        else{
            return array('status'=>false,'message'=>'OK');

        }

    }
}

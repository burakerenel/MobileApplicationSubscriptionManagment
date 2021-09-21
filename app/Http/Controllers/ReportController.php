<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;

class ReportController extends Controller
{

    public function report(Request $request){

        $report = Purchase::select('purchases.*', 'devices.os', 'devices.uid')->join('devices', 'devices.uid', '=', 'purchases.uid');

        if($request->start_date){
            $report->where('devices.created_at','>=',$request->start_date);
        }
        if($request->end_date){
            $report->where('devices.updated_at','<=',$request->end_date);
        }
        if($request->appId){
            $report->where('devices.appId',$request->appId);
        }
        if($request->os){
            $report->where('devices.OS',$request->os);
        }
        if($request->subscription){
            $report->where('subscription',$request->subscription);
        }


        return $report->get()->count();
    }
}

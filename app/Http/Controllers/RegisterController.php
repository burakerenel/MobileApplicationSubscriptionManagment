<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;


use App\Models\Device;

class RegisterController extends Controller
{
    public function register(Request $request){
        /*
            Gelen istek için uid,appId,language,os değerlerinin null olmadığını kontrol ediyoruz.
        */
        $validator = Validator::make($request->all(), [
            'uid' => 'required',
            'appId' => 'required',
            'language' => 'required',
            'os' => 'required|in:android,ios',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $message) {
                return response()->json(array('status'=>false,'message'=>$message));
                break;
            }
        }
        $checkDeviceApp = Device::where('uid',$request->uid)->where('appId',$request->appId)->first();

        /* accessToken mükerrer kayıt olmaması için aşağıdaki şekilde kriptolandı.*/
        $accessToken = Crypt::encryptString($request->uid.$request->appid.time());
        if($checkDeviceApp){
            /* Eğer uid ve appId daha önce kayıt olmuşsa eski token güncelleyip, yeni accessToken geri dönüyoruz.*/
            Device::where('uid',$request->uid)->where('appId',$request->appId)->update(['accessToken'=>$accessToken]);
        }
        else{
            /* Eğer uid ve appId daha önce kayıt olmamışsa kayıt edip, accessToken dönüyoruz.*/
            $device = new Device;
            $device->uid = $request->uid;
            $device->appId = $request->appId;
            $device->language = $request->language;
            $device->os = $request->os;
            $device->accessToken = $accessToken;
            $device->save();
        }
        return response()->json(array('status'=>true,'accessToken'=>$accessToken,'message'=>'Register OK'),200);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function sms_sender(Request $request)
    {
        $curl = curl_init();
        $sms = [['phone' => $request->input('phone'), 'text' => $request->input('text')]];
        $data = 'login=' . urlencode('travelcars');
        $data .= '&password=' . urlencode('zCOHfWVGiz5hL4NRovM2XY');
        $data .= '&data=' . urlencode(json_encode($sms));
        curl_setopt($curl, CURLOPT_URL, 'http://185.8.212.184/smsgateway/');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Opera 10.00');
        $res = curl_exec($curl);
        echo $res;
        curl_close($curl);
    }
}

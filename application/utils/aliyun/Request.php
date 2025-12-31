<?php
namespace app\utils\aliyun;

use app\lib\exception\ApiException;

class Request
{
    // post
    public static function post($params, $errmsg = 'Error')
    {
        $method = 'POST';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $params['url']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $params['headers']);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$params['host'], "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params['bodys']);
        $string = curl_exec($curl);
        $array = explode("\r\n", $string);

        // 判断第0个http响应是否正确
        if (strpos($array[0], '200')) {
            $success = json_decode($array[count($array) - 1], true);
            $results = [
                'code' => 200,
                'msg'  => 'Success',
                'data' => $success['result']
            ];
            return $results;
        }

        $abn = [
            'code' => 0,
            'msg'  => $errmsg
        ];
        for ($i=0; $i<count($array); $i++) {
            if (strpos($array[$i], 'error-message')) {
                $abn['error_msg'] = $array[$i];
                return $abn;
            }
        }
    }
}

<?php
/**
 * OCR身份证识别
 */
namespace app\utils\aliyun;

use app\service\AttaService;
use app\lib\exception\ApiException;

class OcrIdcard
{
    // base64
    private $base64;

    // 附件id
    private $attaid;

    // host
    private $host;

    // path
    private $path;

    public function __construct($attaid)
    {
        $this->attaid = $attaid;
        $this->host = 'https://yixi.market.alicloudapi.com';
        $this->path = '/ocr/idcard';
    }

    // 识别图片
    public function identify()
    {
        $base64 = $this->idToBase64();
        $appcode = config('aliyun.id_code');
        $headers = array();
        array_push($headers, "Authorization:APPCODE {$appcode}");
        array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
        $apiError = [
            'code' => 0,
            'msg'  => '图片识别失败'
        ];

        $querys = "";
        $bodys = 'image='.$base64.'&side=front';
        $url   = $this->host.$this->path;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$this->host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        $results = curl_exec($curl);
        $array = explode("\r\n", $results);
        // 判断0是否存在200
        if (!strpos($array[0], '200')) {
            $apiError['error_msg'] = !empty($array[10]) ? $array[10] : $array[11];
            throw new ApiException($apiError);
        }
        return json_decode($array[12], true);
    }

    // 附件id转base64
    public function idToBase64()
    {
        $attaInfo = AttaService::getJsonById($this->attaid);
        $file = $_SERVER['DOCUMENT_ROOT'].'/public'.$attaInfo['json'];

        try {
            if ($fp = fopen($file, 'rb', 0)) {
                $gambar = fread($fp, filesize($file));
                fclose($fp);

                $base64 = chunk_split(base64_encode($gambar));
                return $base64;
            }
        } catch (\Exception $e) {
            $err = [
                'code' => 0,
                'msg' => '分析失败',
                'error_msg' => $e->getMessage()
            ];
            throw new ApiException($err);
        }
    }
}

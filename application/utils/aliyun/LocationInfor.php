<?php
namespace app\utils\aliyun;

use app\utils\aliyun\Request;

// 解析经纬度
class LocationInfor
{
    private $host;
    private $path;
    private $longitude; // 经度
    private $latitude;  // 纬度
    private $type; // 0-GPS 2-GCJ02

    public function __construct($longitude = 0, $latitude = 0, $type = 0)
    {
        $this->host = 'https://bgps.market.alicloudapi.com';
        $this->path = '/api/getLocationinfor';
        $this->type = $type;
        $this->longitude = $longitude;
        $this->latitude  = $latitude;
    }

    public function infor()
    {
        $appcode = config('aliyun.id_code');
        $params['headers'] = array();
        array_push($params['headers'], "Authorization:APPCODE {$appcode}");
        array_push($params['headers'], "Content-Type".":"."application/json; charset=UTF-8");
        $querys = "latlng={$this->latitude},{$this->longitude}&type={$this->type}";
        $params['bodys'] = 'null';
        $params['url'] = $this->host . $this->path . '?' . $querys;
        $params['host'] = $this->host;
        return Request::post($params);
    }
}

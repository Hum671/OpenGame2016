<?php
namespace app\utils\wechat;

use app\utils\wechat\Notice;

class Base
{
    // api定义
    public $api;

    // 初始化
    public function __construct()
    {
        $this->api = 'https://api.weixin.qq.com';
    }

    // 获取二维码
    public function getQrcode($id)
    {
        $notice = new Notice();
        $token = $notice->access();
        $url = "{$this->api}/wxa/getwxacodeunlimit?access_token={$token}";
        $data = [
            'scene' => $id,
            'env_version' => config('app.app_debug') ? 'develop' : 'release',
        ];
        $results = fetchPost($url, json_encode($data), '', true);
        return $results;
    }
}
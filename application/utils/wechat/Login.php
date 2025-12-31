<?php
namespace app\utils\wechat;

use app\lib\exception\ApiException;

class Login
{
    // api定义
    public $api;

    // 初始化
    public function __construct()
    {
        $this->api = 'https://api.weixin.qq.com';
    }

    // 获取Openid和Session_key
    public function getCode2Session($code = '')
    {
        $appid  = config('wechat.appid');
        $secret = config('wechat.secret');
        $type = config('wechat.grant_type');

        $url = "{$this->api}/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type={$type}";
        $obj = json_decode(fetchGet($url), true);

        if (!empty($obj['openid'])) {
            return $obj;
        }
        $results = [
            'msg' => '登录失败,请重试!',
            'error_msg' => $obj
        ];
        throw new ApiException($results);
    }
}
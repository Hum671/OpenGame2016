<?php
namespace app\utils\wechat;

class User
{
    protected $api = 'https://api.weixin.qq.com';

    /**
     * 获取用户手机号码
     */
    public function getPhoneNumber($token) {
        $host = "{$this->api}/wxa/business/getuserphonenumber?access_token={$token}";
    }
}

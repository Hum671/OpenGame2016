<?php
/**
 * 登录服务
 */
namespace app\service;

use app\model\UserModel;
use app\utils\wechat\Login;
use app\utils\sms\Code;
use app\lib\exception\ApiException;
use app\lib\exception\CException;

class LoginService
{
    /**
     * 微信登录
     *
     * @param  [type] $data
     * @param  [type] $uid
     * @return [type]       [description]
     */
    public static function wechat($data, $uid)
    {
        $user = UserModel::get($uid);
        return [
            'type' => $user['type'],
            'is_perfect' => empty($user->info->id_number) ? 1 : 0
        ];
    }

    /**
     * 短信验证码登录
     *
     * @param  [type] $data
     * @param  [type] $uid
     * @return [type]       [description]
     */
    public static function mobile($data, $uid)
    {
        $user = UserModel::get($uid);

        // 验证短信验证码是否正确
        $code = new Code($data['phone'], config('aliyun.sms_login'));
        if (empty($code->get())
            || empty($data['sms_code'])
            || $code->get() != $data['sms_code']) {
            throw new ApiException([ 'msg' => '验证码错误' ]);
        }

        $code->remove();
        return [
            'type' => $user['type'],
            'is_perfect' => empty($user->info->id_number) ? 1 : 0
        ];
    }

    /**
     * 密码登录
     *
     * @param  [type] $data
     * @param  [type] $uid
     * @return [type]       [description]
     */
    public static function password($data, $uid)
    {
        $user = UserModel::get($uid);
        if ($data['phone'] != $user['phone']) {
            throw new ApiException([ 'msg' => '账号不存在' ]);
        }
        $password = md5($data['password'].strtotime($user['create_time']));
        if ($password != $user['password']) {
            throw new ApiException([ 'msg' => '密码错误' ]);
        }

        return [
            'type' => $user['type'],
            'is_perfect' => empty($user->info->id_number) ? 1 : 0
        ];
    }
}

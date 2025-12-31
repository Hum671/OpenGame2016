<?php
namespace app\publics\validate;

use think\Validate;

class UserLogin extends Validate
{
    protected $rule = [
        'login_user' => 'require',
        'login_pass' => 'require',
        'code'       => 'require'
    ];

    protected $message = [
        'login_user.require' => '请输入用户名',
        'login_pass.require' => '请输入密码',
        'code.require'       => '请输入验证码'
    ];
}
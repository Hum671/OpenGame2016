<?php
namespace app\publics\validate;

use think\Validate;

class UserRegister extends Validate
{
    protected $rule = [
        'login_user'   => 'require|length:6,16',
        'username'     => 'require',
        'login_pass'   => 'require|length:6,16',
        'role'         => 'require',
        'max_conn'     => 'require',
        'priority'     => 'require'
    ];

    protected $message = [
        'login_user.require'   => '请输入账号',
        'username.require'     => '请输入姓名',
        'login_user.length'    => '账号长度必须为6-16位',
        'login_pass.require'   => '请输入密码',
        'login_pass.length'    => '密码长度必须为6-16位',
        'role.require'         => '请选择角色',
        'max_conn.require'     => '请输入最大连接数',
        'priority.require'     => '请输入优先级'
    ];
}
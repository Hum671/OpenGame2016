<?php
namespace app\publics\validate;

use think\Validate;

class UserEdit extends Validate
{
    protected $rule = [
        'login_user'   => 'require|length:6,16',
        'username'     => 'require',
        'role'         => 'require',
        'max_conn'     => 'require',
        'priority'     => 'require'
    ];

    protected $message = [
        'login_user.require'   => '请输入账号',
        'login_user.length'    => '账号长度必须为6-16位',
        'username.require'     => '请输入姓名',
        'role.require'         => '请选择角色',
        'max_conn.require'     => '请输入最大连接数',
        'priority.require'     => '请输入优先级'
    ];
}
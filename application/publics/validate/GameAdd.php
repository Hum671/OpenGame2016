<?php
namespace app\publics\validate;

use think\Validate;

class GameAdd extends Validate
{
    protected $rule = [
        'num' => 'require',
        'amount' => 'require',
    ];

    protected $message = [
        'num.require' => '购买错误，请联系管理员',
        'amount.require' => '请输入购买金额',
    ];
}
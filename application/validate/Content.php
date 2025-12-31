<?php
/**
 * 内容信息
 */
namespace app\validate;

use think\Validate;

class Content extends Validate
{
    protected $rule = [
        'type'      => 'require',
        'title'     => 'require',
        'html'      => 'require'
    ];

    protected $message = [
        'type.require'      => '请选择类型',
        'title.require'     => '请输入标题',
        'html.require'      => '请输入内容'
    ];
}

<?php
/**
 * 链接信息
 */
namespace app\validate;

use think\Validate;

class Link extends Validate
{
    protected $rule = [
        'name'      => 'require',
        'url'       => 'require'
    ];

    protected $message = [
        'name.require'  => '请输入链接标题',
        'url.require'   => '请输入链接URL'
    ];
}

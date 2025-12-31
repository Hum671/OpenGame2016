<?php
namespace app\lib\exception;

use think\Exception;

class BaseException extends Exception
{
    public $code = 0;
    public $msg = '请求错误';
    public $error_msg = '';

    public function __construct($params = [])
    {
        if (!is_array($params)) {
            return ;
        }

        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }

        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }

        if (array_key_exists('error_msg', $params)) {
            $this->error_msg = $params['error_msg'];
        }
    }
}
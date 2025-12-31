<?php
namespace app\lib\exception;

use Exception;
use think\exception\Handle;

class ExceptionHandle extends Handle
{
    // 全局异常返回变量定义
    private $code;
    private $msg;
    private $error_msg;

    // 重写Render
    public function render(Exception $e)
    {
        if ($e instanceof BaseException) {
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->error_msg = $e->error_msg;
        } else {
            if (config('app_debug')) {
                return parent::render($e);
            } else {
                $this->code = 500;
                $this->msg  = '服务器异常';
            }
        }

        if (is_array($this->msg)) {
            $msg = reset($this->msg);
        } else {
            $msg = $this->msg;
        }

        $result = [
            'msg' => $msg,
            'code' => $this->code
        ];

        if (!empty($this->error_msg)) {
            $result['error_msg'] = $this->error_msg;
        }

        return json($result, $this->code);
    }
}

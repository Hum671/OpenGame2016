<?php
namespace app\lib\exception;

use app\lib\exception\ApiException;

class CException
{
    /**
     * 缺失异常
     *
     * @param  [type] $data [description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function missing($data, $name)
    {
        if (empty($data[$name])) {
            throw new ApiException([ 'msg' => "缺少 {$name} 参数!" ]);
        }
    }
}
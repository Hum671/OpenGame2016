<?php
namespace app\lib\exception;

class ApiException extends BaseException
{
    public $code = 0;
    public $msg = 'Error';
}
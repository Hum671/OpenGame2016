<?php
namespace app\utils\sms;

use think\facade\Cache;

class Code
{
    // key
    private $key;

    // 缓存的时间
    private $time;

    public function __construct($phone, $tempID)
    {
        $this->key  = $tempID.$phone;
        $this->time = 5*60;
    }

    // 生成code
    public function build()
    {
        $code = mt_rand(100000, 999999);
        Cache::set($this->key, $code, $this->time);
        return $code;
    }

    // 移除code
    public function remove()
    {
        Cache::rm($this->key);
    }

    // 获取code
    public function get()
    {
        return Cache::get($this->key, '');
    }
}

<?php
namespace app\lib\common;

class TimeLib
{
    // 时间转 刚刚，分，小时
    public static function unixTime($date)
    {
        $time = strtotime($date);
        $day = strtotime(date('Y-m-d'), time()); // 凌晨
        $pday = strtotime(date('Y-m-d'), strtotime('-1 day')); // 昨天
        $now = time(); // 现在

        $t = $now - $time;

        if ($time < $pday) {
            $str = $date;
        } elseif ($time < $day && $time > $pday) {
            $str = '昨天';
        } elseif ($t > 60*60) {
            $str = floor($t / (60*60)).'小时前';
        } elseif ($t > 60) {
            $str = floor($t / 60).'分钟前';
        } else {
            $str = '刚刚';
        }

        return $str;
    }
}

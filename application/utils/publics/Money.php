<?php
/**
 * 资金工具
 */
namespace app\utils\publics;

class Money
{
    // 分转元
    public static function pennyToYuan($penny)
    {
        return number_format($penny / 100, 2, '.', '');
    }

    // 转元,格式化
    public static function toYuan($penny)
    {
        return number_format($penny / 100, 2);
    }

    // 元转分
    public static function yuanToPenny($yuan)
    {
        return (int)($yuan * 100);
    }
}

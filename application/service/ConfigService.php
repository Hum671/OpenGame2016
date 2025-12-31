<?php
namespace app\service;

use app\model\ConfigModel;

class ConfigService
{
    /**
     * 判断是否需要绑定手机号
     */
    public static function isNeedBindPhone()
    {
        $configModel = new ConfigModel;
        $value = $configModel->getValue('is_bind_phone');
        return intval($value) == 1;
    }

    /**
     * 获取value by name
     */
    public static function get_value_by_name($name)
    {
        $configModel = new ConfigModel;
        $find = $configModel->where('name', $name)->find();
        if (empty($find)) {
            return '';
        }
        return $find['value'];
    }

    /**
     * 新增
     */
    public static function add($data)
    {
        $configModel = new ConfigModel;
        return $configModel->save($data);
    }

    /**
     * 删除
     */
    public static function delete_by_name($name)
    {
        $configModel = new ConfigModel;
        return $configModel->where('name', $name)->delete();
    }
}

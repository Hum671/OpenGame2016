<?php
namespace app\model;

use think\Model;

class ConfigModel extends Model
{
    protected $name = 'config';
    protected $autoWriteTimestamp = false;

    // 获取配置变量
    public function getValue($name)
    {
        return $this->getFieldByName($name, 'value');
    }

    // 获取变量根据类型获取
    public function getValueByType($type)
    {
        return $this->where(['inc_type' => $type])->all();
    }

    // 保存或更新数据
    public function setSave($post, $type = '', $desc = '')
    {
        $update_data = [];
        $save_data   = [];
        foreach ($post as $k => $v) {
            $save['value'] = $v;
            $save['name']  = $k;
            $save['inc_type'] = $type;
            $save['desc'] = $desc;

            // 查询是否已经存在数据
            $id = $this->getFieldByName($save['name'], 'id');
            if (!empty($id)) {
                $update = $save;
                $update['id'] = $id;
                array_push($update_data, $update);
            } else {
                array_push($save_data, $save);
            }
        }

        $this->saveAll($update_data);
        $this->saveAll($save_data);
    }
}

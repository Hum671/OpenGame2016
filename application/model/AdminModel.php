<?php
namespace app\model;

use think\Model;
use think\Db;

class AdminModel extends Model
{
    protected $name = 'admin';

    // 查看用户身份权限
    public function getUserInfo()
    {
        return Db::name('auth_role_user')
            ->alias('aru')
            ->rightJoin('p_admin a', 'aru.user_id = a.id')
            ->leftjoin('p_auth_role ar', 'aru.role_id = ar.id')
            ->field('a.*, ar.name')
            ->order('a.id asc')
            ->select();
    }

    // 获取全部角色
    public function getAllRole()
    {
        return Db::name('auth_role')
            ->select();
    }
}

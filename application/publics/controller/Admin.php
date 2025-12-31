<?php
/**
 * 后台用户管理
 * User: Administrator
 * Date: 2018/8/24 0024
 * Time: 16:34
 */
namespace app\publics\controller;

use app\model\AdminModel;
use think\facade\Request;
use think\Db;
use thinkcms\auth\library\Tree;
use thinkcms\auth\model\Menu;

class Admin extends Base
{
    // 管理员列表
    public function index()
    {
        $aModel = new AdminModel();

        $list = $aModel->getUserInfo();
        $this->assign('list', $list);
        return $this->fetch();
    }

    // 添加管理员
    public function add()
    {
        $aModel = new AdminModel();

        if (Request::isPost()) {
            $post = input('post.');
            $time = time();

            $validate = new \app\publics\validate\Admin;
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }

            // 判断账号是否已经存在
            if ($aModel->where(['login_user' => $post['login_user']])->count() > 0) {
                $this->error('该账号已存在');
            }

            $post['create_time'] = $time;
            $post['login_pass']  = md5($post['login_pass'].$time);

            $aModel->save($post);
            $aru_data = [
                'role_id' => $post['role_id'],
                'user_id' => $aModel->id
            ];
            Db::name('auth_role_user')->insert($aru_data);
            $this->success('添加用户成功', url('index'));
        }

        // 获取角色
        $this->assign([
            'role_list' => $aModel->getAllRole()
        ]);

        return $this->fetch();
    }

    // 编辑管理员
    public function edit()
    {
        $aModel = new AdminModel();

        if (Request::isPost()) {
            $post = input('post.');

            // 密码验证
            $empty_pass = 'thgj_ThinkPhp5.1';
            $post['login_pass'] = empty($post['login_pass']) ? $empty_pass : $post['login_pass'];

            $validate = new \app\publics\validate\Admin;
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }

            // 更新数据
            $map['login_user'] = $post['login_user'];
            $info = $aModel->where($map)->find();
            $update['username'] = $post['username'];
            if ($post['login_pass'] != $empty_pass) {
                $update['login_pass'] = md5($post['login_pass'].$info['create_time']);
            }
            $aModel->save($update, ['id' => $info['id']]);

            // 更新角色
            Db::name('auth_role_user')
                ->where(['user_id' => $info['id']])
                ->update(['role_id' => $post['role_id']]);

            $this->success('编辑成功', url('index'));
        }

        $id = input('param.id');
        $info = $aModel->where(['id' => $id])->find();

        $this->assign([
            'role_list' => $aModel->getAllRole(),
            'role_id'   => Db::name('auth_role_user')->getFieldByUserId($id, 'role_id'),
            'info'      => $info
        ]);
        return $this->fetch();
    }

    // 管理员状态管理
    public function status()
    {
        $aModel = new AdminModel();
        $get     = input('param.');

        if ($get['id'] == 1) {
            $this->error('该账号不能进行此操作');
        }

        $data['status'] = $get['status'] == 1 ? 0 : 1;
        $aModel->save($data, ['id' => $get['id']]);
        $msg = $data['status'] == 0 ? '开启' : '禁用';

        $this->success('账号'.$msg.'成功');
    }

    // 删除管理员账号
    public function deleted()
    {
        $aModel = new AdminModel();
        $id      = input('param.id');

        if ($id == 1) {
            $this->error('该账号不能进行此操作');
        }

        $aModel->where(['id' => $id])->delete();
        Db::name('auth_role_user')->where(['user_id' => $id])->delete();
        $this->success('删除账号成功');
    }

    // 修改密码
    public function updatePass()
    {
        $aModel = new AdminModel();
        $post = input('post.');

        if ($post['login_pass'] != $post['login_pass_2']) {
            $this->error('两次密码输入不一致');
        }

        $info = $aModel->where(['id' => $this->uid])->find();
        $createTime = $aModel::getFieldById($this->uid, 'create_time');
        $data['login_pass'] = md5($post['login_pass'].$createTime);
        $aModel->save($data, ['id' => $this->uid]);
        $this->success('密码修改成功');
    }
}

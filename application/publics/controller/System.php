<?php
/**
 * 系统设置
 * User: Administrator
 * Date: 2018/8/24 0024
 * Time: 16:34
 */
namespace app\publics\controller;

use app\model\ConfigModel;
use app\model\UserModel;
use think\facade\Request;
use think\Db;
use thinkcms\auth\Auth;
use think\facade\Cache;

class System extends Base
{
    private $cModel;

    public function __construct()
    {
        parent::__construct();
        $this->cModel = new ConfigModel();
    }

    // 常规设置
    public function general()
    {
        if (Request::isPost()) {
            $post = input('post.');
            $this->cModel->setSave($post, 'general', '常规设置');

            Cache::set('config', 0, 1);
            $auth = new Auth();
            $auth->createLog("管理员[ {$this->username} ]更新了系统管理=>系统设置=>常规设置", '更新数据');
            $this->success('操作成功');
        }

        $this->assign([
            'question_tags' => $this->cModel->getValue('question_tags'),
            'site_code'     => $this->cModel->getValue('site_code'),
            'swiper_imgs'   => $this->cModel->getValue('swiper_imgs'),
            'swiper_func'   => $this->cModel->getValue('swiper_func'),
            'salesman_version'  => $this->cModel->getValue('salesman_version'),
        ]);
        return $this->fetch();
    }

    // 关于我们
    public function about()
    {
        if (Request::isPost()) {
            $post = input('post.');
            $this->cModel->setSave($post, 'about', '关于我们');

            Cache::set('config', 0, 1);
            $auth = new Auth();
            $auth->createLog("管理员[ {$this->username} ]更新了系统管理=>系统设置=>关于我们", '更新数据');
            $this->success('操作成功');
        }

        $this->assign([
            'about_us' => $this->cModel->getValue('about_us')
        ]);
        return $this->fetch();
    }

    // 合同模版
    public function contract()
    {
        if (Request::isPost()) {
            $post = input('post.');
            $this->cModel->setSave($post, 'contract', '合同模版');

            Cache::set('config', 0, 1);
            $auth = new Auth();
            $auth->createLog("管理员[ {$this->username} ]更新了系统管理=>系统设置=>合同模版", '更新数据');
            $this->success('操作成功');
        }

        $this->assign([
            'contract_template' => $this->cModel->getValue('contract_template')
        ]);
        return $this->fetch();
    }

    // 管理员日志
    public function log()
    {
        $limit = empty(input('param.limit')) ? 10 : input('param.limit');
        $cur_page   = empty(input('param.page')) ? 1 : input('param.page');

        $query = [
            'limit' => $limit
        ];
        $count = (int)Db::name('action_log')->count();
        $list  = Db::name('action_log')
            ->order('create_time desc')
            ->paginate($limit, $count, [
                'query' => $query
            ]);

        $this->assign([
            'list'  => $list,
            'cur_page' => $cur_page,
            'limit' => selectLimit($limit),
            'count' => $count,
            'page'  => $list->render()
        ]);
        return $this->fetch();
    }

    // 清空日志
    public function clearlog()
    {
        Db::name('action_log')->delete(true);
        $this->success('清空日志成功');
    }
}

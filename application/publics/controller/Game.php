<?php
namespace app\publics\controller;

use app\service\ConfigService;
use app\service\GameService;

class Game extends Base
{
    // 购买信息
    public function info()
    {
        $limit = empty(input('param.limit')) ? 30 : input('param.limit');
        $cur_page   = empty(input('param.page')) ? 1 : input('param.page');

        $query = [
            'limit' => $limit
        ];
        $count = (int)GameService::game_buy_model([])->count();
        $list  = GameService::game_buy_model([])
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

        $this->assign('list', $list);
        return $this->fetch();
    }

    // 删除购买信息
    public function deleted()
    {
        $id = input('param.id');
        if (GameService::game_delete($id) > 0) {
            $this->success('删除成功', url('info'));
        }
        $this->apiError();
    }

    // 新增购买
    public function add()
    {
        $issue = GameService::get_current_issue();

        if (request()->post()) {
            $post = request()->post();

            // 判断是否封板
            $h = date('H');
            $m = date('i');
            if ($h >= 21 && $m >= 30) {
                //$this->error('封盘时间已过');
            }

            $validate = new \app\publics\validate\GameAdd;
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }

            $post['amount'] = $post['amount'] * 100;
            $this->apiSuccess(GameService::add($post));
        }

        $buy_total_amount = GameService::get_total_amount($issue);
        $buy_grid = GameService::get_buy_grid($issue);

        // 购买的号码
        $grid = [];
        for ($i = 1; $i < 50; $i++) {
            $number = '0.00';
            foreach ($buy_grid as $value) {
                if ((string)$value['num'] === (string)$i) {
                    $number = number_format(($value['total_amount'] / 100), 2);
                    break;
                }
            }

            array_push($grid, [
                'num' => $i,
                'total_amount' => $number,
            ]);
        }

        $this->assign([
            'issue' => $issue,
            'buy_total_amount' => number_format(($buy_total_amount / 100), 2),
            'grid' => $grid,
        ]);
        return $this->fetch();
    }

    // 表单
    public function form()
    {
        return $this->fetch();
    }

    // 开奖信息
    public function open()
    {
        $limit = empty(input('param.limit')) ? 30 : input('param.limit');
        $cur_page   = empty(input('param.page')) ? 1 : input('param.page');

        $query = [
            'limit' => $limit
        ];
        $count = (int)GameService::game_result_model()->count();
        $list  = GameService::game_result_model([])
            ->order('create_time desc')
            ->paginate($limit, $count, [
                'query' => $query
            ]);

        $issue = GameService::get_current_issue();
        $key = $issue.'DRAW_NUMBERS';
        $this->assign([
            'issue' => $issue,
            'list'  => $list,
            'cur_page' => $cur_page,
            'limit' => selectLimit($limit),
            'count' => $count,
            'page'  => $list->render(),
            'draw_numbers' => cache($key),
        ]);
        return $this->fetch();
    }

    // 设置号码
    public function setnumber()
    {
        $issue = GameService::get_current_issue();
        $key = $issue.'DRAW_NUMBERS';

        if (request()->post()) {
            $post = request()->post();

            // 判断是否存在重复
            $setArray = [
                (int)$post['num1'],
                (int)$post['num2'],
                (int)$post['num3'],
                (int)$post['num4'],
                (int)$post['num5'],
                (int)$post['num6'],
                (int)$post['num'],
            ];
            if (max(array_count_values($setArray)) > 1) {
                $this->error('号码存在重复!');
            }

            // 判断有效数字
            $hasInvalidNumbers = array_filter($setArray, function ($value) {
                return !is_numeric($value) || $value > 49 || $value < 1;
            });
            if ($hasInvalidNumbers) {
                $this->error('存在无效号码!');
            }

            cache($key, $post, 25 * 60 * 60);
            $this->success('设置成功', url('open'));
        }

        $this->assign([
            'issue' => $issue,
        ]);
        return $this->fetch();
    }

    // 必中6肖
    public function six_animal_sign()
    {
        $limit = empty(input('param.limit')) ? 30 : input('param.limit');
        $cur_page   = empty(input('param.page')) ? 1 : input('param.page');

        $query = [
            'limit' => $limit
        ];
        $count = (int)GameService::six_animal_sign_model([])->count();
        $list  = GameService::six_animal_sign_model([])
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

        $this->assign('list', $list);
        return $this->fetch();
    }

    // 必中6肖-表单
    public function six_animal_sign_form()
    {
        if (request()->post()) {
            $post = request()->post();

            // 判断是否属于生肖内
            $animal_sign_array = mb_str_split($post['animal_sign']);
            if (max(array_count_values($animal_sign_array)) > 1) {
                $this->error('生肖不能重复');
            }

            for ($i = 0; $i < count($animal_sign_array); $i++) {
                if (!in_array($animal_sign_array[$i], GameService::$animal_sign)) {
                    $this->error('请输入正确的生肖');
                }
            }

            if (GameService::add_six_animal_sign($post)) {
                $this->success('添加成功', url('six_animal_sign'));
            }
            $this->error();
        }
        return $this->fetch();
    }

    // 删除6肖
    public function delete_six_animal_sign()
    {
        $id = input('id');
        if (GameService::delete_six_animal_sign($id)) {
            $this->success('删除成功', url('six_animal_sign'));
        }
        $this->error();
    }

    // 1肖
    public function one_animal_sign()
    {
        $limit = empty(input('param.limit')) ? 30 : input('param.limit');
        $cur_page   = empty(input('param.page')) ? 1 : input('param.page');

        $query = [
            'limit' => $limit
        ];
        $count = (int)GameService::one_animal_sign_model([])->count();
        $list  = GameService::one_animal_sign_model([])
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

        $this->assign('list', $list);
        return $this->fetch();
    }

    // 1肖-表单
    public function one_animal_sign_form()
    {
        if (request()->post()) {
            $post = request()->post();

            if (!in_array($post['animal_sign'], GameService::$animal_sign)) {
                $this->error('请输入正确的生肖');
            }

            if (GameService::add_one_animal_sign($post)) {
                $this->success('添加成功', url('one_animal_sign'));
            }
            $this->error();
        }
        return $this->fetch();
    }

    // 1肖
    public function delete_one_animal_sign()
    {
        $id = input('id');
        if (GameService::delete_one_animal_sign($id)) {
            $this->success('删除成功', url('one_animal_sign'));
        }
        $this->error();
    }

    // 6尾
    public function six_tail_number()
    {
        $limit = empty(input('param.limit')) ? 30 : input('param.limit');
        $cur_page   = empty(input('param.page')) ? 1 : input('param.page');

        $query = [
            'limit' => $limit
        ];
        $count = (int)GameService::six_tail_number_model([])->count();
        $list  = GameService::six_tail_number_model([])
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

        $this->assign('list', $list);
        return $this->fetch();
    }

    // 6尾-表单
    public function six_tail_number_form()
    {
        if (request()->post()) {
            $post = request()->post();

            if (GameService::add_six_tail_number($post)) {
                $this->success('添加成功', url('six_tail_number'));
            }
            $this->error();
        }
        return $this->fetch();
    }

    // 6尾
    public function delete_six_tail_number()
    {
        $id = input('id');
        if (GameService::delete_six_tail_number($id)) {
            $this->success('删除成功', url('six_tail_number'));
        }
        $this->error();
    }

    // 资料图片管理
    public function data_img()
    {
        if (request()->post()) {
            $data = request()->post();
            ConfigService::delete_by_name($data['name']);
            if (ConfigService::add($data) > 0) {
                $this->success('添加成功', url('data_img'));
            }
            $this->error();
        }

        $issue = GameService::get_current_issue();
        $this->assign([
            'issue'  => $issue,
            'sibuxiang' => ConfigService::get_value_by_name($issue.'_sibuxiang'),
        ]);
        return $this->fetch();
    }
}

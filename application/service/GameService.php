<?php
namespace app\service;

use app\model\GameBuyModel;
use app\model\GameResultModel;
use app\model\OneAnimalSignModel;
use app\model\SixAnimalSignModel;
use app\model\SixTailNumberModel;

class GameService
{
    /**
     * 生肖
     */
    public static $animal_sign = [
        '鼠',
        '牛',
        '虎',
        '兔',
        '龙',
        '蛇',
        '马',
        '羊',
        '猴',
        '鸡',
        '狗',
        '猪',
    ];

    /**
     * 生肖开始年份
     */
    public static $animal_sign_start_year = 1900;

    /**
     * 获取当前期数
     */
    public static function get_current_issue()
    {
        $year = date('Y');
        $base_timestamp = strtotime($year.'-01-01 00:00:00');
        $timestamp = time();
        $day_timestamp = 24 * 60 * 60;
        $issue = ceil(($timestamp - $base_timestamp) / $day_timestamp);
        
        if ($issue < 10) {
            $issue = '00' . $issue;
        } else if ($issue < 100 && $issue >= 10) {
            $issue = '0' . $issue;
        }

        // 当前期数
        return $year . $issue;
    }

    /**
     * 根据期号获取开奖日期
     */
    public static function get_open_date_by_issue($issue)
    {
        $issue = $issue - 1;
        if (empty($issue)) {
            return null;
        }

        $y = substr($issue, 0, 4);
        $no_date_issue = substr($issue, 4);
        $base_timestamp = strtotime($y.'-01-01 00:00:00');
        $day_timestamp = 24 * 60 * 60;
        $issue_timestamp = $base_timestamp + ($day_timestamp * $no_date_issue);
        $date = date('Y-m-d ', $issue_timestamp);
        $week = date('N', $issue_timestamp - $day_timestamp);
        $weeks = ['星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日'];
        return [
            'date' => $date,
            'week' => $weeks[$week - 1],
            'bdate' => date('Y-m-d ', $issue_timestamp - $day_timestamp),
        ];
    }

    /**
     * 新增购买
     */
    public static function add($data)
    {
        $gameBuyModel = new GameBuyModel;
        return $gameBuyModel->save($data);
    }

    /**
     * 获取总金额
     */
    public static function get_total_amount($issue)
    {
        $gameBuyModel = new GameBuyModel;
        $result = $gameBuyModel
            ->where('issue', $issue)
            ->sum('amount');
        return $result;
    }

    /**
     * 获取购买grid
     */
    public static function get_buy_grid($issue)
    {
        $gameBuyModel = new GameBuyModel;
        return $gameBuyModel->field('SUM(amount) as total_amount, num')
            ->group('num')
            ->where('issue', $issue)
            ->select();
    }

    /**
     * 获取购买model
     */
    public static function game_buy_model($where)
    {
        $gameBuyModel = new GameBuyModel;
        if (isset($where['issue'])) {
            $gameBuyModel->where('issue', $where['issue']);
        }
        return $gameBuyModel->field('id, issue, num, amount, create_time, remark');
    }

    /**
     * 删除游戏
     */
    public static function game_delete($id)
    {
        $gameBuyModel = new GameBuyModel;
        return $gameBuyModel->destroy($id);
    }

    /**
     * 获取游戏结果
     */
    public static function game_result_model()
    {
        $gameResultModel = new GameResultModel;
        return $gameResultModel
            ->field('id, type, issue, num1, num2, num3, num4, num5, num6, num, create_time, update_time');
    }

    /**
     * 获取六合彩顺序
     */
    public static function get_mark_six_animal_sign_sequence($year = '2024')
    {
        $index = self::get_game_animal_sign_index($year); // 1号
        $sequence = [];

        foreach (self::$animal_sign as $item => $value) {
            $index++;
            if ($index > 11) {
                $index = 0;
            }
            $sequence[] = self::$animal_sign[$index];
        }
        return array_reverse($sequence);
    }

    /**
     * 根据号码获取生肖
     */
    public static function get_animal_sign_by_number($number, $issue = '2024001')
    {
        if (empty($number)) {
            return null;
        }
        $year = substr($issue, 0, 4);
        $number_issue = substr($issue, 4, 7);
        
        if ((int)$number_issue < 29) {
            $year = $year - 1;
        }
        
        $mark_six_animal_sign = self::get_mark_six_animal_sign_sequence($year); // 6合彩顺序
        return $mark_six_animal_sign[($number - 1) % 12];
    }

    /**
     * 根据号码获取属性
     */
    public static function get_state_by_number($number, $issue = '2024001')
    {
        if (empty($number)) {
            return null;
        }
        $year = substr($issue, 0, 4);
        $number_issue = substr($issue, 4, 7);

        if ((int)$number_issue < 29) {
            $year = $year - 1;
        }

        // 2024
        $gold = [1, 2, 9, 10, 23, 24, 31, 32, 39, 40]; // 金
        $wood = [5, 6, 13, 14, 21, 22, 35, 36, 43, 44]; // 木
        $water = [11, 12, 19, 20, 27, 28, 41, 42, 49]; // 水
        $fire = [7, 8, 15, 15, 29, 30, 37, 38, 45, 46]; // 火
        $earth = [3, 4, 17, 18, 25, 26, 33, 34, 47, 48]; // 土

        if ($year == '2025' || $year == '2026') {
            $gold = [3, 4, 11, 12, 25, 26, 33, 34, 41, 42]; // 金
            $wood = [7, 8, 15, 16, 23, 24, 37, 38, 45, 46]; // 木
            $water = [13, 14, 21, 22, 29, 30, 43, 44]; // 水
            $fire = [1, 2, 9, 10, 17, 18, 31, 32, 39, 40, 47, 48]; // 火
            $earth = [5, 6, 19, 20, 27, 28, 35, 36, 49]; // 土
        }

        if (in_array($number, $gold)) {
            return '金';
        }
        if (in_array($number, $wood)) {
            return '木';
        }
        if (in_array($number, $water)) {
            return '水';
        }
        if (in_array($number, $fire)) {
            return '火';
        }
        if (in_array($number, $earth)) {
            return '土';
        }

        return null;
    }

    /**
     * 根据号码获取波色
     */
    public static function get_color_by_number($number)
    {
        if (empty($number)) {
            return null;
        }
        $red = [1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46];
        $blue = [3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48];
        $green = [5, 6, 11, 16, 17, 21, 22, 27, 28, 32, 33, 38, 39, 43, 44, 49];

        if (in_array($number, $red)) {
            return 'red';
        }
        if (in_array($number, $blue)) {
            return 'blue';
        }
        if (in_array($number, $green)) {
            return 'green';
        }
        return null;
    }

    /**
     * 根据号码获取波色
     */
    public static function get_color_by_number_1($number)
    {
        if (empty($number)) {
            return null;
        }
        $red = [1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46];
        $blue = [3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48];
        $green = [5, 6, 11, 16, 17, 21, 22, 27, 28, 32, 33, 38, 39, 43, 44, 49];

        if (in_array($number, $red)) {
            return 'red';
        }
        if (in_array($number, $blue)) {
            return 'blue';
        }
        if (in_array($number, $green)) {
            return 'green';
        }
        return null;
    }

    /**
     * 新增六肖
     */
    public static function add_six_animal_sign($data)
    {
        $six_animal_sign_model = new SixAnimalSignModel;
        return $six_animal_sign_model->save($data);
    }

    /**
     * 删除六肖
     */
    public static function delete_six_animal_sign($id)
    {
        $six_animal_sign_model = new SixAnimalSignModel;
        return $six_animal_sign_model->destroy($id);
    }

    /**
     * 六肖model
     */
    public static function six_animal_sign_model()
    {
        $six_animal_sign_model = new SixAnimalSignModel;
        return $six_animal_sign_model->field('id, issue, animal_sign, create_time');
    }

    /**
     * 新增yi肖
     */
    public static function add_one_animal_sign($data)
    {
        $one_animal_sign_model = new OneAnimalSignModel;
        return $one_animal_sign_model->save($data);
    }

    /**
     * 删除yi肖
     */
    public static function delete_one_animal_sign($id)
    {
        $one_animal_sign_model = new OneAnimalSignModel;
        return $one_animal_sign_model->destroy($id);
    }

    /**
     * yi肖model
     */
    public static function one_animal_sign_model()
    {
        $one_animal_sign_model = new OneAnimalSignModel;
        return $one_animal_sign_model->field('id, issue, animal_sign, create_time');
    }

    /**
     * 新增6尾
     */
    public static function add_six_tail_number($data)
    {
        $six_tail_number_model = new SixTailNumberModel;
        return $six_tail_number_model->save($data);
    }

    /**
     * 删除6尾
     */
    public static function delete_six_tail_number($id)
    {
        $six_tail_number_model = new SixTailNumberModel;
        return $six_tail_number_model->destroy($id);
    }

    /**
     * 6尾model
     */
    public static function six_tail_number_model()
    {
        $six_tail_number_model = new SixTailNumberModel;
        return $six_tail_number_model->field('id, issue, nums, create_time');
    }

    /**
     * 获取当年游戏生肖的下标
     */
    public static function get_game_animal_sign_index($year = '2024')
    {
        return ($year - self::$animal_sign_start_year) % 12;
    }

    /**
     * 获取游戏随机数
     */
    public static function get_game_rand()
    {
        return rand(1, 49);
    }

    /**
     * 获取不重复的随机数
     */
    public static function get_not_repeat_num($nums)
    {
        $num = self::get_game_rand();
        while (1) {
            if (in_array($num, $nums)) {
                $num = self::get_game_rand();
            } else {
                return $num;
            }
        }
    }

    /**
     * 获取游戏结果
     */
    public static function get_game_result_by_issue($issue)
    {
        $game_result_model = new GameResultModel;
        return $game_result_model
            ->where('issue', date('Y') . $issue)
            ->field('num')
            ->find();
    }

    /**
     * 获取游戏结果
     */
    public static function get_game_result_by_issue_no_date($issue)
    {
        $game_result_model = new GameResultModel;
        return $game_result_model
            ->where('issue', $issue)
            ->find();
    }

    /**
     * 获取游戏六肖
     */
    public static function get_game_animal_sign_list()
    {
        $six_animal_sign_model = new SixAnimalSignModel;
        $list = $six_animal_sign_model
            ->field('issue, animal_sign')
            ->order('create_time desc')
            ->limit(11)
            ->select();

        foreach ($list as $value) {
            $value['issue'] = sprintf('%03d', $value['issue']);
            $result = self::get_game_result_by_issue($value['issue']);
            $value['animal_sign_list'] = mb_str_split($value['animal_sign']);
            if (empty($result)) {
                $value['result'] = null;
                $value['result_animal_sign'] = null;
            } else {
                $value['result'] = sprintf('%02d', $result['num']);
                $value['result_animal_sign'] = self::get_animal_sign_by_number($result['num']);
            }
        }

        $newList = [];
        for ($i = count($list) - 1; $i >= 0; $i--) {
            $newList[] = $list[$i];
        }

        return $newList;
    }

    /**
     * 获取游戏yi肖
     */
    public static function get_game_one_snimal_sign_list()
    {
        $one_animal_sign_model = new OneAnimalSignModel;
        $list = $one_animal_sign_model
            ->field('issue, animal_sign')
            ->order('create_time desc')
            ->limit(7)
            ->select();

        foreach ($list as $value) {
            $value['issue'] = sprintf('%03d', $value['issue']);
            $result = self::get_game_result_by_issue($value['issue']);
            if (empty($result)) {
                $value['result'] = null;
                $value['result_animal_sign'] = null;
            } else {
                $value['result'] = sprintf('%02d', $result['num']);
                $value['result_animal_sign'] = self::get_animal_sign_by_number($result['num']);
            }
        }

        $newList = [];
        for ($i = count($list) - 1; $i >= 0; $i--) {
            $newList[] = $list[$i];
        }
        return $newList;
    }

    /**
     * 必中6尾
     */
    public static function get_game_six_tail_number_list()
    {
        $six_tail_number_model = new SixTailNumberModel;
        $list = $six_tail_number_model
            ->field('issue, nums')
            ->order('create_time desc')
            ->limit(20)
            ->select();

        foreach ($list as $value) {
            $value['issue'] = sprintf('%03d', $value['issue']);
            $result = self::get_game_result_by_issue($value['issue']);
            $value['num_list'] = explode('-', $value['nums']);
            if (empty($result)) {
                $value['result'] = null;
                $value['result_animal_sign'] = null;
            } else {
                $value['result'] = sprintf('%02d', $result['num']);
                $value['result_animal_sign'] = self::get_animal_sign_by_number($result['num']);
            }
        }

        $newList = [];
        for ($i = count($list) - 1; $i >= 0; $i--) {
            $newList[] = $list[$i];
        }
        return $newList;
    }

    /**
     * 游戏开奖
     */
    public static function game_draw()
    {
        /**
         * 22:30，一共7个数字，每一次执行开一个数字
         * 该函数为定时任务，每隔30s执行一次
         * 如果没有达到22:30或者已开完奖，则不执行
         */
        $hour = date('H');
        $minute = date('i');
        if ($hour < 22 || ($hour == 22 && $minute <= 33)) {
            return false;
        }

        $issue = self::get_current_issue();
        $key = $issue.'DRAW_NUMBERS';

        // 判断当前期是否已经存在数据
        $game_result_model = new GameResultModel;
        $find = $game_result_model->where('issue', $issue)->find();

        // 查询当前期总金额最高的6位
        $game_buy_model = new GameBuyModel;
        $buy_list = $game_buy_model->field('SUM(amount) as total_amount, num')
            ->group('num')
            ->where('issue', $issue)
            ->order('total_amount desc')
            ->limit(6)
            ->select();

        $nums = [];
        $session_result = cache($key);
        if (!$find) {
            if (!isset($buy_list[0]['num']) || empty($buy_list[0]['num'])) {
                $num = self::get_not_repeat_num($nums);
            } else {
                $num = $buy_list[0]['num'];
            }
            if (!empty($session_result) && !empty($session_result['num1'])) {
                $num = $session_result['num1'];
            }

            $data = [
                'type' => 'ZONG_AO_MENG',
                'num1' => $num,
                'issue' => $issue,
            ];
            if ($game_result_model->save($data) > 0) {
                echo "open num1 success! \n";
            } else {
                echo "open num1 fail! \n";
            }

            array_push($nums, $num);
        }

        $find = $game_result_model->where('issue', $issue)->find();
        if (empty($find['num2'])) {
            if (!isset($buy_list[1]['num']) || empty($buy_list[1]['num'])) {
                $num = self::get_not_repeat_num($nums);
            } else {
                $num = $buy_list[1]['num'];
            }
            if (!empty($session_result) && !empty($session_result['num2'])) {
                $num = $session_result['num2'];
            }

            if ($game_result_model->save(['num2' => $num], ['id' => $find['id']]) > 0) {
                echo "open num2 success! \n";
            } else {
                echo "open num2 fail! \n";
            }
            array_push($nums, $num);
        }

        if (empty($find['num3'])) {
            if (!isset($buy_list[2]['num']) || empty($buy_list[2]['num'])) {
                $num = self::get_not_repeat_num($nums);
            } else {
                $num = $buy_list[2]['num'];
            }
            if (!empty($session_result) && !empty($session_result['num3'])) {
                $num = $session_result['num3'];
            }

            if ($game_result_model->save(['num3' => $num], ['id' => $find['id']]) > 0) {
                echo "open num3 success! \n";
            } else {
                echo "open num3 fail! \n";
            }
            array_push($nums, $num);
        }

        if (empty($find['num4'])) {
            if (!isset($buy_list[3]['num']) || empty($buy_list[3]['num'])) {
                $num = self::get_not_repeat_num($nums);
            } else {
                $num = $buy_list[3]['num'];
            }
            if (!empty($session_result) && !empty($session_result['num4'])) {
                $num = $session_result['num4'];
            }

            if ($game_result_model->save(['num4' => $num], ['id' => $find['id']]) > 0) {
                echo "open num4 success! \n";
            } else {
                echo "open num4 fail! \n";
            }
            array_push($nums, $num);
        }

        if (empty($find['num5'])) {
            if (!isset($buy_list[4]['num']) || empty($buy_list[4]['num'])) {
                $num = self::get_not_repeat_num($nums);
            } else {
                $num = $buy_list[4]['num'];
            }
            if (!empty($session_result) && !empty($session_result['num5'])) {
                $num = $session_result['num5'];
            }

            if ($game_result_model->save(['num5' => $num], ['id' => $find['id']]) > 0) {
                echo "open num5 success! \n";
            } else {
                echo "open num5 fail! \n";
            }
            array_push($nums, $num);
        }

        if (empty($find['num6'])) {
            if (!isset($buy_list[5]['num']) || empty($buy_list[5]['num'])) {
                $num = self::get_not_repeat_num($nums);
            } else {
                $num = $buy_list[5]['num'];
            }
            if (!empty($session_result) && !empty($session_result['num6'])) {
                $num = $session_result['num6'];
            }

            if ($game_result_model->save(['num6' => $num], ['id' => $find['id']]) > 0) {
                echo "open num6 success! \n";
            } else {
                echo "open num6 fail! \n";
            }
            array_push($nums, $num);
        }

        if (!empty($find['num'])) {
            return false;
        }

        // 查询购买最低的号码
        $min_list = $game_buy_model->field('SUM(amount) as total_amount, num')
            ->group('num')
            ->where('issue', $issue)
            ->order('total_amount asc')
            ->select();
        if (empty($min_list) || count($min_list) === 0) {
            $num = self::get_not_repeat_num($nums);
            if (!empty($session_result) && !empty($session_result['num'])) {
                $num = $session_result['num'];
            }

            if ($game_result_model->save(['num' => $num], ['id' => $find['id']]) > 0) {
                echo "open num success! \n";
            } else {
                echo "open num fail! \n";
            }
            return true;
        }

        // 如果49个号码没有出售完，则获取没有购买的号码
        if (count($min_list) < 49) {
            $not_buy_nums = [];
            for ($i = 1; $i <= 49; $i++) {
                $not_buy = true;
                foreach ($min_list as $value) {
                    if ((int) $value['num'] === $i) {
                        $not_buy = false;
                        break;
                    }
                }
                if ($not_buy) {
                    array_push($not_buy_nums, $i);
                }
            }

            // 随机打乱数组
            shuffle($not_buy_nums);
            $result_nums = array_values(array_diff($not_buy_nums, $nums));
            $num = $result_nums[0];
            if (!empty($session_result) && !empty($session_result['num'])) {
                $num = $session_result['num'];
            }
            if ($game_result_model->save(['num' => $num], ['id' => $find['id']]) > 0) {
                echo "open num success! \n";
            } else {
                echo "open num fail! \n";
            }
            return true;
        }

        // 如果出售完49个号码则获取购买最低的号码
        $num = $min_list[0]['num'];
        if (!empty($session_result) && !empty($session_result['num'])) {
            $num = $session_result['num'];
        }
        if ($game_result_model->save(['num' => $num], ['id' => $find['id']]) > 0) {
            echo "open num success! \n";
        } else {
            echo "open num fail! \n";
        }
    }

    /**
     * 游戏记录
     */
    public static function game_result_list($isPage = false)
    {
        $game_result_model = new GameResultModel();
        $model = $game_result_model->order('issue', 'desc');

        if (!$isPage) {
            return $model->all();
        }
        return $model->page(1, 5)->all();
    }
}

<?php
namespace app\index\controller;

use app\service\GameService;
use app\service\ConfigService;

class Index extends Base
{
    // draw
    public function draw()
    {
        echo "open";
        $hour = date('H');
        $minute = date('i');
        echo $hour;
        echo $minute;
        GameService::game_draw();
    }

    // 首页
    public function index()
    {
        $issue = GameService::get_current_issue();
        $next_issue = $issue;
        $result = GameService::get_game_result_by_issue_no_date($issue);

        if (empty($result)) {
            $issue--;
            $next_issue = $issue + 1;

            if ($issue == '2026000') {
                $issue = '2025365';
            }
            $result = GameService::get_game_result_by_issue_no_date($issue);
        } else {
            $next_issue = $next_issue + 1;
        }

        $nums = ['num1', 'num2', 'num3', 'num4', 'num5', 'num6', 'num'];
        $is_refrsh = 0;
        if (!empty($result)) {
            for ($i = 0; $i < count($nums); $i++) {
                $result[$nums[$i] . '_animal_sign'] = GameService::get_animal_sign_by_number($result[$nums[$i]], $result['issue']);
                $result[$nums[$i] . '_state'] = GameService::get_state_by_number($result[$nums[$i]], $result['issue']);
                $result[$nums[$i] . '_color'] = GameService::get_color_by_number($result[$nums[$i]]);
                $result[$nums[$i]] = sprintf('%02d', $result[$nums[$i]]);
            }
            if (empty($result['num'])) {
                $is_refrsh = 1;
            }
        }

        $this->assign([
            'current_issue' => $issue,
            'next_issue' => $next_issue,
            'nums' => $nums,
            'date' => GameService::get_open_date_by_issue($next_issue),
            'is_refrsh' => $is_refrsh,
            'result' => $result,
        ]);

        $issue = GameService::get_current_issue();
        $this->assign([
            'six_animal_sign_list' => GameService::get_game_animal_sign_list(),
            'one_animal_sign_list' => GameService::get_game_one_snimal_sign_list(),
            'six_tail_number_list' => GameService::get_game_six_tail_number_list(),
            'sibuxiang' => ConfigService::get_value_by_name($issue.'_sibuxiang'),
        ]);

        $list = GameService::game_result_list(true);
        $nums = ['num1', 'num2', 'num3', 'num4', 'num5', 'num6', 'num'];

        foreach ($list as $lresult) {
            for ($i = 0; $i < count($nums); $i++) {
                $lresult[$nums[$i] . '_animal_sign'] = GameService::get_animal_sign_by_number($lresult[$nums[$i]], $lresult['issue']);
                $lresult[$nums[$i] . '_state'] = GameService::get_state_by_number($lresult[$nums[$i]], $result['issue']);
                $lresult[$nums[$i] . '_color'] = GameService::get_color_by_number_1($lresult[$nums[$i]]);
                $lresult[$nums[$i]] = sprintf('%02d', $lresult[$nums[$i]]);
            }
            $lresult['issue_name'] = str_replace(date('Y'), '', $lresult['issue']);
            $lresult['date'] = GameService::get_open_date_by_issue($lresult['issue']);
        }

        $this->assign([
            'year' => date('Y'),
            'nums' => $nums,
            'list' => $list,
        ]);
        return $this->fetch();
    }

    // 游戏
    public function game()
    {
        $issue = GameService::get_current_issue();
        $next_issue = $issue;
        $result = GameService::get_game_result_by_issue_no_date($issue);

        if (empty($result)) {
            $issue--;
            $next_issue = $issue + 1;
            if ($issue == '2026000') {
                $issue = '2025365';
            }
            $result = GameService::get_game_result_by_issue_no_date($issue);
        }

        $nums = ['num1', 'num2', 'num3', 'num4', 'num5', 'num6', 'num'];
        $is_refrsh = 0;
        if (!empty($result)) {
            for ($i = 0; $i < count($nums); $i++) {
                $result[$nums[$i] . '_animal_sign'] = GameService::get_animal_sign_by_number($result[$nums[$i]], $result['issue']);
                $result[$nums[$i] . '_state'] = GameService::get_state_by_number($result[$nums[$i]], $result['issue']);
                $result[$nums[$i] . '_color'] = GameService::get_color_by_number($result[$nums[$i]]);
                $result[$nums[$i]] = sprintf('%02d', $result[$nums[$i]]);
            }
            if (empty($result['num'])) {
                $is_refrsh = 1;
            }
        }

        $this->assign([
            'current_issue' => $issue,
            'next_issue' => $next_issue,
            'nums' => $nums,
            'date' => GameService::get_open_date_by_issue($next_issue),
            'is_refrsh' => $is_refrsh,
            'result' => $result,
        ]);
        return $this->fetch();
    }
    
    // 游戏
    public function game_json()
    {
        $issue = GameService::get_current_issue();
        $next_issue = $issue;
        $result = GameService::get_game_result_by_issue_no_date($issue);

        if (empty($result)) {
            $issue--;
            $next_issue = $issue + 1;
            if ($issue == '2026000') {
                $issue = '2025365';
            }
            $result = GameService::get_game_result_by_issue_no_date($issue);
        }

        $nums = ['num1', 'num2', 'num3', 'num4', 'num5', 'num6', 'num'];
        $is_refrsh = 0;
        if (!empty($result)) {
            for ($i = 0; $i < count($nums); $i++) {
                $result[$nums[$i] . '_animal_sign'] = GameService::get_animal_sign_by_number($result[$nums[$i]], $result['issue']);
                $result[$nums[$i] . '_state'] = GameService::get_state_by_number($result[$nums[$i]], $result['issue']);
                $result[$nums[$i] . '_color'] = GameService::get_color_by_number($result[$nums[$i]]);
                $result[$nums[$i]] = sprintf('%02d', $result[$nums[$i]]);
            }
            if (empty($result['num'])) {
                $is_refrsh = 1;
            }
        }
        
        return json([
            'current_issue' => $issue,
            'next_issue' => $next_issue,
            'nums' => $nums,
            'date' => GameService::get_open_date_by_issue($next_issue),
            'is_refrsh' => $is_refrsh,
            'result' => $result,
        ]);
    }

    // 历史
    public function history()
    {
        $list = GameService::game_result_list();
        $nums = ['num1', 'num2', 'num3', 'num4', 'num5', 'num6', 'num'];

        foreach ($list as $result) {
            for ($i = 0; $i < count($nums); $i++) {
                $result[$nums[$i] . '_animal_sign'] = GameService::get_animal_sign_by_number($result[$nums[$i]], $result['issue']);
                $result[$nums[$i] . '_state'] = GameService::get_state_by_number($result[$nums[$i]], $result['issue']);
                $result[$nums[$i] . '_color'] = GameService::get_color_by_number_1($result[$nums[$i]]);
                $result[$nums[$i]] = sprintf('%02d', $result[$nums[$i]]);
            }
            $result['issue_name'] = $result['issue'];
            $result['date'] = GameService::get_open_date_by_issue($result['issue']);
        }

        $this->assign([
            'year' => date('Y'),
            'nums' => $nums,
            'list' => $list,
        ]);
        return $this->fetch();
    }
}

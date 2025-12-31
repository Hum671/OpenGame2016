<?php
/**
 * 微信通知
 */
namespace app\utils\wechat;

use think\facade\Cache;
use app\lib\exception\ApiException;

class Notice
{
    // 获取凭证
    public function access()
    {
        $access = Cache::get(WECHAT_ACCESS);
        if (empty($access)) {
            $appid = config('wechat.appid');
            $appsecret = config('wechat.secret');
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";

            $results = fetchGet($url);
            $json = json_decode($results, true);
            Cache::set(WECHAT_ACCESS, $json['access_token'], $json['expires_in']);
            $access = $json['access_token'];
        }

        return $access;
    }

    // 发送 openid, tid, page, data
    public function send($params)
    {
        $access = $this->access();
        $url = "https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token={$access}";

        $page = config('app.app_debug') ? 'developer' : 'formal';
        $data = [
            'access_token'  => $access,
            'touser'        => $params['openid'],
            'template_id'   => $params['tid'],
            'page'          => $params['page'],
            'data'          => $this->templateData($params['tid'], $params['data']),
            'miniprogram_state' => $page
        ];
        $result = fetchPost($url, json_encode($data));
        $json = json_decode($result, true);

        if ($json['errcode']) {
            $api = [
                'code'  => 0,
                'msg'   => '发送通知失败',
                'error_msg' => $json
            ];
            return $api;
        }
        return $json;
    }

    // 模版数据
    public function templateData($tid, $data)
    {
        // 1.合同到期提醒
        if ($tid == config('wechat.sms_c_expires')) {
            // 提醒房东 count(3)
            return [
                'thing1' => [
                    'value' => $data[0] // 社区房间号
                ],
                'thing2' => [
                    'value' => $data[1] // 承租人(租客)
                ],
                'time3' => [
                    'value' => $data[2] // 到期时间
                ],
                'thing4' => [
                    'value' => '合同即将到期，请在小程序提交续租申请'
                ]
            ];
        }

        // 2.提现审核通知
        if ($tid == config('wechat.sms_w_audio')) {
            // 提醒房东和租客 count(5)
            return [
                'phrase1' => [
                    'value' => $data[0] // 审核结果
                ],
                'amount2' => [
                    'value' => $data[1] // 提现金额
                ],
                'phrase3' => [
                    'value' => $data[2] // 审核状态
                ],
                'time5' => [
                    'value' => $data[3] // 申请时间
                ],
                'thing4' => [
                    'value' => $data[4] // 备注
                ]
            ];
        }

        // 3.房屋到期提醒
        if ($tid == config('wechat.sms_h_expires')) {
            // 提醒租客 count(2)
            return [
                'thing1' => [
                    'value' => $data[0] // 房屋信息
                ],
                'thing2' => [
                    'value' => $data[1] // 到期时间
                ]
            ];
        }

        // 4.房租支付通知
        if ($tid == config('wechat.sms_h_notice')) {
            // 提醒租客 count(2)
            return [
                'amount1' => [
                    'value' => $data[0] // 支付金额
                ],
                'thing4' => [
                    'value' => $data[1] // 备注信息,说明哪里的房租快到期
                ]
            ];
        }

        // 5.收租提醒
        if ($tid == config('wechat.sms_e_notice')) {
            // 提醒房东 count(5)
            return [
                'thing1' => [
                    'value' => $data[0] // 收租名称，如：xx小区xx房子
                ],
                'name2' => [
                    'value' => $data[1] // 租客名称
                ],
                'phone_number3' => [
                    'value' => $data[2] // 租客手机号
                ],
                'date4' => [
                    'value' => $data[3] // 收租时间
                ],
                'thing5' => [
                    'value' => $data[4] // 备注
                ]
            ];
        }

        // 6.审核结果通知
        if ($tid == config('wechat.sms_h_audio')) {
            // 提醒房东 count(4)
            return [
                'thing2' => [
                    'value' => $data[0] // 申请项目
                ],
                'date4' => [
                    'value' => $data[1] // 处理时间
                ],
                'thing18' => [
                    'value' => $data[2] // 审核类型
                ],
                'thing19' => [
                    'value' => $data[3] // 审核结果
                ]
            ];
        }

        // 7.房单快到期提醒
        if ($tid == config('wechat.sms_h_iexpires')) {
            // 提醒房东和租客 count(2)
            return [
                'thing1' => [
                    'value' => $data[0] // 房屋信息
                ],
                'time2' => [
                    'value' => $data[1] // 到期时间
                ]
            ];
        }

        // 8.退款通知
        if ($tid == config('wechat.sms_a_refund')) {
            // 提醒房东和租客 count(3)
            return [
                'thing2' => [
                    'value' => $data[0]           // 商品名称
                ],
                'amount3' => [
                    'value' => $data[1].'元'      // 退款金额
                ],
                'time5' => [
                    'value' => date('Y-m-d H:m') // 退款时间
                ],
                'thing6' => [
                    'value' => $data[2]          // 备注
                ]
            ];
        }
    }
}

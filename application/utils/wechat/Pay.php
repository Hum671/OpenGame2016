<?php
/**
 * 微信支付
 */
namespace app\utils\wechat;

use app\lib\exception\ApiException;
use app\lib\exception\CException;
use app\utils\publics\Classes;

class Pay
{
    private $payHost; // 支付接口
    private $data;

    /**
     * - $data
     * - desc       描述
     * - order_id   订单id
     * - amount     支付金额(单位分)
     * - openid     微信openid
     * - notify_url 通知地址
     * - client_ip  客户端ip
     * - attach     附加参数 json字符串
     */
    public function __construct($data)
    {
        CException::missing($data, 'desc');
        CException::missing($data, 'order_id');
        CException::missing($data, 'amount');
        CException::missing($data, 'openid');
        CException::missing($data, 'notify_url');
        CException::missing($data, 'attach');

        $this->payHost = 'https://api.mch.weixin.qq.com/v3/pay/transactions/jsapi';
        $this->data = $data;
    }

    // 微信支付参数
    private function params()
    {
        $array = [
            'appid'         => config('wechat.appid'),
            'mchid'         => config('wechat.mchid'),
            'description'   => $this->data['desc'],
            'out_trade_no'  => $this->data['order_id'],
            'notify_url'    => config('app.app_host').'/api/asyn.'.$this->data['notify_url'],
            'attach'        => $this->data['attach'],
            'amount'        => array(
                'total'     => $this->data['amount'],
                'currency'  => 'CNY'
            ),
            'payer'         => array(
                'openid'    => $this->data['openid']
            ),
            'scene_info'    => array(
                'payer_client_ip' => $this->data['client_ip']
            )
        ];
        return $array;
    }

    // 生成sign
    public function sign()
    {
        $noncestr = strtoupper(Classes::signString());
        $data   = json_encode($this->params());
        $time   = time();
        $urlarr = parse_url($this->payHost);
        $message = "POST"."\n".$urlarr['path']."\n".$time."\n".$noncestr."\n".$data."\n";

        $sign = $this->getSign($message);
        $params = [
            'mchid'     => config('wechat.mchid'),
            'serial_no' => config('wechat.apikey'),
            'nonce_str' => $noncestr,
            'timestamp' => $time,
            'signature' => $sign
        ];
        return $params;
    }

    // 请求参数转换
    public function signToString($signArray)
    {
        $count = 0;
        $string = "";
        foreach ($signArray as $k => $v) {
            $count++;
            $string .= "{$k}=\"{$v}\"";
            if ($count < count($signArray)) {
                $string .= ",";
            }
        }
        return $string;
    }

    // 获取getPrepayId
    public function getPrepayId()
    {
        $sign = $this->sign();
        $data = $this->params();
        $obj  = fetchPost($this->payHost, json_encode($data), $this->signToString($sign), true);
        $results = [
            'code' => 0,
            'msg'  => '请求支付错误'
        ];

        $array = json_decode($obj, true);
        if (empty($array['prepay_id'])) {
            $results['error_msg'] = $array;
            throw new ApiException($results);
        }

        // 配置微信调起支付
        $package = "prepay_id={$array['prepay_id']}";
        $str = config('wechat.appid')."\n".$sign['timestamp']."\n".$sign['nonce_str']."\n{$package}\n";
        $array = [
            'timeStamp'     => $sign['timestamp'],
            'nonceStr'      => $sign['nonce_str'],
            'package'       => $package,
            'signType'      => 'RSA',
            'paySign'       => $this->getSign($str)
        ];
        return $array;
    }

    // 打开证书
    private function getSign($message) {
        $filepath = 'static/mchcert/zzwx_key.pem';
        $key = file_get_contents($filepath);
        return getSha256WithRSA($message, $key);
    }
}

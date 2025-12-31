<?php
namespace app\utils\sms;

use app\utils\sms\Code;
use app\lib\exception\ApiException;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Ali
{
    // 请求域名
    private $host;

    // 手机号码
    private $phone;

    public function __construct($phone)
    {
        $this->host = 'dysmsapi.aliyuncs.com';
        $this->phone = $phone;

        AlibabaCloud::accessKeyClient(config('aliyun.access_key_id'), config('aliyun.access_key_secret'))
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
    }

    // 发送推送短信
    public function push($tmp, $data)
    {
        $$array = [
            'PhoneNumbers' => $this->phone,
            'SignName' => config('aliyun.sms_sign_name'),
            'TemplateCode' => $tmp,
            'TemplateParam' => $data,
            'RegionId' => 'cn-hangzhou'
        ];

        $request = [];
        $results = [
            'code' => 0,
            'msg'  => '请求短信繁忙,请稍后再试'
        ];
        try {
            $request = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host($this->host)
                ->options(['query' => $array])
                ->request();
        } catch (ClientException $e) {
            $results['error_msg'] = $e->getErrorMessage();
            return $results;
        } catch (ServerException $e) {
            $results['error_msg'] = $e->getErrorMessage();
            return $results;
        }

        try {
            $callback = $request->toArray();
            if ($callback['Code'] == 'OK' && $callback['Message'] == 'OK') {
                return ['code' => 200, 'success' => 'Success'];
            }
            $results['error_msg'] = "{code: {$callback['Code']}, msg: {$callback['Message']}}";
            return $results;
        } catch(\Throwable $e) {
            $results['error_msg'] = $e->error_msg;
            return $results;
        }
    }

    // 发送短信
    public function send($templateCode)
    {
        $code = new Code($this->phone, $templateCode);

        $array = [
            'PhoneNumbers' => $this->phone,
            'SignName' => config('aliyun.sms_sign_name'),
            'TemplateCode' => $templateCode,
            'TemplateParam' => $this->setCode($code->build()),
            'RegionId' => 'cn-hangzhou'
        ];
        $request = [];
        $results = [
            'code' => 0,
            'msg'  => '请求短信验证码繁忙,请稍后再试'
        ];
        try {
            $request = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host($this->host)
                ->options(['query' => $array])
                ->request();
        } catch (ClientException $e) {
            $results['error_msg'] = $e->getErrorMessage();
            throw new ApiException($results);
        } catch (ServerException $e) {
            $results['error_msg'] = $e->getErrorMessage();
            throw new ApiException($results);
        }

        try {
            $callback = $request->toArray();
            if ($callback['Code'] == 'OK' && $callback['Message'] == 'OK') {
                return true;
            }
            $results['error_msg'] = "{code: {$callback['Code']}, msg: {$callback['Message']}}";
            throw new ApiException($results);
        } catch(\Throwable $e) {
            $results['error_msg'] = $e->error_msg;
            throw new ApiException($results);
        }
    }

    // 设置模版变量Code
    private function setCode($code)
    {
        $array = ['code' => $code];
        return json_encode($array);
    }
}

<?php
/**
 * 人脸服务
 */
namespace app\utils\aliyun;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use app\lib\exception\ApiException;

class Face
{
    private $result;

    public function __construct()
    {
        $this->result = [
            'code'  => 0,
            'msg'   => '识别失败'
        ];

        AlibabaCloud::accessKeyClient(config('aliyun.access_key_id'), config('aliyun.access_key_secret'))
            ->regionId('cn-shanghai')
            ->asDefaultClient();
    }

    // 识别图中是否存在人脸
    public function isFace($image)
    {
        try {
            $result = AlibabaCloud::rpc()
                ->product('facebody')
                ->version('2019-12-30')
                ->action('DetectBodyCount')
                ->method('POST')
                ->host('facebody.cn-shanghai.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-shanghai",
                        'ImageURL' => $image
                    ]
                ])
                ->request();

        } catch (ClientException $e) {
            $this->result['error_msg'] = $e->getErrorMessage();
            throw new ApiException($this->result);
        } catch (ServerException $e) {
            $this->result['error_msg'] = $e->getErrorMessage();
            throw new ApiException($this->result);
        }

        try {
            $callback = $result->toArray();
            if (!empty($callback['Data'])
                && $callback['Data']['PersonNumber'] == 1) {
                return true;
            }
            $this->result['error_msg'] = $result;
            throw new ApiException($this->result);
        } catch(\Throwable $e) {
            $this->result['error_msg'] = '解析json失败';
            throw new ApiException($this->result);
        }
    }

    // 对比人脸
    public function compare($img1, $img2)
    {
        try {
            $result = AlibabaCloud::rpc()
              ->product('facebody')
              ->version('2019-12-30')
              ->action('CompareFace')
              ->method('POST')
              ->host('facebody.cn-shanghai.aliyuncs.com')
              ->options([
                'query' => [
                    'RegionId' => "cn-shanghai",
                    'ImageURLA' => $img1,
                    'ImageURLB' => $img2
                ]])
              ->request();
        } catch (ClientException $e) {
            $this->result['error_msg'] = $e->getErrorMessage();
            throw new ApiException($this->result);
        } catch (ServerException $e) {
            $this->result['error_msg'] = $e->getErrorMessage();
            throw new ApiException($this->result);
        }

        try {
            $callback = $result->toArray();
            if (!empty($callback['Data'])
                && !empty($callback['Data']['Confidence'])) {
                return $callback['Data']['Confidence'];
            }
            $this->result['error_msg'] = $result;
            throw new ApiException($this->result);
        } catch(\Throwable $e) {
            $this->result['error_msg'] = '解析json失败';
            throw new ApiException($this->result);
        }
    }
}

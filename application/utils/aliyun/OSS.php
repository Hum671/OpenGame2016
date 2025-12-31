<?php
namespace app\utils\aliyun;

use OSS\OssClient;
use OSS\Core\OssException;
use app\lib\exception\ApiException;

class OSS
{
    private $keyId;
    private $keySecret;
    private $endpoint;
    private $client;
    private $result;
    private $bucket;
    private $file;

    public function __construct()
    {
        $this->keyId = config('aliyun.access_key_id');
        $this->keySecret = config('aliyun.access_key_secret');
        $this->endpoint = 'https://oss-cn-shanghai.aliyuncs.com';
        $this->bucket = 'guanghuihihui';
        $this->result = [
            'code'  => 0,
            'msg'   => '认证失败'
        ];
        $this->client = new OssClient($this->keyId, $this->keySecret, $this->endpoint);
    }

    // 获取文件信息
    public function get()
    {
        $obj = $this->client->getObjectMeta($this->bucket, $this->file);
        return $obj['info']['url'];
    }

    // 上传
    public function upload($filename, $path)
    {
        try {
            $pos = stripos($path, '.');
            $format = substr($path, $pos);
            $this->file = $filename."{$format}";

            $this->client->uploadFile($this->bucket, $this->file, $path);
        } catch (OssException $e) {
            $this->result['error_msg'] = $e->getMessage();
            throw new ApiException($this->result);
        }

        return true;
    }
}

<?php
/**
 * 公共类
 */
namespace app\utils\publics;

use think\facade\Log;

class Classes
{
    // 转换
    public static function replcaeALinkAll($str)
    {
        $str = Classes::replaceALink($str, '岑巩', '/');
        $str = Classes::replaceALink($str, '凯里', '/');
        $str = Classes::replaceALink($str, '施秉', '/');
        $str = Classes::replaceALink($str, '黎平', '/');
        $str = Classes::replaceALink($str, '黔东南', '/');
        $str = Classes::replaceALink($str, '榕江', '/');
        $str = Classes::replaceALink($str, '锦屏', '/');
        $str = Classes::replaceALink($str, '丹寨', '/');
        $str = Classes::replaceALink($str, '麻江', '/');
        $str = Classes::replaceALink($str, '剑河', '/');
        $str = Classes::replaceALink($str, '公务员', '/');
        $str = Classes::replaceALink($str, '天柱', '/');
        $str = Classes::replaceALink($str, '从江', '/');
        $str = Classes::replaceALink($str, '黄平', '/');
        $str = Classes::replaceALink($str, '三穗', '/');
        $str = Classes::replaceALink($str, '雷山', '/');

        $str = Classes::replaceALink($str, '事业单位', '/information/career.html');
        $str = Classes::replaceALink($str, '招聘', '');

        // $str = preg_replace('/www\.gzdysx\.com/', 'qdnzsksxxw.com?', $str);

        return $str;
    }

    // 替换A链接内容
    public static function replaceALink($str, $name, $url)
    {
        $url = config('app.app_host').$url;

        $pattern = '/<a.{30,120}?>'.$name.'<\/a>/';
        $replace = '<a target="_blank" href="'.$url.'">'.$name.'</a>';
        $str = preg_replace($pattern, $replace, $str);
        return $str;
    }

    /**
     * 32全数字唯一订单
     * @return [type] [description]
     */
    public static function order()
    {
        $d = explode(" ", microtime());
        return date('YmdHis').substr($d[0], 2).rand(1000000000, $d[1]);
    }

    /**
     * 随机字符串
     */
    public static function signString($param = 32)
    {
        $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $key = "";
        for($i = 0; $i < $param; $i++)
        {
            $key .= $str{mt_rand(0,32)};
        }
        return $key;
    }

    /**
     * 写入日志
     * @param  [type] $filename [description]
     * @param  [type] $content  [description]
     * @return [type]           [description]
     */
    public static function logWrite($filename, $content)
    {
        $path = $_SERVER['DOCUMENT_ROOT'].'/runtime/logs/'.$filename.'/';
        Log::init(['type' => 'File', 'path' => $path]);
        Log::write($content, $filename);
    }

    /**
     * 微信回调解密
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public static function wechatDecrypt($str) {
        $str    = htmlspecialchars_decode($str, ENT_COMPAT);
        $post   = json_decode($str, true);
        $key    = config('wechat.apiv3Key');
        $text   = base64_decode($post['resource']['ciphertext']);
        $str    = sodium_crypto_aead_aes256gcm_decrypt($text, $post['resource']['associated_data'], $post['resource']['nonce'], $key);
        return json_decode($str, true);
    }
}

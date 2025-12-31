<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use think\facade\Cache;

// 常量配置
const ADMIN_LOCK_COUNT  = 'admin_lock_count';   // 后台登录错误锁
const IP_LOCK_COUNT     = 'ip_lock_count';      // 用户ip锁
const SMS_CODE_LOCK     = 'sms_code_lock';      // 验证码锁
const USER_LOGIN_LOCK   = 'user_login_lock';    // 用户登录锁
const WECHAT_ACCESS     = 'wechat_access';      // 微信凭证

const CODE_PHONE = 'code_phone'; // 手机验证码

// 异步锁
const ASYN_LOCAK_PAY = 'alp';   // 支付异步锁

// 附件类型
const ATTACH_TYPE_IMAGE = 'image';  // 图片
const ATTACH_TYPE_VIDEO = 'video';  // 视频
const ATTACH_TYPE_FILE  = 'file';   // 文件

// 内容类型
const C_TYPE = [];
// 内容类型
const C_TYPE_CITY = [];
const C_TYPE_NAME = [];
const C_TYPE_CITY_NAME = [];

// 支付类型
const UFL_TYPE_PAY          = 'utp';    // 支付
const UFL_TYPE_BACK         = 'utb';    // 退回押金
const UFL_TYPE_GETBACK      = 'utc';    // 获得退回的押金
const UFL_TYPE_WITHDRAWAL   = 'utw';    // 提现
const UFL_TYPE_PLATFORM     = 'utm';    // 平台利润
const UFL_TYPE_SALESMAN     = 'uts';    // 业务员利润
const UFL_TYPE_LANDLORD     = 'utl';    // 房东利润

// 全国地区
const AREA_LIST = array('北京', '天津', '河北', '山西', '内蒙古', '辽宁', '吉林', '黑龙江', '上海', '江苏', '浙江', '安徽', '福建', '江西', '山东', '河南', '湖北', '湖南', '广东', '广西', '海南', '重庆', '四川', '贵州', '云南', '西藏', '陕西', '甘肃', '青海', '宁夏', '新疆', '台湾', '香港', '澳门');

// 前端默认LIMIT
const APP_LIMIT = 15;

/**
 * 后台访问时 获取客户IP
 * @return string
 */
function getIp()
{
    if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']) {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ip[0]);
        $ip = $ip ? $ip : '127.0.0.1';
    }else{
        $ip = '127.0.0.1';
    }
    return $ip;
}

/**
 * post请求
 * @param  [type] $url      [description]
 * @param  [type] $data     [description]
 * @param  [type] $headers  [description]
 * @return [type]           [description]
 */
function fetchPost($url, $data, $sign = '', $wechat = false)
{
    $ch = curl_init();

    $headers = [];
    if ($wechat) {
        array_push($headers, "Content-Type:application/json");
        array_push($headers, "Accept:application/json");
        array_push($headers, "User-Agent:*/*");
        array_push($headers, "Authorization: WECHATPAY2-SHA256-RSA2048 ".$sign);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // 忽略证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}

/**
 * get请求
 * @param  [type] $url  [description]
 * @return [type]       [description]
 */
function fetchGet($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // 忽略证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}

/**
 * ip锁
 */
function lock($ip) {
    $key = "{$ip}_LOCK";
    $count = cache($key);
    if ($count >= 10) {
        return true;
    }
    if (empty($count)) {
        $count = 0;
    }
    cache($key, $count + 1, 3600);
    return false;
}

/**
 * JWT加密
 * @param  [type] $data array需要加密的字符串, 必须包含ID
 * @return [type]         [description]
 */
function buildJwt($data)
{
    $key = 'jwt_'.$data['id'];
    $jwt_sign = Cache::get($key);
    if (!empty($jwt_sign)) {
        return $jwt_sign;
    }

    $builder = new Builder();
    $signer  = new Sha256();

    $builder->setIssuer(config()['app']['app_name']);
    $builder->setAudience(config()['app']['app_name']);
    $builder->setIssuedAt(time());
    $builder->setNotBefore(time() + 60);
    $builder->setExpiration(time() + 3600 * 24 * 7);
    $builder->set('data', json_encode($data));
    $builder->sign($signer, '__TOKEN_SIGN_KEY__');
    $token = (string)$builder->getToken();

    Cache::set($key, $token, 3600 * 24 * 7);
    return $token;
}

/**
 * 解析jwt
 * @param  $jwt
 * @return UserId
 */
function parseJwt($jwt)
{
    try {
        $token = (new Parser())->parse($jwt);
        $data = $token->getClaim('data');
        $array = json_decode($data, true);
        return $array['id'];
    } catch(RuntimeException $e) {
        return 0;
    }
}

/**
 * 验证JWT
 * @param  [type] $string 需要解密验证的字符串
 * @param  [type] $token  [description]
 * @return [type]         [description]
 */
function verifyJwt($data, $token)
{
    $key = 'jwt_'.$data['id'];
    $jwt_sign = Cache::get($key);

    if (empty($jwt_sign) || $token != $jwt_sign) {
        return false;
    }

    return true;
}

/**
 * 获取入站关键词
 * @param  [type] $url      [description]
 * @param  [type] $kw_start [description]
 * @return [type]           [description]
 */
function getKeyword($url, $kw_start)
{
    $start = stripos($url, $kw_start);
    $url = substr($url, $start + strlen($kw_start));
    $start = stripos($url, '&');
    if ($start > 0) {
        $s_s_keyword = substr($url,0,$start);
    } else {
        $s_s_keyword = substr($url,0);
    }

    return $s_s_keyword;
}

/**
 * 获取入站涞源
 * @return [type] [description]
 */
function getFrom()
{
    $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    if (empty($url)) {
        return [
            'from'      => '',
            'keyword'   => ''
        ];
    }

    $search_1 = "google.com"; //q= utf8
    $search_2 = "baidu.com";  //wd= gbk
    $search_3 = "yahoo.cn";   //q= utf8
    $search_4 = "sogou.com";  //query= gbk
    $search_5 = "soso.com";   //w= gbk
    $search_6 = "bing.com";   //q= utf8
    $search_7 = "youdao.com"; //q= utf8

    $google = preg_match("/\b{$search_1}\b/", $url); //记录匹配情况，用于入站判断。
    $baidu  = preg_match("/\b{$search_2}\b/", $url);
    $yahoo  = preg_match("/\b{$search_3}\b/", $url);
    $sogou  = preg_match("/\b{$search_4}\b/", $url);
    $soso   = preg_match("/\b{$search_5}\b/", $url);
    $bing   = preg_match("/\b{$search_6}\b/", $url);
    $youdao = preg_match("/\b{$search_7}\b/", $url);
    $s_s_keyword = "";
    $bul = $_SERVER['HTTP_REFERER'];

    //获取没参数域名
    preg_match('@^(?:http://)?([^/]+)@i', $bul, $matches);
    $burl = $matches[1];

    $curl  = "www.qdnzsksxxw.com";
    $ncurl = "qdnzsksxxw.com";
    $data  = [];
    if ($burl != $curl && $burl != $ncurl) {
        if ($google) {
            $s_s_keyword = get_keyword($url, 'q='); //关键词前的字符为"q="。
            $s_s_keyword = urldecode($s_s_keyword);
            $urlname = "谷歌搜索";
            $data = [
                'from'      => $urlname,
                'keyword'   => $s_s_keyword
            ];
        } else if ($baidu) {
            $s_s_keyword = get_keyword($url, 'wd='); //关键词前的字符为"wb="。
            $s_s_keyword = urldecode($s_s_keyword);
            $s_s_keyword = iconv("GBK", "UTF-8", $s_s_keyword);
            $urlname = "百度搜索";
            $data = [
                'from'      => $urlname,
                'keyword'   => $s_s_keyword
            ];
        } else if ($yahoo) {
            $s_s_keyword = get_keyword($url, 'q='); //关键词前的字符为"wb="。
            $s_s_keyword = urldecode($s_s_keyword);
            $urlname = "雅虎搜索";
            $data = [
                'from'      => $urlname,
                'keyword'   => $s_s_keyword
            ];
        } else if ($sogou) {
            $s_s_keyword = get_keyword($url, 'w='); //关键词前的字符为"wb="。
            $s_s_keyword = urldecode($s_s_keyword);
            $s_s_keyword = iconv("GBK", "UTF-8", $s_s_keyword);
            $urlname = "搜狗搜索";
            $data = [
                'from'      => $urlname,
                'keyword'   => $s_s_keyword
            ];
        } else {
            $data = [
                'from'      => $burl,
                'keyword'   => ""
            ];
        }
    } else {
        $data = [
            'from'      => $burl,
            'keyword'   => ""
        ];
    }
    return $data;
}

/**
 * 处理limit
 * @param  [type] $limit [description]
 * @return [type]        [description]
 */
function handleLimit($limit)
{
    $cLimit = Cache::get('limit');
    $dLimit = 30;

    $gLimit = empty($cLimit) ? $dLimit : $cLimit;
    $limit  = empty($limit) ? $gLimit : $limit;
    Cache::set('limit', $limit, 24*60*60);
    return $limit;
}

/**
 * 处理公共参数
 * @param  [type] $req [description]
 * @return [type]      [description]
 */
function handleDefaultParams($req)
{
    $limit = empty($req['limit']) ? null : $req['limit'];
    $params = $req;
    $params['limit'] = handleLimit($limit);
    $params['page']  = empty($req['page']) ? 1 : $req['page'];
    return $params;
}

/**
 * 分页条数选项
 * @param  [type] $limit 每页多少条
 * @return [type]        [description]
 */
function selectLimit($limit)
{
    $count = 5;
    $option = '';
    for ($i = 1; $i <= $count; $i++) {
        if ($limit == $i*10) {
            $option .= "<option selected value='".($i*10)."'>".($i*10)."条/页</option>";
        } else {
            $option .= "<option value='".($i*10)."'>".($i*10)."条/页</option>";;
        }
    }

    $html  = '<select onchange="selectlimit()" class="select-page" name="limit">';
    $html .= $option;
    $html .= '</select>';

    return $html;
}

/**
 * 生成sha256WithRsa签名
 * @param  [type] $content     [description]
 * @param  [type] $private_key [description]
 * @return [type]              [description]
 */
function getSha256WithRSA($content, $private_key)
{
    $private_key = openssl_pkey_get_private($private_key);
    openssl_sign($content, $signature, $private_key, "SHA256");
    openssl_free_key($private_key);
    return base64_encode($signature);
}

/**
 * 字符串转数组
 */
if (!function_exists('mb_str_split'))
{
    function mb_str_split($string, $split_length = 1)
    {
        $array = [];
        $strlen = mb_strlen($string, 'UTF-8');
        for ($i = 0; $i < $strlen; $i += $split_length) {
            $array[] = mb_substr($string, $i, $split_length, 'UTF-8');
        }
        return $array;
    }
}

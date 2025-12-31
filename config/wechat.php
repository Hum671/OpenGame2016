<?php
// +----------------------------------------------------------------------
// | 微信配置
// +----------------------------------------------------------------------

return [
    'url'           => 'https://api.weixin.qq.com',
    'mchid'         => '',
    'appid'         => 'wxbcbbfa0b5c70f27e',
    'secret'        => 'cc265480bc38ab6d26f94d146c06f991',
    'grant_type'    => 'authorization_code',
    'apikey'        => '',
    'apiv3Key'      => '',

    // 订阅消息模版
    'sms_c_expires'     => 'Zjt037-RrwkweBOt_iLTnwJslfzZuOFEjJez1sdmt3w',   // 1.合同到期提醒
    'sms_w_audio'       => 'lvgC7aKAo4qxSAIUyG85QtQK7-zZIjMaKWxz7AaGm7M',   // 2.提现审核通知
    'sms_h_expires'     => 'SGvqKo17NAuh2HgYJVH_pybniokjOek6WxpUnjx2UrA',   // 3.房屋到期提醒
    'sms_h_notice'      => 'HkfZFQlmE55dzjojcoB1z23AayAeGwiuZb8W3BEKYjg',   // 4.房子支付通知
    'sms_e_notice'      => '27QmOZudN6GoGp0s5P9jWjiEa43DkcLUWpG5Q2qGE6M',   // 5.收租提醒
    'sms_h_audio'       => 'nef-ybwCIJjcKuYzsinPVNBAG_v7UR5bMJTeFoqznx8',   // 6.房子审核通知
    'sms_h_iexpires'    => 'qmUnY6cqpacu5Rzm7dKRpiAGlMWKxXD9tyw7QQmL04U',   // 7.房单快到期提醒
    'sms_a_refund'      => 'm1FPqnESQC-FfNcjmHS6p768Lolp3Gj0fcyuzAG7Imo',   // 8.退款通知
];

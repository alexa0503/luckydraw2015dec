<?php
namespace AppBundle\Helper;
use AppBundle\Helper;
class SMS
{
    static public function send($prize, $mobile)
    {
        $array = array(
            1=>'见顾碧婷',
            2=>'平板电脑',
            3=>'空气净化器',
            4=>'运动手表',
            5=>'金项链',
            6=>'格瓦拉电影票',
            7=>'郭碧婷笔记本',
            8=>'洗漱包',
        );
        $url = 'http://101.227.67.142:86/sms/smsInterface.do';
        $data = array(
            'username'=>'miketest',
            'password'=>'123456',
            'mobile'=>$mobile,
            'content'=>'恭喜你赢得舒蕾”美丽心愿，从头开始”活动的奖品-['.$array[$prize].']【舒蕾秀发护理】',
        );
        $result = Helper\HttpClient::post($url, http_build_query($data));
        $handle = fopen('sms.log','a+');
        fwrite($handle,$mobile.'|'.$prize."\n");
        fclose($handle);
        return $result;
    }
}
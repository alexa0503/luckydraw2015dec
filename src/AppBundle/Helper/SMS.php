<?php
namespace AppBundle\Helper;
use AppBundle\Helper;
class SMS
{
    static public function send($em, $params)
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
        if($params['type'] == 0){
            $content = '【舒蕾秀发护理】恭喜你赢得舒蕾”美丽心愿，从头开始”活动奖品-['.$array[$params['prize']].']，请回复准确寄送地址。';
        }
        else{
            $content = '【舒蕾秀发护理】恭喜你赢得舒蕾”美丽心愿，从头开始”活动奖品-['.$array[$params['prize']].']。';
        }
        $data = array(
            'username'=>'miketest',
            'password'=>'123456',
            'mobile'=>$params['mobile'],
            'content'=>$content,
        );
        Helper\HttpClient::post($url, http_build_query($data));
        $sms = $em->getRepository('AppBundle:SMS')->findOneBy(array('info'=>$params['info']));
        if( null == $sms){
            $sms = new \AppBundle\Entity\SMS();
            $sms->setInfo($params['info']);
            $sms->setPrize($params['prize']);
            $sms->setContent($data['content']);
            $sms->setMobile($params['mobile']);
            $sms->setType($params['type']);
        }
        $sms->setCreateTime(new \DateTime('now'));
        $em->persist($sms);
        $em->flush();
        /*
        if( $handle = @fopen('sms.log','a+')){
            fwrite($handle,$mobile.'|'.$prize."\n");
            fclose($handle);
        }
        */

        //return $result;
    }
}
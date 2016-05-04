<?php
namespace AppBundle\Helper;
use AppBundle\Helper;
class SMS
{
    static public function send($em, $params)
    {
        $array = array(
            0=>'测试',
            1=>'见顾碧婷',
            2=>'平板电脑',
            3=>'空气净化器',
            4=>'运动手表',
            5=>'金项链',
            6=>'格瓦拉电影票',
            7=>'郭碧婷笔记本',
            8=>'洗漱包',
        );
        $url = 'https://sms-api.luosimao.com/v1/send.json';
        if($params['type'] == 0){
            $content = '恭喜你赢得舒蕾”美丽心愿，从头开始”活动奖品-['.$array[$params['prize']].']，请回复准确寄送地址。【舒蕾秀发护理】';
        }
        else{
            $content = '恭喜你赢得舒蕾”美丽心愿，从头开始”活动奖品-['.$array[$params['prize']].']。【舒蕾秀发护理】';
        }
        //$content = '舒蕾的幸运儿，快回复你的收奖地址。【舒蕾秀发护理】';
        $data = array(
            'key'=>'api:key-80156cec18eccad9639d392781da37d2',
            'mobile'=>$params['mobile'],
            'content'=>$content,
        );
        $res = Helper\HttpClient::sms($url, $data);
        $res = json_decode($res, true);
        if( $res && $res['error'] == 0){
            $sms = $em->getRepository('AppBundle:SMS')->findOneBy(array('info'=>$params['info']));
            if( null == $sms){
                $sms = new \AppBundle\Entity\SMS();
                $sms->setInfo($params['info']);
                $sms->setPrize($params['prize']);
                $sms->setContent($data['content']);
                $sms->setMobile($params['mobile']);
                $sms->setType($params['type']);
                $sms->setCreateTime(new \DateTime('now'));
                $em->persist($sms);
                $em->flush();
                $result = array('ret'=>0,'msg'=>'');
            }
            else{
                $result = array('ret'=>0,'msg'=>'');
                //$result = array('ret'=>1000,'msg'=>'此信息不存在');
            }
        }
        else{
            $result = array('ret'=>2000,'msg'=>'短信发送失败~');

        }

        /*
        if( $handle = @fopen('sms.log','a+')){
            fwrite($handle,$mobile.'|'.$prize."\n");
            fclose($handle);
        }
        */

        return $result;
    }
}
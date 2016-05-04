<?php
namespace AppBundle\Helper;
class HttpClient
{
    static public function get($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
        $res = curl_exec($curl);
        curl_close($curl);
        //$res = file_get_contents($url);
        //var_dump($res,file_get_contents($url));
        return $res;
    }
    static public function post($url, $data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置为POST方式
        curl_setopt($curl, CURLOPT_POST, 1);
        //POST数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
    static public function sms($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, HTTP_SSL_VERSION_TLSv1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTP_VERSION  , CURL_HTTP_VERSION_1_0 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPAUTH , CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD  , $data['key']);

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('mobile' => $data['mobile'],'message' => $data['content']));

        $res = curl_exec( $ch );
        //$r = curl_getinfo($ch);
        //$res1  = curl_error( $ch );
        //var_dump($res,$r,$res1);
        curl_close( $ch );
        return $res;
    }
}
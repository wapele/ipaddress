<?php
/*
 *  +---------------------------------------------------------------------
 *  |thinkphp5 ��չ
 *  +---------------------------------------------------------------------
 *  |��д���ڣ�2016-07-23
 *  +---------------------------------------------------------------------
 *  |���ߣ�����̶
 *  +---------------------------------------------------------------------
 *  |��ϵ��386117932
 *  +---------------------------------------------------------------------
*/
namespace think\driver;

class Taobao
{
    protected static $config;
    public function __construct($config=[]){
        self::$config=$config;
        
    }
    public function getipaddress(){
        return self::httpget();
    }
    protected static function httpget(){
        $url="http://ip.taobao.com/service/getIpInfo.php?ip=".self::$config["ip"];
        $content = file_get_contents($url);
        $json = json_decode($content,true);//ת��������
        $json=self::getdata($json);
        return $json;
    }
    //ת��ͳһ����
    protected static function getdata($arr){
        $data["country"]=$arr["data"]["country"];//�й�
        $data["province"]=$arr["data"]["region"];//ʡ��
        $data["city"]=$arr["data"]["city"];//����
        $data["isp"]=$arr["data"]["isp"];//�ƶ�
        $data["ip"]=$arr["data"]["ip"];//ip
        if (self::$config["result"]=="string"){
            $data=$data["province"].$data["city"];
        }
        return $data;
        
        
    }
}
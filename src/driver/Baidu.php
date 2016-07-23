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

class Baidu{
    protected static $config;
    public function __construct($config=[]){
        self::$config=$config;
        
    }
    public function getipaddress(){
        return self::httpget();
    }
    protected static function httpget(){
        $url="http://api.map.baidu.com/location/ip?ak=7IZ6fgGEGohCrRKUE9Rj4TSQ";
        if (self::$config["ip"]){
            $url.="&ip=".self::$config["ip"];
        }
        $content   = file_get_contents($url); 
        
        $json = json_decode($content,true);//ת��������
        $json=self::getdata($json);
        return $json;
    }
    //ת��ͳһ����
    protected static function getdata($arr){
        $data["province"]=$arr["content"]["address_detail"]["province"];//ʡ��
        $data["city"]=$arr["content"]["address_detail"]["city"];//����
        if (self::$config["result"]=="string"){
            $data=$arr["content"]["address"];
        }
        return $data;
        
        
    }
}
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

class Sina{
    protected static $config;
    public function __construct($config=[]){
        self::$config=$config;
        
    }
    public function getipaddress(){
        return self::httpget();
    }
    protected static function httpget(){
        $url="http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json";
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
        $data["country"]=$arr["country"];//�й�
        $data["province"]=$arr["province"];//ʡ��
        $data["city"]=$arr["city"];//����
        if (self::$config["result"]=="string"){
            $data=$arr["province"].$arr["city"];
        }
        return $data;
        
        
    }
}
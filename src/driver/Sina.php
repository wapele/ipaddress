<?php
/*
 *  +---------------------------------------------------------------------
 *  |thinkphp5 扩展
 *  +---------------------------------------------------------------------
 *  |编写日期：2016-07-23
 *  +---------------------------------------------------------------------
 *  |作者：陈序潭
 *  +---------------------------------------------------------------------
 *  |联系：386117932
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
        
        $json = json_decode($content,true);//转化成数组
        $json=self::getdata($json);
        return $json;
    }
    //转换统一数组
    protected static function getdata($arr){
        $data["country"]=$arr["country"];//中国
        $data["province"]=$arr["province"];//省份
        $data["city"]=$arr["city"];//城市
        if (self::$config["result"]=="string"){
            $data=$arr["province"].$arr["city"];
        }
        return $data;
        
        
    }
}
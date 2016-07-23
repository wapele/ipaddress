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
        
        $json = json_decode($content,true);//转化成数组
        $json=self::getdata($json);
        return $json;
    }
    //转换统一数组
    protected static function getdata($arr){
        $data["province"]=$arr["content"]["address_detail"]["province"];//省份
        $data["city"]=$arr["content"]["address_detail"]["city"];//城市
        if (self::$config["result"]=="string"){
            $data=$arr["content"]["address"];
        }
        return $data;
        
        
    }
}
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
        $json = json_decode($content,true);//转化成数组
        $json=self::getdata($json);
        return $json;
    }
    //转换统一数组
    protected static function getdata($arr){
        $data["country"]=$arr["data"]["country"];//中国
        $data["province"]=$arr["data"]["region"];//省份
        $data["city"]=$arr["data"]["city"];//城市
        $data["isp"]=$arr["data"]["isp"];//移动
        $data["ip"]=$arr["data"]["ip"];//ip
        if (self::$config["result"]=="string"){
            $data=$data["province"].$data["city"];
        }
        return $data;
        
        
    }
}
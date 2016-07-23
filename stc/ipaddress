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
namespace think;
class Ipaddress{
    //操作句柄
    protected static $handler = null;
    //配置数组
    protected static $config=[
            "type"=>"taobao",   //taobao sina baidu local
            "result"=>"array", //array string 
    ]; 
    //程序初始化
    public function __construct($config=[]){
        self::$config = array_merge(self::$config, $config);
        
    }
    /*
     * 获取ip地址库
    */
    public static function getipaddress($ip=null){        
        if ($ip){
            //这里需要对IP进行验证
        }
        self::$config = array_merge(self::$config, ["ip"=>$ip]);
        $class         = '\\think\\ipaddress\\driver\\' . ucwords(self::$config["type"]);
        self::$handler = new $class(self::$config);
        
        return self::$handler->getipaddress();
    }
    //
    public static function ipaddress($ip=null){
        foreach(["taobao","sina","baidu","local"] as $v){
            self::$config["type"]=$v;
            $result=self::getipaddress($ip);
            if ($result){
                break;
            }
            return $result;
        }
    }
}

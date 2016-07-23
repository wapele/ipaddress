# ipaddress
获取IP地址库
thinkphp5 的ipaddress 库
#安装
>composer require wapele/ipaddress

#使用
        //方式1 return array
        \think\Ipaddress::getipaddress("112.17.247.154");
        //方式2 return string
        $ip=new \think\Ipaddress(["type"=>"sina",result"=>"string"]);
        $ip::getipaddress("112.17.247.154");
        //方式3 return array
        $ip=new \think\Ipaddress(["type"=>"baidu"]);
        $ip::getipaddress();
        //方式4 return array
        $ip=new \think\Ipaddress(["type"=>"local"]);
        $ip::getipaddress();
        
ps:第一次使用git还不习惯

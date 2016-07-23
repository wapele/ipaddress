<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace think\driver;

class Local{
    /**
     * QQWry.Dat�ļ�ָ��
     *
     * @var resource
     */
    private $fp;

    /**
     * ��һ��IP��¼��ƫ�Ƶ�ַ
     *
     * @var int
     */
    private $firstip;

    /**
     * ���һ��IP��¼��ƫ�Ƶ�ַ
     *
     * @var int
     */
    private $lastip;

    /**
     * IP��¼�����������������汾��Ϣ��¼��
     *
     * @var int
     */
    private $totalip;

    /**
     * ���캯������ QQWry.Dat �ļ�����ʼ�����е���Ϣ
     *
     * @param string $filename
     * @return IpLocation
     */
    protected static $config;
    public function __construct($config=[],$filename = "UTFWry.dat")
    {
        self::$config=$config;
        $this->fp = 0;
        if (($this->fp = fopen(dirname(__FILE__) . '/' . $filename, 'rb')) !== false) {
            $this->firstip = $this->getlong();
            $this->lastip  = $this->getlong();
            $this->totalip = ($this->lastip - $this->firstip) / 7;
        }
    }

    /**
     * ���ض�ȡ�ĳ�������
     *
     * @access private
     * @return int
     */
    private function getlong()
    {
        //����ȡ��little-endian�����4���ֽ�ת��Ϊ��������
        $result = unpack('Vlong', fread($this->fp, 4));
        return $result['long'];
    }

    /**
     * ���ض�ȡ��3���ֽڵĳ�������
     *
     * @access private
     * @return int
     */
    private function getlong3()
    {
        //����ȡ��little-endian�����3���ֽ�ת��Ϊ��������
        $result = unpack('Vlong', fread($this->fp, 3) . chr(0));
        return $result['long'];
    }

    /**
     * ����ѹ����ɽ��бȽϵ�IP��ַ
     *
     * @access private
     * @param string $ip
     * @return string
     */
    private function packip($ip)
    {
        // ��IP��ַת��Ϊ���������������PHP5�У�IP��ַ�����򷵻�False��
        // ��ʱintval��Flaseת��Ϊ����-1��֮��ѹ����big-endian������ַ���
        return pack('N', intval(ip2long($ip)));
    }

    /**
     * ���ض�ȡ���ַ���
     *
     * @access private
     * @param string $data
     * @return string
     */
    private function getstring($data = "")
    {
        $char = fread($this->fp, 1);
        while (ord($char) > 0) {
            // �ַ�������C��ʽ���棬��\0����
            $data .= $char; // ����ȡ���ַ����ӵ������ַ���֮��
            $char = fread($this->fp, 1);
        }
        return $data;
    }

    /**
     * ���ص�����Ϣ
     *
     * @access private
     * @return string
     */
    private function getarea()
    {
        $byte = fread($this->fp, 1); // ��־�ֽ�
        switch (ord($byte)) {
            case 0: // û��������Ϣ
                $area = "";
                break;
            case 1:
            case 2: // ��־�ֽ�Ϊ1��2����ʾ������Ϣ���ض���
                fseek($this->fp, $this->getlong3());
                $area = $this->getstring();
                break;
            default: // ���򣬱�ʾ������Ϣû�б��ض���
                $area = $this->getstring($byte);
                break;
        }
        return $area;
    }
    /**
     * ��ȡ�ͻ���IP��ַ
     * @param integer $type �������� 0 ����IP��ַ 1 ����IPV4��ַ����
     * @param boolean $adv �Ƿ���и߼�ģʽ��ȡ���п��ܱ�αװ��
     * @return mixed
     */
    function get_client_ip($type = 0, $adv = false)
    {
        $type      = $type ? 1 : 0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }
    
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
    
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP��ַ�Ϸ���֤
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
    /**
     * �򻯲���
     *
     * @access public
     * @param string $ip
     * @return string
     */
    public function getipaddress(){
        $result=$this->getlocation(self::$config["ip"]);
        if (self::$config["result"]=="string"){
            $result=$result["country"];
        }
        return $result;
        
    }
    /**
     * �������� IP ��ַ�������������ڵ�����Ϣ
     *
     * @access public
     * @param string $ip
     * @return array
     */
    public function getlocation($ip = '')
    {
        if (!$this->fp) {
            return null;
        }
        // ��������ļ�û�б���ȷ�򿪣���ֱ�ӷ��ؿ�
        if (empty($ip)) {
            $ip = $this->get_client_ip();
        }

        $location['ip'] = gethostbyname($ip); // �����������ת��ΪIP��ַ
        $ip             = $this->packip($location['ip']); // �������IP��ַת��Ϊ�ɱȽϵ�IP��ַ
        // ���Ϸ���IP��ַ�ᱻת��Ϊ255.255.255.255
        // �Է�����
        $l      = 0; // �������±߽�
        $u      = $this->totalip; // �������ϱ߽�
        $findip = $this->lastip; // ���û���ҵ��ͷ������һ��IP��¼��QQWry.Dat�İ汾��Ϣ��
        while ($l <= $u) {
            // ���ϱ߽�С���±߽�ʱ������ʧ��
            $i = floor(($l + $u) / 2); // ��������м��¼
            fseek($this->fp, $this->firstip + $i * 7);
            $beginip = strrev(fread($this->fp, 4)); // ��ȡ�м��¼�Ŀ�ʼIP��ַ
            // strrev����������������ǽ�little-endian��ѹ��IP��ַת��Ϊbig-endian�ĸ�ʽ
            // �Ա����ڱȽϣ�������ͬ��
            if ($ip < $beginip) { // �û���IPС���м��¼�Ŀ�ʼIP��ַʱ
                $u = $i - 1; // ���������ϱ߽��޸�Ϊ�м��¼��һ
            } else {
                fseek($this->fp, $this->getlong3());
                $endip = strrev(fread($this->fp, 4)); // ��ȡ�м��¼�Ľ���IP��ַ
                if ($ip > $endip) { // �û���IP�����м��¼�Ľ���IP��ַʱ
                    $l = $i + 1; // ���������±߽��޸�Ϊ�м��¼��һ
                } else {
                    // �û���IP���м��¼��IP��Χ��ʱ
                    $findip = $this->firstip + $i * 7;
                    break; // ���ʾ�ҵ�������˳�ѭ��
                }
            }
        }

        //��ȡ���ҵ���IP����λ����Ϣ
        fseek($this->fp, $findip);
        $location['beginip'] = long2ip($this->getlong()); // �û�IP���ڷ�Χ�Ŀ�ʼ��ַ
        $offset              = $this->getlong3();
        fseek($this->fp, $offset);
        $location['endip'] = long2ip($this->getlong()); // �û�IP���ڷ�Χ�Ľ�����ַ
        $byte              = fread($this->fp, 1); // ��־�ֽ�
        switch (ord($byte)) {
            case 1: // ��־�ֽ�Ϊ1����ʾ���Һ�������Ϣ����ͬʱ�ض���
                $countryOffset = $this->getlong3(); // �ض����ַ
                fseek($this->fp, $countryOffset);
                $byte = fread($this->fp, 1); // ��־�ֽ�
                switch (ord($byte)) {
                    case 2: // ��־�ֽ�Ϊ2����ʾ������Ϣ�ֱ��ض���
                        fseek($this->fp, $this->getlong3());
                        $location['country'] = $this->getstring();
                        fseek($this->fp, $countryOffset + 4);
                        $location['area'] = $this->getarea();
                        break;
                    default: // ���򣬱�ʾ������Ϣû�б��ض���
                        $location['country'] = $this->getstring($byte);
                        $location['area']    = $this->getarea();
                        break;
                }
                break;
            case 2: // ��־�ֽ�Ϊ2����ʾ������Ϣ���ض���
                fseek($this->fp, $this->getlong3());
                $location['country'] = $this->getstring();
                fseek($this->fp, $offset + 8);
                $location['area'] = $this->getarea();
                break;
            default: // ���򣬱�ʾ������Ϣû�б��ض���
                $location['country'] = $this->getstring($byte);
                $location['area']    = $this->getarea();
                break;
        }
        if (trim($location['country']) == 'CZ88.NET') {
            // CZ88.NET��ʾû����Ч��Ϣ
            $location['country'] = 'δ֪';
        }
        if (trim($location['area']) == 'CZ88.NET') {
            $location['area'] = '';
        }
        return $location;
    }

    /**
     * ����������������ҳ��ִ�н������Զ��رմ򿪵��ļ���
     *
     */
    public function __destruct()
    {
        if ($this->fp) {
            fclose($this->fp);
        }
        $this->fp = 0;
    }

}
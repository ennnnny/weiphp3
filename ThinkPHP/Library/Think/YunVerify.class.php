<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 陌路生人 <ms_test@qq.com> <http://www.les-lbjp.com>
// +----------------------------------------------------------------------

namespace Think;

class YunVerify {	
    private static $id       = "AKIDIhWnlCRMFdXzzRuyzWY7Es5mzm3znEWM";  // 开发商的ID，用于检索KEY
    private static $key      = "SMjvNQyxYeILz8p3o6sAtD3NCYNiZz1Z";      // 开发商的KEY，用于传输数据进行签名加密以及解密
    private static $buid     = "";                                      // 使用验证码的业务，用于生成统计数据，如果不区分则为空
    private static $sceneid  = "";                                      // 使用验证码的场景，用于生成统计数据，如果不区分则为空
    private static $uid      = "";                                      // 用户UID，如果不区分用户则为空
    private static $captype  = "2";                                     // 验证码类型，具体说明可参考开发手册
    private static $callback = "";                                      // query接口的回调函数
    private static $version  = "v1";                                    // 接口版本
    private static $ip       = "api.guard.qcloud.com";                  // 验证码服务IP
    private static $port     = "80";                                    // 验证码服务PORT


    /*
     * 用于生成拉取JS API的URL
     */
    public function CreateQueryUrl($cjid)
    {
        $nonce=strval(rand(1,1000));
        $timestamp=strval(time());

        $uri  = "/" . self::$version;
        $uri .= "/captcha/query?";
        $uri .= "cs-secretid=" . self::$id;
        $uri .= "&cs-uid=" . self::$uid;
        $uri .= "&cs-nonce=" . $nonce;
        $uri .= "&cs-timestamp=" . $timestamp;
        $uri .= "&buid=" . self::$buid;
        $uri .= "&sceneid=" . $cjid;
        $uri .= "&captype=" . self::$captype;
        $uri .= "&callback=callback";

        $plaintext = "body=&method=GET&url=" . $uri;                                //组成原文
        $cs_sig = base64_encode(hash_hmac("sha1", $plaintext, self::$key, true));   //对原文进行加密
        $cs_sig = urlencode($cs_sig);                                               //对密文进行URL编码

        $uri = $uri . "&cs-sig=" . $cs_sig;
        $url = "http://" . self::$ip . ":" . self::$port . $uri;                    //生成最后的URL
        return $url;
    }

    /*
     * 用于生成检查ticket的URL
     */
    private function CreateCheckUrl($ticket)
    {
        $nonce=strval(rand(1,1000));
        $timestamp=strval(time());

        $uri  = "/" . self::$version;
        $uri .= "/captcha/check?";
        $uri .= "cs-secretid=" . self::$id;
        $uri .= "&cs-uid=" . self::$uid;
        $uri .= "&cs-nonce=" . $nonce;
        $uri .= "&cs-timestamp=" . $timestamp;
        $uri .= "&buid=" . self::$buid;
        $uri .= "&sceneid=" . self::$sceneid;
        $uri .= "&captype=" . self::$captype;
        $uri .= "&ticket=" . $ticket;

        $plaintext = "body=&method=GET&url=" . $uri;                                //组成原文
        $cs_sig = base64_encode(hash_hmac("sha1", $plaintext, self::$key, true));   //对原文进行加密
        $cs_sig = urlencode($cs_sig);                                               //对密文进行URL编码

        $uri = $uri . "&cs-sig=" . $cs_sig;
        $url = "http://" . self::$ip . ":" . self::$port . $uri;                    //生成最后的URL
        //$url = "http://127.0.0.1" . $uri;                    //生成最后的URL
        return $url;
    }

    /*
     * 用于检查ticket
     */
    public function CheckTicket($ticket)
    {
        $check_url=self::CreateCheckUrl($ticket);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $check_url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result=curl_exec($ch);                                 //获取check接口的返回数据
        curl_close($ch);

        if (!$result) return 0;

        $json = json_decode($result, true);
        $cs_sig = $json["cs-sig"];                              //取出签名
        unset($json["cs-sig"]);

        if ($cs_sig != "") {
            $my_sig = base64_encode(hash_hmac("sha1", json_encode($json), $key, true));     //对原文进行加密
            if ($my_sig != $cs_sig) {                                                       //判断本地加密是否和签名一致
                return 0;
            } elseif ($json["errorCode"] == 0){
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
	
	
	
	/**
	 * 验证验证码是否正确
	 *
	 * @param string $code 用户验证码
	 * @return bool 用户验证码是否正确
	 */
	public function check($code, $id = '') {
		
		return false;
	}
	
	//字符编码
	private function _ue($str){
		return urlencode($str);	
	}
	
	/**
	 * 输出验证码并把验证码的值保存的session中
	 * 验证码保存到session的格式为： array('code' => '验证码值', 'time' => '验证码创建时间');
	 */
	public function entry($id = '1') {
		return $this->CreateQueryUrl($id);
	}
	
}

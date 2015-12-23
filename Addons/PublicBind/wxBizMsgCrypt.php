<?php
class ErrorCode {
	public static $OK = 0;
	public static $ValidateSignatureError = - 40001;
	public static $ParseXmlError = - 40002;
	public static $ComputeSignatureError = - 40003;
	public static $IllegalAesKey = - 40004;
	public static $ValidateAppidError = - 40005;
	public static $EncryptAESError = - 40006;
	public static $DecryptAESError = - 40007;
	public static $IllegalBuffer = - 40008;
	public static $EncodeBase64Error = - 40009;
	public static $DecodeBase64Error = - 40010;
	public static $GenReturnXmlError = - 40011;
}
class SHA1 {
	public function getSHA1($token, $timestamp, $nonce, $encrypt_msg) {
		// 排序
		try {
			$array = array (
					$encrypt_msg,
					$token,
					$timestamp,
					$nonce 
			);
			sort ( $array, SORT_STRING );
			$str = implode ( $array );
			return array (
					ErrorCode::$OK,
					sha1 ( $str ) 
			);
		} catch ( Exception $e ) {
			// print $e . "\n";
			return array (
					ErrorCode::$ComputeSignatureError,
					null 
			);
		}
	}
}
class XMLParse {
	public function extract($xmltext) {
		try {
			$res = @simplexml_load_string ( $xmltext, NULL, LIBXML_NOCDATA );
			$res = json_decode ( json_encode ( $res ), true );
			$encrypt = $res ['Encrypt'];
			$tousername = $res ['ToUserName'];
			
			return array (
					0,
					$encrypt,
					$tousername 
			);
		} catch ( Exception $e ) {
			// print $e . "\n";
			return array (
					ErrorCode::$ParseXmlError,
					null,
					null 
			);
		}
	}
	public function generate($encrypt, $signature, $timestamp, $nonce) {
		$format = "
    %s
    %s
    %s
    %s
    ";
		return sprintf ( $format, $encrypt, $signature, $timestamp, $nonce );
	}
}
class PKCS7Encoder {
	public static $block_size = 32;
	function encode($text) {
		$block_size = PKCS7Encoder::$block_size;
		$text_length = strlen ( $text );
		// 计算需要填充的位数
		$amount_to_pad = PKCS7Encoder::$block_size - ($text_length % PKCS7Encoder::$block_size);
		if ($amount_to_pad == 0) {
			$amount_to_pad = PKCS7Encoder::block_size;
		}
		// 获得补位所用的字符
		$pad_chr = chr ( $amount_to_pad );
		$tmp = "";
		for($index = 0; $index < $amount_to_pad; $index ++) {
			$tmp .= $pad_chr;
		}
		return $text . $tmp;
	}
	function decode($text) {
		$pad = ord ( substr ( $text, - 1 ) );
		if ($pad < 1 || $pad > 32) {
			$pad = 0;
		}
		return substr ( $text, 0, (strlen ( $text ) - $pad) );
	}
}
class Prpcrypt {
	public $key;
	function Prpcrypt($k) {
		$this->key = base64_decode ( $k . "=" );
	}
	public function encrypt($text, $appid) {
		try {
			// 获得16位随机字符串，填充到明文之前
			$random = $this->getRandomStr ();
			$text = $random . pack ( "N", strlen ( $text ) ) . $text . $appid;
			// 网络字节序
			$size = mcrypt_get_block_size ( MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC );
			$module = mcrypt_module_open ( MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '' );
			$iv = substr ( $this->key, 0, 16 );
			// 使用自定义的填充方式对明文进行补位填充
			$pkc_encoder = new PKCS7Encoder ();
			$text = $pkc_encoder->encode ( $text );
			mcrypt_generic_init ( $module, $this->key, $iv );
			// 加密
			$encrypted = mcrypt_generic ( $module, $text );
			mcrypt_generic_deinit ( $module );
			mcrypt_module_close ( $module );
			// print(base64_encode($encrypted));
			// 使用BASE64对加密后的字符串进行编码
			return array (
					ErrorCode::$OK,
					base64_encode ( $encrypted ) 
			);
		} catch ( Exception $e ) {
			// print $e;
			return array (
					ErrorCode::$EncryptAESError,
					null 
			);
		}
	}
	public function decrypt($encrypted, $appid) {
		try {
			// 使用BASE64对需要解密的字符串进行解码
			$ciphertext_dec = base64_decode ( $encrypted );
			$module = mcrypt_module_open ( MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '' );
			$iv = substr ( $this->key, 0, 16 );
			mcrypt_generic_init ( $module, $this->key, $iv );
			// 解密
			$decrypted = mdecrypt_generic ( $module, $ciphertext_dec );
			mcrypt_generic_deinit ( $module );
			mcrypt_module_close ( $module );
		} catch ( Exception $e ) {
			return array (
					ErrorCode::$DecryptAESError,
					null 
			);
		}
		try {
			// 去除补位字符
			$pkc_encoder = new PKCS7Encoder ();
			$result = $pkc_encoder->decode ( $decrypted );
			// 去除16位随机字符串,网络字节序和AppId
			if (strlen ( $result ) < 16)
				return "";
			$content = substr ( $result, 16, strlen ( $result ) );
			$len_list = unpack ( "N", substr ( $content, 0, 4 ) );
			$xml_len = $len_list [1];
			$xml_content = substr ( $content, 4, $xml_len );
			$from_appid = substr ( $content, $xml_len + 4 );
		} catch ( Exception $e ) {
			// print $e;
			return array (
					ErrorCode::$IllegalBuffer,
					null 
			);
		}
		if ($from_appid != $appid)
			return array (
					ErrorCode::$ValidateAppidError,
					null 
			);
		return array (
				0,
				$xml_content 
		);
	}
	function getRandomStr() {
		$str = "";
		$str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen ( $str_pol ) - 1;
		for($i = 0; $i < 16; $i ++) {
			$str .= $str_pol [mt_rand ( 0, $max )];
		}
		return $str;
	}
}
class WXBizMsgCrypt {
	private $token;
	private $encodingAesKey;
	private $appId;
	public function WXBizMsgCrypt($token, $encodingAesKey, $appId) {
		$this->token = $token;
		$this->encodingAesKey = $encodingAesKey;
		$this->appId = $appId;
	}
	public function encryptMsg($replyMsg, $timeStamp, $nonce, &$encryptMsg) {
		$pc = new Prpcrypt ( $this->encodingAesKey );
		// 加密
		$array = $pc->encrypt ( $replyMsg, $this->appId );
		$ret = $array [0];
		if ($ret != 0) {
			return $ret;
		}
		if ($timeStamp == null) {
			$timeStamp = time ();
		}
		$encrypt = $array [1];
		// 生成安全签名
		$sha1 = new SHA1 ();
		$array = $sha1->getSHA1 ( $this->token, $timeStamp, $nonce, $encrypt );
		$ret = $array [0];
		if ($ret != 0) {
			return $ret;
		}
		$signature = $array [1];
		// 生成发送的xml
		$xmlparse = new XMLParse ();
		$encryptMsg = $xmlparse->generate ( $encrypt, $signature, $timeStamp, $nonce );
		return ErrorCode::$OK;
	}
	public function decryptMsg($msgSignature, $timestamp = null, $nonce, $postData, &$msg) {
		if (strlen ( $this->encodingAesKey ) != 43) {
			return ErrorCode::$IllegalAesKey;
		}
		$pc = new Prpcrypt ( $this->encodingAesKey );
		// 提取密文
		$xmlparse = new XMLParse ();
		$array = $xmlparse->extract ( $postData );
		$ret = $array [0];
		if ($ret != 0) {
			return $ret;
		}
		if ($timestamp == null) {
			$timestamp = time ();
		}
		$encrypt = $array [1];
		$touser_name = $array [2];
		// 验证安全签名
		$sha1 = new SHA1 ();
		$array = $sha1->getSHA1 ( $this->token, $timestamp, $nonce, $encrypt );
		$ret = $array [0];
		if ($ret != 0) {
			return $ret;
		}
		$signature = $array [1];
		if ($signature != $msgSignature) {
			return ErrorCode::$ValidateSignatureError;
		}
		$result = $pc->decrypt ( $encrypt, $this->appId );
		if ($result [0] != 0) {
			return $result [0];
		}
		$msg = $result [1];
		return ErrorCode::$OK;
	}
}
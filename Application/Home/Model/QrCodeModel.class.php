<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Home\Model;

use Think\Model;

/**
 * 分类模型
 */
class QrCodeModel extends Model {
	var $tableName = 'qr_code';
	private $appID;
	private $appSecret;
	private $accessToken;
	public function _initialize() {
		$token = get_token ();
		$public = get_token_appinfo ( $token );
		
		$this->appID = trim ( $public ['appid'] );
		$this->appSecret = trim ( $public ['secret'] );
		
		$this->accessToken = get_access_token ();
	}
	
	// 增加二维码
	function add_qr_code($action_name = 'QR_SCENE', $addon = '', $aim_id = '', $extra_int = '', $extra_text = '') {
		set_time_limit ( 30 );
		
		$data ['scene_id'] = $this->get_scene_id ( $action_name );
		if (! $data ['scene_id']) {
			return - 1; // 场景值已满
		}
		
		$data ['addon'] = $addon;
		$data ['aim_id'] = $aim_id;
		$data ['action_name'] = $action_name;
		$data ['extra_text'] = $extra_text;
		$data ['extra_int'] = $extra_int;
		
		$data ['cTime'] = time ();
		$data ['token'] = get_token ();
		
		$data ['qr_code'] = $this->QrcodeCreate ( $data ['scene_id'], $data ['action_name'] );
		// dump ( $data );
		// exit ();
		if (! $data ['qr_code']) {
			return - 2; // 获取二维码失败
		}
		
		$res = $this->add ( $data );
		if (! $res) {
			return - 3; // 保存数据失败
		}
		
		return $data ['qr_code'];
	}
	// 自动获取空闲的scene_id
	function get_scene_id($action_name) {
		$max_scene_id = $this->where ( "action_name='$action_name'" )->order ( 'scene_id desc' )->getField ( 'scene_id' );
		if ($action_name == 'QR_SCENE') {
			if (! $max_scene_id) {
				return 100001; // 临时的从100001开始算起
			}
			return $max_scene_id + 1;
		}
		
		if ($max_scene_id < 100000) {
			return $max_scene_id + 1;
		}
		
		$count = $this->where ( "action_name='$action_name'" )->count ();
		if ($count == $max_scene_id) {
			return 0;
		}
		
		for($i = 0; $i < 100; $i ++) {
			$start = $i * 1000;
			$end = ($i + 1) * 1000;
			$ids = $this->where ( "action_name='$action_name'" )->limit ( $start, $end )->getField ( 'scene_id' );
			if (count ( $ids ) == 1000)
				continue;
			
			for($j = $start; $j < $end; $j ++) {
				$arr [] = $j;
			}
			$diff = array_diff ( ( array ) $arr, ( array ) $ids );
			return $diff [0];
		}
	}
	
	/* 创建二维码 @param - $qrcodeID传递的参数，$qrcodeType二维码类型 默认为临时二维码 @return - 返回二维码图片地址 */
	private function QrcodeCreate($qrcodeID, $qrcodeType = 'QR_SCENE') {
		if ($qrcodeType == 'QR_LIMIT_SCENE') {
			$tempJson = '{"action_name": "' . $qrcodeType . '", "action_info": {"scene": {"scene_id": ' . $qrcodeID . '}}}';
		} else {
			$tempJson = '{"expire_seconds": 1800, "action_name": "' . $qrcodeType . '", "action_info": {"scene": {"scene_id": ' . $qrcodeID . '}}}';
		}
		$access_token = $this->accessToken;
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $access_token;
		$tempArr = json_decode ( $this->JsonPost ( $url, $tempJson ), true );
		if (@array_key_exists ( 'ticket', $tempArr )) {
			return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $tempArr ['ticket'];
		} else {
			$this->ErrorLogger ( 'qrcode create falied.' );
			return false;
		}
	}
	
	/* 从微信服务器获取access_token并写入配置文件 */
	private function AccessTokenGet($token) {
		return get_access_token ();
	}
	/* 用户分组查询 */
	public function GroupsQuery() {
		$access_token = $this->accessToken;
		$url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token=' . $access_token;
		$tempArr = json_decode ( wp_file_get_contents ( $url ), true );
		if (@array_key_exists ( 'groups', $tempArr )) {
			return $tempArr ['groups']; // 返回数组格式的分组信息
		} else {
			$this->ErrorLogger ( 'groups query falied.' );
			$this->AccessTokenGet ();
			$this->GroupsQuery ();
		}
	}
	
	// 工具函数 //
	/* 使用curl来post一个json数据 */
	// CURLOPT_SSL_VERIFYPEER,CURLOPT_SSL_VERIFYHOST - 在做https中要用到
	// CURLOPT_RETURNTRANSFER - 不以文件流返回，带1
	private function JsonPost($url, $jsonData) {
		$curl = curl_init ();
		curl_setopt ( $curl, CURLOPT_URL, $url );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $curl, CURLOPT_POST, 1 );
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, $jsonData );
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$result = curl_exec ( $curl );
		if (curl_errno ( $curl )) {
			$this->ErrorLogger ( 'curl falied. Error Info: ' . curl_error ( $curl ) );
		}
		curl_close ( $curl );
		return $result;
	}
	/* 错误日志记录 */
	private function ErrorLogger($errMsg) {
		$logger = fopen ( './ErrorLog.txt', 'a+' );
		fwrite ( $logger, date ( 'Y-m-d H:i:s' ) . " Error Info : " . $errMsg . "\r\n" );
		// dump ( $errMsg );
	}
}

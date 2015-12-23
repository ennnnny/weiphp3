<?php

namespace Addons\PublicBind\Model;

use Think\Model;

/**
 * PublicBind模型
 */
class PublicBindModel extends Model {
	protected $tableName = 'addons';
	public $component_appid = 'wxea0485bef5247236';
	public $component_appsecret = '0c79e1fa963cd80cc0be99b20a18faeb';
	function _initialize() {
		$this->component_appid = C ( 'COMPONENT_APPID' );
		$this->component_appsecret = C ( 'COMPONENT_APPSECRET' );
	}
	function _get_component_access_token() {
		$key = 'component_access_token_' . $this->component_appid;
		$component_access_token = S ( $key );
		
		if ($component_access_token === false) {
			$url = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
			
			$param ['component_appid'] = $this->component_appid;
			$param ['component_appsecret'] = $this->component_appsecret;
			
			$map ['name'] = 'PublicBind';
			$config = M ( 'addons' )->where ( $map )->getField ( 'config' );
			$config = ( array ) json_decode ( $config, true );
			$param ['component_verify_ticket'] = $config ['ComponentVerifyTicket'];
			
			$data = post_data ( $url, $param );
			if (! isset ( $data ['component_access_token'] )) {
				return false;
			}
			
			$component_access_token = $data ['component_access_token'];
			
			S ( $key, $component_access_token, 3600 );
		}
		return $component_access_token;
	}
	function _get_pre_auth_code($component_access_token = '') {
		empty ( $component_access_token ) && $component_access_token = $this->_get_component_access_token ();
		
		$key1 = 'pre_auth_code';
		$pre_auth_code = S ( $key1 );
		if ($pre_auth_code === false) {
			$url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=' . $component_access_token;
			
			$param ['component_appid'] = $this->component_appid;
			
			$data = post_data ( $url, $param );
			if (! isset ( $data ['pre_auth_code'] )) {
				return false;
			}
			
			$pre_auth_code = $data ['pre_auth_code'];
			
			S ( $key1, $pre_auth_code, 600 );
		}
		return $pre_auth_code;
	}
	function bind() {
		$res ['status'] = false;
		
		// 第一步：获取第三方平台access_token
		$component_access_token = $this->_get_component_access_token ();
		if ($component_access_token == false) {
			$res ['msg'] = '获取access_token失败！';
			return $res;
		}
		
		// 获取预授权码
		$pre_auth_code = $this->_get_pre_auth_code ( $component_access_token );
		if ($pre_auth_code == false) {
			$res ['msg'] = '获取pre_auth_code失败！';
			return $res;
		}
		
		$callback = addons_url ( 'PublicBind://PublicBind/after_auth' );
		$jumpURL = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=' . $this->component_appid . '&pre_auth_code=' . $pre_auth_code . '&redirect_uri=' . $callback;
		
		$res ['status'] = true;
		$res ['jumpURL'] = $jumpURL;
		return $res;
	}
	// 换取公众号的授权信息
	function getAuthInfo($auth_code) {
		$res ['status'] = false;
		
		$component_access_token = $this->_get_component_access_token ();
		if ($component_access_token == false) {
			$res ['msg'] = '获取access_token失败！';
			return $res;
		}
		
		$key = 'getAuthInfo_' . $auth_code;
		$info = S ( $key );
		
		if ($info === false) {
			$url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=' . $component_access_token;
			
			$param ['component_appid'] = $this->component_appid;
			$param ['authorization_code'] = $auth_code;
			
			$info = post_data ( $url, $param );
			if (! isset ( $info ['authorization_info'] ['authorizer_appid'] )) {
				$res ['msg'] = '获取authorizer_appid失败！';
				return $res;
			}
			
			S ( $key, $info, $info ['expires_in'] );
		}
		return $info;
	}
	// 获取授权方的账户信息
	function getPublicInfo($authorizer_appid) {
		$res ['status'] = false;
		
		$component_access_token = $this->_get_component_access_token ();
		if ($component_access_token == false) {
			$res ['msg'] = '获取access_token失败！';
			return $res;
		}
		
		$key = 'getPublicInfo_' . $authorizer_appid;
		$data = S ( $key );
		
		if ($data === false) {
			$url = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=' . $component_access_token;
			
			$param ['component_appid'] = $this->component_appid;
			$param ['authorizer_appid'] = $authorizer_appid;
			
			$data = post_data ( $url, $param );
			if (! isset ( $data ['authorizer_info'] ['user_name'] )) {
				$res ['msg'] = '获取公众号信息失败！';
				return $res;
			}
			
			S ( $key, $data, $data ['expires_in'] );
		}
		
		return $data;
	}
}

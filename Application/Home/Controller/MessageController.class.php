<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class MessageController extends HomeController {
	function _initialize() {
		parent::_initialize ();
		
		$act = strtolower ( ACTION_NAME );
		
		$res ['title'] = '高级群发';
		$res ['url'] = U ( 'add' );
		$res ['class'] = $act == 'add' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '客服群发';
		$res ['url'] = U ( 'custom_sendall' );
		$res ['class'] = $act == 'custom_sendall' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '消息管理';
		$res ['url'] = U ( 'message_lists' );
		$res ['class'] = $act == 'message_lists' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
	}
	public function lists() {
		$this->display ( 'Addons/lists' );
	}
	public function add() {
		$model = $this->getModel ( 'Message' );
		if (IS_POST) {
			if (! C ( 'SEND_GROUP_MSG' )) {
				$this->error ( '抱歉，您的公众号没有群发消息的权限' );
			}
			$send_type = I ( 'send_type', 0, 'intval' );
			$group_id = I ( 'group_id', 0, 'intval' );
			$send_openids = I ( 'send_openids' );
			
			if ($send_type == 0) {
				$_POST ['msg_id'] = $this->_send_by_group ( $group_id );
			} else {
				$_POST ['msg_id'] = $this->_send_by_openid ( $send_openids );
			}
			
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->_saveKeyword ( $model, $id );
				
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
				
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'add' ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'], $model ['field_sort'] );
			
			$this->assign ( 'fields', $fields );
			$this->meta_title = '新增' . $model ['title'];
			
			! C ( 'SEND_GROUP_MSG' ) && $this->assign ( 'normal_tips', '温馨提示：目前微信仅开放认证公众号的群发消息权限，未认证公众号无法使用此功能' );
			
			$map ['token'] = get_token ();
			$map ['manager_id'] = $this->mid;
			$map ['is_del'] = 0;
			$group_list = M ( 'auth_group' )->where ( $map )->select ();
			$this->assign ( 'group_list', $group_list );
			
			$this->display ();
		}
	}
	function del() {
		$model = $this->getModel ( 'Message' );
		parent::common_del ( $model );
	}
	// 预览群发的信息
	function preview() {
		if (! C ( 'SEND_GROUP_MSG' )) {
			$this->error ( '抱歉，您的公众号没有群发消息的权限' );
		}
		$openids = wp_explode ( I ( 'preview_openids' ) );
		if (empty ( $openids )) {
			$this->error ( '预览OpenID不能为空' );
		}
		
		$info = $this->_sucai_media_info ();
		if ($info ['msgtype'] == 'text') {
			$param ['text'] ['content'] = $info ['media_id'];
		} else if ($info ['msgtype'] == 'mpnews') {
			$param ['mpnews'] ['media_id'] = $info ['media_id'];
		} else if ($info ['msgtype'] == 'voice') {
			$param ['voice'] ['media_id'] = $info ['media_id'];
		} else if ($info ['msgtype'] == 'mpvideo') {
			$param ['mpvideo'] ['media_id'] = $info ['media_id'];
		}
		$param ['msgtype'] = $info ['msgtype'];
		
		$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=' . get_access_token ();
		foreach ( $openids as $openid ) {
			$param ['touser'] = $openid;
			
			$res = post_data ( $url, $param );
		}
		$this->success ( '发送成功~' );
	}
	// 按用户组发送
	function _send_by_group($group_id) {
		if ($group_id) { // 本地分组ID换取微信端的分组ID
			$map ['id'] = $group_id;
			$groupid = M ( 'auth_group' )->where ( $map )->getField ( 'wechat_group_id' );
		}
		
		$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=' . get_access_token ();
		
		$paramStr = '';
		if ($group_id) {
			// $param ['filter'] ['is_to_all'] = "false";
			// $param ['filter'] ['group_id'] = $groupid;
			$paramStr .= '{"filter":{"is_to_all":false,"group_id":"' . $groupid . '"},';
		} else {
			// $param ['filter'] ['is_to_all'] = "true";
			$paramStr .= '{"filter":{"is_to_all":true},';
		}
		$info = $this->_sucai_media_info ();
		
		if ($info ['msgtype'] == 'text') {
			// $param ['text'] ['content'] = $info ['media_id'];
			$paramStr .= '"text":{"content":"' . $info ['media_id'] . '"},"msgtype":"text"}';
		} else if ($info ['msgtype'] == 'mpnews') {
			// $param ['mpnews'] ['media_id'] = $info ['media_id'];
			$paramStr .= '"mpnews":{"media_id":"' . $info ['media_id'] . '"},"msgtype":"mpnews"}';
		} else if ($info ['msgtype'] == 'voice') {
			// $param ['mpnews'] ['media_id'] = $info ['media_id'];
			$paramStr .= '"voice":{"media_id":"' . $info ['media_id'] . '"},"msgtype":"voice"}';
		} else if ($info ['msgtype'] == 'mpvideo') {
			// $param ['mpnews'] ['media_id'] = $info ['media_id'];
			$paramStr .= '"mpvideo":{"media_id":"' . $info ['media_id'] . '"},"msgtype":"mpvideo"}';
		}
		// $param ['msgtype'] = $info ['msgtype'];
		// $param ['msgtype'] = 'news';
		// dump($paramStr);
		
		$res = post_data ( $url, $paramStr );
		if ($res ['errcode'] != 0) {
			$this->error ( error_msg ( $res ) );
		} else {
			return $res ['msg_id'];
		}
	}
	// 按用户组发送 订阅号不可用，服务号认证后可用
	function _send_by_openid($openids) {
		$openids = wp_explode ( $openids );
		if (empty ( $openids )) {
			$this->error ( '要发送的OpenID值不能为空' );
		}
		if (count ( $openids ) < 2) {
			$this->error ( 'OpenID至少需要2个或者2个以上' );
		}
		
		$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=' . get_access_token ();
		
		$info = $this->_sucai_media_info ();
		
		$param ['touser'] = $openids;
		if ($info ['msgtype'] == 'text') {
			$param ['text'] ['content'] = $info ['media_id'];
			$param ['msgtype'] = $info ['msgtype'];
		} else if ($info ['msgtype'] == 'mpnews') {
			$param ['mpnews'] ['media_id'] = $info ['media_id'];
			$param ['msgtype'] = $info ['msgtype'];
		} else if ($info ['msgtype'] == 'voice') {
			$param ['voice'] ['media_id'] = $info ['media_id'];
			$param ['msgtype'] = $info ['msgtype'];
		} else if ($info ['msgtype'] == 'mpvideo') {
			$param ['video'] ['media_id'] = $info ['media_id'];
			$param ['msgtype'] = $info ['video'];
		}
		
		$param = JSON ( $param );
		$res = post_data ( $url, $param );
		if ($res ['errcode'] != 0) {
			$this->error ( error_msg ( $res ) );
		} else {
			return $res ['msg_id'];
		}
	}
	// 获取素材的media_id
	function _sucai_media_info() {
		$type = I ( 'msg_type' );
		$content = I ( 'content' );
		$appmsg_id = I ( 'appmsg_id' );
		// $image = I ( 'image' );
		
		if ($type == 'text') {
			if (empty ( $content ))
				$this->error ( '文本内容不能为空' );
			
			$res ['media_id'] = $content;
			$res ['msgtype'] = 'text';
			$_POST ['content'] = $content;
		} else if ($type == 'appmsg') {
			if (empty ( $appmsg_id ))
				$this->error ( '图文素材不能为空' );
			$res ['media_id'] = D ( 'Home/Material' )->getMediaIdByGroupId ( $appmsg_id );
			$_POST ['media_id'] = $res ['media_id'];
			$res ['msgtype'] = 'mpnews';
		} else if ($type == 'voice') {
			$voice = I ( 'voice_id' );
			if (empty ( $voice ))
				$this->error ( '语音素材不能为空' );
			$file = M ( 'material_file' )->find ( $voice );
			if ($file ['media_id']) {
				$res ['media_id'] = $file ['media_id'];
			} else {
				$res ['media_id'] = D ( 'Common/Custom' )->get_file_media_id ( $file ['file_id'], 'voice' );
			}
			$res ['msgtype'] = 'voice';
		} else if ($type == 'video') {
			$video = I ( 'video_id' );
			if (empty ( $video ))
				$this->error ( '视频素材不能为空' );
			$file = M ( 'material_file' )->find ( $video );
			if ($file ['media_id']) {
				$mediaId = $file ['media_id'];
			} else {
				$mediaId = D ( 'Common/Custom' )->get_file_media_id ( $file ['file_id'], 'video' );
			}
			$data ['media_id'] = $mediaId;
			$data ['title'] = $file ['title'];
			$data ['description'] = $file ['introduction'];
			$url1 = "https://file.api.weixin.qq.com/cgi-bin/media/uploadvideo?access_token=" . get_access_token ();
			$result = post_data ( $url1, $data );
			$res ['media_id'] = $result ['media_id'];
			$res ['msgtype'] = 'mpvideo';
		}
		$_POST ['msgtype'] = $res ['msgtype'];
		return $res;
	}
	function custom_sendall() {
		$this->assign ( 'noraml_tips', '当用户发消息给认证公众号时，管理员可以在48小时内给用户回复信息' );
		if (IS_POST) {
			$data ['ToUserName'] = get_token ();
			$data ['cTime'] = time ();
			$data ['msgType'] = $_POST ['msg_type'];
			$data ['manager_id'] = $this->mid;
			$data ['content'] = $_POST ['content'];
			$data ['send_type'] = $sendType = $_POST ['send_type'];
			$data ['group_id'] = $groupId = $_POST ['group_id'];
			$data ['send_openids'] = $sendOpenid = $_POST ['send_openids'];
			if ($sendType == 1 && $sendOpenid == '') {
				$this->error ( '指定的Openid值不能为空' );
			}
			if ($data['msgType']=='appmsg'){
			    $data['msgType']='news';
			}
			$map1 ['ToUserName'] = get_token ();
			$diff = M ( 'custom_sendall' )->where ( $map1 )->getField ( "max(diff) as diff" );
			$diff += 1;
			$openidArr = $this->_get_user_openid ( $sendType, $groupId, $sendOpenid );
			$count = 0;
			foreach ( $openidArr as $k => $v ) {
				if ($data ['msgType'] == 'text') {
					// 文本
					$result = D ( 'Common/Custom' )->replyText ( $k, $data ['content'] );
				} else if ($data ['msgType'] == 'news') {
					// 图文
					$data ['msgType'] = 'news';
					$data ['news_group_id'] = $_POST ['appmsg_id'];
					if (empty ( $data ['news_group_id'] )) {
						$this->error ( '请选择图文消息！' );
					}
					$result = D ( 'Common/Custom' )->replyNews ( $k, $data ['news_group_id'] );
				} else if ($data ['msgType'] == 'image') {
					// 图片
					$image_material = $_POST ['image_material'];
					$image_cover_id = $_POST ['image'];
					if ($image_cover_id) {
						$data ['image_id'] = $image_cover_id;
						$data ['media_id'] = D ( 'Common/Custom' )->get_image_media_id ( $image_cover_id );
						$result = D ( 'Common/Custom' )->replyImage ( $k, $data ['media_id'], '' );
					} else if ($image_material) {
						$imageMaterial = M ( 'material_image' )->find ( $image_material );
						$data ['image_id'] = $imageMaterial ['cover_id'];
						if ($imageMaterial ['media_id']) {
							$data ['media_id'] = $imageMaterial ['media_id'];
						} else {
							$data ['media_id'] = D ( 'Common/Custom' )->get_image_media_id ( $image_material );
						}
						$result = D ( 'Common/Custom' )->replyImage ( $k, $data ['media_id'], '' );
					} else {
						$this->error ( '请选择要发送的图片' );
					}
				} else if ($data ['msgType'] == 'voice') {
					
					// 语音
					$data ['voice_id'] = $voiceId = $_POST ['voice_id'];
					if (empty ( $voiceId )) {
						$this->error ( '请选择语音消息' );
					}
					
					$voiceMaterial = M ( 'material_file' )->find ( $voiceId );
					if ($voiceMaterial ['media_id']) {
						$data ['media_id'] = $voiceMaterial ['media_id'];
					} else {
						$data ['media_id'] = D ( 'Common/Custom' )->get_file_media_id ( $voiceMaterial ['file_id'], 'voice' );
					}
					$result = D ( 'Common/Custom' )->replyVoice ( $k, $data ['media_id'], '' );
				} else if ($data ['msgType'] == 'video') {
					// 视频
					$data ['video_id'] = $videoId = $_POST ['video_id'];
					if (empty ( $videoId )) {
						$this->error ( '请选择视频消息' );
					}
					$videoMaterial = M ( 'material_file' )->find ( $videoId );
					$data ['video_title'] = $videoMaterial ['title'];
					$data ['video_description'] = $videoMaterial ['introduction'];
					$data ['video_thumb'] = D ( 'Common/Custom' )->get_thumb_media_id ();
					
					if ($videoMaterial ['media_id']) {
						$data ['media_id'] = $videoMaterial ['media_id'];
					} else {
						$data ['media_id'] = D ( 'Common/Custom' )->get_file_media_id ( $videoMaterial ['file_id'], 'video' );
					}
					$result = D ( 'Common/Custom' )->replyVideo ( $k, $data ['media_id'], '', $data ['video_thumb'], $videoMaterial ['title'], $data ['video_description'] );
				}
				if ($result ['status'] == 1) {
					$data ['is_send'] = 1;
				} else {
					$data ['is_send'] = 0;
					$count ++;
				}
				$data ['FromUserName'] = $v;
				$data ['uid'] = $k;
				$data ['diff'] = $diff;
				M ( 'custom_sendall' )->add ( $data );
			}
			if ($count == 0) {
				$this->success ( '发送成功！' );
			} else {
				$this->error ( '有 ' . $count . ' 条消息 发送失败！' );
			}
		} else {
			$map ['token'] = get_token ();
			$map ['manager_id'] = $this->mid;
			$map ['is_del'] = 0;
			$group_list = M ( 'auth_group' )->where ( $map )->select ();
			$this->assign ( 'group_list', $group_list );
			$this->display ();
		}
	}
	
	// 未发送成功的消息重新发
	function sendOldMessage() {
		$map ['manager_id'] = $this->mid;
		$map ['ToUserName'] = get_token ();
		$map ['is_send'] = 0;
		$diff = I ( 'diff' );
		if ($diff) {
			$map ['diff'] = $diff;
		}
		$messageData = M ( 'custom_sendall' )->where ( $map )->select ();
		$count = 0;
		if (! empty ( $messageData )) {
			foreach ( $messageData as $data ) {
				if ($data ['msgType'] == 'text') {
					// 文本
					$result = D ( 'Common/Custom' )->replyText ( $data ['uid'], $data ['content'] );
				} else if ($data ['msgType'] == 'news') {
					// 图文
					$result = D ( 'Common/Custom' )->replyNews ( $data ['uid'], $data ['news_group_id'] );
				} else if ($data ['msgType'] == 'image') {
					// 图片
					$result = D ( 'Common/Custom' )->replyImage ( $data ['uid'], $data ['media_id'], '' );
				} else if ($data ['msgType'] == 'voice') {
					// 语言
					$result = D ( 'Common/Custom' )->replyVoice ( $data ['uid'], $data ['media_id'], '' );
				} else if ($data ['msgType'] == 'video') {
					// 视频
					$result = D ( 'Common/Custom' )->replyVoice ( $data ['uid'], $data ['media_id'], '', $data ['video_thumb'], $data ['video_title'], $data ['video_description'] );
				}
				
				if ($result ['status'] == 1) {
					$ids [$data ['id']] = $data ['id'];
				}
			}
			if ($ids) {
				$map1 ['id'] = array (
						'in',
						$ids 
				);
				$save ['is_send'] = 1;
				$res = M ( 'custom_sendall' )->where ( $map1 )->save ( $save );
				if ($res) {
					$count ++;
				}
			}
		}
		echo $count;
	}
	
	/*
	 * sendType:0 按组发 1：指定opendid
	 * groupid :0 指所有用户
	 */
	function _get_user_openid($sendType = 0, $groupId = 0, $openidStr = '') {
		$map ['has_subscribe'] = 1;
		$map ['token'] = get_token ();
		$allUser = M ( 'public_follow' )->where ( $map )->getFields ( 'uid,openid' );
		foreach ( $allUser as $k => $v ) {
			$uidArr [$k] = $k;
			$openidArr [$v] = $k;
		}
		if ($sendType == 0 && $groupId == 0) {
			return $allUser;
		} else if ($sendType == 0 && $groupId != 0) {
			$map1 ['uid'] = array (
					'in',
					$uidArr 
			);
			$map1['group_id']=$groupId;
			$groupData = M ( 'auth_group_access' )->where ( $map1 )->select ();
			foreach ( $groupData as $gr ) {
				$data [$gr ['uid']] = $allUser [$gr ['uid']];
			}
			return $data;
		} else if ($sendType == 1) {
			$openids = wp_explode ( $openidStr );
			foreach ( $openids as $op ) {
				$uid = $openidArr [$op];
				if ($uid) {
					$data [$uid] = $op;
				} else {
					$this->error ( 'Openid为: ' . $op . ' 的用户不存在' );
				}
			}
			return $data;
		}
	}
	/**
	 * ***********消息管理*******************
	 */
	// 用户发送的消息
	function message_lists() {
		$act = strtolower ( ACTION_NAME );
		$res ['title'] = '用户发送消息';
		$res ['url'] = U ( 'message_lists' );
		$res ['class'] = $act == 'message_lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '高级群发消息';
		$res ['url'] = U ( 'sendall_lists' );
		$res ['class'] = $act == 'sendall_lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '客服接口群发消息';
		$res ['url'] = U ( 'custom_sendall_lists' );
		$res ['class'] = $act == 'custom_sendall_lists' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
		
		$this->assign ( 'nav', $nav );
		$day = I ( 'send_time', 1 );
		$now_day = strtotime ( time_format ( time (), 'Y-m-d' ) );
		if ($day == 1) {
			// 最近五天
			$time = $now_day - 4 * 24 * 60 * 60;
			$map ['CreateTime'] = array (
					'egt',
					$time 
			);
		} else if ($day == 2) {
			// 今天
			$map ['CreateTime'] = array (
					'egt',
					$now_day 
			);
		} else if ($day == 3) {
			// 昨天
			$zhuo_day = $now_day - 1 * 24 * 60 * 60;
			$map ['CreateTime'] = array (
					'between',
					$zhuo_day,
					$now_day 
			);
		} else if ($day == 4) {
			// 前天
			$qian_day = $now_day - 2 * 24 * 60 * 60;
			$map ['CreateTime'] = array (
					'between',
					$qian_day,
					$now_day 
			);
		} else if ($day == 5) {
			// 更早
			$qian_day = $now_day - 4 * 24 * 60 * 60;
			$map ['CreateTime'] = array (
					'elt',
					$qian_day 
			);
		}
		
		// $map ['FromUserName'] = I ( 'openid' );
		$map ['ToUserName'] = get_token ();
		$map ['type'] = 0;
		$list = M ( 'weixin_message' )->where ( $map )->order ( 'id desc' )->selectPage ();
		
		$dao = D ( 'Common/User' );
		foreach ( $list ['list_data'] as &$v ) {
			if ($v ['type'] == 1) {
				$user = $dao->getUserInfo ( $v ['MsgId'] );
			} else {
				$user = $dao->getUserInfoByOpenid ( $v ['FromUserName'] );
				$toUser = $user;
			}
			if ($user) {
				$v ['user'] = $user;
			}
			
			// $v ['Content'] = $this->_deal_content ( $v );
		}
		// dump($list);
		$url = U ( 'message_lists' );
		$this->assign ( 'searchUrl', $url );
		$this->assign ( $list );
		$this->assign ( 'noraml_tips', '当用户发消息给认证公众号时，管理员可以在48小时内给用户回复信息' );
		
		$this->display ();
	}
	// 客服群发消息管理
	function custom_sendall_lists() {
		$act = strtolower ( ACTION_NAME );
		$res ['title'] = '用户发送消息';
		$res ['url'] = U ( 'message_lists' );
		$res ['class'] = $act == 'message_lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '高级群发消息';
		$res ['url'] = U ( 'sendall_lists' );
		$res ['class'] = $act == 'sendall_lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '客服接口群发消息';
		$res ['url'] = U ( 'custom_sendall_lists' );
		$res ['class'] = $act == 'custom_sendall_lists' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
		
		$this->assign ( 'nav', $nav );
		$day = I ( 'send_time', 1 );
		$now_day = strtotime ( time_format ( time (), 'Y-m-d' ) );
		if ($day == 1) {
			// 最近五天
			$time = $now_day - 4 * 24 * 60 * 60;
			$map ['cTime'] = array (
					'egt',
					$time 
			);
		} else if ($day == 2) {
			// 今天
			$map ['cTime'] = array (
					'egt',
					$now_day 
			);
		} else if ($day == 3) {
			// 昨天
			$zhuo_day = $now_day - 1 * 24 * 60 * 60;
			$map ['cTime'] = array (
					'between',
					$zhuo_day,
					$now_day 
			);
		} else if ($day == 4) {
			// 前天
			$qian_day = $now_day - 2 * 24 * 60 * 60;
			$map ['cTime'] = array (
					'between',
					$qian_day,
					$now_day 
			);
		} else if ($day == 5) {
			// 更早
			$qian_day = $now_day - 4 * 24 * 60 * 60;
			$map ['cTime'] = array (
					'elt',
					$qian_day 
			);
		}
		
		// $map ['FromUserName'] = I ( 'openid' );
		$map ['ToUserName'] = get_token ();
		$map ['manager_id'] = $this->mid;
		$list = M ( 'custom_sendall' )->where ( $map )->order ( 'id desc' )->group ( 'diff' )->selectPage ();
		// dump($shibai);
		// dump($chenggong);
		
		$dao = D ( 'Common/User' );
		foreach ( $list ['list_data'] as &$v ) {
			$countData = M ( 'custom_sendall' )->where ( array (
					'diff' => $v ['diff'],
					'is_send' => 1 
			) )->order ( 'id desc' )->field ( 'count(uid) count,diff' )->select ();
			$v ['chenggong'] = $countData [0] ['count'];
			$countData = M ( 'custom_sendall' )->where ( array (
					'diff' => $v ['diff'],
					'is_send' => 0 
			) )->order ( 'id desc' )->field ( 'count(uid) count,diff' )->select ();
			$v ['shibai'] = $countData [0] ['count'];
			
			if ($v ['send_type'] == 0 && $v ['group_id'] == 0) {
				$v ['send_tip'] = '所有用户';
			} else if ($v ['send_type'] == 0 && $v ['group_id'] != 0) {
				$v ['send_tip'] = M ( 'auth_group' )->where ( array (
						'id' => $v ['group_id'] 
				) )->getField ( 'title' );
			} else {
				$v ['send_tip'] = '指定用户';
			}
			if ($v ['msgType'] == 'text') {
				$v ['Content'] = '[文字] ' . $v ['content'];
			} else if ($v ['msgType'] == 'news') {
				$map ['group_id'] = $v ['news_group_id'];
				$appMsgData = M ( 'material_news' )->where ( $map )->select ();
				foreach ( $appMsgData as $vo ) {
					// $art ['description'] = $vo ['intro'];
					if (empty ( $vo ['url'] )) {
						$art_url = U ( 'Material/news_detail', array (
								'id' => $vo ['id'] 
						) );
						// 文章内容
						$v ['Content'] = '<a href="' . $art_url . '" >[图文] ' . $vo ['title'] . '</a>';
					} else {
						$art_url = $vo ['url'];
						$v ['Content'] = '<a href="' . $art_url . '" >[图文] ' . $vo ['title'] . '</a>';
					}
					$v ['image_url'] = get_cover_url ( $vo ['cover_id'] );
				}
			} else if ($v ['msgType'] == 'video') {
				$videoMaterial = M ( 'material_file' )->find ( $vo ['video_id'] );
				$v ['Content'] = '[语音] ' . $videoMaterial ['title'];
			} else if ($v ['msgType'] == 'voice') {
				$voiceMaterial = M ( 'material_file' )->find ( $vo ['voice_id'] );
				$v ['Content'] = '[视频] ' . $voiceMaterial ['title'];
			} else if ($v ['msgType'] == 'image') {
				$v ['Content'] = '[图片] ' . '<img src="' . get_cover_url ( $v ['image_id'] ) . '"/>';
				$v ['image_url'] = get_cover_url ( $v ['image_id'] );
			}
		}
		$url = U ( 'custom_sendall_lists' );
		$this->assign ( 'searchUrl', $url );
		$this->assign ( $list );
		$this->assign ( 'noraml_tips', '当用户发消息给认证公众号时，管理员可以在48小时内给用户回复信息' );
		
		$this->display ( '' );
	}
	// 群发消息管理
	function sendall_lists() {
		$act = strtolower ( ACTION_NAME );
		$res ['title'] = '用户发送消息';
		$res ['url'] = U ( 'message_lists' );
		$res ['class'] = $act == 'message_lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '高级群发消息';
		$res ['url'] = U ( 'sendall_lists' );
		$res ['class'] = $act == 'sendall_lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '客服接口群发消息';
		$res ['url'] = U ( 'custom_sendall_lists' );
		$res ['class'] = $act == 'custom_sendall_lists' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
		
		$this->assign ( 'nav', $nav );
		$day = I ( 'send_time', 1 );
		$now_day = strtotime ( time_format ( time (), 'Y-m-d' ) );
		if ($day == 1) {
			// 最近五天
			$time = $now_day - 4 * 24 * 60 * 60;
			$map ['cTime'] = array (
					'egt',
					$time 
			);
		} else if ($day == 2) {
			// 今天
			$map ['cTime'] = array (
					'egt',
					$now_day 
			);
		} else if ($day == 3) {
			// 昨天
			$zhuo_day = $now_day - 1 * 24 * 60 * 60;
			$map ['cTime'] = array (
					'between',
					$zhuo_day,
					$now_day 
			);
		} else if ($day == 4) {
			// 前天
			$qian_day = $now_day - 2 * 24 * 60 * 60;
			$map ['cTime'] = array (
					'between',
					$qian_day,
					$now_day 
			);
		} else if ($day == 5) {
			// 更早
			$qian_day = $now_day - 4 * 24 * 60 * 60;
			$map ['cTime'] = array (
					'elt',
					$qian_day 
			);
		}
		
		// $map ['FromUserName'] = I ( 'openid' );
		$map ['token'] = get_token ();
		$list = M ( 'message' )->where ( $map )->order ( 'id desc' )->selectPage ();
		// dump($shibai);
		// dump($chenggong);
		
		foreach ( $list ['list_data'] as &$v ) {
			
			if ($v ['send_type'] == 0 && $v ['group_id'] == 0) {
				$v ['send_tip'] = '所有用户';
			} else if ($v ['send_type'] == 0 && $v ['group_id'] != 0) {
				$v ['send_tip'] = M ( 'auth_group' )->where ( array (
						'id' => $v ['group_id'] 
				) )->getField ( 'title' );
			} else {
				$v ['send_tip'] = '指定用户';
			}
			if ($v ['msgtype'] == 'text') {
				$v ['Content'] = '[文字] ' . $v ['content'];
			} else if ($v ['msgtype'] == 'news') {
				$map ['group_id'] = $v ['appmsg_id'];
				$appMsgData = M ( 'material_news' )->where ( $map )->select ();
				foreach ( $appMsgData as $vo ) {
					// $art ['description'] = $vo ['intro'];
					if (empty ( $vo ['url'] )) {
						$art_url = U ( 'Material/news_detail', array (
								'id' => $vo ['id'] 
						) );
						// 文章内容
						$v ['Content'] = '<a href="' . $art_url . '" >[图文] ' . $vo ['title'] . '</a>';
					} else {
						$art_url = $vo ['url'];
						$v ['Content'] = '<a href="' . $art_url . '" >[图文] ' . $vo ['title'] . '</a>';
					}
					$v ['image_url'] = get_cover_url ( $vo ['cover_id'] );
				}
			} else if ($v ['msgtype'] == 'mpvideo') {
				$videoMaterial = M ( 'material_file' )->find ( $vo ['video_id'] );
				$v ['Content'] = '[语音] ' . $videoMaterial ['title'];
			} else if ($v ['msgtype'] == 'voice') {
				$voiceMaterial = M ( 'material_file' )->find ( $vo ['voice_id'] );
				$v ['Content'] = '[视频] ' . $voiceMaterial ['title'];
			} else if ($v ['msgType'] == 'image') {
				$v ['Content'] = '[图片] ' . '<img src="' . get_cover_url ( $v ['image_id'] ) . '"/>';
				$v ['image_url'] = get_cover_url ( $v ['image_id'] );
			}
		}
		$url = U ( 'sendall_lists' );
		$this->assign ( 'searchUrl', $url );
		$this->assign ( $list );
		$this->assign ( 'noraml_tips', '当用户发消息给认证公众号时，管理员可以在48小时内给用户回复信息' );
		
		$this->display ();
	}
}
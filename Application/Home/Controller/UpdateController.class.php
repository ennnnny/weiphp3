<?php

namespace Home\Controller;

class UpdateController extends HomeController {
	// 合并user表和follow表的用户数据
	function user_merge() {
		set_time_limit ( 0 );
		$usercopy = M ( 'user_copy' )->select ();
		foreach ( $usercopy as $vo ) {
			$user = array (
					'uid' => $vo ['uid'],
					'nickname' => $vo ['nickname'],
					'password' => $vo ['password'],
					'truename' => $vo ['truename'],
					'mobile' => $vo ['mobile'],
					'email' => $vo ['email'],
					'sex' => $vo ['sex'],
					'headimgurl' => $vo ['headimgurl'],
					'city' => $vo ['city'],
					'province' => $vo ['province'],
					'country' => $vo ['country'],
					'language' => $vo ['language'],
					'score' => intval ( $vo ['score'] ),
					'experience' => intval ( $vo ['experience'] ),
					
					'login_count' => intval ( $vo ['login'] ),
					'reg_ip' => $vo ['reg_ip'],
					'reg_time' => $vo ['reg_time'],
					'last_login_ip' => $vo ['last_login_ip'],
					'last_login_time' => $vo ['last_login_time'],
					
					'status' => intval ( $vo ['status'] ),
					'is_init' => intval ( $vo ['is_init'] ),
					'is_audit' => intval ( $vo ['is_audit'] ) 
			);
			$map ['uid'] = $vo ['uid'];
			if (M ( 'user' )->where ( $map )->find ()) {
				$res = M ( 'user' )->where ( $map )->save ( $user );
			} else {
				$res = M ( 'user' )->add ( $user );
			}
			lastsql ();
			dump ( $res );
			$manager = array (
					'uid' => $vo ['uid'],
					'has_public' => intval ( $vo ['has_public'] ),
					'headface_url' => $vo ['headface_url'],
					
					'GammaAppId' => $vo ['GammaAppId'],
					'GammaSecret' => $vo ['GammaSecret'],
					
					'copy_right' => $vo ['copy_right'],
					'tongji_code' => $vo ['tongji_code'],
					'website_logo' => $vo ['website_logo'] 
			);
			if (M ( 'manager' )->where ( $map )->find ()) {
				$res = M ( 'manager' )->where ( $map )->save ( $manager );
			} else {
				$res = M ( 'manager' )->add ( $manager );
			}
			lastsql ();
			dump ( $res );
		}
	}
	function follow_merge() {
		set_time_limit ( 0 );
		$id = I ( 'id', 0, 'intval' );
		$map ['id'] = array (
				'gt',
				$id 
		);
		$follow = M ( 'follow' )->where ( $map )->limit ( 10 )->order ( 'id asc' )->select ();
		if (! $follow) {
			die ( 'It is Over!' );
		}
		foreach ( $follow as $vo ) {
			$user = array (
					'uid' => $vo ['id'],
					'nickname' => $vo ['nickname'],
					'password' => $vo ['password'],
					'truename' => $vo ['truename'],
					'mobile' => $vo ['mobile'],
					'email' => $vo ['email'],
					'sex' => $vo ['sex'],
					'headimgurl' => $vo ['headimgurl'],
					'city' => $vo ['city'],
					'province' => $vo ['province'],
					'country' => $vo ['country'],
					'language' => $vo ['language'],
					'score' => intval ( $vo ['score'] ),
					'experience' => intval ( $vo ['experience'] ),
					'unionid' => $vo ['unionid'],
					
					'status' => intval ( $vo ['status'] ),
					'is_init' => 1,
					'is_audit' => 1 
			);
			
			$map ['uid'] = $param ['id'] = $vo ['id'];
			if (M ( 'user' )->where ( $map )->find ()) {
				$res = M ( 'user' )->where ( $map )->save ( $user );
			} else {
				$res = M ( 'user' )->add ( $user );
			}
			lastsql ();
			dump ( $res );
		}
		
		$url = U ( 'follow_merge', $param );
		echo '<script>window.location.href="' . $url . '"</script> ';
	}
	
	// 处理model表的field_sort字段
	function field_sort() {
		$list = M ( 'model' )->select ();
		// dump ( $list );
		foreach ( $list as $v ) {
			if (empty ( $v ['field_sort'] ))
				continue;
			
			$field_sort = json_decode ( $v ['field_sort'], true );
			if (! is_array ( $field_sort [1] ))
				continue;
			
			$field_sort = json_encode ( $field_sort [1] );
			// dump ( $field_sort );
			$map ['id'] = $v ['id'];
			$res = M ( 'model' )->where ( $map )->setField ( 'field_sort', $field_sort );
			dump ( $res );
			lastsql ();
		}
		dump ( 'It is over' );
	}
}

<?php

namespace Addons\Scratch\Controller;

use Home\Controller\AddonsController;

class ScratchController extends AddonsController {
	function edit() {
		$id = I ( 'id' );
		$model = $this->getModel ();
		
		if (IS_POST) {
			$this->checkPostData ();
			
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->_saveKeyword ( $model, $id );
				
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
				D ( 'Scratch' )->getScratchInfo ( $id, true );
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			// 获取数据
			$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $model ['title'];
			
			$this->_deal_data ();
			
			$this->display ();
		}
	}
	function checkPostData() {
		if (! I ( 'post.keyword' )) {
			$this->error ( '关键词不能为空' );
		}
		if (! I ( 'post.title' )) {
			$this->error ( '标题不能为空' );
		}
		if (! I ( 'post.use_tips' )) {
			$this->error ( '使用说明不能为空' );
		}
		if (! I ( 'post.end_tips' )) {
			$this->error ( '过期说明不能为空' );
		}
		if (! I ( 'post.start_time' )) {
			$this->error ( '请选择开始时间' );
		} else if (! I ( 'post.end_time' )) {
			$this->error ( '请选择结束时间' );
		} else if (strtotime ( I ( 'post.start_time' ) ) > strtotime ( I ( 'post.end_time' ) )) {
			$this->error ( '开始时间不能大于结束时间' );
		}
		if (I ( 'post.predict_num' ) <= 0) {
			$this->error ( '预计参与人数必须大于0！' );
		}
	}
	function add() {
		$model = $this->getModel ();
		if (IS_POST) {
			$this->checkPostData ();
			
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->_saveKeyword ( $model, $id );
				
				// 清空缓存
				method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
				D ( 'Scratch' )->getScratchInfo ( $id, true );
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			
			$this->assign ( 'fields', $fields );
			$this->meta_title = '新增' . $model ['title'];
			
			$this->_deal_data ();
			
			$this->display ();
		}
	}
	
	// 增加或者编辑时公共部分
	function _deal_data() {
		$normal_tips = '插件场景限制参数说明：格式：[插件名:id],如<br/>
				[投票:10]，表示对ID为10的投票投完对能领取<br/>
				[投票:*]，表示只要投过票就可以领取<br/>
				[微调研:15]，表示完成ID为15的调研就能领取<br/>
				[微考试:10]，表示完成ID为10的考试就能领取<br/>';
		$this->assign ( 'normal_tips', $normal_tips );
	}
	// 异步加载刮刮卡数据
	function ajax_data() {
		$public_info = get_token_appinfo ();
		$this->assign ( 'public_info', $public_info );
		$id = I ( 'id' );
		$data = D ( 'Scratch' )->getScratchInfo ( $id );
		$this->assign ( 'data', $data );
		// dump($data);
		
		// 奖项
		$addon = 'Scratch';
		$prizes = D ( 'Prize' )->getPrizes ( $id, $addon );
		$this->assign ( 'prizes', $prizes );
		
		// 抽奖记录
		// $all_prizes = M ( 'sn_code' )->where ( $map )->order ( 'id desc' )->select ();
		$all_prizes = D ( 'SnCode' )->getSnCodes ( $id, $addon );
		// dump ( $all_prizes );
		foreach ( $all_prizes as $all ) {
			if ($all ['prize_id'] > 0) {
				$has [$all ['prize_id']] += 1; // 每个奖项已经中过的次数
				$new_prizes [] = $all; // 最新中奖记录
				$all ['uid'] == $this->mid && $my_prizes [] = $all; // 我的中奖记录
			} else {
				$no_count += 1; // 没有中奖的次数
			}
			
			// 记录我已抽奖的次数
			$all ['uid'] == $this->mid && $my_count += 1;
		}
		
		$this->assign ( 'new_prizes', $new_prizes );
		$this->assign ( 'my_prizes', $my_prizes );
		// dump ( $new_prizes );
		// dump ( $my_prizes );
		
		// 权限判断
		$follow = get_followinfo ( $this->mid );
		$is_admin = is_login ();
		$error = '';
		if ($data ['start_time'] > time ()) {
			$error = '活动还没开始';
		}
		if ($data ['end_time'] <= time ()) {
			$error = '活动已结束';
		} else if ($data ['max_num'] > 0 && $data ['max_num'] <= $my_count) {
			$error = '您的刮卡机会已用完啦';
		} else if ($data ['follower_condtion'] > intval ( $follow ['status'] ) && ! $is_admin) {
			switch ($data ['follower_condtion']) {
				case 1 :
					$error = '关注后才能参与';
					break;
				case 2 :
					$error = '用户绑定后才能参与';
					break;
				case 3 :
					$error = '领取会员卡后才能参与';
					break;
			}
		} else if ($data ['credit_conditon'] > intval ( $follow ['score'] ) && ! $is_admin) {
			$error = '您的金币值不足';
		} else if ($data ['credit_bug'] > intval ( $follow ['score'] ) && ! $is_admin) {
			$error = '您的金币值不够扣除';
		} else if (! empty ( $data ['addon_condition'] )) {
			addon_condition_check ( $data ['addon_condition'] ) || $error = '您没权限参与';
		}
		$this->assign ( 'error', $error );
		// 抽奖算法
		if (empty ( $error )) {
			$prize = D ( 'Scratch' )->_lottery ( $data, $prizes, $new_prizes, $my_count, $has, $no_count );
			$prizes = D ( 'Prize' )->getPrizes ( $id, $addon );
			$prize['img']=get_cover_url($prize['img']);
			$this->assign ( 'prize', $prize );
		}
		$content = $this->fetch ( ONETHINK_ADDON_PATH . 'Scratch/View/default/Scratch/data.html' );
		$returnData = I ( 'callback' ) . '({"html":"' . rawurlencode ( $content ) . '","prizeJson":"' . rawurlencode ( json_encode ( $prize ) ) . '"})';
		echo $returnData;
		exit;
	}
	// 记录中奖数据到数据库
	function set_sn_code() {
		$data ['sn'] = uniqid ();
		$data ['uid'] = $this->mid;
		$data ['cTime'] = time ();
		$data ['addon'] = 'Scratch';
		$data ['target_id'] = I ( 'id' );
		$data ['token'] = get_token ();
		$data ['prize_id'] = $prize_id = I ( 'prize_id' );
		$title = '';
		if (! empty ( $prize_id )) {
			// $title = M ( 'prize' )->where ( $map )->getField ( 'title' );
			$prize = D ( 'Prize' )->getPrizeInfo ( $prize_id );
			$title = $prize ['title'];
			$title || $title = '';
		}
		$data ['prize_title'] = $title;
		// dump ( $data );
		$res = M ( 'sn_code' )->add ( $data );
		D ( 'SnCode' )->getSnCodes ( $data ['target_id'], $data ['addon'], true );
		if ($res) {
			// 更新获取数
			$scratch = D ( 'Scratch' )->getScratchInfo ( $data ['target_id'] );
			$s ['collect_count'] = $scratch ['collect_count'] + 1;
			// M ( "scratch" )->where ( 'id=' . $data ['target_id'] )->setInc ( "collect_count" );
			D ( 'Scratch' )->updateCount ( $data ['target_id'], $s );
			// 扣除积分
			$data = D ( 'Scratch' )->getScratchInfo ( $data ['target_id'] );
			if (! empty ( $data ['credit_bug'] )) {
				$credit ['score'] = $data ['credit_bug'];
				$credit ['experience'] = 0;
				add_credit ( 'scratch_credit_bug', 5, $credit );
			}
		}
		echo $res;
	}
	function index() {
		$id = I ( 'id' );
		// $data = M ( 'scratch' )->find ( $id );
		$data = D ( 'Scratch' )->getScratchInfo ( $id );
		$this->assign ( 'data', $data );
		// 奖项
		$addon = 'Scratch';
		// $prizes = M ( 'prize' )->where ( $map )->select ();
		$prizes = D ( 'Prize' )->getPrizes ( $id, $addon );
		$this->assign ( 'prizes', $prizes );
		
		$ajaxDataUrl = addons_url ( 'Scratch://Scratch/ajax_data', array (
				'id' => $data ['id'] 
		) );
		$this->assign ( 'ajaxDataUrl', $ajaxDataUrl );
		// 添加模板目录
		$this->display ();
	}
	function preview(){
		$vote_id = I ( 'id', 0, 'intval' );
		$url = U('index',array('id'=>$vote_id));
		$this -> assign('url',$url);
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
	
	function lists() {
	    $isAjax = I ( 'isAjax' );
	    $isRadio = I ( 'isRadio' );
	    $model = $this->getModel ( 'scratch' );
	    $list_data = $this->_get_model_list ( $model, 0, 'id desc', true );
	    // 		判断该活动是否已经设置投票调查
	    if ($isAjax) {
	        $this->assign('isRadio',$isRadio);
	        $this->assign ( $list_data );
	        $this->display ( 'ajax_lists_data' );
	    } else {
	        $this->assign ( $list_data );
	        $this->display ();
	    }
	}
	
}

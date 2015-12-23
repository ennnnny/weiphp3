<?php

namespace Addons\Guess\Controller;

use Home\Controller\AddonsController;

class GuessController extends AddonsController {
	protected $model;
	protected $option;
	public function __construct() {
		parent::__construct ();
		$this->model = getModelByName ( $_REQUEST ['_controller'] );
		$this->model || $this->error ( '模型不存在！' );
		
		$this->assign ( 'model', $this->model );
		
		$this->option = getModelByName ( 'guess_option' );
		$this->assign ( 'option', $this->option );
	}
	function index() {
		$info = $publicInfo = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		$param ['id'] = $id = I ( 'id' );
		$openid = get_openid ();
		$token = get_token ();
		$followid=$this->mid;
		$info = $this->_getGuessInfo ( $id );
		$canJoin = ! empty ( $openid ) && ! empty ( $token ) && ! ($this->_is_overtime ( $id )) && ! ($this->_is_join ( $id,$followid, $token ));
		$this->assign ( 'canJoin', $canJoin );
		$this->assign ( 'publicInfo', $publicInfo );
		$this -> display();
	}
	function preview(){
		$id = I ( 'id', 0, 'intval' );
		$url = U('index',array('id'=>$id));
		$this -> assign('url',$url);
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
	
	function lists() {
	    $isAjax = I ( 'isAjax' );
	    $isRadio = I ( 'isRadio' );
	    $model = $this->getModel ( 'guess' );
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
	public function edit() {
		// 获取模型信息
		$id = I ( 'id', 0, 'intval' );
		if (IS_POST) {
			$_POST ['mTime'] = time ();
			// $_POST ['create_time'] = time ();
			$this->checkPostData ();
			
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			// 增加选项
			$res = D ( 'GuessOption' )->set ( I ( 'post.id' ), I ( 'post.' ) );
			D ( 'GuessOption' )->getGuessOption ( $id, true );
			
			if ($Model->create () && $Model->save () || $res) {
				D ( 'Guess' )->getInfo ( $id, true );
				$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'lists?model=' . $this->model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $this->model ['id'] );
			
			// 获取数据
			$data = M ( get_table_name ( $this->model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			
			$option_list = M ( 'guess_option' )->where ( 'guess_id=' . $id )->order ( '`order` asc' )->select ();
			$this->assign ( 'option_list', $option_list );
			// dump($data);
			$sucai_info = get_sucai_template_info ( $data ['template'], 'Guess' );
			$this->assign ( 'sucai_info', $sucai_info );
			// dump($sucai_info);
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $this->model ['title'];
			$this->display ( T ( 'Addons://Guess@Guess/edit' ) );
		}
	}
	public function add() {
		if (IS_POST) {
			// 自动补充token
			$this->checkPostData ();
			$_POST ['token'] = get_token ();
			// $_POST ['create_time'] = time ();
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $guess_id = $Model->add ()) {
				// 增加选项
				D ( 'GuessOption' )->set ( $guess_id, I ( 'post.' ) );
				D ( 'GuessOption' )->getGuessOption ( $guess_id, true );
				$this->success ( '添加' . $this->model ['title'] . '成功！', U ( 'lists?model=' . $this->model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			
			$guess_fields = get_model_attribute ( $this->model ['id'] );
			$this->assign ( 'fields', $guess_fields );
			// 选项表
			$option_fields = get_model_attribute ( $this->option ['id'] );
			$this->assign ( 'option_fields', $option_fields );
			$this->display ( $this->model ['template_add'] ? $this->model ['template_add'] : T ( 'Addons://Guess@Guess/add' ) );
		}
	}
	protected function checkPostData() {
		if (! I ( 'post.title' )) {
			$this->error ( '请填写竞猜标题' );
		}
		if (! I ( 'post.desc' )) {
			$this->error ( '请填写活动说明' );
		}
		if (! I ( 'post.cover' )) {
			$this->error ( '请选择主题图片' );
		}
		// 判断时间选择是否正确
		if (! I ( 'post.start_time' )) {
			$this->error ( '请选择开始时间' );
		} else if (! I ( 'post.end_time' )) {
			$this->error ( '请选择结束时间' );
		} else if (strtotime ( I ( 'post.start_time' ) ) > strtotime ( I ( 'post.end_time' ) )) {
			$this->error ( '开始时间不能大于结束时间' );
		}
		// 判断选项是否有填
		
		if (! I ( 'post.name' ) || count ( I ( 'post.name' ) ) < 2) {
			$this->error ( '请添加至少两个竞猜选项选项！' );
		} else {
			$optionName = I ( 'post.name' );
			foreach ( $optionName as $k => $v ) {
				if (empty ( $v )) {
					$this->error ( '选项标题不能为空' );
				}
			}
		}
	}
	protected function checkAttr($Model, $model_id) {
		$fields = get_model_attribute ( $model_id );
		$validate = $auto = array ();
		foreach ( $fields as $key => $attr ) {
			if ($attr ['is_must']) { // 必填字段
				$validate [] = array (
						$attr ['name'],
						'require',
						$attr ['title'] . '必须!' 
				);
			}
			// 自动验证规则
			if (! empty ( $attr ['validate_rule'] ) || $attr ['validate_type'] == 'unique') {
				$validate [] = array (
						$attr ['name'],
						$attr ['validate_rule'],
						$attr ['error_info'] ? $attr ['error_info'] : $attr ['title'] . '验证错误',
						0,
						$attr ['validate_type'],
						$attr ['validate_time'] 
				);
			}
			// 自动完成规则
			if (! empty ( $attr ['auto_rule'] )) {
				$auto [] = array (
						$attr ['name'],
						$attr ['auto_rule'],
						$attr ['auto_time'],
						$attr ['auto_type'] 
				);
			} elseif ('checkbox' == $attr ['type']) { // 多选型
				$auto [] = array (
						$attr ['name'],
						'arr2str',
						3,
						'function' 
				);
			} elseif ('datetime' == $attr ['type']) { // 日期型
				$auto [] = array (
						$attr ['name'],
						'strtotime',
						3,
						'function' 
				);
			}
		}
		return $Model->validate ( $validate )->auto ( $auto );
	}
	function _getGuessInfo($id) {
		// 检查ID是否合法
		if (empty ( $id ) || 0 == $id) {
			$this->error ( "错误的竞猜ID" );
		}
		
		// $map ['id'] = $map2 ['guess_id'] = $map3['guess_id'] = intval ( $id );
		$guess_id = intval ( $id );
		$follow_id = get_mid ();
		
		// $info = M ( 'guess' )->where ( $map )->find ();
		$info = D ( 'Guess' )->getInfo ( $guess_id );
		$this->assign ( 'info', $info );
		// dump($info);
		// $opts = M ( 'guess_option' )->where ( $map2 )->order ( '`order` asc' )->select ();
		$opts = D ( 'GuessOption' )->getGuessOption ( $guess_id );
		foreach ( $opts as $p ) {
			$total += $p ['guess_count'];
		}
		foreach ( $opts as &$vo ) {
			$vo ['percent'] = round ( $vo ['guess_count'] * 100 / $total, 1 );
		}
		$this->assign ( 'opts', $opts );
		$this->assign ( 'num_total', $total );
		
		// $voteInfo = M ( 'guess_log' )->where ( $map3 )->select ();
		$voteInfo = D ( 'GuessLog' )->getFollowLog ( $follow_id, $guess_id );
		// dump($voteInfo);
		// exit;
		if ($voteInfo) {
			$joinData = explode ( ',', $voteInfo [0] ['optionids'] );
			$this->assign ( 'joinData', $joinData );
			// dump($joinData);
			// exit;
		}
		
		return $info;
	}
	// 保存用户竞猜数据
	function saveGuess() {
		$token = get_token ();
		$opts_ids = array_filter ( I ( 'post.optArr' ) );
		
		$guess_id = intval ( $_POST ["guess_id"] );
		if (empty ( $guess_id ) || 0 == $guess_id) {
			$returnData ['msg'] = "错误的投票ID";
			$returnData ['result'] = "fail";
			$this->ajaxReturn ( $returnData, "JSON" );
			exit ();
		}
		if (empty ( $_POST ['optArr'] )) {
			$returnData ['msg'] = "请先选择投票项";
			$returnData ['result'] = "fail";
			$this->ajaxReturn ( $returnData, "JSON" );
			exit ();
		}
		if ($this->_is_overtime ( $guess_id )) {
			$returnData ['msg'] = "请在指定的时间内投票";
			$returnData ['result'] = "fail";
			$this->ajaxReturn ( $returnData, "JSON" );
			exit ();
		}
		if ($this->_is_join ( $guess_id, $this->mid, $token )) {
			$returnData ['msg'] = "请不要重复参与活动";
			$returnData ['result'] = "fail";
			$this->ajaxReturn ( $returnData, "JSON" );
			exit ();
		}
		
		// 如果没投过，就添加
		$data ["user_id"] = $this->mid;
		$data ["guess_id"] = $guess_id;
		$data ["token"] = $token;
		$data ["optionIds"] = implode ( ',', $opts_ids );
		$data ["cTime"] = time ();
		
		$addid = M ( "guess_log" )->add ( $data );
		
		D ( 'GuessLog' )->getFollowLog ( $data ['user_id'], $data ['guess_id'], $token, true );
		// 投票选项信息的num+1
		$optionDao=D('Addons://Guess/GuessOption');
		foreach ( $opts_ids as $v ) {
		  $v = intval ( $v );
		  $map['id']=$v;
		  $res1 = $optionDao->where ( $map)->setInc ( "guess_count" );
		  $optionDao->getInfo($v,true);
		}
		$optionDao->getGuessOption($guess_id,true);
// 		D ( 'GuessOption' )->updateOptCount ( $guess_id, $opts_ids );
		// 投票信息的vote_count+1
		// $res = M ( "guess" )->where ( 'id=' . $guess_id )->setInc ( "guess_count" );
		$guess = D ( 'Guess' )->getInfo ( $guess_id );
		$g ['guess_count'] = $guess ['guess_count'] + 1;
		$res = D ( 'Guess' )->update ( $guess_id, $g );
		// 增加积分
		// add_credit ( 'vote' );
		$returnData ['msg'] = "竞猜成功,静候结果";
		$returnData ['result'] = "success";
		$this->ajaxReturn ( $returnData, "JSON" );
	}
	// 已过期返回 true ,否则返回 false
	private function _is_overtime($guess_id) {
		// 先看看投票期限过期与否
		// $the_vote = M ( "guess" )->where ( "id=$guess_id" )->find ();
		$the_vote = D ( 'Guess' )->getInfo ( $guess_id );
		
		if (! empty ( $the_vote ['start_time'] ) && $the_vote ['start_time'] > NOW_TIME)
			return ture;
		
		$deadline = $the_vote ['end_time'] + 86400;
		if (! empty ( $the_vote ['end_time'] ) && $deadline <= NOW_TIME)
			return ture;
		
		return false;
	}
	private function _is_join($guess_id, $user_id, $token) {
		$guess_limit = 1;
		// $list = M ( "guess_log" )->where ( "guess_id=$guess_id AND user_id='$user_id' AND token='$token' AND optionIds <>''" )->select ();
		// $count = count ( $list );
// 		$user_id=11869;
		$list = D ( 'GuessLog' )->getFollowLog ( $user_id, $guess_id, $token,true );
		$count = count ( $list );
		$info = array_pop ( $list );
		if ($info) {
			$joinData = explode ( ',', $info ['optionIds'] );
			$this->assign ( 'joinData', $joinData );
		}
		if ($count >= $guess_limit) {
			return true;
		}
		return false;
	}
	
	// ////////显示竞猜选项////////////////
	function guessOption() {
		$nav [0] ['title'] = "竞猜";
		$nav [0] ['class'] = "";
		$nav [0] ['url'] = U ( "lists" );
		$nav [1] ['title'] = "竞猜选项";
		$nav [1] ['class'] = "current";
		$this->assign ( 'nav', $nav );
		
		$this->assign ( 'add_button', false );
		$this->assign ( 'search_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		
		$guess_id = I ( 'guess_id' );
		$model = $this->option;
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data ['fields'];
		
		D ( 'GuessOption' )->updateOptCount ( $guess_id ,'');
		$page = I ( 'p', '1', 'intval' );
		
		$map ['guess_id'] = $guess_id;
		
		session ( 'common_condition', $map );
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();
		
		// 活动名称
		$guessinfo = D ( 'Guess' );
		$info = $guessinfo->getInfo ( $guess_id );
		$title = $info ['title'];
		foreach ( $data as $key => &$value ) {
			$value ['title'] = $title;
		}
		$list_data ['list_data'] = $data;
		$count = M ( $name )->where ( $map )->count ();
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		$this->assign ( $list_data );
		// dump($list_data);
		$this->display ( './Application/Home/View/default/Addons/lists.html' );
	}
	
	// ///////////显示所有参加竞猜记录////////////////
	function guessLog() {
		$guess_id = I ( 'guess_id' );
		$map ['guess_id'] = $guess_id;
		$this->common_log ( $guess_id, $map );
	}
	// ///////////根据竞猜选项显示对应的选项竞猜记录/////////////////////
	function optionLog() {
		$guess_id = I ( 'guess_id' );
		$title = '选项竞猜记录';
		$map ['guess_id'] = $guess_id;
		$map ['optionIds'] = I ( 'option_id' );
		$this->common_log ( $guess_id, $map, $title );
	}
	function common_log($guess_id, $map, $title = '竞猜记录') {
		// $guess_id=I('guess_id');
		$nav [0] ['title'] = "竞猜";
		$nav [0] ['class'] = "";
		$nav [0] ['url'] = U ( "lists" );
		$nav [1] ['title'] = "竞猜选项";
		$nav [1] ['class'] = "";
		$nav [1] ['url'] = addons_url ( 'Guess://Guess/guessOption?guess_id=' . $guess_id );
		
		$nav [2] ['title'] = $title;
		$nav [2] ['class'] = "current";
		$this->assign ( 'nav', $nav );
		
		$this->assign ( 'add_button', false );
		$this->assign ( 'search_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		
		$model = M('model')->getByName ( 'guess_log' );
		
		$list_data = $this->_list_grid ( $model );
		unset ( $list_data ['list_grids'] [5] );
		$fields = $list_data ['fields'];
		$page = I ( 'p', '1', 'intval' );
		
		// $map['guess_id']=$guess_id;
		// $map['optionIds']=I('option_id');
		session ( 'common_condition', $map );
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();
		
		$option = D ( 'GuessOption' );
		foreach ( $data as $key => &$value ) {
			$value ['user_name'] = get_nickname ( $value ['user_id'] );
			$info = $option->getInfo ( $value ['optionIds'] );
			$value ['optionIds'] = $info ['name'];
		}
		$list_data ['list_data'] = $data;
		$count = M ( $name )->where ( $map )->count ();
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		$this->assign ( $list_data );
		// dump($list_data);die();
		$this->display ( './Application/Home/View/default/Addons/lists.html' );
	}
}

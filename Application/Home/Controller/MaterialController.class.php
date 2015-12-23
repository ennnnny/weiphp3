<?php

namespace Home\Controller;

/**
 * 素材管理控制器
 */
class MaterialController extends HomeController {
	function _initialize() {
		parent::_initialize ();
		
		$act = strtolower ( ACTION_NAME );
		
		$res ['title'] = '图文素材';
		$res ['url'] = U ( 'material_lists' );
		$res ['class'] = $act == 'material_lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '图片素材';
		$res ['url'] = U ( 'picture_lists' );
		$res ['class'] = strpos ( $act, 'picture' ) !== false ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '语音素材';
		$res ['url'] = U ( 'voice_lists' );
		$res ['class'] = strpos ( $act, 'voice' ) !== false ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '视频素材';
		$res ['url'] = U ( 'video_lists' );
		$res ['class'] = strpos ( $act, 'video' ) !== false ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '文本素材';
		$res ['url'] = U ( 'text_lists' );
		$res ['class'] = strpos ( $act, 'text' ) !== false ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	public function lists() {
		redirect ( U ( 'material_lists' ) );
	}
	public function doAdd() {
		$textArr = array (
				1 => '一',
				2 => '二',
				3 => '三',
				4 => '四',
				5 => '五',
				6 => '六',
				7 => '七',
				8 => '八',
				9 => '九',
				10 => '十' 
		);
		$data = json_decode ( $_POST ['dataStr'], true );
		// dump ( $_POST );
		// dump ( $data );
		// exit ();
		$ids = array ();
		foreach ( $data as $key => $vo ) {
			$save = array ();
			foreach ( $vo as $k => $v ) {
				$save [$v ['name']] = safe ( $v ['value'] );
			}
			if (empty ( $save ['title'] )) {
				$this->error ( '请填写第' . $textArr [$key + 1] . '篇文章的标题' );
			}
			if (empty ( $save ['cover_id'] )) {
				$this->error ( '请上传第' . $textArr [$key + 1] . '篇文章的封面图片' );
			}
			if (empty ( $save ['content'] )) {
				$this->error ( '请填写第' . $textArr [$key + 1] . '篇文章的正文内容' );
			}
			if (! empty ( $save ['id'] )) { // 更新数据
				$map2 ['id'] = $save ['id'];
				M ( 'material_news' )->where ( $map2 )->save ( $save );
			} else { // 新增加
				$save ['cTime'] = NOW_TIME;
				$save ['manager_id'] = $this->mid;
				$save ['token'] = get_token ();
				$id = M ( 'material_news' )->add ( $save );
				if ($id) {
					$ids [] = $id;
				} else {
					if (! empty ( $ids )) {
						$map ['id'] = array (
								'in',
								$ids 
						);
						M ( 'material_news' )->where ( $map )->delete ();
					}
					$this->error ( '增加第' . $textArr [$key + 1] . '篇文章失败，请检查数据后重试' );
				}
			}
		}
		if (! empty ( $ids )) {
			$map ['id'] = array (
					'in',
					$ids 
			);
			$group_id = I ( 'get.group_id', 0, 'intval' );
			empty ( $group_id ) && $group_id = $ids [0];
			M ( 'material_news' )->where ( $map )->setField ( 'group_id', $group_id );
		}
		
		$this->success ( '操作成功', U ( 'material_lists' ) );
	}
	function material_lists() {
		$map ['manager_id'] = $this->mid;
		$map ['token'] = get_token ();
		$title = I ( 'title' );
		if (! empty ( $title )) {
			$map ['title'] = array (
					'like',
					"%$title%" 
			);
		}
		
		$field = 'id,title,cover_id,intro,group_id';
		$list = M ( 'material_news' )->where ( $map )->field ( $field . ',count(id) as count' )->group ( 'group_id' )->order ( 'group_id desc' )->selectPage ();
		
		foreach ( $list ['list_data'] as &$vo ) {
			if ($vo ['count'] == 1)
				continue;
			
			$map2 ['group_id'] = $vo ['group_id'];
			$map2 ['id'] = array (
					'exp',
					'!=' . $vo ['id'] 
			);
			
			$vo ['child'] = M ( 'material_news' )->field ( $field )->where ( $map2 )->select ();
		}
		$this->assign ( $list );
		$this->assign ( 'add_url', U ( 'add_material' ) );
		$this->display ();
	}
	function add_material() {
		$map ['group_id'] = I ( 'group_id', 0, 'intval' );
		if (! empty ( $map ['group_id'] )) {
			$list = M ( 'material_news' )->where ( $map )->order ( 'id asc' )->select ();
			$count = count ( $list );
			
			$main = $list [0];
			unset ( $list [0] );
			if (! empty ( $list )) {
				$others = $list;
			}
			
			$this->assign ( 'main', $main );
			$this->assign ( 'others', $others );
		}
		
		$this->assign ( 'post_url', U ( 'doAdd', $map ) );
		$this->display ();
	}
	function del_material_by_id() {
		$map ['id'] = I ( 'id' );
		echo M ( 'material_news' )->where ( $map )->delete ();
	}
	function del_material_by_groupid() {
		$map ['group_id'] = I ( 'group_id' );
		$map['token']=get_token();
		$res = M ( 'material_news' )->where ( $map )->delete ();
		if ($res) {
			$this->success ( '删除成功' );
		} else {
			$this->error ( '删除失败' );
		}
	}
	function material_data() {
		$map ['manager_id'] = $this->mid;
		$map ['token'] = get_token ();
		
		$field = 'id,title,cover_id,intro,group_id';
		$list = M ( 'material_news' )->where ( $map )->field ( $field . ',count(id) as count' )->group ( 'group_id' )->order ( 'group_id desc' )->selectPage ();
		
		foreach ( $list ['list_data'] as &$vo ) {
			if ($vo ['count'] == 1)
				continue;
			
			$map2 ['group_id'] = $vo ['group_id'];
			$map2 ['id'] = array (
					'exp',
					'!=' . $vo ['id'] 
			);
			
			$vo ['child'] = M ( 'material_news' )->field ( $field )->where ( $map2 )->select ();
		}
		// dump ( $list );
		$this->assign ( $list );
		// 弹框数据
		$this->display ();
	}
	function get_news_by_group_id(){
	    $map['group_id']=I('group_id');
	    $map ['manager_id'] = $this->mid;
	    $map ['token'] = get_token ();
	    $appMsgData=M ( 'material_news' )->where($map)->select();
	    foreach ($appMsgData as $vo){
	        if ($vo['id']==$map['group_id']){
	            $articles[]=array(
	                'id'=>$vo['id'],
	                'title'=>$vo['title'],
	                'intro'=>empty($vo['description'])?'':$vo['description'],
	                'img_url'=>get_cover_url($vo['cover_id'])
	            );
	        }else{
	            //文章内容
	            $art['id']=$vo['id'];
	            $art ['title'] = $vo['title'];
	            $art ['img_url']=get_cover_url($vo['cover_id']);
	            $articles[]=$art;
	        }
	    }
	    $this->ajaxReturn($articles);
	}
	
	// 与微信同步
	function syc_news_to_wechat() {
		// 上传本地素材
		$map ['media_id'] = '0';
		$map ['manager_id'] = $this->mid;
		$map ['token'] = get_token ();
		
		$field = 'id,title,cover_id,intro,author,content,group_id,thumb_media_id';
		$list = M ( 'material_news' )->limit ( 10 )->where ( $map )->field ( $field . ',count(id) as count' )->group ( 'group_id' )->order ( 'group_id desc' )->select ();
		if (empty ( $list )) {
			$this->success ( '上传素材完成', U ( 'material_lists' ) );
			exit ();
		}
// 		dump($list);
		foreach ( $list as $vo ) {
			$ids [] = $vo ['id'];
			$gids [] = $vo ['group_id'];
		}
		$map2 ['id'] = array (
				'not in',
				$ids 
		);
		$map2 ['group_id'] = array (
				'in',
				$gids 
		);
		$child = M ( 'material_news' )->where ( $map2 )->field ( $field )->select ();
		empty ( $child ) || $list = array_merge ( $list, $child );
		
		foreach ( $list as $vo ) {
			$data ['title'] = $vo ['title'];
			$data ['thumb_media_id'] = empty ( $vo ['thumb_media_id'] ) ? $this->_thumb_media_id ( $vo ['cover_id'] ) : $vo ['thumb_media_id'];
			$data ['author'] = $vo ['author'];
			$data ['digest'] = $vo ['intro'];
			$data ['show_cover_pic'] = 1;
			$data ['content'] = str_replace ( '"', '\'', $vo ['content'] );
			$data ['content_source_url'] = U ( 'news_detail', array (
					'id' => $vo ['id'] 
			) );
			
			$articles [$vo ['group_id']] [] = $data;
		}
		$url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=' . get_access_token ();
// 		dump($url);
		foreach ( $articles as $group_id => $art ) {
			$param ['articles'] = $art;
			// dump(JSON($param));
			$res = post_data ( $url, $param );
			if ($res ['errcode'] != 0) {
				$this->error ( error_msg ( $res ) );
			} else {
				$map3 ['group_id'] = $group_id;
				M ( 'material_news' )->where ( $map3 )->setField ( 'media_id', $res ['media_id'] );
				$newsUrl = $this->_news_url ( $res ['media_id'] );
				foreach ( $art as $a ) {
				    $map4['group_id']=$group_id;
					$map4 ['title'] = $a ['title'];
					M ( 'material_news' )->where ( $map4 )->setField ( 'url', $newsUrl [$a ['title']] );
				}
			}
		}
		$url = U ( 'syc_news_to_wechat' );
		$this->success ( '上传本地素材到微信中，请勿关闭', $url );
	}
	// 获取图文素材url
	function _news_url($media_id) {
		$url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . get_access_token ();
		$param ['media_id'] = $media_id;
		$news = post_data ( $url, $param );
		if (isset ( $news ['errcode'] ) && $news ['errcode'] != 0) {
			$this->error ( error_msg ( $news ) );
		}
		foreach ( $news ['news_item'] as $vo ) {
			$newsUrl [$vo ['title']] = $vo ['url'];
		}
		return $newsUrl;
	}
	function syc_news_from_wechat() {
		$url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . get_access_token ();
		$param ['type'] = 'news';
		$param ['offset'] = I ( 'offset', 0, 'intval' );
		$param ['count'] = 20;
		$list = post_data ( $url, $param );
		if (isset ( $list ['errcode'] ) && $list ['errcode'] != 0) {
			$this->error ( error_msg ( $list ) );
		}
		if (empty ( $list ['item'] )) {
			$this->success ( '下载素材完成', U ( 'material_lists' ) );
			exit ();
		}
		$map ['media_id'] = array (
				'in',
				getSubByKey ( $list ['item'], 'media_id' ) 
		);
		$map['token']=get_token();
		$map['manager_id']=$this->mid;
		$has = M ( 'material_news' )->where ( $map )->getField ( 'DISTINCT media_id,id' );
		foreach ( $list ['item'] as $item ) {
			$media_id = $item ['media_id'];
			if (isset ( $has [$media_id] ))
				continue;
			
			$ids = array ();
			foreach ( $item ['content'] ['news_item'] as $vo ) {
				$data ['title'] = $vo ['title'];
				$data ['author'] = $vo ['author'];
				$data ['intro'] = $vo ['digest'];
				$data ['content'] = $vo ['content'];
				$data ['thumb_media_id'] = $vo ['thumb_media_id'];
				$data ['media_id'] = $media_id;
				$data ['cover_id'] = $this->_download_imgage ( $data ['thumb_media_id'] );
				$data ['url'] = $vo ['url'];
				$data ['cTime'] = NOW_TIME;
				$data ['manager_id'] = $this->mid;
				$data ['token'] = get_token ();
				$ids [] = M ( 'material_news' )->add ( $data );
			}
			
			if (! empty ( $ids )) {
				$map2 ['id'] = array (
						'in',
						$ids 
				);
				M ( 'material_news' )->where ( $map2 )->setField ( 'group_id', $ids [0] );
			}
		}
		$url = U ( 'syc_news_from_wechat', array (
				'offset' => $param ['offset'] + $list ['item_count'] 
		) );
		$this->success ( '下载微信素材中，请勿关闭', $url );
	}
	function _thumb_media_id($cover_id) {
		$cover = get_cover ( $cover_id );
		$driver = C ( 'PICTURE_UPLOAD_DRIVER' );
		if ($driver != 'Local' && ! file_exists ( SITE_PATH . $cover ['path'] )) { // 先把图片下载到本地
			
			$pathinfo = pathinfo ( SITE_PATH . $cover ['path'] );
			mkdirs ( $pathinfo ['dirname'] );
			
			$content = wp_file_get_contents ( $cover ['url'] );
			$res = file_put_contents ( SITE_PATH . $cover ['path'], $content );
			if (! $res) {
				$this->error ( '远程图片下载失败' );
			}
		}
		
		$path = $cover ['path'];
		if (! $path) {
			$this->error ( '获取文章封面失败，请确认是否增加封面' );
		}
		
		$param ['type'] = 'thumb';
		$param ['media'] = '@' . realpath ( SITE_PATH . $path );
		$url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . get_access_token ();
		$res = post_data ( $url, $param, true );
		
		if (isset ( $res ['errcode'] ) && $res ['errcode'] != 0) {
			$this->error ( error_msg ( $res, '封面图上传' ) );
		}
		
		$map ['cover_id'] = $cover_id;
		$map ['manager_id'] = $this->mid;
		M ( 'material_news' )->where ( $map )->setField ( 'thumb_media_id', $res ['media_id'] );
		
		return $res ['media_id'];
	}
	function _image_media_id($cover_id) {
		$cover = get_cover ( $cover_id );
		$driver = C ( 'PICTURE_UPLOAD_DRIVER' );
		if ($driver != 'Local' && ! file_exists ( SITE_PATH . $cover ['path'] )) { // 先把图片下载到本地
			
			$pathinfo = pathinfo ( SITE_PATH . $cover ['path'] );
			mkdirs ( $pathinfo ['dirname'] );
			
			$content = wp_file_get_contents ( $cover ['url'] );
			$res = file_put_contents ( SITE_PATH . $cover ['path'], $content );
			if (! $res) {
				$this->error ( '远程图片下载失败' );
			}
		}
		
		$path = $cover ['path'];
		// if (! $path) {
		// $this->error ( '获取图片素材失败' );
		// exit();
		// }
		
		$param ['type'] = 'image';
		$param ['media'] = '@' . realpath ( SITE_PATH . $path );
		$url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . get_access_token ();
		$res = post_data ( $url, $param, true );
		if (isset ( $res ['errcode'] ) && $res ['errcode'] != 0) {
			$this->error ( error_msg ( $res, '图片上传' ) );
			exit ();
		}
		return $res ['media_id'];
	}
	function _download_imgage($media_id, $picUrl = '') {
		$savePath = SITE_PATH . '/Uploads/Picture/' . time_format ( NOW_TIME, 'Y-m-d' );
		mkdirs ( $savePath );
		if (empty ( $picUrl )) {
			// 获取图片URL
			$url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . get_access_token ();
			$param ['media_id'] = $media_id;
			$picContent = post_data ( $url, $param, false, false );
			$picjson = json_decode ( $picContent, true );
			if (isset ( $picjson ['errcode'] ) && $picjson ['errcode'] != 0) {
				$this->error ( error_msg ( $picjson, '下载图片' ) );
				exit ();
			}
			// dump($picContent);
			// dump($picjson);
			// if ($picContent){
			$picName = NOW_TIME . '.jpg';
			$picPath = $savePath . '/' . $picName;
			$res = file_put_contents ( $picPath, $picContent );
			// }
		} else {
			$content = wp_file_get_contents ( $picUrl );
			// 获取图片扩展名
			$picExt = substr ( $picUrl, strrpos ( $picUrl, '=' ) + 1 );
			// $picExt=='jpeg'
			if (empty ( $picExt ) || $picExt == 'jpeg') {
				$picExt = 'jpg';
			}
			$picName = NOW_TIME . '.' . $picExt;
			$picPath = $savePath . '/' . $picName;
			$res = file_put_contents ( $picPath, $content );
			if (! $res) {
				$this->error ( '远程图片下载失败' );
				exit ();
			}
		}
		$cover_id = 0;
		if ($res) {
			// 保存记录，添加到picture表里，获取coverid
			$url = U ( 'File/uploadPicture', array (
					'session_id' => session_id () 
			) );
			$_FILES ['download'] = array (
					'name' => $picName,
					'type' => 'application/octet-stream',
					'tmp_name' => $picPath,
					'size' => $res,
					'error' => 0 
			);
			$Picture = D ( 'Picture' );
			$pic_driver = C ( 'PICTURE_UPLOAD_DRIVER' );
			$info = $Picture->upload ( $_FILES, C ( 'PICTURE_UPLOAD' ), C ( 'PICTURE_UPLOAD_DRIVER' ), C ( "UPLOAD_{$pic_driver}_CONFIG" ) );
			$cover_id = $info ['download'] ['id'];
			unlink ( $picPath );
		}
		return $cover_id;
	}
	function news_detail() {
		$map ['id'] = I ( 'id' );
		$info = M ( 'material_news' )->where ( $map )->find ();
		$this->assign ( 'info', $info );
		
		$this->display ();
	}
	/**
	 * ********************************图片素材*************************************************
	 */
	function picture_lists() {
		$this->assign ( 'normal_tips', '温馨提示：图片大小不超过5M,    格式: bmp, png, jpeg, jpg, gif' );
		$map['is_use']=1;
		$map ['manager_id'] = $this->mid;
		$map ['token'] = get_token ();
		$list = M ( 'material_image' )->where ( $map )->field ( 'id,cover_url' )->order ( 'id desc' )->selectPage ( 39 );
		$this->assign ( $list );
		$this->display ();
	}
	function add_picture() {
		$save ['cover_id'] = I ( 'cover_id' );
		$save ['cover_url'] = I ( 'src' );
		if (empty ( $save ['cover_id'] ) || empty ( $save ['cover_url'] )) {
			$this->error ( '图片参数出错' );
		}
		
		$save ['cTime'] = NOW_TIME;
		$save ['manager_id'] = $this->mid;
		$save ['token'] = get_token ();
		M ( 'material_image' )->add ( $save );
		$this->success ( '增加成功' );
	}
	function del_picture() {
		$map ['id'] = I ( 'id' );
		echo M ( 'material_image' )->where ( $map )->delete ();
	}
	
	function picture_data() {
// 	    $this->assign ( 'normal_tips', '温馨提示：图片大小不超过5M,    格式: bmp, png, jpeg, jpg, gif' );
	    $map ['manager_id'] = $this->mid;
	    $map['is_use']=1;
	    $map ['token'] = get_token ();
	    $list = M ( 'material_image' )->where ( $map )->field ( 'id,cover_url' )->order ( 'id desc' )->selectPage ( 39 );
	    $this->assign ( $list );
	    $this->display ();
	}
	
	// 上传图片素材
	function syc_image_to_wechat() {
		// 上传本地素材
		$map ['media_id'] = '0';
		$map ['manager_id'] = $this->mid;
		$map ['token'] = get_token ();
		$list = M ( 'material_image' )->limit ( 10 )->where ( $map )->field ( 'id,cover_id' )->order ( 'cTime desc' )->select ();
		if (empty ( $list )) {
			$this->success ( '上传素材完成', U ( 'picture_lists' ) );
			exit ();
		}
		foreach ( $list as $vo ) {
			
			$mediaId = $this->_image_media_id ( $vo ['cover_id'] );
			if ($mediaId) {
				$save ['media_id'] = $mediaId;
				M ( 'material_image' )->where ( array (
						'id' => $vo ['id'] 
				) )->save ( $save );
			}
		}
		$url = U ( 'syc_image_to_wechat' );
		$this->success ( '上传本地素材到微信中，请勿关闭', $url );
	}
	// 下载图片
	function syc_image_from_wechat() {
		$url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . get_access_token ();
		$param ['type'] = 'image';
		$param ['offset'] = I ( 'offset', 0, 'intval' );
		$param ['count'] = 20;
		$list = post_data ( $url, $param );
		// dump ( $list );
		// die;
		if (isset ( $list ['errcode'] ) && $list ['errcode'] != 0) {
			$this->error ( error_msg ( $list ) );
		}
		if (empty ( $list ['item'] )) {
			$this->success ( '下载素材完成', U ( 'picture_lists' ) );
			exit ();
		}
		
		$map ['media_id'] = array (
				'in',
				getSubByKey ( $list ['item'], 'media_id' ) 
		);
		$map ['manager_id'] = $this->mid;
		$map ['token'] = get_token ();
		$has = M ( 'material_image' )->where ( $map )->getField ( 'DISTINCT media_id,id' );
		// dump($map);
		// dump($has);
		
		foreach ( $list ['item'] as $item ) {
			$media_id = $item ['media_id'];
			if (isset ( $has [$media_id] ))
				continue;
			if ($item ['url']) {
				$ids = array ();
				$data ['cover_id'] = $this->_download_imgage ( $media_id, $item ['url'] );
				$data ['cover_url'] = get_cover_url ( $data ['cover_id'] );
				$data ['wechat_url'] = $item ['url'];
				$data ['media_id'] = $media_id;
				$data ['cTime'] = NOW_TIME;
				$data ['manager_id'] = $this->mid;
				$data ['token'] = get_token ();
				$ids [] = M ( 'material_image' )->add ( $data );
			}
		}
		$url = U ( 'syc_image_from_wechat', array (
				'offset' => $param ['offset'] + $list ['item_count'] 
		) );
		$this->success ( '下载微信素材中，请勿关闭', $url );
	}
	/**
	 * ********************************音频素材*************************************************
	 */
	function voice_lists() {
		$this->assign ( 'normal_tips', '温馨提示：语音大小不超过5M，长度不超过60秒，支持mp3/wma/wav/amr格式' );
		
		$map ['manager_id'] = $this->mid;
		$map ['token'] = get_token ();
		$map ['type'] = 1;
		$map['is_use']=1;
		$list = M ( 'material_file' )->where ( $map )->order ( 'id desc' )->selectPage ( 30 );
		
		vendor ( "getID3.getid3.getid3" );
		
		$getID3 = new \getID3 (); // 实例化类
		
		if (! empty ( $list ['list_data'] )) {
			$file_ids = getSubByKey ( $list ['list_data'], 'file_id' );
			$file_map ['id'] = array (
					'in',
					$file_ids 
			);
			$file_list = M ( 'file' )->where ( $file_map )->select ();
			foreach ( $file_list as $vo ) {
				$path = C ( 'DOWNLOAD_UPLOAD.rootPath' ) . $vo ['savepath'] . $vo ['savename'];
				$vo ['path'] = $path = realpath ( SITE_PATH . $path );
				$vo ['playtime'] = '未知时长';
				if (file_exists ( $path )) {
					$info = $getID3->analyze ( $path );
					// 以下算法只适用于1个小时以内的时长显示
					$vo ['playtime'] = date ( "i:s", $info ['playtime_seconds'] );
				}
				$file_arr [$vo ['id']] = $vo;
			}
			foreach ( $list ['list_data'] as &$v ) {
				$v ['file_info'] = $file_arr [$v ['file_id']];
			}
		}
		
		$this->assign ( $list );
		$this->display ();
	}
	function voice_data() {
// 	    $this->assign ( 'normal_tips', '温馨提示：语音大小不超过5M，长度不超过60秒，支持mp3/wma/wav/amr格式' );
	    $map['is_use']=1;
	    $map ['manager_id'] = $this->mid;
	    $map ['token'] = get_token ();
	    $map ['type'] = 1;
	    $list = M ( 'material_file' )->where ( $map )->order ( 'id desc' )->selectPage ( 30 );
	
	    vendor ( "getID3.getid3.getid3" );
	
	    $getID3 = new \getID3 (); // 实例化类
	
	    if (! empty ( $list ['list_data'] )) {
	        $file_ids = getSubByKey ( $list ['list_data'], 'file_id' );
	        $file_map ['id'] = array (
	            'in',
	            $file_ids
	        );
	        $file_list = M ( 'file' )->where ( $file_map )->select ();
	        foreach ( $file_list as $vo ) {
	            $path = C ( 'DOWNLOAD_UPLOAD.rootPath' ) . $vo ['savepath'] . $vo ['savename'];
	            $vo ['path'] = $path = realpath ( SITE_PATH . $path );
	            $vo ['playtime'] = '未知时长';
	            if (file_exists ( $path )) {
	                $info = $getID3->analyze ( $path );
	                // 以下算法只适用于1个小时以内的时长显示
	                $vo ['playtime'] = date ( "i:s", $info ['playtime_seconds'] );
	            }
	            $file_arr [$vo ['id']] = $vo;
	        }
	        foreach ( $list ['list_data'] as &$v ) {
	            $v ['file_info'] = $file_arr [$v ['file_id']];
	        }
	    }
	
	    $this->assign ( $list );
	    $this->display ();
	}
	
	function voice_add() {
		$model = $this->getModel ( 'material_file' );
		
		if (IS_POST) {
			$_POST ['type'] = 1;
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'voice_lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			
			$this->assign ( 'post_url', U ( 'voice_add' ) );
			
			$this->display ( 'add' );
		}
	}
	function voice_del() {
		$model = $this->getModel ( 'material_file' );
		parent::common_del ( $model );
	}
	function voice_edit() {
		$model = $this->getModel ( 'material_file' );
		$id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		
		$token = get_token ();
		if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
			$this->error ( '非法访问！' );
		}
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'voice_lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$this->assign ( 'post_url', U ( 'voice_edit' ) );
			
			$this->display ( 'Addons:edit' );
		}
	}
	// 下载音频
	function _voice_download($media_id,$cover_url) {
	    $savePath = SITE_PATH . '/Uploads/Download/' . time_format ( NOW_TIME, 'Y-m-d' );
	    mkdirs ( $savePath );
	    $ext='mp3';
	    if (empty ( $cover_url )) {
	        // 获取图片URL
	        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . get_access_token ();
	        $param ['media_id'] = $media_id;
	        $picContent = post_data ( $url, $param, false, false );
	        $picjson = json_decode ( $picContent, true );
	        if (isset ( $picjson ['errcode'] ) && $picjson ['errcode'] != 0) {
	            $this->error ( error_msg ( $picjson, '下载音频文件素材失败' ) );
	            exit ();
	        }
	        $picName = NOW_TIME . '.'.$ext;
	        $picPath = $savePath . '/' . $picName;
	        $res = file_put_contents ( $picPath, $picContent );
	        // }
	    } else {
	        $content = wp_file_get_contents ( $cover_url );
	        // 获取图片扩展名
	        $picExt = substr ( $cover_url, strrpos ( $cover_url, '=' ) + 1 );
	        // $picExt=='jpeg'
	        if (empty ( $picExt ) ) {
	            $picExt = $ext;
	        }
	        $picName = NOW_TIME . '.' . $picExt;
	        $picPath = $savePath . '/' . $picName;
	        $res = file_put_contents ( $picPath, $content );
	        if (! $res) {
	            $this->error ( '远程音频文件下载失败' );
	            exit ();
	        }
	    }
	    $cover_id = 0;
	    if ($res) {
	        // 保存记录，添加到picture表里，获取coverid
	        $url = U ( 'File/uploadPicture', array (
	            'session_id' => session_id ()
	        ) );
	        $_FILES ['download'] = array (
	            'name' => $picName,
	            'type' => 'application/octet-stream',
	            'tmp_name' => $picPath,
	            'size' => $res,
	            'error' => 0
	        );
	        $File = D ( 'File' );
	       $file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		   $info = $File->upload(
			$_FILES,
			C('DOWNLOAD_UPLOAD'),
			C('DOWNLOAD_UPLOAD_DRIVER'),
			C("UPLOAD_{$file_driver}_CONFIG")
		  );
		 $cover_id = $info ['download'] ['id'];
	        unlink ( $picPath );
	    }
	    return $cover_id;
	}
	
	
	function syc_voice_to_wechat(){
	    // 上传本地语音素材
	    $map ['media_id'] = '0';
	    $map ['manager_id'] = $this->mid;
	    $map ['token'] = get_token ();
	    $map['type']=1;
	    $list = M ( 'material_file' )->limit ( 10 )->where ( $map )->field ( 'id,file_id' )->order ( 'cTime desc' )->select ();
	    if (empty ( $list )) {
	        $this->success ( '上传素材完成', U ( 'voice_lists' ) );
	        exit ();
	    }
	    foreach ( $list as $vo ) {
	        	
	        $mediaId = $this->_get_file_media_id ( $vo ['file_id'] ,'voice');
	        if ($mediaId) {
	            $save ['media_id'] = $mediaId;
	            M ( 'material_file' )->where ( array (
	            'id' => $vo ['id']
	            ) )->save ( $save );
	        }
	    }
	    $url = U ( 'syc_voice_to_wechat' );
	    $this->success ( '上传本地素材到微信中，请勿关闭', $url );
	}
	
	
	
	
	/**
	 * ********************************视频素材*************************************************
	 */
	function video_lists() {
		$this->assign ( 'normal_tips', '温馨提示：视频不能超过20M，支持大部分主流视频格式，超过20M的视频可至腾讯视频上传后添加' );
		$map['is_use']=1;
		$map ['manager_id'] = $this->mid;
		$map ['token'] = get_token ();
		$map ['type'] = 2;
		$list = M ( 'material_file' )->where ( $map )->order ( 'id desc' )->selectPage ( 39 );
		$this->assign ( $list );
		$this->display ();
	}
	function video_data() {
// 	    $this->assign ( 'normal_tips', '温馨提示：视频不能超过20M，支持大部分主流视频格式，超过20M的视频可至腾讯视频上传后添加' );
	    $map['is_use']=1;
	    $map ['manager_id'] = $this->mid;
	    $map ['token'] = get_token ();
	    $map ['type'] = 2;
	    $list = M ( 'material_file' )->where ( $map )->order ( 'id desc' )->selectPage ( 39 );
	    $this->assign ( $list );
	    $this->display ();
	}
	function video_add() {
		$model = $this->getModel ( 'material_file' );
		
		if (IS_POST) {
			$_POST ['type'] = 2;
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'video_lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$fields['introduction']['is_show']=1;
			$this->assign ( 'fields', $fields );
			$this->assign ( 'post_url', U ( 'video_add' ) );
			
			$this->display ( 'Addons:add' );
		}
	}
	function video_del() {
		$model = $this->getModel ( 'material_file' );
		parent::common_del ( $model );
	}
	function video_edit() {
		$model = $this->getModel ( 'material_file' );
		$id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		
		$token = get_token ();
		if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
			$this->error ( '非法访问！' );
		}
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'video_lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$fields['introduction']['is_show']=1;
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$this->assign ( 'post_url', U ( 'video_edit' ) );
			
			$this->display ( 'Addons:edit' );
		}
	}
	function syc_video_to_wechat(){
	    // 上传本地视频素材
	    $map ['media_id'] = '0';
	    $map ['manager_id'] = $this->mid;
	    $map ['token'] = get_token ();
	    $map['type']=2;
	    $list = M ( 'material_file' )->limit ( 10 )->where ( $map )->field ( 'id,file_id,title,introduction' )->order ( 'cTime desc' )->select ();
	    if (empty ( $list )) {
	        $this->success ( '上传素材完成', U ( 'video_lists' ) );
	        exit ();
	    }
	    foreach ( $list as $vo ) {
	
	        $mediaId = $this->_get_file_media_id ( $vo ['file_id'] ,'video',$vo['title'],$vo['introduction']);
	        if ($mediaId) {
	            $save ['media_id'] = $mediaId;
	            M ( 'material_file' )->where ( array (
	            'id' => $vo ['id']
	            ) )->save ( $save );
	        }
	    }
	    $url = U ( 'syc_video_to_wechat' );
	    $this->success ( '上传本地素材到微信中，请勿关闭', $url );
	}
	// 下载音频：未实现 TODO
	function _video_download($media_id,$cover_url) {
	    $savePath = SITE_PATH . '/Uploads/Download/' . time_format ( NOW_TIME, 'Y-m-d' );
	    mkdirs ( $savePath );
	    $ext='mp4';
	    if (empty ( $cover_url )) {
	        // 获取图片URL
	        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . get_access_token ();
	        $param ['media_id'] = $media_id;
	        $info = post_data ( $url, $param);
// 	        $picjson = json_decode ( $picContent, true );
	        if (isset ( $info ['errcode'] ) && $info ['errcode'] != 0) {
	            $this->error ( error_msg ( $info, '下载视频文件素材失败' ) );
	            exit ();
	        }
// 	        $this->_video_download(0, $info['down_url']);
	         return $info;
	        // }
	    } else {
	        $content = wp_file_get_contents ( $cover_url );
	        // 获取图片扩展名
	        $picExt = substr ( $cover_url, strrpos ( $cover_url, '=' ) + 1 );
	        // $picExt=='jpeg'
	        if (empty ( $picExt ) ) {
	            $picExt = $ext;
	        }
	        $picName = NOW_TIME . '.' . $picExt;
	        $picPath = $savePath . '/' . $picName;
	        $res = file_put_contents ( $picPath, $content );
	        if (! $res) {
	            $this->error ( '远程视频文件下载失败' );
	            exit ();
	        }
	    }
	    $cover_id = 0;
	    if ($res) {
	        // 保存记录，添加到picture表里，获取coverid
	        $url = U ( 'File/uploadPicture', array (
	            'session_id' => session_id ()
	        ) );
	        $_FILES ['download'] = array (
	            'name' => $picName,
	            'type' => 'application/octet-stream',
	            'tmp_name' => $picPath,
	            'size' => $res,
	            'error' => 0
	        );
	        $File = D ( 'File' );
	        $file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
	        $info = $File->upload(
	            $_FILES,
	            C('DOWNLOAD_UPLOAD'),
	            C('DOWNLOAD_UPLOAD_DRIVER'),
	            C("UPLOAD_{$file_driver}_CONFIG")
	        );
	        $cover_id = $info ['download'] ['id'];
	        unlink ( $picPath );
	    }
	    return $cover_id;
	}
	/**
	 * *******************多媒体共用***********************
	 */
	
	function syc_file_from_wechat(){
	    $type=I('type',1);
	    $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . get_access_token ();
	    $type_name=$type ==1?'voice':'video';
	    $param ['type'] = $type_name;
	    $param ['offset'] = I ( 'offset', 0, 'intval' );
	    $param ['count'] = 20;
	    $list = post_data ( $url, $param );
	    if (isset ( $list ['errcode'] ) && $list ['errcode'] != 0) {
	        $this->error ( error_msg ( $list ) );
	    }
	    if (empty ( $list ['item'] )) {
	        if ($type == 1){
	            $this->success ( '下载素材完成', U ( 'voice_lists' ) );
	            exit ();
	        }else {
	            $this->success ( '下载素材完成', U ( 'video_lists' ) );
	            exit ();
	        }
	    }
	    $map ['media_id'] = array (
	        'in',
	        getSubByKey ( $list ['item'], 'media_id' )
	    );
	    $map['type']=$type;
	    $map['token']=get_token();
	    $map['manager_id']=$this->mid;
	    $has = M ( 'material_file' )->where ( $map )->getField ( 'DISTINCT media_id,id' );
	    foreach ( $list ['item'] as $item ) {
	        $media_id = $item ['media_id'];
	        if (isset ( $has [$media_id] ))
	            continue;
	        $ids = array ();
	        if ($type ==1){
	            $data ['title'] = $item['name'];
	            $data ['file_id'] = $this->_voice_download ($media_id, $item ['url'] );
	        }else {
	            //视频
	            $video = $this->_video_download ($media_id, '' );
	            $data['title']=$video['title'];
	            $data['introduction']=$video['description'];
	            $data['wechat_url']=$video['down_url'];
	            
	            $data ['file_id'] = $this->_video_download (0, $data['wechat_url'] );
	        }
	        $data ['wechat_url'] = $item ['url'];
	        $data ['media_id'] = $media_id;
	        $data ['cTime'] = $item['update_time'];
	        $data ['manager_id'] = $this->mid;
	        $data ['token'] = get_token ();
	        $data ['type']=$type;
	        $ids [] = M ( 'material_file' )->add ( $data );
	    }
	    $url = U ( 'syc_file_from_wechat', array (
	        'offset' => $param ['offset'] + $list ['item_count'] ,
	        'type'=>$type
	    ) );
	    $this->success ( '下载微信素材中，请勿关闭', $url );
	}
	
	//上传视频、语音素材
	function _get_file_media_id($file_id,$type='voice',$title='',$introduction=''){
	    $fileInfo=M('file')->find($file_id);
	    if ($fileInfo){
	        $path='/Uploads/Download/'.$fileInfo['savepath'].$fileInfo['savename'];
	        if (! $path) {
	            $this->error ('获取素材失败' );
	            exit ();
	        }
	        $param ['type'] = $type;
	        $param ['media'] = '@' . realpath ( SITE_PATH . $path );
	        if ($type=='video'){
	            $param['description']['title']=$title;
	            $param['description']['introduction']=$introduction;
	        }
	        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . get_access_token ();
	        $res = post_data ( $url, $param);
	        if (!$res){
	            $this->error('同步失败');
	        }
	        if (isset ( $res ['errcode'] ) && $res ['errcode'] != 0) {
	            $this->error ( error_msg ( $res, '素材上传' ) );
	            exit ();
	        }
	    }
	    return $res ['media_id'];
	}
	
	/**
	 * ********************************文本素材*************************************************
	 */
	function text_lists() {
		$model = $this->getModel ( 'material_text' );
	
		$this->assign ( 'add_url', U ( 'text_add' ) );
		$this->assign ( 'del_url', U ( 'text_del' ) );
		$this->assign ( 'search_url', U ( 'text_lists' ) );
		
		$isAjax = I ( 'isAjax' );
		$isRadio = I ( 'isRadio' );
		
		
		$map['is_use']=1;
		session ( 'common_condition' ,$map);
// 		parent::common_lists ( $model, '0', );
		// 获取模型信息
		is_array ( $model ) || $model = $this->getModel ( $model );
		
		$list_data = $this->_get_model_list ( $model );
		$this->assign ( $list_data );
		
		if ($isAjax) {
		    $this->assign('isRadio',$isRadio);
		    $this->assign ( $list_data );
		    $this->display ( 'text_lists_data' );
		} else {
		    $this->assign ( $list_data );
		    $this->display ( 'Addons:lists'  );
		}
	}
	function text_add() {
		$model = $this->getModel ( 'material_text' );
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'text_lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			
			$this->assign ( 'post_url', U ( 'text_add' ) );
			
			$this->display ( 'Addons:add' );
		}
	}
	function text_del() {
		$model = $this->getModel ( 'material_text' );
		parent::common_del ( $model );
	}
	function text_edit() {
		$model = $this->getModel ( 'material_text' );
		$id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		
		$token = get_token ();
		if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
			$this->error ( '非法访问！' );
		}
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'text_lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$this->assign ( 'post_url', U ( 'text_edit' ) );
			
			$this->display ( 'Addons:edit' );
		}
	}
	function get_content_by_id(){
	    $map['id']=I('id');
	    $content1=M('material_text')->where($map)->getField('content');
	    echo $content1;
	}
}
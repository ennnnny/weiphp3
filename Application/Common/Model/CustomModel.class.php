<?php

namespace Common\Model;

use Think\Model;

/**
 * 微信客服接口操作类
 */
class CustomModel extends Model {
	protected $tableName = 'user';
	/* 回复文本消息 */
	public function replyText($uid, $content) {
		$param ['text'] ['content'] = $content;
		return $this->_replyData ( $uid, $param, 'text' );
	}
	/* 回复图片消息 */
	public function replyImage($uid, $media_id, $type = 'cover_id') {
		$type == 'cover_id' && $media_id = $this->get_image_media_id ( $media_id );
		//素材图片id
		if ($type =='material_image'){
		    $imageMaterial=M('material_image')->find($media_id);
		    if ($imageMaterial['media_id']){
		        $media_id=$imageMaterial['media_id'];
		    }else{
		        $media_id=$this->get_image_media_id($media_id);
		    }
		}
		$param ['image'] ['media_id'] = $media_id;
		
		return $this->_replyData ( $uid, $param, 'image' );
	}
	/* 回复语音消息 TODO */
	/**
	 * 
	 * @param unknown $uid
	 * @param unknown $media_id: id值
	 * @param string $type 决定id值的类型： material_file：文件素材的id, file_id:文件id  '':media_id
	 * @return Ambigous <number, string>
	 */
	public function replyVoice($uid,$media_id,$type='file_id') {
	    $type == 'file_id' && $media_id = $this->get_file_media_id( $media_id,'voice' );
	    if ($type =='material_file'){
	        $fileMaterial=M('material_file')->find($media_id);
	        if ($fileMaterial['media_id']){
	            $media_id=$fileMaterial['media_id'];
	        }else{
	            $media_id=$this->get_file_media_id($fileMaterial['file_id'],'voice');
	        }
	    }
		$msg ['voice'] ['media_id'] = $media_id;
		return $this->_replyData ($uid, $msg, 'voice' );
	}
	/* 回复视频消息 TODO */
	public function replyVideo($uid,$media_id, $type='file_id',$thumb='',$title = '', $description = '') {
	    $type == 'file_id' && $media_id = $this->get_file_media_id( $media_id,'video' );
	    if ($type =='material_file'){
	        $fileMaterial=M('material_file')->find($media_id);
	        empty($title)&&$title=$fileMaterial['title'];
	        empty($description) && $description=$fileMaterial['introduction'];
	        if ($fileMaterial['media_id']){
	            $media_id=$fileMaterial['media_id'];
	        }else{
	            $media_id=$this->get_image_media_id($fileMaterial['file_id'],'video');
	        }
	    }
	    $msg ['video'] ['media_id'] = $media_id;
	    $msg ['video'] ['thumb_media_id'] =$thumb?$thumb:$this->get_thumb_media_id(); //缩略图
		$msg ['video'] ['title'] = $title;
		$msg ['video'] ['description'] = $description;
		return $this->_replyData ($uid, $msg, 'video' );
	}
	/* 回复音乐消息 TODO */
	public function replyMusic($uid,$media_id, $title = '', $description = '', $music_url, $HQ_music_url) {
		$msg ['Music'] ['ThumbMediaId'] = $media_id;
		$msg ['Music'] ['Title'] = $title;
		$msg ['Music'] ['Description'] = $description;
		$msg ['Music'] ['MusicURL'] = $music_url;
		$msg ['Music'] ['HQMusicUrl'] = $HQ_music_url;
		return $this->_replyData ( $uid,$msg, 'music' );
	}
	/*
	 * 回复图文消息 传出图文素材的ID
	 */
	public function replyNews($uid, $sucai_id) {
		$map ['group_id'] = $sucai_id;
		$appMsgData = M ( 'material_news' )->where ( $map )->select ();
		foreach ( $appMsgData as $vo ) {
			// 文章内容
			$art ['title'] = $vo ['title'];
			$art ['description'] = $vo ['intro'];
			if (empty ( $vo ['url'] )) {
				$art ['url'] = U ( 'Material/news_detail', array (
						'id' => $vo ['id'] 
				) );
			} else {
				$art ['url'] = $vo ['url'];
			}
			
			// 获取封面图片URL
			$coverId = $vo ['cover_id'];
			$art ['picurl'] = get_cover_url ( $coverId );
			$articles [] = $art;
		}
		$param ['news'] ['articles'] = $articles;
		
		return $this->_replyData ( $uid, $param, 'news' );
	}
	/* 发送回复消息到微信平台 */
	function _replyData($uid, $param, $msg_type) {
		$map ['token'] = get_token ();
		$map ['uid'] = $uid;
		
		$param ['touser'] = M ( 'public_follow' )->where ( $map )->getField ( 'openid' );
		$param ['msgtype'] = $msg_type;
		
		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . get_access_token ();
		
		// dump($param);
		// die;
		$result ['status'] = 0;
		$result ['msg'] = '回复失败';
		$res = post_data ( $url, $param );
		if ($res ['errcode'] != 0) {
			$result ['msg'] = error_msg ( $res );
		} else {
			$data ['ToUserName'] = get_token ();
			$data ['FromUserName'] = $param ['touser'];
			$data ['CreateTime'] = NOW_TIME;
			$data ['Content'] = isset ( $param ['text'] ['content'] ) ? $param ['text'] ['content'] : json_encode ( $param );
			$data ['MsgId'] = get_mid(); // 该字段保存管理员ID
			$data ['type'] = 1;
			$data ['is_read'] = 1;
			M ( 'weixin_message' )->add ( $data );
			
			$result ['status'] = 1;
			$result ['msg'] = '回复成功';
		}
		return $result;
	}
	// 新增临时图片素材
	function get_image_media_id($cover_id) {
		$cover = get_cover ( $cover_id );
		$driver = C ( 'PICTURE_UPLOAD_DRIVER' );
		if ($driver != 'Local' && ! file_exists ( SITE_PATH . $cover ['path'] )) { // 先把图片下载到本地
			
			$pathinfo = pathinfo ( SITE_PATH . $cover ['path'] );
			mkdirs ( $pathinfo ['dirname'] );
			
			$content = wp_file_get_contents ( $cover ['url'] );
			$res = file_put_contents ( SITE_PATH . $cover ['path'], $content );
			if (! $res) {
				return 0;
			}
		}
		$path = $cover ['path'];
		if (! $path) {
			return 0;
		}
		$param ['type'] = 'image';
		$param ['media'] = '@' . realpath ( SITE_PATH . $path );
		$url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . get_access_token ();
		$res = post_data ( $url, $param, true );
		if (isset ( $res ['errcode'] ) && $res ['errcode'] != 0) {
			return 0;
		}
		return $res ['media_id'];
	}
	
	// 新增临时 voice 语音/ video 视频素材
	function get_file_media_id($file_id,$type='voice') {
	    $fileInfo=M('file')->find($file_id);
	    if ($fileInfo){
	        $path='/Uploads/Download/'.$fileInfo['savepath'].$fileInfo['savename'];
	        if (! $path) {
	           return 0;
	        }
	        $param ['type'] = $type;
	        $param ['media'] = '@' . realpath ( SITE_PATH . $path );
	        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . get_access_token ();
	        $res = post_data ( $url, $param, true );
	        if (isset ( $res ['errcode'] ) && $res ['errcode'] != 0) {
	           return 0;
	        }
	    }else {
	        return 0;
	    }
	   
	    return $res ['media_id'];
	}
	
	// 临时缩略图素材
	function get_thumb_media_id($path='') {
// 	    $cover = get_cover ( $cover_id );
// 	    $driver = C ( 'PICTURE_UPLOAD_DRIVER' );
// 	    if ($driver != 'Local' && ! file_exists ( SITE_PATH . $cover ['path'] )) { // 先把图片下载到本地
// 	        $pathinfo = pathinfo ( SITE_PATH . $cover ['path'] );
// 	        mkdirs ( $pathinfo ['dirname'] );
// 	        $content = wp_file_get_contents ( $cover ['url'] );
// 	        $res = file_put_contents ( SITE_PATH . $cover ['path'], $content );
// 	        if (! $res) {
// 	            return 0;
// 	        }
// 	    }
// 	    $path = $cover ['path'];
	     

	    if (!$path){
	        $path='/Public/Home/images/spec_img_add.jpg';
	    }
	    $param ['type'] = 'thumb';
	    $param ['media'] = '@' . realpath ( SITE_PATH . $path );
	    $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . get_access_token ();
	    $res = post_data ( $url, $param, true );
	    if (isset ( $res ['errcode'] ) && $res ['errcode'] != 0) {
	        return 0;
	    }
	    return $res ['thumb_media_id'];
	}
}
?>

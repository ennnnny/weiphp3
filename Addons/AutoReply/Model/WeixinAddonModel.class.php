<?php

namespace Addons\AutoReply\Model;

use Home\Model\WeixinModel;

/**
 * AutoReply的微信模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		$map ['id'] = $keywordArr ['aim_id'];
		
		$info = M ( 'auto_reply' )->where ( $map )->find ();
		if ($info ['msg_type'] == 'news') {
			$map_news ['group_id'] = $info ['group_id'];
			$list = M ( 'material_news' )->where ( $map_news )->select ();
			
			$param ['publicid'] = get_token_appinfo ( '', 'id' );
			
			foreach ( $list as $k => $vo ) {
				if ($k > 8)
					continue;
				
				$articles [] = array (
						'Title' => $vo ['title'],
						'Description' => $vo ['intro'],
						'PicUrl' => get_cover_url ( $vo ['cover_id'] ),
						'Url' => $this->_getNewsUrl ( $vo, $param ) 
				);
			}
			
			$res = $this->replyNews ( $articles );
		} elseif ($info ['msg_type'] == 'image') {
			if ($info['image_id']){
// 				$d['image_id']=url_img_html(get_cover_url($d['image_id']));
				$media_id=D ( 'Common/Custom' )->get_image_media_id($info['image_id']);
			}else if($info['image_material']){
				$map2 ['id'] = $info ['image_material'];
				$media_img = M ( 'material_image' )->where ( $map2 )->find ( );
				$media_id=$media_img['image_id'];
				if (!$media_id){
					$media_id=D ( 'Common/Custom' )->get_image_media_id($media_img['cover_id']);
				}
			}
			$this->replyImage ( $media_id);
		} else {
			$contetn = replace_url ( htmlspecialchars_decode ( $info ['content'] ) );
			$this->replyText ( $contetn );
		}
	}
	function _getNewsUrl($info, $param) {
		if (! empty ( $info ['link'] )) {
			$url = replace_url ( $info ['link'] );
		} else {
			$param ['id'] = $info ['id'];
			$url = U ( 'Home/Material/news_detail', $param );
		}
		return $url;
	}
}
        	
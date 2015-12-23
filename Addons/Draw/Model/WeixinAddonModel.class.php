<?php
        	
namespace Addons\Draw\Model;
use Home\Model\WeixinModel;
        	
/**
 * Draw的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Draw' ); // 获取后台插件的配置参数	
		//dump($config);
		
		
		$map ['token'] = get_token ();
		$keywordArr ['aim_id'] && $map ['id'] = $keywordArr ['aim_id'];
		$data = M ( 'lottery_games' )->where ( $map )->find ();
		
		// 其中token和openid这两个参数一定要传，否则程序不知道是哪个微信用户进入了系统
		$param ['token'] = get_token ();
		$param ['openid'] = get_openid ();
		$param ['games_id'] = $data ['id'];
		$url = addons_url ( 'Draw://Wap/index', $param );
		
		$articles [0] = array (
				'Title' => $data ['title'],
				'Url' => $url,
				'Description' => $data ['intro']
		);
		switch($data['game_type']){
			case 1:$articles [0] ['PicUrl'] = SITE_URL.'/Addons/Draw/View/default/Public/guaguale_cover.jpg';
			break;
			case 2:$articles [0] ['PicUrl'] =    SITE_URL.'/Addons/Draw/View/default/Public/dzp_cover.jpg';
			break;
			case 3:$articles [0] ['PicUrl'] =     SITE_URL.'/Addons/Draw/View/default/Public/zjd_cover.jpg';
			break;
			case 4:$articles [0] ['PicUrl'] =    SITE_URL.'/Addons/Draw/View/default/Public/nine_cover.jpg';
			break;
		}
		
		$this->replyNews ( $articles );

	}
}
        	
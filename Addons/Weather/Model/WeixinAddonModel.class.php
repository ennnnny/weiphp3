<?php
        	
namespace Addons\Weather\Model;
use Home\Model\WeixinModel;
        	
/**
 * Weather的微信模型
 * Weiphp天气预报
 * Rieck @版权所有 QQ419421248
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Weather' ); // 获取后台插件的配置参数	
		preg_match('/(.+)天气/i',$dataArr[Content], $matchs);
		
		$json_array = file_get_contents('http://api.map.baidu.com/telematics/v3/weather?location='.$matchs[1].'&output=json&ak=5slgyqGDENN7Sy7pw29IUvrZ');
		$json_array = json_decode($json_array,true);
		$array = $json_array['results'][0]['weather_data'];
		print_r($json_array);
		date_default_timezone_set ('Asia/Shanghai');
		$h=date('H');
		if($json_array['error'] > -3){
			foreach ($array as $key=>$val){
				date_default_timezone_set(PRC);
				$h=date('H');
				if($h>=8 && $h<=19){
					$articles [$key] = array (
							'Title' => $val['date']."\n".$val['weather']." ".$val['wind']." ".$val['temperature'],
							'Description' => '',
							'PicUrl' => $val['dayPictureUrl'],
							'Url' => '' 
					);
				}else {
					$articles [$key] = array (
							'Title' => $val['date']."\n".$val['weather']." ".$val['wind']." ".$val['temperature'],
							'Description' => '',
							'PicUrl' => $val['nightPictureUrl'],
							'Url' => '' 
					);
				}
			}
			
			$tarray = array (
				'Title' => $json_array['results'][0]['currentCity']."天气预报",
				'Description' => '',
				'PicUrl' => '',
				'Url' => '' 
				);
			array_unshift($articles,$tarray);
			$this->replyNews($articles);
		}else {
			$this->replyText("没找到耶！...〒_〒");
		}

	} 

	// 关注公众号事件
	public function subscribe() {
		return true;
	}
	
	// 取消关注公众号事件
	public function unsubscribe() {
		return true;
	}
	
	// 扫描带参数二维码事件
	public function scan() {
		return true;
	}
	
	// 上报地理位置事件
	public function location() {
		return true;
	}
	
	// 自定义菜单事件
	public function click() {
		return true;
	}	
}
        	
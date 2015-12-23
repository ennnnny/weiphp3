// JavaScript Document
var defaultGoodsList = new Array();
for(var i=0;i<4;i++){
	var obj = new Object();
	obj.id = 0;
	obj.title = "商品标题";
	obj.img = IMG_PATH +'/default_goods_pic.jpg';
	obj.market_price = 0.00;
	obj.stock_num = 0.00;
	obj.url = "";
	defaultGoodsList.push(obj);
}
var defaultCaseList = new Array();
for(var i=0;i<3;i++){
	var obj = new Object();
	obj.picId = 0;
	obj.pic = IMG_PATH+"/no_cover_pic_s.png";
	obj.url = "";
	obj.title = "";
	defaultCaseList.push(obj);
}
var app = angular.module('app', []).controller('commonCtrl', function($scope) {
	var id= $('input[name="id"]').val();
	if(id && activeModels!=''){
		$scope.activeModules = JSON.parse(activeModels);
	}else{
		$scope.activeModules = [{"id":"header","name":"\u5fae\u9875\u9762\u6807\u9898","params":{"title":"","description":"","bgColor":"#fff"},"issystem":1,"index":0,"displayorder":"0"}];
		if(useFor=="goodsDetail"){
			$scope.activeModules.push({"id":"goodsdetail","name":"商品详情页","params":{},"issystem":1,"index":0,"disable":1,"displayorder":"0"});
		}else if(useFor=="userCenter"){
			$scope.activeModules.push({"id":"usercenter","name":"个人详情页","params":{},"issystem":1,"disable":1,"index":0,"displayorder":"0"});
		}else if(useFor=="index"){
			$scope.activeModules.push({"id":"fixedmodule","name":"首页","params":{'title':'首页固定模块','desc':'商城基本信息，商城LOGO，推荐商品和分类'},"issystem":1,"disable":1,"index":0,"displayorder":"0"});	
		}else if(useFor=="cart"){
			$scope.activeModules.push({"id":"fixedmodule","name":"购物车页","params":{'title':'购物车固定模块','desc':'我的购物车列表'},"issystem":1,"disable":1,"index":0,"displayorder":"0"});	
		}else if(useFor=="orderlist"){
			$scope.activeModules.push({"id":"fixedmodule","name":"订单列表页","params":{'title':'订单固定模块','desc':'我的订单列表'},"issystem":1,"disable":1,"index":0,"displayorder":"0"});	
		}
	}
	$scope.activeItem = $scope.activeModules[0];
	$scope.editors = ['header'];
	$scope.modules = [
		{"id":"richtext","name":"富文本","params":{"content":"",'bgColor':'','color':'','fontsize':'','align':''},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"goods","name":"商品","params":{"list_style":1,'hasTestData':1,'show_price':1,'show_btn':1,"goods_list":defaultGoodsList},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"banner","name":"幻灯片","params":{"show_cursor":1,"show_title":1,"is_auto":1,'banner_list':new Array()},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"piclist","name":"图片","params":{"list_style":1,"show_title":0,'pic_list':new Array()},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"blank","name":"辅助空白","params":{"height":10},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"title","name":"标题","params":{"title":"","subtitle":"",'bgColor':'','maincolor':'','subcolor':'','align':''},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"textnav","name":"文本导航","params":{"title":"",'bgColor':'','color':'','text_nav_list':new Array()},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"picnav","name":"图片导航","params":{"title":"",'nav_style':2,'pic_nav_list':new Array()},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"searchgoods","name":"商品搜索","params":{},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"blankline","name":"辅助线","params":{'borderWidth':1,'borderColor':'#ccc','borderStyle':'dotted'},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"case","name":"橱窗","params":{'title':'','contentTitle':'','content':'','style':2,'show_title':1,'pic_list':defaultCaseList},"issystem":0,"index":0,"displayorder":"0"},
		{"id":"notice","name":"公告","params":{'content':'','bgColor':'','color':''},"issystem":0,"index":0,"displayorder":"0"}
	];
	$scope.addItem = function(id){
		try{
			
			//var addItem = jQuery.extend(true,{}, $scope.getModelById(id));
			var addItem = angular.copy($scope.getModelById(id));
			addItem.index = $scope.activeModules.length;
			$scope.activeModules.push(addItem);
			$scope.activeItem = addItem;
			$scope.initWidget(id);
			setTimeout(function(){
				$scope.initEditorTop(addItem.index);
			},100);
			if($scope.UEditor && id =="richtext"){
				$scope.UEditor.setContent($scope.activeItem.params.content);
			}
			
		}catch(e){
				
		}
	}
	$scope.editItem = function(mudule){
		$scope.activeItem = mudule;
		var tempId =  mudule.id;
		$scope.initWidget(tempId);
		setTimeout(function(){
			$scope.initEditorTop(mudule.index);
		},100);
		if($scope.UEditor && tempId=="richtext"){
			$scope.UEditor.setContent($scope.activeItem.params.content==""?'<p></p>':$scope.activeItem.params.content);                           
		}
	}
	$scope.UEditor = null;
	$scope.initWidget = function(id){
		if($.inArray(id, $scope.editors)<0){
			$scope.editors.push(id);
			setTimeout(function(){
				
				if(id=="richtext"){
					$scope.UEditor = $.Editor.initEditor(
					'diy_editor_richcontent',
					editorUrl.ue_upimg,
					editorUrl.ue_mgimg,
					editorUrl.get_article_style
					)
					if($scope.UEditor){
						$scope.UEditor.addListener("contentChange",function(){
							$scope.activeItem.params.content = $scope.UEditor.getContent();
							$('.temp_click').click();
						});
					}
				}
				
			},200)
		}
	}
	$scope.deleteItem = function(mudule){
		$scope.activeModules.remove(mudule);
		for(var i=0;i<$scope.activeModules.legnth;i++){
			$scope.activeModules[i].index = i;
		}
		if(mudule == $scope.activeItem){
			$scope.activeItem = $scope.activeModules[0];
		}	
				
	}
	$scope.initEditorTop =function(index){
		var oTop = $('#module-'+index).offset().top;
		var nTop = $('.app_inner').offset().top;
		$('#editor'+$scope.activeItem.id).css({'margin-top':oTop-nTop});
	}
	$scope.getModelById = function(id){
		var tempItem = new Object();
		for(var m in $scope.modules){
			if($scope.modules[m].id==id){
				tempItem = $scope.modules[m];
				break;
			}
		}
		return tempItem;
	}
	$scope.submitForm =function(){
		var url = $('#form').attr('action');
		var id = $('input[name="id"]').val();
		$.post(url,
			{	
				id:id,
				title:$scope.activeModules[0].params.title,
				desc:$scope.activeModules[0].params.description,
				config:encodeURIComponent(JSON.stringify($scope.activeModules))
			},
			function(data){
				if(data){
					updateAlert('提交成功!','success');
					setTimeout(function(){
						window.location.href = data.url;
					},300);
				}	
			}
		)
	}
	$scope.colorPicker = function($event){
		try{
			var ele = $($event.toElement);
			var top = ele.offset().top;
			var left = ele.offset().left;
			var w = ele.width();
			var h = ele.height();
			ele.addClass('active-color');
			$('.colpick').show().css({'top':top+h,'left':left+w});
		}catch(e){
			
		}
	}
	$('.color_picker_hide').colpick({
		colorScheme:'white',
		submitText:"确定",
		layout:'rgbhex',
		color:'ff8800',
		onSubmit:function(hsb,hex,rgb,el) {
			$scope.activeItem.params[$('.active-color').data('color')] = '#'+hex;
			$('.active-color').css('background-color', '#'+hex);
			$(el).colpickHide();
			$('.active-color').removeClass('active-color');
			$('.temp_click').click();
		}
	})
	//添加商品
	$scope.addGoodsDialog = function(dataUrl){
		$.WeiPHP.openSelectGoods(dataUrl,function(goodsList){
			if(goodsList.length>0){
				if($scope.activeItem.params.hasTestData==1){
						$scope.activeItem.params.goods_list = new Array();
				}
				for(var i=0;i<goodsList.length;i++){
					$scope.activeItem.params.goods_list.push(goodsList[i]);
				}
				$scope.activeItem.params.hasTestData = 0;
				//console.log($scope.activeItem.params.goods_list);
				$('.temp_click').click();
			}
		});
	}
	//删除商品
	$scope.deleteGoods = function(obj){
		$scope.activeItem.params.goods_list.remove(obj);
	}
	
	//添加幻灯片
	$scope.addBanner = function(){
		var obj = new Object();
		obj.pic = IMG_PATH+"/no_cover_pic_s.png";
		obj.picId = 0;
		obj.title = "";
		obj.url = "";
		$scope.activeItem.params.banner_list.push(obj);
	}
	$scope.addBannerPic = function(obj){
		$.WeiPHP.uploadImgDialog(1,function(data){
			obj.pic = data[0].src;
			obj.picId = data[0].id;
			$('.temp_click').click();
		})
	}
	$scope.deleteBanner = function(obj){
		$scope.activeItem.params.banner_list.remove(obj);
	}
	$scope.tempClick = function($event){
		//TODo nothing
	}
	$scope.$on('ngRepeatFinished', function (ngRepeatFinishedEvent) {
		//下面是在table render完成后执行的js
		//try{
			//console.log('init done!')
			//$.WeiPHP.initBanner(false,5000);
			$("#modules").dragsort('destroy');
			$("#modules").dragsort({
			    itemSelector: ".js-sorttable", dragSelector: ".js-sorttable", dragBetween: true, placeHolderTemplate: "<div class='.js-sorttable'></div>",dragSelectorExclude:'.aciton_wrap',dragEnd: function() {$(".js-sorttable").attr('style','')}
		    });
		//}catch(e){
		
		//}
	});
}).controller('picListController', function($scope) {
	//添加图片
	$scope.addPicList = function(){
		var obj = new Object();
		obj.pic = IMG_PATH+"/no_cover_pic_s.png";
		obj.picId = 0;
		obj.title = "";
		obj.url = "";
		$scope.activeItem.params.pic_list.push(obj);
	}
	$scope.addPicListPic = function(obj){
		$.WeiPHP.uploadImgDialog(1,function(data){
			obj.pic = data[0].src;
			obj.picId = data[0].id;
			$('.temp_click').click();
			//console.log($scope.activeItem.params.pic_list);
		})
	}
	$scope.deletePicListPic = function(obj){
		$scope.activeItem.params.pic_list.remove(obj);
	}
}).controller('textNavListController',function($scope){
	$scope.addTextNav = function(){
		var obj = new Object();
		obj.title = "";
		obj.url = "";
		$scope.activeItem.params.text_nav_list.push(obj);
	}
	$scope.deleteTextNav = function(b){
		$scope.activeItem.params.text_nav_list.remove(b);
	}
}).controller('picNavController',function($scope){
	$scope.addPicNav = function(){
		var obj = new Object();
		obj.title = "";
		obj.url = "";
		obj.pic = IMG_PATH+"/no_cover_pic_s.png";
		obj.picId = 0;
		$scope.activeItem.params.pic_nav_list.push(obj);
	}
	$scope.addPicNavPic = function(obj){
		$.WeiPHP.uploadImgDialog(1,function(data){
			obj.pic = data[0].src;
			obj.picId = data[0].id;
			$('.temp_click').click();
			//console.log($scope.activeItem.params.pic_list);
		})
	}
	$scope.deletePicNav = function(b){
		$scope.activeItem.params.pic_nav_list.remove(b);
	}
}).controller('caseController',function($scope){
	$scope.addCasePic = function(obj){
		$.WeiPHP.uploadImgDialog(1,function(data){
			obj.pic = data[0].src;
			obj.picId = data[0].id;
			$('.temp_click').click();
			//console.log($scope.activeItem.params.pic_list);
		})
	}
})
app.directive('onFinishRenderFilters', function ($timeout) {
	return {
		restrict: 'A',
		link: function(scope, element, attr) {
			if (scope.$last === true) {
				$timeout(function() {
					scope.$emit('ngRepeatFinished');
				});
			}
		}
	};
});
app.filter('to_trusted', ['$sce', function($sce){
	return function(text) {
		return $sce.trustAsHtml(text);
	};
}]);
angular.bootstrap(document, ['app']);
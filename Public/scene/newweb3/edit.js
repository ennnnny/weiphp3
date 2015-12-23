
function delLayout(id, y,screenId){
	/* 删除模块事件 */
		if( ! confirm('将同时删除内容数据，不可恢复，您确定删除该模块吗？') ){
			return false;
		}
		messageNote('正在为您删除...', 2);
		$.getJSON('/my/screen/del?screenId='+screenId, {}, function(result){
			if(result.success) {
				messageNote('删除模块成功！', 1);
				del_callback();
			}else{
				alert(result.data);
			}
		});
}


function saveLayout(appId, index_sort){

	$.getJSON('/my/screen/update_sort', {'ids':index_sort}, function(result){
		if(result.success){
			// 显示保存成功信息
			messageNote('更新成功！', 1);
			//var mframe = document.getElementById('app_review');
			//mframe.src = mframe.src;
		}else{
			alert(result.message);
		}
	});
}

/**
 * 编辑layout页面
 * @param id
 * @param style 增加style字段 用于个性化显示综艺选秀的multi插件
 */
function editLayout(appId, y,screenId)
{
	var editFrame = $("#app_item_setting");
	var src = '/my/screen/add?appId='+appId+'&type=0&screenId='+screenId+'&y='+(y+55)+'&t_='+Math.ceil(new Date()/1000);
	$('#app_item_setting_div').addClass("opt-bdline");
	editFrame.attr('src', src);
	
}

function insertScreen(appId,y,fromScreenId){
	addLayout(appId,y,1,fromScreenId);
}


/**
 * 添加页面
 * @param id
 * @param y
 */
function addLayout(id, y,type,fromScreenId)
{
	
	if( $(".edit_box").length == 0){
		$("#templateEdit").css({position:'relative'}).append('<div class="edit_box"><iframe frameborder="0" scrolling="no" class="default" id="editFrame" src=""></iframe></div>');
	}
	
	var editFrame = $("#editFrame");
	var box = $(".edit_box");
	var eframe =null;
	
	var src = '/my/screen/add?appId='+id+'&type='+type+'&fromScreenId='+fromScreenId+'&y='+(y+55)+'&t_='+Math.ceil(new Date()/1000);

	var html = '<div id="openLoading" style="width:300px; display:none; color:#999; font-size:13px; text-align:center; margin:80px auto 0 auto; line-height:24px;"><p><img src="/manage/res/images/w_loader.gif" style="width:30px;"/><br />请稍候...</p></div>';
	if(editFrame.attr('src') != src){
		// 清空历史打开页面
		eframe = document.getElementById('editFrame').contentWindow;
		// 显示loading
		$(eframe.document.body).replaceWith(html);
		eframe.document.getElementById('openLoading').style.display = 'block';
		
		editFrame.attr('src', src);
	}
	
	var historyTop = parseInt( box.css('margin-top') );
	historyTop = historyTop ? historyTop : 0; // 兼容IE
	y = (y-40) > 0 ? (y-40) : 0;
	if( Math.abs(y - historyTop) > 10 ){
		box.css('margin-top', y); // 浮框位移
	}
	
	// 隐藏添加模块窗
	if(document.getElementById('addFrame')){
		$(".add_box").hide(); 
		document.getElementById('addFrame').src = '';
	}
	if (eframe) {
		// 预载loading内容
		$(eframe.document.body).append(html);
	}
	
	box.show();
	
}




/**
 * 添加控件
 * @param id
 * @param y
 */
function addItemLayout(screenId, y)
{
	
	if( $(".edit_box").length == 0){
		$("#templateEdit").css({position:'relative'}).append('<div class="edit_box"><iframe frameborder="0" scrolling="no" class="default" id="editFrame" src=""></iframe></div>');
	}
	
	var editFrame = $("#editFrame");
	var box = $(".edit_box");
	var eframe =null;
	
	var src = '/my/item/list?screenId='+screenId+'&y='+(y+55)+'&t_='+Math.ceil(new Date()/1000);

	var html = '<div id="openLoading" style="width:300px; display:none; color:#999; font-size:13px; text-align:center; margin:80px auto 0 auto; line-height:24px;"><p><img src="/manage/res/images/w_loader.gif" style="width:30px;"/><br />请稍候...</p></div>';
	if(editFrame.attr('src') != src){
		// 清空历史打开页面
		eframe = document.getElementById('editFrame').contentWindow;
		// 显示loading
		$(eframe.document.body).replaceWith(html);
		eframe.document.getElementById('openLoading').style.display = 'block';
		
		editFrame.attr('src', src);
	}
	
	var historyTop = parseInt( box.css('margin-top') );
	historyTop = historyTop ? historyTop : 0; // 兼容IE
	y = (y-40) > 0 ? (y-40) : 0;
	if( Math.abs(y - historyTop) > 10 ){
		box.css('margin-top', y); // 浮框位移
	}
	
	// 隐藏添加模块窗
	if(document.getElementById('addFrame')){
		$(".add_box").hide(); 
		document.getElementById('addFrame').src = '';
	}
	if (eframe) {
		// 预载loading内容
		$(eframe.document.body).append(html);
	}
	
	box.show();
	
}

function editItemLayout(screenItemId, y, tab)
{	 
	var editFrame = $("#app_item_setting");
	var src = '/my/item/edit?screenItemId='+screenItemId+'&y='+(y+55)+'&t_='+Math.ceil(new Date()/1000) + "#" + tab;
	$('#app_item_setting_div').addClass("opt-bdline");
	editFrame.attr('src', src);
	//editFrame.show();
}


function delItemLayout(screenItemId,y){
	
	/* 删除模块事件 */
		if( ! confirm('将同时删除内容数据，不可恢复，您确定删除该控件吗？') ){
			return false;
		}
		messageNote('正在为您删除...', 2);
		$.getJSON('/my/item/del?screenItemId='+screenItemId, {}, function(result){
			if(result.success) {
				messageNote('删除控件成功！', 1);
				
				reFlushAppShow();
				closeTheEditor();
			}else{
				alert(result.data);
			}
		});
}


function delItemLayout_by_m(screenItemId,y){
	
	/* 删除模块事件 */
		if( ! confirm('将同时删除内容数据，不可恢复，您确定删除该控件吗？') ){
			return false;
		}
		messageNote('正在为您删除...', 2);
		$.getJSON('/my/item/del?screenItemId='+screenItemId, {}, function(result){
			if(result.success) {
				messageNote('删除控件成功！', 1);
				
				reFlushAppShow();
				reFlushEditShow();
			}else{
				alert(result.data);
			}
		});
}


/**
 * 取消模块编辑
 */
function canelEdit()
{
	document.getElementById('app_item_setting').src = ''; // 清空编辑浮框iframe
	$('#app_item_setting_div').removeClass("opt-bdline");
	messageNote('添加操作已取消！', 2);
}

/**
 * 取消添加模块
 */
function canelAdd()
{
	document.getElementById('app_item_setting').src = ''; // 清空编辑浮框iframe
	$('#app_item_setting_div').removeClass("opt-bdline");
	messageNote('添加操作已取消！', 2);
	
	//reFlushAppShow();
}

/**
 * 关闭编辑浮框
 */
function closeEdit(layout_id, param)
{
 	
	// 固定布局
	if(param.fixed == 1){
		if (param.refresh) {
			// 需要刷新布局
			var mframe = document.getElementById('app_review');
			mframe.src = mframe.src;
		} else if (param.state != 3) {
			// 不需要刷新
			var mframe = document.getElementById('app_review').contentWindow;
		}
		messageNote('更新成功！', 1);
	}else{
		if (param.refresh) {
			// 需要刷新布局
			var mframe = document.getElementById('app_review');
			mframe.src = mframe.src;
		}else{
			 
		}
		
		messageNote('更新成功！', 1);
	}
			
	document.getElementById('app_item_setting').src = ''; // 清空编辑浮框iframe
	$('#app_item_setting_div').removeClass("opt-bdline");
}

function flushAppReivew(){
	// 需要刷新布局
	var mframe = document.getElementById('app_review');
	mframe.src = mframe.src;
}

/**
 * 关闭添加浮框
 */
function closeAdd()
{
	document.getElementById('app_item_setting').src = ''; // 清空编辑浮框iframe
	$('#app_item_setting_div').removeClass("opt-bdline");
}
/**
 * 弹出窗口
 */
var dialogBox;
function openPage (url, title) {
	dialogBox = dialog({type:'iframe', value:url}, {title:title, closeText:'×'});
	$(".dialog iframe").css('width', '800px');
	$(".dialog iframe").css('height', '500px');
	var left = ($(window).width() - 800) / 2;
	var top = ($(window).height() - 500) / 2+20;
	top = top < 130? 130: top;
	$(".dialog").css({top:top,left:left});
}

/**
 * 信息通知
 * @param int $type 1成功通知，2提示通知, 3警告通知, 0失败通知
 * @param int hideTime 自动关闭通知时间秒数，0为不关闭
 */
var isScroll = false;
function messageNote(msg, type, hideTime)
{
	type = type ? type : 0;
	hideTime = hideTime ? hideTime : 3000;
	
	if($('#msg-note').length == 0){
		$('body').append('<div id="msg-note" class="msg-note" style="display:none;z-index: 200;"></div>');
	}
	
	var msgBox = $('#msg-note');
	msgBox.css({
		position : 'absolute',
		padding : '5px 10px',
		color : '#ffffff'
	});
	msgBox.html(msg);
	
	var left = ($(window).width() - msgBox.width()) / 2;
    var top = $(document).scrollTop()+170;
    var bgColors = { 
    		0 : 'red',
    		1 : 'green',
    		2 : '#cccccc', 
    		3 : '#580000'
    }
    
	msgBox.css({
		top : top,
		left : left,
		'background-color' : bgColors[type]
	});
    
    if(isScroll == false){
    	$(window).scroll(function(){
    		msgBox.css({'top':$(document).scrollTop() + 170});
        });
    }
	
	msgBox.show();
	if(hideTime > 0){
		setTimeout(function(){ msgBox.hide() }, hideTime);
	}else{
		setTimeout(function(){ msgBox.hide() }, 20000);
	}
}

/****
示例：
function info(width, height){
	console.info(width + '; ' + height);
}
<input type="file" name="file" id="file">
<button onclick="getImageInfo('file', info)">Get Image Info</button>
*/
function getImageInfo(fileId, callback){
	var file = document.getElementById(fileId);
	var img = new Image();
	img.onload = function(e){
		callback.apply(null, [img.width, img.height]);
	}

	if("FileReader" in window){
		var reader = new FileReader()

		reader.onload = function(e){
			var dataUri = e.target.result;

			img.src = dataUri;
		}

		reader.readAsDataURL(file.files[0]);
	}else{
		img.src = file.value;
	}
}


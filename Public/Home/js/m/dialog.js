// JavaScript Document dialog
/**
 *(**************************** 通用对话框************************* 
 */
(function(){
	var elemDialog, elemOverlay, elemContent, elemTitle,
		inited = false,
		body = document.compatMode && document.compatMode !== 'BackCompat' ?
					document.documentElement : document.body,
		cssFixed;
	
	function init(){
		if (!inited){
			createOverlay();
			createDialog();
			inited = true;
		}
	}
	
	function createOverlay(){
		if (!elemOverlay){
			elemOverlay = $('<div class="box_overlay"></div>');
			$('body').append(elemOverlay);
		}
	}
	function createDialog(){
		if (!elemDialog){
			
					elemDialog = $('<div class="dialog">'+
						'<div class="dialog_content"></div>'+
						'</div>');
					
					elemContent = $('.dialog_content', elemDialog);
					$('body').append(elemDialog);
					elemDialog.show();
					
				
		}
	}
	function open(){
		elemDialog.show();
		elemOverlay.show();
		//$('select').hide();
	}
	function close(){
		elemDialog.hide();
		if(elemOverlay)elemOverlay.hide();
		elemContent.empty();
		//$('select').show();
	}
	
	function setHtml(html){
		elemContent.html(html);
	}	
	var Dialog = {
		loading:function(){
			this.open("<p class='dialog_loading'></p>");
			},
		success:function(){
			var successTips = "操作成功!";
			if(arguments[0]!=null)successTips = arguments[0];
			this.open("<p class='dialog_success'>"+successTips+"</p>");
			setTimeout(function(){
				$.Dialog.close();
				},2000)
			},
		fail:function(){
			var failTips = "操作失败!";
			if(arguments[0]!=null)failTips = arguments[0];
			this.open("<p class='dialog_fail'>"+failTips+"</p>");
			setTimeout(function(){
				$.Dialog.close();
				},2000)
			},
		confirm:function(title,msg,callback,jump_url){
				var _title = title;
				var _msg = msg;
				var tempHtml =$("<div class='dialog_confirm'><p class='title'>"+title+"</p><p class='msg'></p><p class='btnWrap'><a href='javascript:;' class='confirmBtn'>确定</a></p></div>");
				$('.msg',tempHtml).append(msg);
				this.open(tempHtml);
				$('.confirmBtn',tempHtml).click(function(){
					if(callback){
						callback();
					}else if(jump_url){
					     window.location.href=jump_url;
					}else{
						$.Dialog.close();	
					}
				});
			},	
		confirmBox:function(title,msg,opts){
				var _title = title;
				var _msg = msg;
				var leftText = "否";
				var rightText = "是"
				if(opts){
					leftText = opts.leftBtnText || "否";
					rightText = opts.rightBtnText || "是"
				}
				var tempHtml =$("<div class='dialog_confirm'><p class='title'>"+title+"</p><p class='msg'>"+_msg+"</p><p class='btnWrap'><a href='javascript:;' class='leftBtn'>"+leftText+"</a><a href='javascript:;' class='rightBtn'>"+rightText+"</a></p></div>");
				this.open(tempHtml);
				$('.rightBtn',tempHtml).click(function(){
					if(opts && opts.rightCallback){
						opts.rightCallback();
					}else{
						$.Dialog.close();	
					}
				});
				$('.leftBtn',tempHtml).click(function(){
					if(opts && opts.leftCallback){
						opts.leftCallback();
					}else{
						$.Dialog.close();	
					}
				});
			},	
		open: function(html){
			init();
			setHtml(html);
			open();
		},
		close: close
	};
	
	$.extend($,{Dialog: Dialog});
	
})();
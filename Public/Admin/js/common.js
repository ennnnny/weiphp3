//dom加载完成后执行的js
;$(function(){

	//全选的实现
	$(".check-all").click(function(){
		$(".ids").prop("checked", this.checked);
	});
	$(".ids").click(function(){
		var option = $(".ids");
		option.each(function(i){
			if(!this.checked){
				$(".check-all").prop("checked", false);
				return false;
			}else{
				$(".check-all").prop("checked", true);
			}
		});
	});
	
	$('.data-table .confirm').click(function(){
		//if(window.confirm("确认要执行删除操作吗？")){
		//	$.get($(this).attr('href'));
		//	$(this).parents('tr').fadeOut();
		//	return false;
		//}else{
		//	return false;
		//}	
	})

    //ajax get请求
    $('.ajax-get').click(function(){
        var target;
        var that = this;
        if ( $(this).hasClass('confirm') ) {
            if(!confirm('确认要执行该操作吗?')){
                return false;
            }
        }
        if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {
            $.get(target).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        updateAlert(data.info + ' 页面即将自动跳转~','alert-success');
                    }else{
                        updateAlert(data.info,'alert-success');
                    }
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else if( $(that).hasClass('no-refresh')){
                            $('#top-alert').find('button').click();
                        }else{
                            location.reload();
                        }
                    },1500);
                }else{
                    updateAlert(data.info);
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else{
                            $('#top-alert').find('button').click();
                        }
                    },1500);
                }
            });

        }
        return false;
    });

    //ajax post submit请求
    $('.ajax-post').click(function(){
        var target,query,form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var nead_confirm=false;
        if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ){
            form = $('.'+target_form);

            if ($(this).attr('hide-data') === 'true'){//无数据时也可以使用的功能
            	form = $('.hide-data');
            	query = form.serialize();
            }else if (form.get(0)==undefined){
            	return false;
            }else if ( form.get(0).nodeName=='FORM' ){
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                if($(this).attr('url') !== undefined){
                	target = $(this).attr('url');
                }else{
                	target = form.get(0).action;
                }
                query = form.serialize();
            }else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {
                form.each(function(k,v){
                    if(v.type=='checkbox' && v.checked==true){
                        nead_confirm = true;
                    }
                })
                if ( nead_confirm && $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.serialize();
            }else{
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
            $.post(target,query).success(function(data){
				$(that).removeClass('disabled').prop('disabled',false);
                if (data.status==1) {
                    if (data.url) {
                        updateAlert(data.info + ' 页面即将自动跳转~','alert-success');
                    }else{
                        updateAlert(data.info ,'alert-success');
                    }
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else if( $(that).hasClass('no-refresh')){
                            $('#top-alert').find('button').click();
                        }else{
                            location.reload();
                        }
                    },1500);
                }else{
                    updateAlert(data.info);
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else{
                            $('#top-alert').find('button').click();
                        }
                    },1500);
                }
            });
        }
        return false;
    });

	/**顶部警告栏*/
	var content = $('#main');
	var top_alert = $('#top-alert');
	top_alert.find('.close').on('click', function () {
		top_alert.removeClass('block').slideUp(200);
		// content.animate({paddingTop:'-=55'},200);
	});

    window.updateAlert = function (text,c) {
		text = text||'default';
		c = c||false;
		if ( text!='default' ) {
            top_alert.find('.alert-content').text(text);
			if (top_alert.hasClass('block')) {
			} else {
				top_alert.addClass('block').slideDown(200);
				// content.animate({paddingTop:'+=55'},200);
			}
		} else {
			if (top_alert.hasClass('block')) {
				top_alert.removeClass('block').slideUp(200);
				// content.animate({paddingTop:'-=55'},200);
			}
		}
		if ( c!=false ) {
            top_alert.removeClass('alert-error alert-warn alert-info alert-success').addClass(c);
		}
		setTimeout(function(){
			if($('#top-alert').is(":visible")){
				$('#top-alert').find('.close').click();
			}
		},3000)
	};

    //按钮组
    (function(){
        //按钮组(鼠标悬浮显示)
        $(".btn-group").mouseenter(function(){
            var userMenu = $(this).children(".dropdown ");
            var icon = $(this).find(".btn i");
            icon.addClass("btn-arrowup").removeClass("btn-arrowdown");
            userMenu.show();
            clearTimeout(userMenu.data("timeout"));
        }).mouseleave(function(){
            var userMenu = $(this).children(".dropdown");
            var icon = $(this).find(".btn i");
            icon.removeClass("btn-arrowup").addClass("btn-arrowdown");
            userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
            userMenu.data("timeout", setTimeout(function(){userMenu.hide()}, 100));
        });

        //按钮组(鼠标点击显示)
        // $(".btn-group-click .btn").click(function(){
        //     var userMenu = $(this).next(".dropdown ");
        //     var icon = $(this).find("i");
        //     icon.toggleClass("btn-arrowup");
        //     userMenu.toggleClass("block");
        // });
        $(".btn-group-click .btn").click(function(e){
            if ($(this).next(".dropdown").is(":hidden")) {
                $(this).next(".dropdown").show();
                $(this).find("i").addClass("btn-arrowup");
                e.stopPropagation();
            }else{
                $(this).find("i").removeClass("btn-arrowup");
            }
        })
        $(".dropdown").click(function(e) {
            e.stopPropagation();
        });
        $(document).click(function() {
            $(".dropdown").hide();
            $(".btn-group-click .btn").find("i").removeClass("btn-arrowup");
        });
    })();

    // 独立域表单获取焦点样式
    $(".text").focus(function(){
        $(this).addClass("focus");
    }).blur(function(){
        $(this).removeClass('focus');
    });
    $("textarea").focus(function(){
        $(this).closest(".textarea").addClass("focus");
    }).blur(function(){
        $(this).closest(".textarea").removeClass("focus");
    });
});

/* 上传图片预览弹出层 */
$(function(){
    $(window).resize(function(){
        var winW = $(window).width();
        var winH = $(window).height();
        $(".upload-img-box").click(function(){
        	//如果没有图片则不显示
        	if($(this).find('img').attr('src') === undefined){
        		return false;
        	}
            // 创建弹出框以及获取弹出图片
            var imgPopup = "<div id=\"uploadPop\" class=\"upload-img-popup\"></div>"
            var imgItem = $(this).find(".upload-pre-item2").html();

            //如果弹出层存在，则不能再弹出
            var popupLen = $(".upload-img-popup").length;
            if( popupLen < 1 ) {
                $(imgPopup).appendTo("body");
                $(".upload-img-popup").html(
                    imgItem + "<a class=\"close-pop\" href=\"javascript:;\" title=\"关闭\"></a>"
                );
            }

            // 弹出层定位
            var uploadImg = $("#uploadPop").find("img");
            var popW = uploadImg.width();
            var popH = uploadImg.height();
            var left = (winW -popW)/2;
            var top = (winH - popH)/2 + 50;
            $(".upload-img-popup").css({
                "max-width" : winW * 0.9,
                "left": left,
                "top": top
            });
        });

        // 关闭弹出层
        $("body").on("click", "#uploadPop .close-pop", function(){
            $(this).parent().remove();
        });
    }).resize();

    // 缩放图片
    function resizeImg(node,isSmall){
        if(!isSmall){
            $(node).height($(node).height()*1.2);
        } else {
            $(node).height($(node).height()*0.8);
        }
    }
})

//标签页切换(无下一步)
function showTab() {
    $(".tab-nav li").click(function(){
        var self = $(this), target = self.data("tab");
        self.addClass("current").siblings(".current").removeClass("current");
        window.location.hash = "#" + target.substr(3);
        $(".tab-pane.in").removeClass("in");
        $("." + target).addClass("in");
    }).filter("[data-tab=tab" + window.location.hash.substr(1) + "]").click();
}

//标签页切换(有下一步)
function nextTab() {
     $(".tab-nav li").click(function(){
        var self = $(this), target = self.data("tab");
        self.addClass("current").siblings(".current").removeClass("current");
        window.location.hash = "#" + target.substr(3);
        $(".tab-pane.in").removeClass("in");
        $("." + target).addClass("in");
        showBtn();
    }).filter("[data-tab=tab" + window.location.hash.substr(1) + "]").click();

    $("#submit-next").click(function(){
        $(".tab-nav li.current").next().click();
        showBtn();
    });
}

// 下一步按钮切换
function showBtn() {
    var lastTabItem = $(".tab-nav li:last");
    if( lastTabItem.hasClass("current") ) {
        $("#submit").removeClass("hidden");
        $("#submit-next").addClass("hidden");
    } else {
        $("#submit").addClass("hidden");
        $("#submit-next").removeClass("hidden");
    }
}
//导航高亮
function highlight_subnav(url){
    $('.side-sub-menu').find('a[href="'+url+'"]').closest('li').addClass('current');
}
//weiphp 设置默认高亮
highlight_subnav(window.location.href);

function closeUpdateTips(){
	
	}
function change_event(obj){	
	var hiderel = $(obj).attr('toggle-data');
	if(hiderel=='' || hiderel==undefined)	return false;
	
	var arr = new Array();
    arr = hiderel.split(",");
    $.each(arr, function (index, tx) {		
		var arr2 = new Array();
		arr2 = tx.split("@");

		if(arr2[1]=='hide'){
		    $('.toggle-'+arr2[0]).hide();
		}else{
			$('.toggle-'+arr2[0]).show();
		}
	});
	
}

function parseSecondToMinAndSecond(value){
	var mins = Math.floor( value / 60 );
	var seconds = ( value - mins*60 );
	return (mins < 10 ? "0"+mins : mins) + ":" + ( seconds == 0 ? "00" : seconds );
}
function parseSecondToMinAndSecond2(value){
	var mins = Math.floor( value / 60 );
	var seconds = ( value - mins*60 );
	return (mins < 10 ? "0"+mins : mins) + ":00";
}
//上传图片组件
function initUploadImg(){
	
	$(".uploadrow2").each(function(index, obj) {
		var name = $(obj).attr('rel');
		var is_mult = $(obj).hasClass('mult');
		$('#upload_picture_'+name).uploadify({
			"height"          : 100,
			"swf"             : STATIC+"/uploadify/uploadify.swf",
			"fileObjName"     : "download",
			"buttonText"      : "上传图片",
			"uploader"        : UPLOAD_PICTURE,
			"width"           : 100,
			'removeTimeout'	  : 1,
			'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
			"onUploadSuccess" : function(file, data, response) {
				console.log(22);
				console.log($('#cover_id_'+name).parent().find('.upload-img-box'));
                onUploadImsSuccess(file, data, name, is_mult);
            },
			"onUploadError" : function(file, data, response) {
				console.log(33);
				console.log(data);
               // onUploadImsSuccess(file, data, name, is_mult);
            }
	    });
	});
}

function onUploadImsSuccess(file, data, name, is_mult){
	var data = $.parseJSON(data);
	var src = '';
	if(data.status){
		$('#cover_id_'+name).val(data.id);
		src = data.url || ROOT + data.path;
		
		if(is_mult){
			$("#mutl_picture_{$field.name}").append(
				'<div class="upload-pre-item22"><img width="100" height="100" src="' + src + '"/>'
				+'<input type="hidden" name="'+name+'[]" value="'+data.id+'"/></div>'
			);
			
			$('.upload-pre-item22').click(function() {
				if(confirm('确认删除？')){
					$(this).remove();
				}
			});
		}else{										
			$('#cover_id_'+name).parent().find('.upload-img-box').html(
				'<div class="upload-pre-item2"><img width="100" height="100" src="' + src + '"/></div><em class="edit_img_icon">&nbsp;</em>'
			);
			
			$('.weixin-cover-pic').attr('src',src);
		}
	} else {
		updateAlert(data.info);
		setTimeout(function(){
			$('#top-alert').find('button').click();
			$(that).removeClass('disabled').prop('disabled',false);
		},1500);
	}
}
//上传附件组件
function initUploadFile(){
	$(".upload_file").each(function(index, obj) {
		var name = $(obj).find('input[type="hidden"]').attr('name');
		$("#upload_file_"+name).uploadify({
			"height"          : 30,
			"swf"             : STATIC+"/uploadify/uploadify.swf",
			"fileObjName"     : "download",
			"buttonText"      : "上传附件",
			"uploader"        : UPLOAD_FILE,
			"width"           : 120,
			'removeTimeout'	  : 1,
			"onUploadSuccess" : function(file, data, response) {
                onUploadFileSuccess(file, data, name);
            }
		});
	});
}								
function onUploadFileSuccess(file, data, name){
	var data = $.parseJSON(data);
	if(data.status){
		$("input[name="+name+"]").val(data.id);
		$("input[name="+name+"]").parent().find('.upload-img-box').html(
			"<div class=\"upload-pre-file\"><span class=\"upload_icon_all\"></span>" + data.name + "</div>"
		);
	} else {
		updateAlert(data.info);
		setTimeout(function(){
			$('#top-alert').find('button').click();
			$(that).removeClass('disabled').prop('disabled',false);
		},1500);
	}
}

//颜色拾取
function simpleColorPicker(_this,callback){
	var currentColor = $(this).find('input').val();
	var left = $(_this).offset().left;
	var top = $(_this).offset().top;
	var height = $(_this).height();
	var colors = ["#55BD47","#10AD61","#35A4DE","#3D78DA","#9058CB","#DE9C33","#EBAC16","#F9861F","#E75735","#D54036"];
	var colorEles = "";
	for(var i=0;i<colors.length;i++){
		colorEles += "<span data-color='"+colors[i]+"' style='background-color:"+colors[i]+"'></span>";
	}
	var $html = $("<div class='simpleColorBox'>"+colorEles+"</div>");
	$html.css({'top':top+height,'left':left});
	$('body').append($html); 
	$('span',$html).click(function(){
		var color = $(this).data('color');
		$(_this).css({'background':color});
		$(_this).find('input').val(color);
		if(callback)callback(color);
		$html.remove();
	})
}
(function(){
	/*
	* 选择贺卡魔板
	* dataUrl请求数据
	* callback 回调
	*/
	function chooseWishTemplateDialog(dataUrl,callback){
		var $contentHtml = $('<div class="chooseTemplateDialog"><div id="cateList" class="mt_10"></div><ul class="mt_10"><center><br/><br/><br/><img src="'+IMG_PATH+'/loading.gif"/></center></ul></div>');
		$.Dialog.open("选择模板",{width:900,height:600},$contentHtml);
		$.ajax({
			url:dataUrl,
			data:{'type':'ajax'},
			dataType:'JSON',
			success:function(data){
				var cateArr = data.tempListCate;
				var contentArr = data.tempList;
				var cateHtml = '<a href="javascript:;" class="current" data-file="">全部</a>';
				for(var i=0;i<cateArr.length;i++){
					cateHtml += '<a href="javascript:;" data-file="'+cateArr[i].file+'">'+cateArr[i].title+'</a>';
				}
				$('#cateList',$contentHtml).html(cateHtml);
				if(contentArr && contentArr.length>0){
					var liHtml = "";
					for(var i=0;i<contentArr.length;i++){
						var contentJson = contentArr[i];
						liHtml += '<li title="点击使用该模板" data-cate="'+contentJson.cate+'" data-template="'+contentJson.dirName+'"><img src="'+contentJson.icon+'"/><span>'+contentJson.desc+'</span><p></p></li>';
					}
					$('ul',$contentHtml).html(liHtml);
				}else{
					$('ul',$contentHtml).html("<center><br/><br/><br/>该分类没有任何模板</center>");
				}
				$('#cateList a',$contentHtml).on('click',function(){
					if($(this).hasClass('current'))return;
					$(this).addClass('current');
					$(this).siblings().removeClass('current');
					$('ul',$contentHtml).html('<center><br/><br/><br/><img src="'+IMG_PATH+'/loading.gif"/></center>');
					var cateFile = $(this).data('file');
					$.ajax({
						url:dataUrl,
						data:{'type':'ajax','cateFile':cateFile},
						dataType:'JSON',
						success:function(data){
							var contentArr = data.tempList;
							if(contentArr && contentArr.length>0){
								var liHtml = "";
								for(var i=0;i<contentArr.length;i++){
									var contentJson = contentArr[i];
									liHtml += '<li title="点击使用该模板" data-cate="'+contentJson.cate+'" data-template="'+contentJson.dirName+'"><img src="'+contentJson.icon+'"/><span>'+contentJson.desc+'</span><p></p></li>';
								}
								$('ul',$contentHtml).html(liHtml);
								$('li',$contentHtml).on('click',function(){
									callback(this);
								})
							}else{
								$('ul',$contentHtml).html("<center><br/><br/><br/>该分类没有任何模板</center>");
							}
						}
					})
				})
				$('li',$contentHtml).on('click',function(){
					callback(this);
				})
			}
		})
	}
	/*
	* 选择贺卡内容
	* addUrl 新添跳转连接
	* dataUrl 请求数据链接
	* callback 回调
	*/
	function chooseWishContentDialog(addUrl,dataUrl,callback){
		var $contentHtml = $('<div class="chooseWishDialog"><div id="cateList" class="mt_10"></div><ul class="mt_10"><center><br/><br/><br/><img src="'+IMG_PATH+'/loading.gif"/></center></ul><br/><center class="mt_10"><a href="javascript:;" id="addNewContentBtn" class="border-btn">添加新的祝福语</a</center>></div>');
		$.Dialog.open("选择祝福语",{width:600,height:500},$contentHtml);
		$('#addNewContentBtn',$contentHtml).click(function(){
			window.open(addUrl);
		})
		$.ajax({
			url:dataUrl,
			data:{'type':'ajax'},
			dataType:'JSON',
			success:function(data){
				var cateArr = data.cate;
				var contentArr = data.content;
				var cateHtml = '<a href="javascript:;" class="current" data-id="0">全部</a>';
				for(var i=0;i<cateArr.length;i++){
					var cateJson = cateArr[i];
					cateHtml += '<a href="javascript:;" data-id="'+cateJson.id+'">'+cateJson.content_cate_name+'</a>';
				}
				$('#cateList',$contentHtml).html(cateHtml);
				if(contentArr && contentArr.length>0){
					var liHtml = "";
					for(var i=0;i<contentArr.length;i++){
						var contentJson = contentArr[i];
						liHtml += '<li>'+contentJson.content+'</li>';
					}
					$('ul',$contentHtml).html(liHtml);
				}else{
					$('ul',$contentHtml).html("<center><br/><br/><br/>该分类没有添加任何祝福语</center>");
				}
				$('#cateList a',$contentHtml).on('click',function(){
					if($(this).hasClass('current'))return;
					$(this).addClass('current');
					$(this).siblings().removeClass('current');
					$('ul',$contentHtml).html('<center><br/><br/><br/><img src="'+IMG_PATH+'/loading.gif"/></center>');
					var cateId = $(this).data('id');
					$.ajax({
						url:dataUrl,
						data:{'type':'ajax','cateId':cateId},
						dataType:'JSON',
						success:function(data){
							var contentArr = data.content;
							if(contentArr && contentArr.length>0){
								var liHtml = "";
								for(var i=0;i<contentArr.length;i++){
									var contentJson = contentArr[i];
									liHtml += '<li>'+contentJson.content+'</li>';
								}
								$('ul',$contentHtml).html(liHtml);
								$('li',$contentHtml).on('click',function(){
									callback(this);
								})
							}else{
								$('ul',$contentHtml).html("<center><br/><br/><br/>该分类没有添加任何祝福语</center>");
							}
						}
					})
				})
				$('li',$contentHtml).on('click',function(){
					callback(this);
				})
			}
		})
	}
	var WeiPHP = {
		chooseWishTemplateDialog:chooseWishTemplateDialog,
		chooseWishContentDialog:chooseWishContentDialog
	}
	$.extend({WeiPHP:WeiPHP});
})();
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
		if(window.confirm("确认要执行删除操作吗？")){
			$.get($(this).attr('href'));
			$(this).parents('tr').fadeOut();
			return false;
		}else{
			return false;
		}	
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
					if($(that).hasClass('dialog_submit')){
						//对话框中的提交动作
						
						if (data.url) {
							window.parent.location.href=data.url;
						}else{
							window.parent.location.reload();
						}
						window.parent.$.Dialog.close();
					}else{
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
					}
                }else{
					if($(that).hasClass('dialog_submit')){
						alert(data.info);
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
		},2000)
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
		/*
        $(document).click(function() {
            $(".dropdown").hide();
            $(".btn-group-click .btn").find("i").removeClass("btn-arrowup");
        });
		*/
		
		$('.dropdown').hover(function(){
				$(this).find('.dropdown-menu').show();
				$(this).find('.dropdown-toggle').addClass('hover');
			}
			,function(){
				$(this).find('.dropdown-menu').hide();
				$(this).find('.dropdown-toggle').removeClass('hover');
			});
    })();
	//侧栏
	if(!$('#main-container .sidebar').html()){
		$('#main-container .main_body').width(1200);
	}

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
	
	//侧栏
	if(!$('#main-container .sidebar').html()){
		$('#main-container .main_body').width(1200);
	}
	/*
	$('.sidenav_parent').click(function(){
		if($(this).hasClass('isopen')){
			$(this).next().hide();
			$(this).find('.ficon').addClass('fa-plus-square').removeClass('fa-minus-square');
			$(this).removeClass('isopen');
		}else{
			$(this).addClass('isopen');
			$(this).next().show();
			$(this).find('.ficon').removeClass('fa-plus-square').addClass('fa-minus-square');
			}
		})
	$('.sidenav_sub li').each(function(index, element) {
        if($(this).hasClass('active')){
			$(this).parent().show();
			$(this).parent().prev().addClass('isopen');
			$(this).parent().prev().find('.ficon').removeClass('fa-plus-square').addClass('fa-minus-square');
			}
    });
	*/
	//ajax加载select 素材template数据
	if($('#template') && $('#template').data('url')){
		$('#template').prev().html('请选择一个素材模板<span class="check-tips">(不选择将使用默认模版)</span>');
		var $addBtn = $('<a class="border-btn" href="javascript:;">选择模板</a><br/>');
		var $prevHtml = $('<div class="mt10"><p style="line-height:40px" class="name"></p><img style="max-width:280px" src="'+IMG_PATH+'/loading.gif"/></div>')
		$addBtn.insertBefore($('#template'));
		$prevHtml.insertBefore($('#template'));
		$('#template').hide().css({'visibility':'hidden','height':0,'padding':0,'margin':0});
		var currentTemplate = $('#template').val();
		$.ajax({
			url:$('#template').data('url'),
			type:'GET',
			dataType:'JSON',
			success: function(data){
				//console.log(data)
				if(data){
					var opts = "";
					for(var i=0;i<data.length;i++){
						var json = data[i];
					 	opts += '<option value="'+data[i].template+'">'+data[i].title+'</option>';
						if(data[i].template == currentTemplate || currentTemplate==""){
							$('img',$prevHtml).attr('src',data[i].icon);
							$('.name',$prevHtml).text('当前使用的是：'+data[i].title);
						}
					}
					$('#template').append(opts);
					$addBtn.click(function(){
						var $contentHtml = $('<div class="chooseTemplateDialog"><div id="cateList" class="mt_10"></div><ul class="mt_10"><center><br/><br/><br/><img src="'+IMG_PATH+'/loading.gif"/></center></ul></div>');
						$.Dialog.open("选择模板",{width:900,height:600},$contentHtml);
						var liHtml="";
						for(var i=0;i<data.length;i++){
							var contentJson = data[i];
							liHtml += '<li title="点击使用该模板" data-template="'+contentJson.template+'"><img src="'+contentJson.icon+'"/><span>'+contentJson.title+'</span><p></p></li>';
						}
						$('ul',$contentHtml).html(liHtml);
						$('li',$contentHtml).on('click',function(){
							$('#template').val($(this).data('template')).hide();
							$('img',$prevHtml).attr('src',$(this).find('img').attr('src'));
							$('.name',$prevHtml).text('当前使用的是：'+$(this).find('span').text());
							$.Dialog.close();
							$('#template').hide();
							
						})
					})
				}
			}
		})
	}
	
	$('.upload-pre-item22 em').click(function() {
		if(confirm('确认删除？')){
			$(this).parent().remove();
		}
	});
	
	//初始化复制按钮
	$('.list_copy_link').each(function(index, element) {
        var id = $(element).attr('id');
		$.WeiPHP.initCopyBtn(id);
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
	
	//选择文字
	$(".selectRange").parent().on('mouseup',function (e) {
		var text = "";
        if (document.selection) {
            text = document.selection.createRange().text;
        }else if (window.getSelection()) {
            text = window.getSelection();
        }
        if (text!= "") {
			var url = $(this).find('.selectRange').data('url');	
			showAddToBlack(e.pageX + 10,e.pageY + 10,text,url);
        }
    });
	$(document).click(function(e){
		if(e && !(e.target == $('.selectRange')[0]) && $(addToBlackBtn).is(":visible") && !(e.target == $('#addToBlackBtn a')[0])){
			$('#addToBlackBtn').hide();
		}
	})
	//列表显示大图
	var listPicZoom = $('<div class="list_pic_zoom" style="position:absolute; border:1px solid #ddd; background:#fff;padding:10px;display:none; z-index:10000;"><img width="200" height="200" src=""/></div>');
	$('body').append(listPicZoom);
	$('.data-table .list_img').hover(function(){
		var left = $(this).offset().left+50;
		var top = $(this).offset().top-225;
		$('img',listPicZoom).attr('src',$(this).attr('src'));
		listPicZoom.css({left:left,top:top}).show();
	},
	function(){
		listPicZoom.hide();
		$('img',listPicZoom).attr('src','');
	})
})
var addToBlackBtn;
function showAddToBlack(x,y,t,url){
	if(!addToBlackBtn){
		addToBlackBtn = $("<div id='addToBlackBtn'><a href='javascript:;' style='background:#44b549;color:#fff;padding:2px 4px;' class='btn'>添加至黑名单</a></div>");
		$("body").append(addToBlackBtn);
		$(addToBlackBtn).click(function(){
			$(this).hide();
			var word = $(addToBlackBtn).data('text');	
			var url = $(addToBlackBtn).data('url');	
			//console.log(url);
			var data = {word:word+""};
			if(confirm('确认要将“'+word+'”加入黑名单吗?')){
				$.post(url,data,function(d){
					if(d.result=='success'){
						 updateAlert(d.msg ,'alert-success');
					}else{
						 updateAlert(d.msg);
					}
					
				});
			}
		})
	}
	$(addToBlackBtn).data('text',t);
	$(addToBlackBtn).data('url',url)
	setTimeout(function(){
		$("#addToBlackBtn").css({
			"top": y + "px",
			"left": x + "px",
			"position": "absolute"
		}).show();
	},200)
	
}

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
$(function(){
	var $window = $(window), $subnav = $("#subnav"), url;
	$window.resize(function(){
		$("#main").css("min-height", $window.height() - 130);
	}).resize();

	/* 表单获取焦点变色 */
	$("form").on("focus", "input", function(){
		$(this).addClass('focus');
	}).on("blur","input",function(){
				$(this).removeClass('focus');
			});
	$("form").on("focus", "textarea", function(){
		$(this).closest('label').addClass('focus');
	}).on("blur","textarea",function(){
		$(this).closest('label').removeClass('focus');
	});
	$('a').each(function(index, element) {
       if($(this).text()=="预览")$(this).attr('target','_blank');
    });
	
	//$('.sidebar').css('min-height',$('body').height());
	//控制聚焦
	$('.sidenav_sub li').each(function(){
		if($(this).hasClass('active')){
			var cate_id = $(this).data('id');
			$('.top_nav li').each(function(){
				if($(this).data('id')==cate_id){
					$(this).addClass('active');
				}
			})
		}
	})
});
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
function initUploadImg(opts){
	if(opts && opts.width && opts.height){
		width = opts.width;
		height = opts.height;
	}else{
		width = height = 100;
	}
	$(".upload-img-view").dragsort({
	    itemSelector: ".upload-pre-item22", dragSelector: ".upload-pre-item22", dragBetween: false, placeHolderTemplate: "<div class='upload-pre-item22'></div>",dragSelectorExclude:'em',dragEnd: function() {$(".upload-pre-item22").attr('style','')}
	});
	$uploadDom = $('.uploadrow2');
	if(opts && opts.uploadDom){
		$uploadDom = opts.uploadDom;
	}
	$uploadDom.each(function(index, obj) {
		setTimeout(function(){
			var name = $(obj).attr('rel');
			var is_mult = $(obj).hasClass('mult');
			$('#upload_picture_'+name).uploadify({
				"height"          : height,
				"swf"             : STATIC+"/uploadify/uploadify.swf",
				"fileObjName"     : "download",
				"buttonText"      : "上传图片",
				"uploader"        : UPLOAD_PICTURE,
				"width"           : width,
				'cancelImg'		  : 'JS/jquery.uploadify-v2.1.0/cancel.png',
				'removeTimeout'	  : 1,
				'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
				"onUploadSuccess" : function(file, data, response) {
					//console.log(22);
					//console.log($('#cover_id_'+name).parent().find('.upload-img-box'));
					$('#cover_id_'+name).parent().find('.upload-img-box').show();
					onUploadImsSuccess(file, data, name, is_mult,opts);
				},
				"onUploadError" : function(file, data, response) {
					//console.log(33);
					//console.log(data);
				   // onUploadImsSuccess(file, data, name, is_mult);
				}
			});
		},400);
		
	});
	
}

function onUploadImsSuccess(file, data, name, is_mult,opts){
	var data = $.parseJSON(data);
	var src = '';
	if(opts){
		var width = opts.width;
		var height = opts.height;
	}else{
		var width = height = 100;
	}
	if(data.status){
		$('#cover_id_'+name).val(data.id);
		src = data.url || ROOT + data.path;
		
		if(is_mult){
			$addImg = $('<div class="upload-pre-item22"><img width="100" height="100" src="' + src + '"/>'
				+'<input type="hidden" name="'+name+'[]" value="'+data.id+'"/><em onClick="if(confirm(\'确认删除？\')){$(this).parent().remove();}">&nbsp;</em></div>');
			$("#mutl_picture_"+name).append($addImg);
			
			
			$("#mutl_picture_"+name).dragsort('destroy');
			$("#mutl_picture_"+name).dragsort({
			    itemSelector: ".upload-pre-item22", dragSelector: ".upload-pre-item22", dragBetween: false, placeHolderTemplate: "<div class='upload-pre-item22'></div>",dragSelectorExclude:'em',dragEnd: function() {$(".upload-pre-item22").attr('style','')}
		    });
		}else{										
			$('#cover_id_'+name).parent().find('.upload-img-box').html(
				'<div class="upload-pre-item2" style="width:'+width+'px;height:'+height+'px;max-height:'+height+'px"><img width="'+width+'" height="'+height+'" src="' + src + '"/></div><em class="edit_img_icon">&nbsp;</em>'
			);
			
			$('.weixin-cover-pic').attr('src',src);
		}
		//外部回调函数
		if(!(opts && opts.callback)){
			var callback = $('#cover_id_'+name).data('callback');
			if(callback){
				eval(callback+'(\''+name+'\','+data.id+',\''+src+'\')');
			}
		}else{
			opts.callback(name,data.id,src);
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
function initUploadFile(callback){
	$(".upload_file").each(function(index, obj) {
		setTimeout(function(){
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
					if(callback) callback(data,name);
				}
			});
		},300);
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
		//console.log(data);
		updateAlert(data.info);
		setTimeout(function(){
			$('#top-alert').find('button').click();
			//$(that).removeClass('disabled').prop('disabled',false);
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
		$html.remove();
		if(callback)callback(color);
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
	/* 选择图文素材 */
	function openSelectAppMsg(dataUrl,callback,title){
		var $contentHtml = $('<div class="appmsg_dialog" style="padding:10px; max-height:560px;overflow-y:auto;overflow-x:hidden;"><ul class="mt_10"><center><br/><br/><br/><img src="'+IMG_PATH+'/loading.gif"/></center></ul></div>');
		$.Dialog.open(title?title:"选择图文素材",{width:1000,height:640},$contentHtml);
		$.ajax({
			url:dataUrl,
			data:{'type':'ajax'},
			dataType:'html',
			success:function(data){
				$data = $(data);
				$('ul',$contentHtml).html($data);
				$data.find('.material_list').masonry({
					// options
					itemSelector : '.appmsg_li'
					//columnWidth : 308
				  });
				$('li',$contentHtml).on('click',function(){
					callback(this);
				});
			}
		})
	}
	function initCopyBtn(id){
		var copyClient = new ZeroClipboard( document.getElementById(id) );
		copyClient.on( "ready", function( readyEvent ) {
		  copyClient.on( "aftercopy", function( event ) {
			updateAlert('复制成功!,请粘贴使用','alert-success');
		  } );
		} );
	}
	function uploadImgDialog(maxNum,callback){
		var $contentHtml = $('<div class="upload_img_dialog"><div class="mult_imgs">'+
                '<div class="upload-img-view" id="mutl_picture_temp_upload">'+
                '</div>'+
                '<div class="controls uploadrow2 mult" title="点击上传图片" rel="temp_upload">'+
                  '<input type="file" id="upload_picture_temp_upload">'+
                '</div>'+
            '</div><div class="btn_bar"><a href="javascript:;" class="btn confirm_btn">确定</a>&nbsp;&nbsp;<a href="javascript:;" class="border-btn cancel_btn">取消</a></div></div>');
		$.Dialog.open("上传图片",{width:600,height:400},$contentHtml);
		initUploadImg({
			uploadDom:$('.uploadrow2',$contentHtml),
			callback:function(name,id,src){
				var curCount = $('.upload-pre-item22',$contentHtml).size();
				if(curCount==maxNum){
					$('.uploadrow2',$contentHtml).css('visibility','hidden');
				}
			}
		});
		var interl = setInterval(function(){
			var curCount = $('.upload-pre-item22',$contentHtml).size();
			if(curCount==0){
				$('.uploadrow2',$contentHtml).css('visibility','visible');
			}
		},100);
		$('.confirm_btn',$contentHtml).click(function(){
			var data = new Array();
			$('.upload-pre-item22',$contentHtml).each(function(index, ele) {
                var obj = new Object();
				obj.id = $(ele).find('input').val();
				obj.src = $(ele).find('img').attr('src');
				data.push(obj);
            });
			if(data.length==0){
				alert('没有添加任何图片!');
				return;
			}else{
				callback(data);
				$.Dialog.close();
				clearInterval(interl);
			}
			
		})
		$('.cancel_btn',$contentHtml).click(function(){
			$.Dialog.close();
		})
	}
	/* 选择商品 */
	function openSelectGoods(dataUrl,callback){
		var $contentHtml = $('<div><div class="goods_dialog" style="padding:10px; height:530px;overflow-y:hidden;overflow-x:hidden;"><div class="mt_10"><iframe id="goodsIframe" name="goodsIframe" style="height:530px;width:100%; border:none" border="0" src="'+dataUrl+'&isAjax=ajax"></iframe></div></div><div class="btn_bar"><a href="javascript:;" class="btn confirm_btn">确定</a>&nbsp;&nbsp;<a href="javascript:;" class="border-btn cancel_btn">取消</a></div></div>');
		$.Dialog.open("选择商品",{width:1000,height:640},$contentHtml);
		
		$('.cancel_btn',$contentHtml).click(function(){
			$.Dialog.close();
		})
		$('.confirm_btn',$contentHtml).click(function(){
			var trs = $(window.frames["goodsIframe"].document,$contentHtml).find("table tr");
			var goodsList = new Array();
			trs.each(function(index, element) {
				if($(element).find(".ids").prop("checked")){
					var obj = new Object();
					obj.id = $(element).find("input").val();
					obj.title = $(element).find('td[type="title"]').text();
					obj.img = $(element).find('img')?$(element).find('img').attr('src'):"";
					obj.market_price = parseFloat($(element).find('td[type="market_price"]').text());
					obj.stock_num = parseFloat($(element).find('td[type="stock_num"]').text());
					obj.url = $(element).find('input').data('url');
					goodsList.push(obj);
				}
            });
			callback(goodsList);
			$.Dialog.close();
		})
	}
    /* 选择门店 */
	function openSelectShops(dataUrl,callback){
		var $contentHtml = $('<div><div class="goods_dialog" style="padding:10px; height:530px;overflow-y:auto;overflow-x:hidden;"><div class="mt_10"><iframe id="shopsIframe" name="shopsIframe" style="height:530px;width:100%; border:none" border="0" src="'+dataUrl+'&isAjax=ajax"></iframe></div></div><div class="btn_bar"><a href="javascript:;" class="btn confirm_btn">确定</a>&nbsp;&nbsp;<a href="javascript:;" class="border-btn cancel_btn">取消</a></div></div>');
		$.Dialog.open("选择门店",{width:640,height:640},$contentHtml);
		
		$('.cancel_btn',$contentHtml).click(function(){
			$.Dialog.close();
		})
		$('.confirm_btn',$contentHtml).click(function(){
			var trs = $(window.frames["shopsIframe"].document,$contentHtml).find("table tr");
			var shopsList = new Array();
			trs.each(function(index, element) {
				if($(element).find(".ids").prop("checked")){
					var obj = new Object();
					obj.id = $(element).find("input").val();
					obj.name = $(element).find('td[type="name"]').text();
					obj.address = $(element).find('td[type="address"]').text();
					shopsList.push(obj);
				}
            });
			callback(shopsList);
			$.Dialog.close();
		})
	}	
	/*
	 * 
	 */
	function openSelectLists(dataUrl,count,title,callback){
		if(count==1){
			dataUrl = dataUrl+'&isAjax=ajax&isRadio=1';
		}else{
			dataUrl = dataUrl+'&isAjax=1';
		}
		var $contentHtml = $('<div><div class="goods_dialog" style="padding:10px; height:530px;overflow-y:hidden;overflow-x:hidden;"><div class="mt_10"><iframe id="usersIframe" name="usersIframe" style="height:530px;width:100%; border:none" border="0" src="'+dataUrl+'"></iframe></div></div><div class="btn_bar"><a href="javascript:;" class="btn confirm_btn">确定</a>&nbsp;&nbsp;<a href="javascript:;" class="border-btn cancel_btn">取消</a></div></div>');
		$.Dialog.open(title,{width:1000,height:640},$contentHtml);
		
		$('.cancel_btn',$contentHtml).click(function(){
			$.Dialog.close();
		})
		$('.confirm_btn',$contentHtml).click(function(){
			var trs = $(window.frames["usersIframe"].document,$contentHtml).find("table tr");
			var usresList = new Array();
			trs.each(function(index, element) {
				if($(element).find(".ids").prop("checked")){
					var obj = new Object();
					obj.id = $(element).find("input").val();
					obj.nickname = $(element).find('td[type="nickname"]').text();
					obj.img = $(element).find('img')?$(element).find('img').attr('src'):"";
					obj.sex_name = $(element).find('td[type="sex_name"]').text();
					obj.group = $(element).find('td[type="group"]').text();
					usresList.push(obj);
				}
            });
			if(usresList.length>count){
				alert("只能选择"+count+"个选项");
				return;
			}
			callback(usresList);
			$.Dialog.close();
		})
	}
	
	/* 选择用户 */
	/*
	* dataUrl 请求数据的链接
	* callback(data) data:选择的用户数组
	* count 0 表示无限制
	*/
	function openSelectUsers(dataUrl,count,callback){
		if(count==1){
			dataUrl = dataUrl+'&isAjax=ajax&isRadio=1';
		}else{
			dataUrl = dataUrl+'&isAjax=1';
		}
		var $contentHtml = $('<div><div class="goods_dialog" style="padding:10px; height:530px;overflow-y:hidden;overflow-x:hidden;"><div class="mt_10"><iframe id="usersIframe" name="usersIframe" style="height:530px;width:100%; border:none" border="0" src="'+dataUrl+'"></iframe></div></div><div class="btn_bar"><a href="javascript:;" class="btn confirm_btn">确定</a>&nbsp;&nbsp;<a href="javascript:;" class="border-btn cancel_btn">取消</a></div></div>');
		$.Dialog.open("选择用户",{width:1000,height:640},$contentHtml);
		
		$('.cancel_btn',$contentHtml).click(function(){
			$.Dialog.close();
		})
		$('.confirm_btn',$contentHtml).click(function(){
			var trs = $(window.frames["usersIframe"].document,$contentHtml).find("table tr");
			var usresList = new Array();
			trs.each(function(index, element) {
				if($(element).find(".ids").prop("checked")){
					var obj = new Object();
					obj.id = $(element).find("input").val();
					obj.nickname = $(element).find('td[type="nickname"]').text();
					obj.img = $(element).find('img')?$(element).find('img').attr('src'):"";
					obj.sex_name = $(element).find('td[type="sex_name"]').text();
					obj.group = $(element).find('td[type="group"]').text();
					obj.openid = $(element).find('input[name="openid"]').val();
					usresList.push(obj);
				}
            });
			if(count>0 && usresList.length>count){
				alert("只能选择"+count+"个用户");
				return;
			}
			callback(usresList);
			$.Dialog.close();
		})
	}
	//选择单用户
	function selectSingleUser(dataUrl,name){
		$.WeiPHP.openSelectUsers(dataUrl,1,function(data){
			if(data && data.length>0){
				for(var i=0;i<data.length;i++){
					var $html = $('<div class="item" onClick="$.WeiPHP.selectSingleUser(\''+dataUrl+'\',\''+name+'\')">'+
								'<img src="'+data[i].img+'"/>'+
								'<span class="name">'+data[i].nickname+'</span>'+
								'<input type="hidden" name="'+name+'" value="'+data[i].id+'"/>'+
							'</div>');
					$('#userList').html($html);
				}
				
			}
		})
	}
	//选择单用户
	function selectMutiUser(dataUrl,count,name){
		$.WeiPHP.openSelectUsers(dataUrl,count,function(data){
			if(data && data.length>0){
				for(var i=0;i<data.length;i++){
					var $html = $('<div class="item" onClick="$.WeiPHP.selectSingleUser('+dataUrl+','+name+')">'+
								'<img src="'+data[i].img+'"/>'+
								'<input type="hidden" name="'+name+'[]" value="'+data[i].id+'"/>'+
								'<em class="del" onClick="$(this).parent().remove();">X</em>'+
								'<span class="name">'+data[i].nickname+'</span>'+
							'</div>');
					$html.insertBefore($('#userList .common_add_btn'));
				}
				
			}
		})
	}
	//banner
	//通用banner
	function banner(isAuto,delayTime){
		var screenWidth = $('.app-content').width();
		var count = $('.banner li').size();
		$('.banner ul').width(screenWidth*count);
		$('.banner ul').height(screenWidth/2);
		$('.banner').height(screenWidth/2);
		$('.banner li').width(screenWidth).height(screenWidth/2);
		$('.banner li img').width(screenWidth).height(screenWidth/2);
		$('.banner li .title').css({'width':'98%','padding-left':'2%'})
		// With options
		$('.banner li .title').each(function(index, element) {
            $(this).text($(this).text().length>15?$(this).text().substring(0,15)+" ...":$(this).text());
        });
		var flipsnap = Flipsnap('.banner ul');
		flipsnap.element.addEventListener('fstouchend', function(ev) {
			$('.identify em').eq(ev.newPoint).addClass('cur').siblings().removeClass('cur');
		}, false);
		$('.identify em').eq(0).addClass('cur')
		if(isAuto){
			var point = 1;
			setInterval(function(){
				//console.log(point);
				flipsnap.moveToPoint(point);
				$('.identify em').eq(point).addClass('cur').siblings().removeClass('cur');
				if(point+1==$('.banner li').size()){
					point=0;
				}else{
					point++;
					}
				
				},delayTime)
		}
	}
	function moneyFormat(value){
		var float = parseFloat(value);
		float = Math.ceil(float*100);
		float = float/100;
		if(Number(float) === float && float % 1 === 0){
			float = float+".00";
		}
		return float;
	}
	/* 框架形式打开提交对话框 */
	function openSubmitDialog(title,url,w,h){
		var $contentHtml = $('<div><div class="goods_dialog" style="padding:0 10px;height:'+h+'px;overflow:hidden"><div class="mt_10"><iframe id="goodsIframe" name="goodsIframe" style="height:'+h+'px;width:100%; border:none" border="0" src="'+url+'&isAjax=ajax"></iframe></div></div></div>');
		$.Dialog.open(title,{width:w,height:h+40},$contentHtml);
	}
	var WeiPHP = {
		chooseWishTemplateDialog:chooseWishTemplateDialog,
		chooseWishContentDialog:chooseWishContentDialog,
		openSelectAppMsg:openSelectAppMsg,
		initCopyBtn:initCopyBtn,
		uploadImgDialog:uploadImgDialog,
		openSelectGoods:openSelectGoods,
		openSelectShops:openSelectShops,
		openSelectUsers:openSelectUsers,
		initBanner:banner,
		moneyFormat:moneyFormat,
		selectSingleUser:selectSingleUser,
		selectMutiUser:selectMutiUser,
		openSubmitDialog:openSubmitDialog,
		openSelectLists:openSelectLists
		
	}
	$.extend({WeiPHP:WeiPHP});
})();
/* base */
Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};
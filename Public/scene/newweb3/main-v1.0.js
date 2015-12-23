$(function(){
	'use strict';
	//列表二维码
	$(".show-img").hover(function(){
		$(this).next().removeClass("f-hide");
	},function(){
		$(this).next().addClass("f-hide");
	});
	$(".show-ewm").hover(function(){
		$(this).removeClass("f-hide");
	},function(){
		$(this).addClass("f-hide");
	});
	$(".icon-ewm").hover(function(){
		$(this).prevAll(".show-ewm").removeClass("f-hide");
	},function(){
		$(this).prevAll(".show-ewm").addClass("f-hide");
	});
	
	
	//关闭窗口
	var $btnclose = $(".popbox").children("h1").children("a");
	$btnclose.click(function(){
		$(this).parents(".popbox").addClass("f-hide")
		       .parent().addClass("f-hide")
			   .parent().css('overflow','auto');
	});
	//header弹出登录注册
	var $hdlogin = $("#login").find(".btn-login");
	var $hdreg = $("#login").find(".btn-reg");
	$hdlogin.click(function(){
		$(this).parents("body").css('overflow','hidden')
		       .children(".maskpop").removeClass("f-hide")
			   .children(".loginbox").removeClass("f-hide")
			   .siblings().addClass("f-hide");
	});
	$hdreg.click(function(){
		$(this).parents("body").css('overflow','hidden')
		       .children(".maskpop").removeClass("f-hide")
			   .children(".regbox").removeClass("f-hide")
			   .siblings().addClass("f-hide");
		
		$('#authImage').attr("src","/auth/imageauth?"+Math.random());
	});
	//登录
	$(".plogin").click(function(){
		$(this).parents(".popbox").addClass("f-hide")
		       .siblings(".loginbox").removeClass("f-hide");
	});
	//注册
	$(".preg").click(function(){
		$(this).parents(".popbox").addClass("f-hide")
		       .siblings(".regbox").removeClass("f-hide");
		$('#authImage').attr("src","/auth/imageauth?"+Math.random());
	});
	//找回密码
	$(".pback").click(function(){
		$(this).parents(".popbox").addClass("f-hide")
		       .siblings(".backbox").removeClass("f-hide");
	});
	//设计师认证信息提交
	$(".design_type").click(function(){
		var num = $(this).children().val();
		if( num == 1) {
			$(this).parent()
			       .next().removeClass("f-hide")
				   .next().addClass("f-hide");
		}else {
			$(this).parent()
			       .next().addClass("f-hide")
				   .next().removeClass("f-hide");
		}
	});
	//返回顶部
	$(".backtop").click(function() {
        pageScroll()
    })
	$(window).scroll(function() {
        (document.documentElement.scrollTop || document.body.scrollTop) > 0 ? $(".backtop").show() : $(".backtop").hide()
    });
});

function pageScroll() {
    $(".backtop").css("background-position-x", "-28px"), window.scrollBy(0, -20), scrolldelay = setTimeout("pageScroll()", 2), 0 == document.documentElement.scrollTop && 0 == document.body.scrollTop && ($(".backtop").css("background-position-x", "0"), clearTimeout(scrolldelay));
}
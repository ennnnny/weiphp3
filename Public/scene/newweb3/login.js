var dialogBox = null;

$(function(){
	
	/* $.ajax(
		 {
		 url:'/web/user/ajax_login',
		 success:function(html){
			
			 $('#ajax_login').html(html);
			
				//header弹出登录注册
				var $hdlogin = $(".header").find(".btn-primary");
				var $hdreg = $(".header").find(".btn-success");
				$hdlogin.click(function(){
					
					$(this).parents(".header")
					       .nextAll(".maskpop").removeClass("f-hide")
						   .children(".loginbox").removeClass("f-hide")
						   .siblings().addClass("f-hide");
				});
				$hdreg.click(function(){
					$(this).parents(".header")
					       .nextAll(".maskpop").removeClass("f-hide")
						   .children(".regbox").removeClass("f-hide")
						   .siblings().addClass("f-hide");
					$('#authImage').attr("src","/auth/imageauth?"+Math.random());
				});
				
         },
        error:function(xml,textStatus,error){
        	 
        	}
        });
	 */
	 $.ajax(
			 {
			 url:'/web/user/ajax_login',
			 success:function(html){
				
				 $('#ajax_login').html(html);
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
					
	         },
	        error:function(xml,textStatus,error){
	        	 
	        	}
	        });
	
	
	$('#authImage').on('click', function(){
		$('#authImage').attr("src","/auth/imageauth?"+Math.random());
	});
	
	function getQueryString(name) {
	    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	    var r = window.location.search.substr(1).match(reg);
	    if (r != null) return unescape(r[2]); return "";
	 }
	
	$('#zuiku_login_btn').on('click', function(){
		var username = $('#loginUserName').find('input[name="username"]').val();
		var password = $('#loginPassWord').find('input[name="password"]').val();

		var that_name = $('#loginUserName_error');
		var that_pass = $('#loginPassWord_error');
		var that = $('.form-signin').find('.u-error');

		if(username != ''){
			that_name.addClass('f-hide');
		}
		if(  password !=''){
			that_pass.addClass('f-hide');
		}
		
		if(username == ''){
			that_name.removeClass('f-hide');
			return false;
		} 
		
		if(password == ''){
			that_pass.removeClass('f-hide');
			return false;
		}
		
		$.post('/web/user/login', {username:username, password:password, update:'login'}, function(result){
			if(result.success){
				url = $('#url').val();
				if(url==''){
					url='/my/app/list';
				}else if(url.indexOf('/my/item/')!=-1){
					url='/my/app/list';
				}else if(url.indexOf('/my/app/set')!=-1){
					url='/my/app/list';
				}else if(url.indexOf('/my/screen/add')!=-1){
					url='/my/app/list';
				}else if(url.indexOf('/my/screen/manage')!=-1){
					url='/my/app/list';
				}else if(url.indexOf('/my/upload')!=-1){
					url='/my/app/list';
				}else if(url.indexOf('/my/app/share_list')!=-1){
					url='/my/app/list';
				}else if(url.indexOf('/my/media/')!=-1){
					url='/my/app/list';
				}else if(url.indexOf('/my/media/pic_list')!=-1){
					url='/my/app/list';
				}
				
				
				
				location.href = url;
			}else{
				that.find('.f-tc').html(result.message);
				that.removeClass('f-hide');
			}
		}, 'json');
	});
	
	/**
	 * 测试某个字符是属于哪一类
	 */
	function CharMode(iN) {
	  if (iN >= 48 && iN <= 57) //数字 
	      return 1;
	  if (iN >= 65 && iN <= 90) //大写字母 
	      return 2;
	  if (iN >= 97 && iN <= 122) //小写 
	      return 4;
	  else
	      return 8; //特殊字符 
	}

	/**
	 * 计算密码一共有多少种模式 
	 */
	function bitTotal(num) {
	  modes = 0;
	  for (i = 0; i < 4; i++) {
	      if (num & 1) modes++;
	      num >>>= 1;
	  }
	  return modes;
	}

	/**
	 * 返回密码的强度级别 
	 */
	function checkStrong(sPW) {
	  if (sPW.length <= 4)
	      return 0; //密码太短 
	  Modes = 0;
	  for (i = 0; i < sPW.length; i++) {
	      //测试每一个字符的类别并统计一共有多少种模式. 
	      Modes |= CharMode(sPW.charCodeAt(i));
	  }
	  return bitTotal(Modes);
	}
	
	$('input[name=password_remove]').on('keyup', function(){
		var pwd = $.trim($(this).val());
		if (pwd != '') {
			var S_level = checkStrong(pwd);
			switch (S_level) {
				case 2:
					S_level = 'middle';
					break;
				case 3:
				case 4:
					S_level = 'high';
					break;
				default:
					S_level = 'low';
			}
			$(this).parents('li').find('.u-form-input').removeClass('low middle high').addClass(S_level).show();
		} else {
			$(this).parents('li').find('.u-form-input').removeClass('low middle high').hide()
		}
	});
	
 
	$('.regbox .required').on('focus blur', function(e){
		checkInput($(this), e.type);
	});
	
	/**
	 * 注册-禁止复制粘贴
	 */
	$('input[type="password"]').on('copy paste cut', function(){
		return false;
	});
	
	/**
	 * ajax提交表单及验证
	 */
	$('#zuiku_reg_btn').on('click', function(){
		var that = $('.regbox');
		var email = that.find('input[name="email"]').val();
		var password = that.find('input[name="password"]').val();
		var check_password = that.find('input[name="check_password"]').val();		
		var validate_code = that.find('input[name="validate_code"]').val();
		var valid = true;
		$(".required").each(function(i, e){
			if (!checkInput($(e), 'focus blur')) {
				valid = false;
			}
		});
		 
		if (!valid) {
			$('.uback:visible:first').parents('td').find('input').focus();
			return;
		} else { 
			
			// 提交数据
			$.ajax({
				url: '/web/user/reg',
				type: 'post',
				data: {
					'email'		: email,
					'password'	: password,
					'validate_code'	: validate_code
				},
				dataType: 'json',
				success: function(result) {
					if(result.success){
						$('#tip').html('注册成功。');
						top.location.href = "/my/app/list";

					}else{
						$('#tip').html(result.message);
					}
				},
				error: function() {
					$('#tip').html('注册失败，请重试');
				}
			});
			
			
		}
	});
	
	
	/**
	 * ajax提交表单 密码找回，add david
	 */
	$('#zuiku_pback_btn').on('click', function(){
		var email = $('#zuiku_pback').val();
		// 提交数据
			$.ajax({
				url: '/web/user/pback',
				type: 'post',
				data: {
					'email'		: email,
				},
				dataType: 'json',
				success: function(result) {
					if(result.success){
						$('#pback').html(result.message);
						//top.location.href = "/my/app/list";
						$('#zuiku_pback').val('');

					}else{
						$('#pback').html(result.message);
						$('#zuiku_pback').val('');
					}
				},
				error: function() {
					$('#pback').html(result.message);
				}
			});
	});
	
	/**
	 * ajax提交表单 密码找回，add david
	 */
	$('#modifyPass').on('click', function(){
		alert('');
		msg='';
		newpassword =$('#newpassword').val();
		repassword =$('#repassword').val();
		if( newpassword != repassword){
			msg+='两次输入的密码不一样。\r\n';
		}
			
		if(msg !=''){
			alert(msg);
			return ;	
		}
		// 提交数据
			$.ajax({
				url: '/web/user/updateepass',
				type: 'post',
				data: {
					'newpassword'		: newpassword,
				},
				dataType: 'json',
				success: function(result) {
					if(result.success){
						$('#message').html(result.message);
						top.location.href = "/web/showmember/view";

					}else{
						$('#message').html(result.message);
					}
				},
				error: function() {
					$('#message').html(result.message);
				}
			});
	});

	
	
	function checkInput(obj, eType) {
		var val = $.trim(obj.val());
		var td = obj.parent();
		var p = td.find('.uback');
		var warn = td.find('.warn');
		var valid = true;
		if (val == '' || val == 0) {
			if(eType == 'blur'){
				warn.addClass('f-hide');					
			}else if(eType == 'focus'){
				warn.removeClass('f-hide');
				p.addClass('f-hide');
			}
			valid = false;
		} else if (obj.hasClass('email')) {
			var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/i;
			if (!reg.test(val)) {
				p.html('邮箱格式不正确');
				valid = false;
			}else{
				 
			}				
		}else if (obj.hasClass('password')) {
			var reg=/^[^\f\n\r\t\v\+]{6,14}$/;
			if (!reg.test(val)) {
				p.html('密码格式不正确');
				valid = false;
			}
		}else if (obj.hasClass('repassword')) {
			var reg=/^[^\f\n\r\t\v\+]{6,14}$/;
			var password = $('.password').val();
			if(val == password){
				if (!reg.test(val)) {
					p.html('密码格式不正确');
					valid = false;
				}				
			}else{
				p.html('两次密码输入不一致');
				valid = false;				
			}
		} 
		if(eType == 'focus'){
			valid = true;
		}
		if (valid == false) {
			obj.addClass('f-failInp');
			if(eType != 'focus'){
				p.removeClass('f-hide');
				warn.addClass('f-hide');				
			}else{
				p.addClass('f-hide');
				warn.removeClass('f-hide');				
			}
			td.find('.finish').addClass('f-hide');
		} else {
			obj.removeClass('f-failInp');
			if(eType == 'blur'){
				warn.addClass('f-hide');
				td.find('.finish').removeClass('f-hide');
			}else if(eType == 'focus'){
				warn.removeClass('f-hide');				
			}
			p.addClass('f-hide');
		}
		return valid;
	}
	//alert(top.location+"-"+document.location);
	
	str = top.location+'';
	if (str.indexOf('m.zuikuapp.com') == -1 && str.indexOf('zuiku.com') == -1
			&& str.indexOf('wx.niaochao88.com') == -1
			&& str.indexOf('lightapponline.com') == -1) {
		//top.location = 'http://www.zuiku.com';
	}
	
	
	if(top.location != document.location){
		
		 if(url.indexOf('wx.niaochao88.com') == -1 ){
				top.location = document.location;
		 } 
	}
	
	url=getQueryString('url');
	if(url!=''){
		$('#url').val(url);
		$('.maskpop').removeClass("f-hide");
		$('#loginbox_div').removeClass("f-hide");
	}
});	





;
function HideDiv() {
            $("#dail2").hide();
            $("#lean_overlay").fadeOut(200);
}
function ShowDiv() {
           
            /*加入遮罩层到Body*/
            var _3 = $("<div id='lean_overlay'>&nbsp;</div>");
            $("body").append(_3);             
            /*最上层DIV*/
            $("#dail2").css(
            {
                     "display": "block",
                     "position": "fixed",
                     "opacity": 1,
                     "z-index": 4  
            });
            /*遮罩层 透明到0.1*/
            $("#lean_overlay").fadeTo(200, 0.6);            
            //$("#lean_overlay").click(HideDiv);
            $("#lean_overlay").show();

}
$(function(){					
	//if(posturl !=""){ ShowDiv();}
	$("#btn_submit").bind("click",function(){						
		var truename = $('#truename').val();
		var mobile = $('#mobile').val();
		if(truename!=undefined && truename==""){
			$.Dialog.fail("请填写姓名！");//成功调用 提示一秒后自动关闭
			return false;
		}
		if(mobile!=undefined && mobile==""){
			$.Dialog.fail("请填写联系电话！");//成功调用 提示一秒后自动关闭
			return false;
		}
	
		$.ajax({
			type: 'POST',
			url: posturl,
			data:{truename:truename,mobile:mobile},
			dataType: 'json',
			cache: false,
			error: function() {},
			success: function(json) {				
				location.reload();
			}
		});
	});
});
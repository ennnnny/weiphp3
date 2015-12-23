$('#shape').click(function(event){
	if(event.target.tagName == "SPAN"){
		$(event.target).addClass('on');
		$(this).unbind("click");
		$.Dialog.loading('加载中...');
		$.get(joinUrl,function(json){
				if(json){
					if(json.status == 0){
						$.Dialog.confirm('提示',json.msg,function(){
							window.location.reload();
						});
						
					}else{
						$.Dialog.confirm('中奖啦',json.msg,"",json.jump_url);
					}
				}else{
					$.Dialog.fail('程序罢工');
				}
		});
	}
})
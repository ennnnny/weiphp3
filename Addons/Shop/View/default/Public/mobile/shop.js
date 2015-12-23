// JavaScript shop by jacy
$(function(){
	//购物添加数量
	$('.buy_count .add').click(function(){
		var val = parseInt($(this).siblings('input').val());
		$(this).siblings('input').val(val+1);
		updatePriceAndCount();
	})
	$('.buy_count .reduce').click(function(){
		var val = parseInt($(this).siblings('input').val());
		if(val>1){
			$(this).siblings('input').val(val-1);
			updatePriceAndCount();
		}
	})
	$('.buy_count input[type="text"]').keyup(function(){
		if($(this).val()==0){
			$.Dialog.fail("购物数量不能小于1件");
			$(this).val(1);
		}else{
			updatePriceAndCount();
		}
	})
	$('input[name="goods_ids[]"]').change(function(){
		updatePriceAndCount();
	})
	
	//全选的实现
	$(".check_all").click(function(){
		if($('input[name="checkAll"]').prop('checked')==true){
		$('input[name="goods_ids[]"]').prop('checked',true);
		
		}else{
			$('input[name="goods_ids[]"]').prop('checked',false);
		}
		updatePriceAndCount();
	});
	
})
//更新购物车价格和数量
function updatePriceAndCount(){
	var totalCount = 0;
	var totalPrice = 0;
	if($('input[name="goods_ids[]"]:checked').size()==$('input[name="goods_ids[]"]').size()){
		$('input[name="checkAll"]').prop('checked',true);
	}else{
		$('input[name="checkAll"]').prop('checked',false);
	}
	$('input[name="goods_ids[]"]:checked').each(function(index, element) {
		var itemElem = $(this).parents('li');
		var price = parseFloat(itemElem.find('.singlePrice').text());
		var count = parseInt(itemElem.find('input[rel="buyCount"]').val());
		totalCount += count;
		totalPrice += count*price
	});
	$('#totalCount').text(totalCount);
	$('#totalPrice').text(totalPrice);
}
//提交检查
function checkCartSubmit(){
	if($('input[name="goods_ids[]"]:checked').size()==0){
		$.Dialog.fail("请先选择要购买的商品");
		return false;
	}
}
function confirmGetGoods(url){
	$.Dialog.confirmBox('温馨提示','确认已收货？',{rightCallback:function(){
		$.Dialog.loading();
		$.post(url,function(res){
			 setTimeout(function(){
				 location.reload();	
			},1500);			
	    });
	}});
}
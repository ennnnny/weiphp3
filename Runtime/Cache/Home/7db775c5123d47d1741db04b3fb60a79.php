<?php if (!defined('THINK_PATH')) exit();?><p class="normal_tips" style="margin:0"><b class="fa fa-info-circle"></b> 常用配置地址：<br/>
&lt;a href=&quot;[follow]&quot;&gt;绑定帐号&lt;/a&gt;<br/>
&lt;a href=&quot;[website]&quot;&gt;微首页&lt;/a&gt;<br/>
&lt;a href=&quot;http://xxxx?token=[token]&quot;&gt;Token&lt;/a&gt;<br/>
&lt;a href=&quot;http://xxxx?opneid=[openid]&quot;&gt;OpenId&lt;/a&gt;
</p>

<div class="form-item cf">
  <label class="item-label"> 类型: </label>
  <div class="controls">
    	<div class="check-item">
      <input type="radio" name="config[type]" value="1" class="regular-radio" id="config[type]_1" onClick="changeOption()">
      <label for="config[type]_1"></label>文本
      </div>
<!--    <label class="radio">
      <input type="radio" name="config[type]" value="2" onClick="changeOption()">
      图片 </label>-->
    <div class="check-item">
      <input type="radio" name="config[type]" value="3" class="regular-radio" id="config[type]_3" onClick="changeOption()">
      <label for="config[type]_3"></label>图文
      </div>
  </div>
</div>
<!-- <div class="form-item cf show show3"> -->
<!--   <label class="item-label"> 标题: </label> -->
<!--   <div class="controls"> -->
<!--     <input type="text" name="config[title]" class="text input-large" value="<?php echo ($data["config"]["title"]["value"]); ?>"> -->
<!--   </div> -->
<!-- </div> -->
<div class="form-item cf show show1">
  <label class="item-label"> 内容: </label>
  <div class="controls">
    <label class="textarea input-large">
      <textarea name="config[description]"><?php echo ($data["config"]["description"]["value"]); ?></textarea>
    </label>
  </div>
  
</div>

<!-- <div class="form-item cf show show3"> -->
<!--   <label class="item-label"> 图片: <span class="check-tips">图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200</span> </label> -->
  
<!--                           <div class="controls uploadrow2" title="点击修改图片" rel="pic_url"> -->
<!--                             <input type="file" id="upload_picture_pic_url"> -->
                            
<!--                             <input type="hidden" name="config[pic_url]" id="cover_id_pic_url" value="<?php echo ($data["config"]["pic_url"]["value"]); ?>"/> -->
<!--                             <div class="upload-img-box"> -->
<!--                               <?php if(!empty($data["config"]["pic_url"]["value"])): ?>-->
<!--                                 <div class="upload-pre-item2"><img width="100" height="100" src="<?php echo (get_cover_url($data["config"]["pic_url"]["value"])); ?>"/></div> -->
<!--                                 <em class="edit_img_icon">&nbsp;</em> -->
<!--<?php endif; ?> -->
<!--                             </div> -->
<!--                           </div> -->

<!-- </div> -->
<!-- <div class="form-item cf show show3"> -->
<!--   <label class="item-label"> 链接: <span class="check-tips">点击图文消息跳转链接</span> </label> -->
<!--   <div class="controls"> -->
<!--     <input type="text" name="config[url]" class="text input-large" value="<?php echo ($data["config"]["url"]["value"]); ?>"> -->
<!--   </div> -->
<!-- </div> -->

<div  class="form-item cf show show3 appmsg_area" id="appmsg_area" style="margin:20px 0;">
                                	<input type="hidden" name="config[appmsg_id]" value="<?php echo ($data["config"]["appmsg_id"]["value"]); ?>"/>
                                    <a class="select_appmsg" href="javascript:;" onClick="$.WeiPHP.openSelectAppMsg('<?php echo U('/Home/Material/material_data');?>',selectAppMsgCallback)">选择图文</a>
                                    <div class="appmsg_wrap" style="height:auto;"></div>
                                    <a class="delete" href="javascript:;" style="left: 310px;">删除</a>
</div>
<script type="text/javascript">
$(function(){
	 initUploadImg();
	
	 })
function changeOption(){
	$(".show").each(function(){
		$(this).hide();
		
	});
	var group_id=$("input[name='config[appmsg_id]']").val();
	$.post("<?php echo U('Home/Material/get_news_by_group_id');?>",{'group_id':group_id},function(vo){
		console.log(vo);
		var html_str='';
		if(vo.length==1){
			html_str='<div class="appmsg_item"><h6>'+vo[0]['title']+'</h6><div class="main_img"><img src="'+vo[0]['img_url']+'"/></div><p class="desc">'+vo[0]['intro']+'</p></div><div class="hover_area"></div>';
		}else{
			for(var i=0;i<vo.length;i++){
				if(vo[i]['id']==group_id){
					html_str='<div class="appmsg_item"><div class="main_img"><img src="'+vo[i]['img_url']+'"/><h6>'+vo[i]['title']+'</h6></div><p class="desc">'+vo[i]['intro']+'</p></div>';
				}else{
					html_str+=' <div class="appmsg_sub_item"><p class="title">'+vo[i]['title']+'</p><div class="main_img"><img src="'+vo[i]['img_url']+'"/></div></div>';
				}
			}
			html_str+='<div class="hover_area"></div>';
		}
		$('.appmsg_wrap').html(html_str).show();
		$('.select_appmsg').hide();
		$('.appmsg_area .delete').show();
	})
// 	var html_str=' <div class="appmsg_item"><p class="title"><?php echo (time_format($vo["cTime"])); ?></p>
//                 <div class="main_img">
//                     <img src="<?php echo (get_cover_url($vo["cover_id"])); ?>"/>
//                     <h6><?php echo ($vo["title"]); ?></h6>
//                 </div>
//                 <p class="desc"><?php echo ($vo["intro"]); ?></p>
//             </div>
//             <?php if(is_array($vo["child"])): $i = 0; $__LIST__ = $vo["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i;?>//             <div class="appmsg_sub_item">
//                 <p class="title"><?php echo ($vv["title"]); ?></p>
//                 <div class="main_img">
//                     <img src="<?php echo (get_cover_url($vv["cover_id"])); ?>"/>
//                 </div>
//             </div>
//<?php endforeach; endif; else: echo "" ;endif; ?>
//             <div class="hover_area"></div>'
            
    
	var val = $("input[name='config[type]']:checked").val();
	$('.show'+val).each(function(){
		$(this).show();
	});
}
$(function(){
	var type = "<?php echo (intval($data["config"]["type"]["value"])); ?>";
	if(type=="0")
	    type = 3;
	$("input[name='config[type]'][value="+type+"]").attr("checked",true); 
	changeOption();
	$('.appmsg_area .delete').click(function(){
		$('.appmsg_wrap').html('').hide();
		$('.select_appmsg').show();
		$('.appmsg_area .delete').hide();
		$('input[name="config[appmsg_id]"]').val(0);
	})
})
function selectAppMsgCallback(_this){
	$('.appmsg_wrap').html($(_this).html()).show();
	$('.select_appmsg').hide();
	$('.appmsg_area .delete').show();
	$('input[name="config[appmsg_id]"]').val($(_this).data('group_id'));
	$.Dialog.close();
}

</script>
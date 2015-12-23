
UE.registerUI('dialog',function(editor,uiName){

    //创建dialog
    var dialog = new UE.ui.Dialog({
        //指定弹出层中页面的路径，请在编辑器初始化后赋值
        iframeUrl:editor.styleUrl,
        //需要指定当前的编辑器实例
        editor:editor,
        //指定dialog的名字
        name:uiName,
        //dialog的标题
        title:"图文样式模板",
        //指定dialog的外围样式
        cssRules:"width:600px;height:500px;",
        //如果给出了buttons就代表dialog有确定和取消
		/*
        buttons:[
		    {
                className:'edui-okbutton',
                label:'确定',
                onclick:function () {
                    dialog.close(true);
                }
            },
            {
                className:'edui-cancelbutton',
                label:'取消',
                onclick:function () {
                    dialog.close(false);
                }
            }
        ]
		*/});

    //参考addCustomizeButton.js
    var btn = new UE.ui.Button({
        name:'图文样式',
        title:'图文样式',
        //需要添加的额外样式，指定icon图标，这里默认使用一个重复的icon
        cssRules :'background-position: -750px -77px;',
        onclick:function () {
            //渲染dialog
            dialog.render();
            dialog.open();
        }
    });
    return btn;
}/*index 指定添加到工具栏上的那个位置，默认时追加到最后,editorId 指定这个UI是那个编辑器实例上的，默认是页面上所有的编辑器都会添加这个按钮*/);
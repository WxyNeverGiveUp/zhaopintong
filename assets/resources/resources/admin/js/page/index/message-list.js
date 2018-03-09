/*-----------------------------------------------------------------------------
* @Description:     index部分-消息列表页相关js(message-list.js)
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.10.31
* ==NOTES:=============================================
* v1.0.0(2014.10.31):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/index/message-list',function(S,core,checkAll){
	PW.namespace('page.messageList');
	PW.page.messageList = function(param){
		new core(param);
		new checkAll(param);
	};
},{
	requires:['messageList/core','module/selectAll']
});
/*------------------------------------消息列表----------------------------------*/
KISSY.add('messageList/core',function(S){
	var
		$ = S.all, on = S.Event.on, delegate = S.Event.delegate,
		Pagination = PW.mod.Pagination,
		MessageHandler = PW.module.indexer.message,
		el = {
			messageList:"#J_template",//指向消息列表
			delBtn:".J_delBatch",//指向删除按钮
			handleBtn:'.J_handleBtn',//指向处理按钮
			sendBtn:'#J_sendBtn'//指向群发按钮
		};

	function core(param){
		this.opts = param;
		this.init();
		this._paging();
	}

	S.augment(core,{
		init:function(){
			this._addEventListener();
		},
		_paging:function(){
			var
				that = this,
				opts = that.opts;
			Pagination.client(opts);
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击删除按钮*/
			on(el.delBtn,'click',function(evt){
				MessageHandler.del(el.messageList);
			});
			/*点击处理按钮*/
			delegate(document,'click',el.handleBtn,function(evt){
				MessageHandler.handle(evt.currentTarget);
			});
			/*点击群发按钮*/
			on(el.sendBtn,'click',function(evt){
				MessageHandler.send();
			});
		}
	});

	return core;
},{
	requires:['mod/pagination','module/index/message']
});
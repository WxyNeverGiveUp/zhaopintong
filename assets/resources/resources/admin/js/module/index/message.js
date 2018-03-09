/*-----------------------------------------------------------------------------
* @Description:     index部分-消息列表页相关js(message.js)
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.10.31
* ==NOTES:=============================================
* v1.0.0(2014.9.15):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/index/message',function(S,del,handle,send){
	PW.namespace('module.indexer.message');
	PW.module.indexer.message = {
		del:function(param){
			new del(param);
		},
		handle:function(param){
			new handle(param);
		},
		send:function(param){
			new send(param);
		}
	};
},{
	requires:['index/message/del','index/message/handle','index/message/send']
});
/*--------------------------------删除信息-------------------------------*/
KISSY.add('index/message/del',function(S){
	var
		$ = S.all, on = S.Event.on,
		el = {

		},
		Dialog = PW.mod.Dialog,
		MessageIO = PW.io.indexer.message,
		DEL_TIP = '确定删除选中信息？';

	function del(param){
		this.opts = param;
		this.init();
	}

	S.augment(del,{
		init:function(){
			this._showDialog();
		},
		/*显示提示信息*/
		_showDialog:function(){
			var
				that = this,
				opts = that.opts;
			Dialog.confirm(
				DEL_TIP,
				function(e,me){
					that._ajaxDelMessage();
				},
				function(e,me){

				},
				{
					position:'fixed',
					width:'400',
					maskColor:'#343434'
				}
			);
		},
		/*删除信息*/
		_ajaxDelMessage:function(){
			var
				that = this,
				checked = that._getCheckedMsg();
			if(checked.ids.length == 0){
				Dialog.alert('当前未选中任何消息！');
			}else{
				MessageIO.del({ids:checked.ids},function(rs){
					if(rs){
						Dialog.alert('删除消息成功！');
						that._delMsg(checked.trs);
					}else{
						Dialog.alert('删除消息失败！');
					}
				});
			}
		},
		/*获取选中的信息*/
		_getCheckedMsg:function(){
			var
				that = this,
				opts = that.opts,
				check = $('input:[type="checkbox"]',opts),
				checked = [],
				trs = [];
			S.each(check,function(i,o){
				if($(i).attr('checked') == 'checked'){
					checked.push($(i).attr('data-id'));
					trs.push($(i).parent().parent());
				}
			});

			return {ids:checked,trs:trs};
		},
		/*删除消息*/
		_delMsg:function(trs){
			S.each(trs,function(i,o){
				$(i).remove();
			});
		}
	});

	return del;
},{
	requires:['mod/dialog','io/index/message']
});
/*-----------------------------------------处理信息---------------------------------------*/
KISSY.add('index/message/handle',function(S){
	var
		$ = S.all,
		MessageIO = PW.io.indexer.message,
		el = {
			handleTimeHolder:'.J_handleTimeHolder',
			stateHolder:'.J_stateHolder'//指向状态
		};

	function handle(param){
		this.opts = param;
		this.init();
	}

	S.augment(handle,{
		init:function(){
			this._ajaxHandle();
		},
		/*ajax处理信息*/
		_ajaxHandle:function(){
			var
				that = this,
				opts = that.opts,
				tr = $(opts).parent().parent(),
				id = $('input:[type="checkbox"]',tr).attr('data-id');
			MessageIO.handle({id:id},function(rs){
				if(rs){
					that._changeState(tr);
				}
			});
		},
		/*改变信息状态*/
		_changeState:function(tr){
			var
				that = this,
				opts = that.opts,
				stateHolder = $(el.stateHolder,tr),
				handleTimeHolder = $(el.handleTimeHolder,tr),
				now = new Date(),
				year = now.getFullYear(),
				month = now.getMonth()+1,
				day = now.getDate(),
				td = $(opts).parent();
			$(stateHolder).text('已处理');
			$(opts).remove();
			$(td).text('无');
			$(handleTimeHolder).text(year+'-'+month+'-'+day);
		}
	});

	return handle;
},{
	requires:['io/index/message']
});
/*--------------------------------------群发信息----------------------------------------*/
KISSY.add('index/message/send',function(S,Juicer){
	var
		$ = S.all, delegate = S.Event.delegate, DOM = S.DOM, 
		MessageIO = PW.io.indexer.message,
		Dialog = PW.mod.Dialog,
		el = {
			sendForm:'.J_sendForm',
			allBtn:'#J_allBtn',
			checkBtn:'.J_checkBtn'
		},
		DIALOG_HTML = '<form class="clearfix J_sendForm">' 
							+'<div class="control-area control-area-long">'
								+'<label>群发人员：</label>'
								+'<div class="checkbox checkbox-short">'
									+'<input id="J_allBtn" type="checkbox" value="0" name="roleList" />全部'
								+'</div>'
								+'{@each data as d}'
									+'<div class="checkbox checkbox-short">'
										+'<input class="J_checkBtn" type="checkbox" value="&{d.id}" name="roleList" />&{d.name}'
									+'</div>'
								+'{@/each}'
							+'</div>'
							+'<div class="control-area control-area-long">'
								+'<label>内容：</label>'
								+'<textarea class="textTheme" name="message"></textarea>'
							+'</div>'
						+'</form>';
	function send(){
		this.init();
	}

	S.augment(send,{
		init:function(){
			this._ajaxGetRole();
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击全选按钮*/
			delegate(document,'click',el.allBtn,function(evt){
				that._selectAll(evt.currentTarget);
			});
			delegate(document,'click',el.checkBtn,function(evt){
				that._cancelCheck(evt.currentTarget);
			});
		},
		/*使用ajax获取角色列表*/
		_ajaxGetRole:function(){
			var
				that = this;
			MessageIO.get({},function(rs,data,errMsg){
				if(rs){
					that._showDialog(data);
				}else{
					S.log(errMsg);
				}
			});
		},
		/*显示对话框*/
		_showDialog:function(data){
			var
				that = this;
			that.dialogContent = that._createDialogHtml(data);
			that.dialogId = Dialog.open({
				title:'群发',
				width:930,
				content:that.dialogContent,
				footer:{
					btns:[
						{
							bid:1,
							text:'确定',
							clickHandler:function(e,me){
								that._ajaxSend(me);
							}
						},{
							bid:2,
							text:'取消',
							clickHandler:function(e,me){
								me.close();
							}
						}
					]
				}
			});
		},
		/*创建对话框内容*/
		_createDialogHtml:function(data){
			var
				html = Juicer(DIALOG_HTML,{data:data});
			return html;
		},
		/*使用ajax发送*/
		_ajaxSend:function(dialog){
			var
				info = DOM.serialize(el.sendForm);
			MessageIO.send(info,function(rs,data,errMsg){
				if(rs){
					dialog.close();
					window.location.reload();
				}else{
					Dialog.alert(errMsg);
				}
			});
		},
		/*全选*/
		_selectAll:function(target){
			var
				that = this,
				parent = $(target).parent().parent(),
				input = $(el.checkBtn,parent),
				checked = $(target).attr('checked');
			if(checked == 'checked'){
				that._check(input);
			}else{
				that._noCheck(input);
			}
			
		},
		_check:function(input){
			S.each(input,function(i,o){
				$(i).attr('checked','checked');
			});
		},
		_noCheck:function(input){
			S.each(input,function(i,o){
				$(i).removeAttr('checked');
			});
		},
		_cancelCheck:function(target){
			var
				that = this,
				checked = $(target).attr('checked'),
				checkedAll = $(el.allBtn).attr('checked');
			if(checked != 'checked' && checkedAll == 'checked'){
				$(el.allBtn).removeAttr('checked');
			}
		}
	});

	return send;
},{
	requires:['mod/juicer','io/index/message','mod/dialog']
});
/*-----------------------------------------------------------------------------
* @Description:     宴请部分相关js
* @Version:         1.0.0
* @author:          daiql(1649500603@qq.com)
* @date             2014.9.22
* ==NOTES:=============================================
* v1.0.0(2014.9.22):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/banquet/select-checkbox',function(S,select){
	PW.namespace('module.banquet.selectCheckbox');
	PW.module.banquet.selectCheckbox = function(param){
		new select(param);
	};
},{
	requires:['selectCheckbox/select']
});
/*--------------------------------选择参加人员-------------------------------*/
KISSY.add('selectCheckbox/select',function(S,Juicer){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Dialog = PW.mod.Dialog,
		Defender = PW.mod.Defender,
		SelectIO = PW.io.banquet.select,
		el = {
			selectCheckForm:'.J_selectCheckForm'//指向选择人员的form
		},
		DIALOG_CHECK_HTML = '<form class="clearfix J_selectCheckForm">' 
							+'<div class="control-area control-area-long">'
								+'<label>供选人员</label>'
								+'{@each data as d}'
									+'<div class="checkbox checkbox-short">'
										+'<input type="checkbox" id="&{d.id}" name="attendeeName" />&{d.name}'
									+'</div>'
								+'{@/each}'
							+'</div>'
						+'</form>',
		DIALOG_RADIO_HTML = '<form class="clearfix J_selectCheckForm">' 
							+'<div class="control-area control-area-long">'
								+'<label>供选人员</label>'
								+'{@each data as d}'
									+'<div class="checkbox checkbox-short">'
										+'<input type="radio" id="&{d.id}" name="contactName" />&{d.name}'
									+'</div>'
								+'{@/each}'
							+'</div>'
						+'</form>';				;

	function select(param){
		this.opts = param;
		this.init();
	}

	S.augment(select,{
		init:function(){
			this._ajaxSelectPerson();
		},
		/*ajax选择人员信息*/
		_ajaxSelectPerson:function(){
			var
				that = this,
				opts = that.opts,
				info = {},
				flag = opts.attr;
			info = that._serialize();
			if(flag == 1){
				SelectIO.attendee(info,function(rs,data,errorMes){
					if(rs){
						that._showDialog(data,flag);
					}else{
						Dialog.error(errorMes);
					}
				});
			}else if(flag == 2){
				SelectIO.contact(info,function(rs,data,errorMes){
					if(rs){
						that._showDialog(data,flag);
					}else{
						Dialog.error(errorMes);
					}
				});
			}
			
		},

		/*序列化选择人员信息的表单*/
		_serialize:function(){
			var
				info = {};
			info = DOM.serialize('form');
			return info;
		},
		/*显示对话框*/
		_showDialog:function(data,flag){
			var
				that = this,
				opts = that.opts;
			that.dialogContent = that._createDialogHtml(data,flag);
			that.dialogId = Dialog.open({
				title:'供选人员列表',
				width:930,
				content:that.dialogContent,
				footer:{
					btns:[
						{
							bid:1,
							text:'确定',
							clickHandler:function(e,me){
								that._writeSelectPersonInfo();
								if(str.length){
									me.close();
								}else{
									Dialog.error('所选人员不能为空！');
								}
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
		
		/*生成对话框内容*/
		_createDialogHtml:function(data,flag){
			var
				that = this,
				info = {},
				SELECT_DIALOG_HTML = '';
			info.data = data;
			if(flag == 1){
				SELECT_DIALOG_HTML= Juicer(DIALOG_CHECK_HTML,info);
			}else{
				SELECT_DIALOG_HTML= Juicer(DIALOG_RADIO_HTML,info);
			}
			return SELECT_DIALOG_HTML;
		},
		
		/*把选择人员信息写回页面*/
		_writeSelectPersonInfo:function(){
			var
				that = this,
				opts = that.opts,
				checkbox = $('input',el.selectCheckForm);
				strOfName = "",
				strOfId = "",
				checkedBox = [],num = 0;
			S.each(checkbox,function(i,o){
				if($(i).attr('checked') == 'checked'){
					checkedBox.push(i);
				}
			});
			num = checkedBox.length - 1;
			S.each(checkedBox,function(i,o){
				if(o == num){
					strOfName = strOfName + $(i).parent().text();
					strOfId = strOfId + $(i).attr("id");
				}else{
					strOfName = strOfName + $(i).parent().text()+',';
					strOfId = strOfId + $(i).attr("id") + ',';
				}
			});
			$(opts.tar).prev().val(strOfName);
			$(opts.tar).parent().next().next().children("input").val(strOfId);
			str = strOfName + strOfId;
			return str;
		}
	});

	return select;
},{
	requires:['mod/juicer','mod/dialog','mod/defender','io/banquet/select-checkbox']
});
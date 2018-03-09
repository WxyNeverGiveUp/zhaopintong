/*-----------------------------------------------------------------------------
* @Description:     招聘信息部分来访人相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.19
* ==NOTES:=============================================
* v1.0.0(2014.9.19):
     初始生成
  v1.0.0(2014.10.02):
  	 添加来访人和删除来访人点击确定不用发送ajax
* ---------------------------------------------------------------------------*/
KISSY.add('module/recruit/visitor',function(S,add,del,getContact,getLeader){
	PW.namespace('module.recruit.visitor');
	PW.module.recruit.visitor = {
		add:function(param){
			new add(param);
		},
		del:function(param){
			new del(param);
		},
		getContact:function(param){
			new getContact(param);
		},
		getLeader:function(param){
			new getLeader(param);
		}
	};
},{
	requires:['visitor/add','visitor/del','visitor/getContact','visitor/getLeader']
});
/*-------------------------------来访信息添加--------------------------------*/
KISSY.add('visitor/add',function(S,Juicer){
	var
		$ = S.all, DOM = S.DOM,
		Dialog = PW.mod.Dialog,
		Defender = PW.mod.Defender,
		// VisitorIO = PW.io.recruit.visitor,
		SelectIO = PW.io.module.select,
		el = {
			visitorForm:'.J_addVisitorForm',//指向添加来访人的表单
			contactHolder:'.J_contactHolder',//指向领队人的下拉列表
			ranKHolder:'.J_rankHolder',//指向职级的下拉列表
			leaderPostHolder:'.J_leaderPostHolder',//指向来访人职务下拉列表
			leaderGradeHolder:'.J_leaderGradeHolder'//指向来访人职级下拉列表
		},
		DIALOG_HTML = '<form class="clearfix J_addVisitorForm">'
						+'<div class="control-area control-area-short">'
							+'<label>来访人姓名</label>'
							+'<input name="name" class="textTheme" autocomplete="off" />'
							// +'<select name="linkmanId" class="textTheme J_select J_leaderHolder J_contactHolder" autocomplete="off" data-valid-rule="scale(0,100000,0)" data-type="visitor">'
							// 	+'<option value="0">请选择</option>'
							// +'</select>'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>来访人职务</label>'
							+'<input name="job" class="textTheme J_leaderPostHolder" autocomplete="off" type="text" />'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>来访人电话</label>'
							+'<input name="tel" class="textTheme" autocomplete="off" type="text" />'
						+'</div>'
						// +'<div class="control-area control-area-short">'
						// 	+'<label>来访人职级</label>'
						// 	+'<input name="rank" class="textTheme J_leaderGradeHolder J_rankHolder" autocomplete="off" data-type="contactsType"/>'
						// +'</div>'
						+'</form>',
		VISITOR_HTML = '<div class="info-divied clearfix">'
						+'<a href="javaScript:;" class="del-btn J_visitorDelBtn">删除</a>'
						+'<div class="control-area control-area-short">'
							+'<label>来访人姓名</label>'
							+'<span>&{name}</span>'
							+'<input type="hidden" name="name" value="&{name}" />'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>来访人职务</label>'
							+'<span>&{job}</span>'
							+'<input type="hidden" name="job" value="&{job}" />'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>来访人电话</label>'
							+'<span>&{tel}</span>'
							+'<input type="hidden" name="tel" value="&{tel}" />'
						+'</div>'
						// +'<div class="control-area control-area-short">'
						// 	+'<label>来访人职级</label>'
						// 	+'<span>&{rank}</span>'
						// +'</div>'
						+'</div>';

	function add(param){
		this.opts = param;
		this.init();
	}

	S.augment(add,{
		init:function(){
			this._showDialog();
			//this._getSelect();
			this.valid = Defender.client(el.visitorForm,{
				showTip:false
			});
		},
		/*显示添加来访人信息*/
		_showDialog:function(){
			var
				that = this,
				opts = that.opts;
			that.dialogContent = that._createDialogContent();
			that.dialogId = Dialog.open({
				title:'添加来访人信息',
				width:930,
				content:that.dialogContent,
				footer:{
					btns:[
						{
							bid:1,
							text:'确定',
							clickHandler:function(e,me){
								//that._removeDisable();
								that._addVisitor(me);
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
		_createDialogContent:function(){
			return DIALOG_HTML;
		},
		/*去掉input的disabled属性*/
		/*_removeDisable:function(){
			$(el.ranKHolder).removeAttr('disabled');
			$(el.leaderPostHolder).removeAttr('disabled');
		},*/
		/*添加来访信息*/
		_addVisitor:function(dialog){
			var
				that = this,
				opts = that.opts,
				valid = that.valid,
				info = {};
			valid.validAll();
			if(valid.getValidResult('bool')){
				info = that._serialize();
				//info.linkmanValue = that._getSelectValue(el.contactHolder,info.linkmanId);
				that._addVisitorInfo(info);
				dialog.close();
				//VisitorIO.add(info,function(rs,data,errMsg){
					//if(rs){
						//dialog.close();
					//}else{
						//Dialog.error(errMsg);
					//}
				//});
			}
		},
		/*表单序列化*/
		_serialize:function(){
			var
				info = {};
			info = DOM.serialize(el.visitorForm);
			return info;
		},
		/*获取下拉列表的值*/
		/*_getSelectValue:function(holder,id){
			var
				options = $('option',holder),
				value = '';
			S.each(options,function(i,o){
				if($(i).val() == id){
					value = $(i).text();
				}
			});

			return value;
		},*/
		/*添加来访人信息*/
		_addVisitorInfo:function(info){
			var
				that = this,
				opts = that.opts,
				visitorHtml = Juicer(VISITOR_HTML,info);
			$(opts.visitorHolder).append(visitorHtml);
		}
		/*获取下拉列表的值*/
		/*_getSelect:function(){
			var
				that = this;
			SelectIO.getOption({type:'grade'},function(rs,data,errMes){
				if(rs){
					that._updateSelect(data);
				}else{
					S.log(errMes);
				}
			});
		},*/
		/*更新下来列表*/
		/*_updateSelect:function(data){
			var
				that = this,
				optionHtml = '<option value="0">请选择</option>';
			S.each(data,function(d,o){
				optionHtml = optionHtml + '<option value="'+d.id+'">'+d.name+'</option>';
			});
			$(el.ranKHolder).html(optionHtml);
			$(el.ranKHolder).val(0);
		}*/
	});

	return add;
},{
	requires:['mod/juicer','mod/dialog','mod/defender','io/module/select/select']
});
/*-------------------------------来访信息删除--------------------------------*/
KISSY.add('visitor/del',function(S){
	var
		$ = S.all,
		Dialog = PW.mod.Dialog,
		// VisitorIO = PW.io.recruit.visitor,
		el = {

		},
		DEL_TIP = '确定删除该条来访人信息？';

	function del(param){
		this.opts = param;
		this.init();
	}

	S.augment(del,{
		init:function(){
			this._openDialog();
		},
		_openDialog:function(){
			var
				that = this;
			Dialog.confirm(
				DEL_TIP,
				function(e,me){
					that._delVisitor();
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
		/*ajax删除来访信息*/
		_delVisitor:function(){
			var
				that = this,
				opts = that.opts,
				visitorInfo = $(opts).parent(),
				id = $(visitorInfo).attr('data-id');
				$(visitorInfo).remove();
			// VisitorIO.del({id:id},function(rs,data,errMsg){
			// 	if(rs){
					
			// 	}else{
			// 		Dialog.error(errMsg);
			// 	}
			// });
		}
	});

	return del;
},{
	requires:['mod/dialog']
});
/*-------------------------------获取联系人信息---------------------------------*/
KISSY.add('visitor/getContact',function(S){
	var
		$ = S.all,
		VisitorIO = PW.io.recruit.visitor,
		Dialog = PW.mod.Dialog,
		el = {
			contactTelHolder:'.J_contactTelHolder'//指向联系人手机
		};

	function getContact(param){
		this.opts = param;
		this.init();
	}

	S.augment(getContact,{
		init:function(){
			this._ajaxGetContact();
		},
		/*ajax获取联系人信息*/
		_ajaxGetContact:function(){
			var
				that = this,
				opts = that.opts,
				id = $(opts).val();
			if(id != 0){
				VisitorIO.getContact({contactId:id},function(rs,data,errMes){
					if(rs){
						$(el.contactTelHolder).val(data.telphone);
					}else{
						Dialog.error(errMes);
					}
				});
			}else{
				$(el.contactTelHolder).val("");
			}
		}
	});

	return getContact;
},{	
	requires:['io/recruit/visitor','mod/dialog']
});
/*-------------------------------获取领队人信息---------------------------------*/
KISSY.add('visitor/getLeader',function(S){
	var
		$ = S.all,
		VisitorIO = PW.io.recruit.visitor,
		Dialog = PW.mod.Dialog,
		el = {
			leaderGradeHolder:'.J_leaderGradeHolder',//指向领队人职级
			leaderPostHolder:'.J_leaderPostHolder'//指向领队人职务
		};

	function getLeader(param){
		this.opts = param;
		this.init();
	}

	S.augment(getLeader,{
		init:function(){
			this._ajaxGetLeader();
		},
		/*ajax获取联系人信息*/
		_ajaxGetLeader:function(){
			var
				that = this,
				opts = that.opts,
				id = $(opts).val(),
				next = $(opts).parent().next(),
				leaderPostHolder = $(el.leaderPostHolder,next),
				leaderGradeHolder = $(el.leaderGradeHolder,$(next).next());
			if(id != 0){
				VisitorIO.getLeader({contactId:id},function(rs,data,errMes){
					if(rs){
						$(leaderGradeHolder).val(data.grade);
						$(leaderPostHolder).val(data.post);
					}else{
						Dialog.error(errMes);
					}	
				});
			}else{
				$(leaderGradeHolder).val(0);
				$(leaderGradeHolder).val("");
			}
		}
	});

	return getLeader;
},{	
	requires:['io/recruit/visitor','mod/dialog']
});
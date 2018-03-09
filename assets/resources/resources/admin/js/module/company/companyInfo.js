/*-----------------------------------------------------------------------------
* @Description: 单位管理--新增单位信息 (companyInfo.js)
* @Version: 	V1.0.0
* @author: 		chenke(396985267@qq.com)
* @date			2014.09.15
* ==NOTES:=============================================
* v1.0.0(2014.09.15):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('module/company/companyInfo',function(S,add,del,delBatch){
	PW.namespace('module.company.companyInfo');
	PW.module.company.companyInfo = {
		add:function(param){
			new add(param);
		},
		del:function(param){
			new del(param);
		},
		delBatch:function(param){
			new delBatch(param);
		}
	};
},{
	requires:['newCompany/add','newCompany/del','newCompany/delBatch']
});
/*----------------------------新增联系人---------------------------------------*/
KISSY.add('newCompany/add',function(S,Juicer){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Dialog = PW.mod.Dialog,
		Defender = PW.mod.Defender,
		NewConnecterIO = PW.io.company.companyInfo,
		SelectIO = PW.io.module.select,
		el = {
			gradeHolder:'#J_gradeHolder'//指向职级下拉框
		},

		DIALOG_HTML = '<form class="clearfix J_addBlock">'
						+'<div class="control-area control-area-short">'
							+'<label>姓名</label>'
							+'<input type="text" class="textTheme" name="name" data-valid-rule="notNull" maxlength="50" autocomplete="off" />'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>手机</label>'
							+'<input type="text" class="textTheme" name="telphone" data-valid-rule="isTelephone|isMobile|isNull" maxlength="50" autocomplete="off" />'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>职务</label>'
							+'<input type="text" class="textTheme" name="post" maxlength="50" data-valid-rule="notNull" autocomplete="off" />'
						+'</div>'
						// +'<div class="control-area control-area-short">'
						// 	+'<label>职级</label>'
						// 	+'<select id="J_gradeHolder" class="textTheme J_selectHolder" name="grade" data-type="contactGrade" autocomplete="off" data-valid-rule="scale(0,1000,0)">'
						// 	+'</select>'
						// +'</div>'
						+'<div class="control-area control-area-long J_typeCheckbox">'
							+'<label>标记</label>'
							+'{@each data as d,index}'
								+'{@if index == 0}'
									+'<div class="checkbox checkbox-long">'
										+'<input type="checkbox" checked="checked" value="&{d.id}" name="typeId">'
										+'<span>&{d.name}</span>'
									+'</div>'
									+'{@else if index != 0}'
										+'<div class="checkbox checkbox-long">'
											+'<input type="checkbox" value="&{d.id}" name="typeId">'
											+'<span>&{d.name}</span>'
										+'</div>'
									+'{@else}'
								+'{@/if}'
							+'{@/each}'
						+'</div>'
					+'</form>',
		ADD_HTML = '<div class="clearfix" data-id="&{contactId}">'
						+'<input type="hidden" name="contactId" value="&{contactId}"/>'
						+'<a href="javaScript:;" class="del J_connecterDelBtn">删除</a>'
						+'<div class="control-area control-area-short">'
							+'<label>姓名</label>'
							+'<span>&{name}</span>'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>手机</label>'
							+'<span>&{telphone}</span>'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>职务</label>'
							+'<span>&{post}</span>'
						+'</div>'
						+'<div class="control-area control-area-long">'
							+'<label>标记</label>'
							+'<span>&{typeValue}</span>'
						+'</div>'
					+'</div>';

		function add(param){
			this.opts = param;
			this.init();
		}

		S.augment(add,{
			init: function(){
				this._ajaxGetType();
			},
			/*点击添加按钮，获取 类别 id与name*/
			_ajaxGetType:function(){
				var 
					that = this;
				SelectIO.getOption({type:'contactsType'},function(rs,data,errorMsg){
					if(rs){
						that._showDialog(data);
					}else{
						S.log(errorMsg);
					}
				});
			},
		/**
		 * 显示添加内容对话框
		 * @param  {[type]} e [指向新增按钮]
		 */
		_showDialog:function(data){
			var
				that = this,
				opts = that.opts,
				checkboxValid;
			that.dialogContent = that._createDialogHtml(data);
			that.dialogId = Dialog.open({
				width:930,
				title:'新增联系人',
				content:that.dialogContent,
				afterOpen:function(){
					that.valid = Defender.client('.J_addBlock',{
						showTip:false
					});
				},
				footer:{
					btns:[
						{
							bid:1,
							text:'确定',
							clickHandler:function(e,me){
								that._ajaxAddConnecter(me);
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
		_createDialogHtml:function(data){
			var
				that = this,
				addConnecterContent;
			addConnecterContent = Juicer(DIALOG_HTML,{data:data});
			return addConnecterContent;
		},
		/*ajax添加需求信息*/
		_ajaxAddConnecter:function(dialog){
			var
				that = this,
				valid = that.valid,
				info = {};
			valid.validAll();
			if(valid.getValidResult('bool')){
				info = that._serialize();
				NewConnecterIO.addConnecter(info,function(rs,data,errorMsg){
					if(rs){
						info.contactId = data.contactId;
						typeValue = that._getCheckboxValue();
						info.typeValue = typeValue;
						gradeValue = that._getSelectValue(info.grade);
						info.gradeValue = gradeValue;
						checkboxValid = that._getCheckboxValue();
						if(checkboxValid.length){
							that._addConnecterInfo(info);
							dialog.close();
						}else{
							Dialog.error('类型复选框不能为空！');
						}
					}else{
						Dialog.error(errorMsg);
					}
				});
			}
		},
		/*序列化添加联系人信息的表单*/
		_serialize:function(){
			var
				info = {};
			info = DOM.serialize('.J_addBlock');
			return info;
		},
		/*把联系人信息写回页面*/
		_addConnecterInfo:function(info){
			var
				that = this,
				opts = that.opts,
				connecterInfoHtml = Juicer(ADD_HTML,info);
			$(opts.companyConnect).append(connecterInfoHtml);
		},
		/*获取下拉列表的值*/
		_getSelectValue:function(grade){
			var
				options = $('option',el.gradeHolder),
				gradeValue = '';
			S.each(options,function(i,o){
				if($(i).val() == grade){
					gradeValue = $(i).text();
				}
			});
			return gradeValue;
		},
		/*获取复选框中的值*/
		_getCheckboxValue:function(){
			var
				checkbox = $('input[type="checkbox"]','.J_addBlock'),//所有的复选框
				checkedBox = [],checkboxValue='',
				length;
				S.each(checkbox,function(i,o){//遍历所有的复选框
					if($(i).attr('checked') == 'checked'){
						checkParent = DOM.parent(i),
						name = $(checkParent).text();
						checkedBox.push(name);
					}
				});
				length = checkedBox.length-1;
				S.each(checkedBox,function(i,o){
					if(o != length){
						checkboxValue = checkboxValue + i+',';
					}else{
						checkboxValue = checkboxValue + i;
					}
				});
				
			return checkboxValue;
		}
	});

	return add;
},{
	requires:[
				'mod/juicer',
				'io/company/companyInfo',
				'io/module/select/select',
				'mod/dialog',
				'mod/defender',
				'core'
			]
});
/*--------------------------------删除联系人信息-------------------------------*/
KISSY.add('newCompany/del',function(S){
	var
		$ = S.all, on = S.Event.on,

		Dialog = PW.mod.Dialog,
		NewConnecterIO = PW.io.company.companyInfo,
		DEL_TIP = '确定删除该条联系人信息？';

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
				opts = that.opts,
				connecterInfoHolder = $(opts).parent(),
				id = $(connecterInfoHolder).attr('data-id');
			Dialog.confirm(
				DEL_TIP,
				function(e,me){
					that._ajaxDelConnecter(connecterInfoHolder,id);
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
		/*根据id删除联系人信息*/
		_ajaxDelConnecter:function(connecterInfoHolder,id){
			NewConnecterIO.delConnecter({id:id},function(rs,data,errorMsg){
				if(rs){
					$(connecterInfoHolder).remove();
				}else{
					Dialog.error(errorMsg);
				}
			});
		}
	});

	return del;
},{
	requires:['mod/dialog','io/company/companyInfo']
});
/*----------------------------------删除-----------------------------------*/
KISSY.add('newCompany/delBatch',function(S){
	var
		$ = S.all, on = S.Event.on,
		Dialog = PW.mod.Dialog,
		CompanyIO = PW.io.company.companyInfo,
		el = {
			
		},
		DEL_TIP = '确定删除该条单位信息？';

	function del(param){
		this.opts = param;
		this.init();
	}

	S.augment(del,{
		init:function(){
			this._showTip();
		},
		/*显示提示信息*/
		_showTip:function(){
			var
				that = this;
			Dialog.confirm(
				DEL_TIP,
				function(){
					that._ajaxDel();
				},
				function(){

				},{
					position:'fixed',
					width:'400',
					maskColor:'#343434'
				}
			);
		},
		/*ajax删除信息*/
		_ajaxDel:function(){
			var
				that = this,
				opts = that.opts,
				checkbox = $('input[type="checkbox"]',opts),
				id = [],checkedBox = [],tr = [];
			S.each(checkbox,function(i,o){
				if($(i).attr('checked') == 'checked'){
					id.push($(i).attr('data-id'));
					checkedBox.push(i);
					tr.push($(i).parent().parent());
				}
			});
			if(checkedBox.length == 0){
				Dialog.alert('没有选中任何信息！');
			}else{
				CompanyIO.delBatch({ids:id},function(rs,data,errMsg){
					if(rs){
						S.each(tr,function(i,o){
							$(i).remove();
						});
					}else{
						Dialog.error(errMsg);
					}
				});
			}
		}
	});

	return del;
},{
	requires:['mod/dialog','io/recruit/recruit']
});
/*-----------------------------------------------------------------------------
* @Description: 单位管理--联系人信息相关操作 (contact.js)
* @Version: 	V1.0.0
* @author: 		chenke(396985267@qq.com)
* @date			2014.09.25
* ==NOTES:=============================================
* v1.0.0(2014.09.25):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('module/company/contact',function(S,add,del,mod){
	PW.namespace('module.company.contact');
	PW.module.company.contact = {
		add:function(param){
			new add(param);
		},
		del:function(param){
			new del(param);
		},
		mod:function(el,a){
			new mod(el,a);
		}
	};
},{
	requires:['contact/add','contact/del','contact/mod']
});
/*-----------------------------------添加联系人信息----------------------------------*/
KISSY.add('contact/add',function(S,Juicer){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Dialog = PW.mod.Dialog,
		Defender = PW.mod.Defender,
		ContactIO = PW.io.company.contact,
		SelectIO = PW.io.module.select,
		DIALOG_HTML = '<div class="clearfix J_addBlock">'
						+'<div class="control-area control-area-short">'
							+'<label>姓名</label>'
							+'<input type="text" class="textTheme" data-valid-rule="notNull" name="name" />'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>单位</label>'
							+'<select class="textTheme J_company J_selectHolder" data-type="company" autocomplete="off" data-valid-rule="scale(0,1000,0)" name="companyId">'
							+'</select>'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>职务</label>'
							+'<input type="text" class="textTheme" data-valid-rule="notNull" maxlength="50" name="post" />'
						+'</div>'
						// +'<div class="control-area control-area-short">'
						// 	+'<label>职级</label>'
						// 	+'<select class="textTheme J_grade J_selectHolder" data-type="contactGrade" autocomplete="off" data-valid-rule="scale(0,1000,0)" name="grade">'
						// 	+'</select>'
						// +'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>手机</label>'
							+'<input type="text" class="textTheme" data-valid-rule="isMobile|isNull" placeholder="请填写手机号码" name="telphone" />'
						+'</div>'
						+'<div class="control-area control-area-long J_checkboxHolder">'
							+'<label>类别</label>'
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
					+'</div>',
		ADD_HTML =  '<tr>'
						+'<td><input type="checkbox" data-id="&{contactId}"></td>'
						+'<td>&{name}</td>'
						+'<td>&{companyName}</td>'
						+'<td>&{typeValue}</td>'
						+'<td>&{post}</td>'
						// +'<td>&{gradeValue}</td>'
						+'<td>&{telphone}</td>'
						+'<td>'
							+'<a class="mod J_modContact" href="javaScript:;" title="编辑">'
								+'<i></i>'
							+'</a>'
						+'</td>'
					+'</tr>';
		function add(param){
			this.opts = param;
			this.init();
		}

		S.augment(add,{
			init:function(){
				this._ajaxGetType();
			},
			/*点击添加按钮，获取复选框 类别 id与name*/
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
			/*显示添加弹出框*/
			_showDialog:function(data){
				var
					that = this;
				that.dialogContent = that._createDialogHtml(data);
				that.dialogId = Dialog.open({
					width:930,
					title:'添加联系人',
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
									that._ajaxAddContact(me);
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
			/*ajax添加联系人信息*/
			_ajaxAddContact:function(dialog){
				var
					that = this,
					valid = that.valid,
					info = {};
				valid.validAll();
				if(valid.getValidResult('bool')){
					info = that._serialize(),
					checkbox = that._getCheckBox();
					if(checkbox.length > 0){
						ContactIO.add(info,function(rs,data,errorMsg){
							if(rs){
								info.contactId = data.contactId;
								companyName = that._getCompanyName(info.companyId);
								info.companyName = companyName;
								typeValue = that._getCheckboxValue();
								info.typeValue = typeValue;
								gradeValue = that._getGradeValue(info.grade);
								info.gradeValue = gradeValue;
								checkboxValid = that._getCheckboxValue();
								if(checkboxValid.length){
									that._addContactInfo(info);
									dialog.close();
								}else{
									Dialog.error('类型复选框不能为空！');
								}
							}else{
								Dialog.error(errorMsg);
							}
						});
					}else{
						Dialog.alert('当前未选中任何类别！');
					}
				}
			},
			/*序列化添加联系人信息的表单*/
			_serialize:function(){
				var
					info = {};
				info = DOM.serialize('.J_addBlock');
				return info;
			},
			/*获取单位下拉列表的值*/
			_getCompanyName:function(companyId){
				var
					that = this,
					opts = that.opts,
					options = $('option',opts.companyHolder),
					companyName = '';
				S.each(options,function(i,o){
					if($(i).val() == companyId){
						companyName = $(i).text();
					}
				});
				return companyName;
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
			},
			/*获取职级下拉列表的值*/
			_getGradeValue:function(grade){
				var
					that = this,
					opts = that.opts,
					options = $('option',opts.gradeHolder),
					gradeValue = '';
				S.each(options,function(i,o){
					if($(i).val() == grade){
						gradeValue = $(i).text();
					}
				});
				return gradeValue;
			},
			/*将新添加的联系人信息显示在表格中*/
			_addContactInfo:function(info){
				var
					that = this,
					opts = that.opts;
				contactInfoHtml = Juicer(ADD_HTML,info);
				$(opts.contactList).prepend(contactInfoHtml);
			},
			_getCheckBox:function(){
				var
					that = this,
					checkedBox = [],
					checkbox = $('input:[type="checkbox"]','.J_checkboxHolder');
				S.each(checkbox,function(i,o){
					if($(i).attr('checked') == 'checked'){
						checkedBox.push(i);
					}
				});
				return checkedBox;
			}
		});

	return add;
},{
	requires:['mod/juicer','io/company/contact','mod/defender','mod/dialog','io/module/select/select']
});
/*-----------------------------------删除联系人信息----------------------------------*/
KISSY.add('contact/del',function(S){
	var
		$ = S.all, on = S.Event.on,
		Dialog = PW.mod.Dialog,
		ContactIO = PW.io.company.contact,
		DEL_TIP = '确定删除所选联系人信息？';

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
					that._ajaxDelContact();
				},
				function(){

				},{
					position:'fixed',
					width:'400',
					maskColor:'#343434'
				}
			);
		},
		/*用ajax删除联系人信息*/
		_ajaxDelContact:function(){
			var
				that = this,
				opts = that.opts,
				checkbox = $('input[type="checkbox"]',opts.contactList),
				id = [],checkedBox = [];
			S.each(checkbox,function(i,o){
				if($(i).attr('checked') == 'checked'){
					id.push($(i).attr('data-id'));
					checkedBox.push(i);
				}
			});
			if(id.length){
				ContactIO.del({ids:id},function(rs,data,errMsg){
					if(rs){
						that._removeContact(checkedBox);
					}else{
						Dialog.error(errMsg);
					}
				});
			}else{
				Dialog.error('请选择要删除的数据！');
			}
		},
		/*移除联系人信息*/
		_removeContact:function(checkedBox){
			var
				that = this,
				tr;
			S.each(checkedBox,function(i,o){
				tr = $(i).parent().parent();
				$(tr).remove();
			});
		}
	});

	return del;
},{
	requires:['mod/dialog','io/company/contact']
});
/*-----------------------------------修改联系人信息----------------------------------*/
KISSY.add('contact/mod',function(S,Juicer){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Dialog = PW.mod.Dialog,
		Defender = PW.mod.Defender,
		ContactIO = PW.io.company.contact,
		DIALOG_HTML = '<div class="clearfix J_addBlock">'
						+'<input type="hidden" name="contactId" value="&{contactId}" >'	
						+'<div class="control-area control-area-short">'
							+'<label>姓名</label>'
							+'<input type="text" class="textTheme" data-valid-rule="notNull" name="name" value="&{name}" />'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>单位</label>'
							+'<select class="textTheme J_company J_selectHolder" data-type="company" autocomplete="off" data-valid-rule="scale(0,1000,0)" name="companyId">'
								+'<option value="&{companyId}">&{companyName}</option>'
							+'</select>'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>职务</label>'
							+'<input type="text" class="textTheme" data-valid-rule="notNull" maxlength="50" name="post" value="&{post}"/>'
						+'</div>'
						// +'<div class="control-area control-area-short">'
						// 	+'<label>职级</label>'
						// 	+'<select class="textTheme J_grade J_selectHolder" data-type="contactGrade" autocomplete="off" data-valid-rule="scale(0,1000,0)" name="grade">'
						// 	+'<option value="&{gradeId}">&{gradeName}</option>'
						// 	+'</select>'
						// +'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>手机</label>'
							+'<input type="text" class="textTheme" data-valid-rule="isMobile|isNull" placeholder="请填写手机号码" name="cellphone" value="&{cellphone}"/>'
						+'</div>'
						+'<div class="control-area control-area-long">'
							+'<label>类别</label>'
							+'{@each type as d}'
								+'{@if d.flag == 0}'
									+'<div class="checkbox checkbox-long">'
										+'<input type="checkbox" value="&{d.id}" name="typeId">'
										+'<span>&{d.name}</span>'
									+'</div>'
									+'{@else if d.flag == 1}'
										+'<div class="checkbox checkbox-long">'
											+'<input type="checkbox" checked="checked" value="&{d.id}" name="typeId">'
											+'<span>&{d.name}</span>'
										+'</div>'
									+'{@else}'
								+'{@/if}'
							+'{@/each}'
						+'</div>'
					+'</div>',
		ADD_HTML =  '<tr>'
						+'<td><input type="checkbox" data-id="&{contactId}"></td>'
						+'<td>&{name}</td>'
						+'<td>&{companyName}</td>'
						+'<td>&{typeValue}</td>'
						+'<td>&{post}</td>'
						// +'<td>&{gradeValue}</td>'
						+'<td>&{cellphone}</td>'
						+'<td>'
							+'<a class="mod J_modContact" href="javaScript:;" title="编辑">'
								+'<i></i>'
							+'</a>'
						+'</td>'
					+'</tr>';
		function mod(el,a){
			this.opts = el;
			this.aTarget = a;
			this.init();
		}

		S.augment(mod,{
			init:function(){
				this._ajaxGetContact();
			},
			/*发送ajax获取要编辑的联系人信息*/
			_ajaxGetContact:function(){
				var
					that = this,
					opts = that.opts,
					contactId = that._getContactId();
				ContactIO.getContact({id:contactId},function(rs,data,errorMsg){
					if(rs){
						that._showDialog(data);
					}else{
						Dialog.error(errorMsg);
					}
				});
			},
			/*获取要编辑的联系人ID*/
			_getContactId:function(){
				var
					that = this,
					aTarget = that.aTarget,
					contactTr = $(aTarget).parent().parent();
				contactCheckbox = $('input[type="checkbox"]',contactTr);
				contactId = $(contactCheckbox).attr('data-id');

				return contactId;
			},
			/*显示添加弹出框*/
			_showDialog:function(data){
				var
					that = this;
				that.dialogContent = that._createDialogHtml(data);
				that.dialogId = Dialog.open({
					width:930,
					title:'修改联系人',
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
									that._ajaxModContact(me);
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
					modConnecterContent;
				data.contactId = that._getContactId();
				modConnecterContent = Juicer(DIALOG_HTML,data);

				return modConnecterContent;
			},
			/*ajax添加联系人信息*/
			_ajaxModContact:function(dialog){
				var
					that = this,
					valid = that.valid,
					info = {},
					contactId = that._getContactId();
				valid.validAll();
				if(valid.getValidResult('bool')){
					info = that._serialize();
					ContactIO.mod(info,function(rs,data,errorMsg){
						if(rs){
							info.contactId = contactId;
							companyName = that._getCompanyName(info.companyId);
							info.companyName = companyName;
							typeValue = that._getCheckboxValue();
							info.typeValue = typeValue;
							gradeValue = that._getGradeValue(info.grade);
							info.gradeValue = gradeValue;
							checkboxValid = that._getCheckboxValue();
							if(checkboxValid.length){
								that._modContactInfo(info);
								dialog.close();
							}else{
								Dialog.error('类型复选框不能为空！');
							}
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
			/*获取单位下拉列表的值*/
			_getCompanyName:function(companyId){
				var
					that = this,
					opts = that.opts,
					options = $('option',opts.companyHolder),
					companyName = '';
				S.each(options,function(i,o){
					if($(i).val() == companyId){
						companyName = $(i).text();
					}
				});
				return companyName;
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
			},
			/*获取职级下拉列表的值*/
			_getGradeValue:function(grade){
				var
					that = this,
					opts = that.opts,
					options = $('option',opts.gradeHolder),
					gradeValue = '';
				S.each(options,function(i,o){
					if($(i).val() == grade){
						gradeValue = $(i).text();
					}
				});
				return gradeValue;
			},
			/*将修改的联系人信息显示在表格中*/
			_modContactInfo:function(info){
				var
					that = this,
					opts = that.opts,
					aTarget = that.aTarget,
					contactTr = $(aTarget).parent().parent();
				contactInfoHtml = Juicer(ADD_HTML,info);
				$(contactTr).remove();
				$(opts.contactList).prepend(contactInfoHtml);
			}
		});

	return mod;
},{
	requires:['mod/juicer','io/company/contact','mod/defender','mod/dialog']
});
/*-----------------------------------------------------------------------------
* @Description: 单位管理--新增单位信息 (company-info-new.js)
* @Version: 	V1.0.0
* @author: 		chenke(396985267@qq.com)
* @date			2014.09.15
* ==NOTES:=============================================
* v1.0.0(2014.09.15):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/company-info-new',function(S,linkage,company,interview,select){
	PW.namespace('page.newCompany');
	PW.page.newCompany = function(param){
		new linkage(param);
		new company(param);
		new interview(param);
		new select(param);
	};
},{
	requires:['module/linkage','newCompany/company','newCompany/interview','module/select']
});
/*---------------------------------表单提交---------------------------------*/
KISSY.add('newCompany/submit',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Defender = PW.mod.Defender,
		Dialog = PW.mod.Dialog,
		el = {
			companyForm:'.J_companyForm', //指向添加单位信息的表单
			submitBtn:'.J_submit',//指向确定提交按钮
			typeCheckbox:'.J_type'//指向类别复选框
		}

	function submit(param){
		this.init();
	}

	S.augment(submit,{
		init:function(){
			this.valid = Defender.client(el.companyForm,{
				showTip:false
			});
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击确定按钮*/
			on(el.submitBtn,'click',function(evt){
				that._formSubmit();
			});
		},
		/*表单提交*/
		_formSubmit:function(){
			var
				that = this,
				valid = that.valid,
				result = true;
			//result = that._validCheckbox();//验证复选框是否为空
			valid.validAll();
			if(valid.getValidResult('bool')){
				//if(result){
				that._removeDisabled();
				jQuery(el.companyForm).submit();
				//}else{
					//Dialog.error('没有选中任何类别！');
				//}
			}
		},
		/*验证复选框*/
		/*_validCheckbox:function(){
			var
				that = this,
				checkedBox = $('input:[type="checkbox"]','.J_type'),
				checkedCheckbox = [];
			S.each(checkedBox,function(i,o){
				if($(i).attr('checked') == 'checked'){
					checkedCheckbox.push(i);
				}
			});
			if(checkedCheckbox.length > 0){
				return true;
			}else{
				return false;
			}
		},*/
		/*移除disabled属性*/
		_removeDisabled:function(){
			var
				that = this,
				input = $('select,input',el.companyForm);
			S.each(input,function(i,o){
				if($(i).attr('disabled') == 'disabled'){
					$(i).removeAttr('disabled');
				}
			});
		}
	});

	return submit;
},{
	requires:['mod/defender','mod/dialog']
});
/*----------------------------联系人信息相关操作-------------------------------*/
KISSY.add('newCompany/company',function(S){
	var
		$ = S.all, on = S.Event.on, delegate = S.Event.delegate,
		companyHandler = PW.module.company.companyInfo,
		el = {
			addConnecterBtn:'#J_addConnecter',//指向新增按钮
			companyConnect:'.J_companyConnect',// 指向联系人div，显示已添加联系人信息
			connecterDelBtn:'.J_connecterDelBtn'//指向联系人信息的删除按钮
		};

	function company(param){
		this.opts = param;
		this.init();
	}

	S.augment(company,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击添加按钮*/
			on(el.addConnecterBtn,'click',function(evt){
				that._addCompany();
			});
			/*点击删除按钮*/
			delegate(document,'click',el.connecterDelBtn,function(evt){
				that._delConnecter(evt.currentTarget);
			});
		},
		/*在弹出框中显示添加单位信息*/
		_addCompany:function(){ 
			companyHandler.add(el);
		},
		/*删除该条单位信息*/
		_delConnecter:function(target){
			companyHandler.del(target);
		}
	});

	return company;
},{
	requires:['module/company/companyInfo']
});
/*---------------------------------走访信息相关操作---------------------------------*/
KISSY.add('newCompany/interview',function(S,Juicer,submit){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Defender = PW.mod.Defender,
		el = {
			companyForm:'.J_companyForm',//指向新增单位的form表单框
			comInterview:'#J_interview', //指向‘单位走访’ 复选框
			intBlock:'.J_intBlock', //指向走访信息内容外面的div框
			intContent:'.J_intContent', //指向走访信息内容form框
			interviewHolder:'.J_interViewHolder'
		},
		INTERVIEW_HTML=
			'<div class="clearfix J_intContent">'
				+'<div class="control-area control-area-short">'
					+'<label>走访日期</label>'
					+'<input type="text" class="textTheme date" data-valid-rule="notNull" maxlength="50" name="interviewDate"/>'
				+'</div>'
				+'<div class="control-area control-area-short">'
					+'<label>走访人姓名</label>'
					+'<input type="text" class="textTheme" data-valid-rule="notNull" maxlength="50" name="interviewer"/>'
				+'</div>'
			+'</div>';

	function interview(param){
		this.sub = new submit()
		this.opts = param;
		this.init();
	}
	S.augment(interview,{
		init:function(){
			
			this._addEventListener();
		},
		/*监听是否选择‘单位走访’多选框*/
		_addEventListener:function(){
			var
				that = this;
			on(el.comInterview,'click',function(evt){
				that._selectInterview(evt.target);
			});
		},
		/*
		判断是否选择了单位走访
		*/
		_selectInterview:function(evt){
			var
				that = this,
				sub = that.sub,	
				judge;
				info = that._serialize();
				
			judge = $(evt).prop('checked');
			if(judge){
				
				interviewInfoHtml = Juicer(INTERVIEW_HTML,info);
				$(el.intBlock).append(interviewInfoHtml);
				$(el.interviewHolder).removeClass('none');
				sub.valid.refresh();
				//把走访信息的走访时间表单框添加到页面之后，才能引入日历控件
				var query = S.query;
				S.each(query('.date'),function(i){
					Calendar = PW.mod.Calendar;
					var calendar = Calendar.client({
			            renderTo: i, //默认只获取第一个
			            select: {
			                rangeSelect: false, //是否允许区间选择
			                dateFmt: 'YYYY-M-D',
			                showTime: false //是否显示时间
			            }
			        });
				});
			}else{
				DOM.remove(el.intContent);
				sub.valid.refresh();
				$(el.interviewHolder).addClass('none');
			}
		},
		/*序列化添加走访信息的表单*/
		_serialize:function(){
			var
				info = {};
			info = DOM.serialize(el.intContent);
			return info;
		}
	});

	return interview;
},{
	requires:['mod/juicer','newCompany/submit','mod/defender','mod/calendar']
});
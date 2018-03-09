/*-----------------------------------------------------------------------------
* @Description: 单位管理--联系人信息管理 (contact.js)
* @Version: 	V1.0.0
* @author: 		chenke(396985267@qq.com)
* @date			2014.09.24
* ==NOTES:=============================================
* v1.0.0(2014.09.24):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/contact',function(S,suggest,search,recruit,contact,selectAll,select){
	PW.namespace('page.contactInfo');
	PW.page.contactInfo = function(param){
		new suggest(param);
		new search(param);
		new recruit(param);
		new contact(param);
		new selectAll(param);
		new select(param);
	}
},{
	requires:['contactInfo/suggest','contactInfo/search','contactInfo/recruit','contactInfo/contact','module/selectAll','module/select']
});
/*---------------------------------suggest------------------------------------*/
KISSY.add('contactInfo/suggest',function(S){
	var
		suggest = PW.module.suggest;
	function suggest(param){
		new suggest(param);
	}

	return suggest;
},{
	requires:['module/suggest']
});
/*--------------------------------查询-----------------------------------------*/
KISSY.add('contactInfo/search',function(S){
	var
		$ = S.all, DOM = S.DOM, on = S.Event.on,
		Calendar = PW.mod.Calendar,
		Defender = PW.mod.Defender,
		Pagination = PW.mod.Pagination,
		el = {
			searchBtn:'.J_searchBtn',//指向查询按钮
			searchForm:'.J_searchForm'//指向查询的表单
		};

	function search(param){
		this.opts = param;
		this.init();
	}

	S.augment(search,{
		init:function(){
			this._paging({});
			this._addEventListener();		
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击查询按钮*/
			on(el.searchBtn,'click',function(evt){
				that._search();
			});
		},
		/*查询*/
		_search:function(){
			var
				that = this,
				info = that._serialize();
			that._paging(info);
		},
		/*分页*/
		_paging:function(param){
			var
				that = this,
				opts = that.opts,
				extraParam = S.merge(opts,{extraParam:param});
			Pagination.client(extraParam);
		},
		/*表单序列化*/
		_serialize:function(){
			var
				info = {};
			info = DOM.serialize(el.searchForm);
			return info;
		}
	});

	return search;
},{
	requires:['mod/pagination','mod/calendar','mod/defender']
});
/*--------------------------------招聘会id隐藏与显示操作-----------------------------------------*/
KISSY.add('contactInfo/recruit',function(S){
	var
		$ = S.all, DOM = S.DOM, on = S.Event.on,
		el = {
			typeSelect:'#J_typeHolder',//指向页面类别下拉框
			recruitIdSelect:'#J_recruitIdHolder'//指向招聘会ID的输入框
		};

	function recruit(param){
		this.init();
	}

	S.augment(recruit,{
		init:function(){
			this._addEventListener();		
		},
		_addEventListener:function(){
			var
				that = this,
				typeOptions = $('option',el.typeSelect);
			/*点击类别下拉框*/
			on(typeOptions,'click',function(evt){
				that._selectRecruit();
			});
		},
		/*是否选择招聘会联系人/带队人*/
		_selectRecruit:function(){
			var 
				recruitId,recruitValue;
			recruitId = $(el.typeSelect).val();
			recruitValue = parseInt(recruitId);
			if(recruitValue == 4||recruitValue == 5){
				$(el.recruitIdSelect).show();
				$(el.recruitIdSelect).removeClass('.none');
			}else{
				$(el.recruitIdSelect).hide();
				$(el.recruitIdSelect).addClass('.none');
				$(el.recruitIdSelect).val('');
			}
		}	
	});

	return recruit;
},{
	requires:['core']
});
/*-----------------------------------联系人信息相关操作----------------------------------*/
KISSY.add('contactInfo/contact',function(S,Juicer){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM, delegate = S.Event.delegate,
		contactHandler = PW.module.company.contact,
		el = {
			addContactBtn:'.J_addContactBtn',//指向添加联系人按钮
			contactList:'#J_template',//指向联系人信息列表
			companyHolder:'.J_company',//指向单位下拉框
			typeHolder:'.J_type',//指向类别下拉框
			gradeHolder:'.J_grade',//指向职级下拉框
			delBatchBtn:'.J_delBatch',//指向批量删除的按钮
			contactList:'#J_template',//指向联系人信息列表
			modContactBtn:'.J_modContact',//指向编辑联系人的按钮
			typeCheckbox:'.J_typeCheckbox'//指向编辑联系人弹出框中类别复选框
		};
		
		function contact(param){
			this.init();
		}

		S.augment(contact,{
			init:function(){
				this._addEventListener();
			},
			_addEventListener:function(){
				var
					that = this;
				/*点击添加按钮*/
				on(el.addContactBtn,'click',function(evt){
					that._addContact();
				});
				/*点击删除按钮*/
				on(el.delBatchBtn,'click',function(evt){
					that._delContact();
				});
				/*点击编辑按钮*/
				delegate(document, 'click', el.modContactBtn, function(evt){
					that._modContact(evt.currentTarget);
				});
			},
			/*在弹出框中显示添加联系人信息*/
			_addContact:function(){ 
				contactHandler.add(el);
			},
			/*删除该条联系人信息*/
			_delContact:function(){
				contactHandler.del(el);
			},
			/*编辑该条联系人信息 a代表点击编辑按钮的a标签*/
			_modContact:function(a){
				contactHandler.mod(el,a);
			},	
		});
	return contact;
},{
	requires:['module/company/contact']
});
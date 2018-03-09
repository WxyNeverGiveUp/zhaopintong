/*-----------------------------------------------------------------------------
* @Description:     新增招聘信息相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.15
* ==NOTES:=============================================
* v1.0.0(2014.9.15):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/recruit/recruit-new',function(S,need,select,visitor,add){
	PW.namespace('page.recruitNew');
	PW.page.recruitNew = function(param){
		new need(param);
		new select(param);
		new visitor(param);
		new add(param);
	};
},{
	requires:['recruitNew/need','recruitNew/select','recruitNew/visitor','recruitNew/add']
});
/*----------------------------需求信息相关操作-------------------------------*/
KISSY.add('recruitNew/need',function(S){
	var
		$ = S.all, on = S.Event.on, delegate = S.Event.delegate
		needHandler = PW.module.recruit.need,
		el = {
			needAddBtn:'.J_addNeedBtn',//指向需求信息添加按钮
			needInfoHolder:'.J_needInfoHolder',//指向需求信息所在的区域
			needDelBtn:'.J_needDelBtn'//指向需求信息的删除按钮
		};

	function need(param){
		this.opts = param;
		this.init();
	}

	S.augment(need,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击添加按钮*/
			on(el.needAddBtn,'click',function(evt){
				that._addNeed();
			});
			/*点击删除按钮*/
			delegate(document,'click',el.needDelBtn,function(evt){
				that._delNeed(evt.currentTarget);
			});
		},
		/*添加需求信息*/
		_addNeed:function(){ 
			needHandler.add(el);
		},
		/*删除需求信息*/
		_delNeed:function(target){
			needHandler.del(target);
		}
	});

	return need;
},{
	requires:['module/recruit/need']
});
/*------------------------------------下拉列表--------------------------------------*/
KISSY.add('recruitNew/select',function(S,Sel){
	var
		$ = S.all,
		el = {
			companyHolder:'.J_companyHolder'
		};
	function select(param){
		this.opts = param;
		this.init();
	}

	S.augment(select,{
		init:function(){
			var
				that = this,
				opts = that.opts,
				companyId = $(el.companyHolder).val(),
				extraParam = {companyId:companyId},
				param = S.merge(opts,{extraParam:extraParam});
			new Sel(param);
		}
	});

	return select;
},{
	requires:['module/select']
});
/*-----------------------------------来访信息---------------------------------*/
KISSY.add('recruitNew/visitor',function(S){
	var
		$ = S.all, on = S.Event.on, delegate = S.Event.delegate,
		Visitor = PW.module.recruit.visitor,
		el = {
			addVisitorBtn:'.J_addVisitorBtn',//指向添加来访人按钮
			visitorHolder:'.J_visitorHolder',//指向添加采访人信息
			delVisitorBtn:'.J_visitorDelBtn',//指向删除来访人信息按钮
			contactHolder:'.J_contactHolder',//指向联系人下拉列表
			leaderHolder:'.J_leaderHolder'//指向领队人下拉列表
		};

	function visitor(param){
		this.opts = param;
		this.init();
	}

	S.augment(visitor,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击添加来访人按钮*/
			on(el.addVisitorBtn,'click',function(){
				that._addVisitor();
			});
			/*点击删除按钮*/
			delegate(document,'click',el.delVisitorBtn,function(evt){
				that._delVisitor(evt.currentTarget);
			});
			/*改变联系人*/
			on(el.contactHolder,'change',function(evt){
				that._getContact(evt.target);
			});
			/*改变领队人*/
			delegate(document,'change',el.leaderHolder,function(evt){
				that._getLeader(evt.currentTarget);
			});
		},
		/*添加来访人信息*/
		_addVisitor:function(){
			Visitor.add(el);
		},
		/*删除来访人信息*/
		_delVisitor:function(target){
			Visitor.del(target);
		},
		/*获取联系人信息*/
		_getContact:function(target){
			Visitor.getContact(target);
		},
		/*获取领队人信息*/
		_getLeader:function(target){
			Visitor.getLeader(target);
		}
	});

	return visitor;
},{
	requires:['module/recruit/visitor']
});
/*-------------------------添加招聘信息----------------------------------*/
KISSY.add('recruitNew/add',function(S){
	var
		$ = S.all, DOM = S.DOM, query = DOM.query, on = S.Event.on,
		Defender = PW.mod.Defender,
		Calendar = PW.mod.Calendar,
		Dialog = PW.mod.Dialog,
		RecruitIO = PW.io.recruit.recruit,
		el = {
			recruitNewForm:'.J_recruitAddForm',//指向添加招聘信息的表单
			timeHolder:'.time',//指向需要时间的input
			startTime:'.J_startTime',//指向开始时间
			endTime:'.J_endTime',//指向结束时间
			recruitIdHolder:'#J_recruitIdHolder',
			placeHolder:'.J_placeHolder',
			dateHolder:'.J_dateHolder',
			nextBtn:'.J_nextBtn'
		};

	function add(param){
		this.opts = param;
		this.init();
	}

	S.augment(add,{
		init:function(){
			this.valid = Defender.client(el.recruitNewForm,{
				showTip:false,
				items:[
					{
						queryName:el.timeHolder,
						pattern: function(input,shell,form){
							var 
								val = S.DOM.val(input);
								if(val != '' &&  /^(([1-9]{1})|([0-1][0-9])|([1-2][0-3])):([0-5][0-9])$/.test(val)){
									return true;
								}else{
									return false;
								}
						},
						tip:'|',
						showEvent:'focus',
						vldEvent:'keyup'
					}
				]
			});
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			S.each(query('.date'),function(i){
				var c = Calendar.client({
					renderTo: i, //默认只获取第一个
	                select: {
	                    rangeSelect: false, //是否允许区间选择
	                    dateFmt: 'YYYY-MM-DD',
	                    showTime: false //是否显示时间
	                }
				});
			});
			/*表单提交*/
			on(el.nextBtn,'click',function(evt){
				that._formSubmit();
			});
		},
		/*表单提交*/
		_formSubmit:function(){
			var
				that = this,
				valid = that.valid,
				timeValid = true,
				needLength = $('.J_jobRequestHolder').length;
			valid.validAll();
			if(valid.getValidResult('bool')){
				timeValid = that._timeValid();
				if(timeValid){
					if(needLength > 0){
						that._ajaxValid();
					}else{
						Dialog.error('至少添加一条需求专业信息！');
					}
				}else{
					Dialog.error('开始时间需小于结束时间！');
				}
			}
		},
		/*验证开始时间小于结束时间*/
		_timeValid:function(){
			var
				startTime = $(el.startTime).val(),
				endTime = $(el.endTime).val(),
				start = startTime.split(':'),
				end = endTime.split(':');
			if(parseInt(start[0]) < parseInt(end[0])){
				return true;
			}else if(parseInt(start[0]) == parseInt(end[0]) && parseInt(start[1]) < parseInt(end[1])){
				return true;
			}else{
				return false
			}
		},
		/*使用ajax验证宣讲会时间地点*/
		_ajaxValid:function(){
			var
				that =this,
				placeId = $(el.placeHolder).val(),
				date = $(el.dateHolder).val(),
				startTime = $(el.startTime).val(),
				endTime = $(el.endTime).val(),
				id,
				info = {};
			if($(el.recruitIdHolder)){
				//修改
				id = $(el.recruitIdHolder).val();
				info = {
					placeId:placeId,
					date:date,
					startTime:startTime,
					endTime:endTime,
					recruitId:id
				}
			}else{
				//添加
				info = {
					placeId:placeId,
					date:date,
					startTime:startTime,
					endTime:endTime
				}
			}
			RecruitIO.valid(info,function(rs,data,errMsg){
				if(rs){
					if(data.flag == true){
						that._removeDisabled();
						jQuery(el.recruitNewForm).submit();
					}else{
						Dialog.error(data.tip);
					}
				}else{
					S.log(errMsg);
				}
			});
		},
		/*移除disabled属性*/
		_removeDisabled:function(){
			var
				that = this,
				input = $('select,input',el.recruitNewForm);
			S.each(input,function(i,o){
				if($(i).attr('disabled') == 'disabled'){
					$(i).removeAttr('disabled');
				}
			});
		}
	});

	return add;
},{
	requires:['mod/defender','mod/calendar','mod/dialog','io/recruit/recruit']
});

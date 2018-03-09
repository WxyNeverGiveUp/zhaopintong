/*-----------------------------------------------------------------------------
* @Description:     招聘信息列表相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.23
* ==NOTES:=============================================
* v1.0.0(2014.9.23):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/recruit/recruit-list',function(S,suggest,linkage,search,selectAll,degree,del,enter){
	PW.namespace('page.recruitList');
	PW.page.recruitList = function(param){
		new suggest(param);
		new linkage(param);
		new search(param);
		new selectAll(param);
		new degree(param);
		new del(param);
		new enter(param);
	};
},{
	requires:['recruitList/suggest','module/linkage','recruitList/search','module/selectAll','recruitList/degree','recruitList/del','recruitList/enter']
});
/*---------------------------------suggest------------------------------------*/
KISSY.add('recruitList/suggest',function(S){
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
KISSY.add('recruitList/search',function(S){
	var
		$ = S.all, DOM = S.DOM, query = DOM.query, on = S.Event.on,
		Calendar = PW.mod.Calendar,
		Defender = PW.mod.Defender,
		Pagination = PW.mod.Pagination,
		Dialog = PW.mod.Dialog,
		el = {
			searchBtn:'.J_searchBtn',//指向查询按钮
			searchForm:'.J_searchForm',//指向查询的表单
			startTimeHolder:'.J_startTimeHolder',
			endTimeHolder:'.J_endTimeHolder'
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
			S.each(query('.date'),function(i){
				Calendar.client({
					renderTo: i, //默认只获取第一个
	                select: {
	                    rangeSelect: false, //是否允许区间选择
	                    dateFmt: 'YYYY-MM-DD',
	                    showTime: false //是否显示时间
	                }
				});
			});
			/*点击查询按钮*/
			on(el.searchBtn,'click',function(evt){
				that._search();
			});
		},
		/*查询*/
		_search:function(){
			var
				that = this,
				info = that._serialize(),
				result = that._validTime();
			if(result){
				that._paging(info);
			}else{
				Dialog.alert('录入的开始时间要小于结束时间！');
			}
			
		},
		/*分页*/
		_paging:function(param){
			var
				that = this,
				opts = that.opts,
				lectureDate = $('#J_lectureDateHolder').val();
				extraParam = {};
			if(lectureDate != ''){
				param.lectureDate = lectureDate;
			}
			extraParam = S.merge(opts,{extraParam:param})
			Pagination.client(extraParam);
		},
		/*表单序列化*/
		_serialize:function(){
			var
				info = {};
			info = DOM.serialize(el.searchForm);
			return info;
		},
		/*验证时间*/
		_validTime:function(){
			var
				that = this,
				startTime = $(el.startTimeHolder).val(),
				endTime = $(el.endTimeHolder).val(),
				start = startTime.split('-'),
				end = endTime.split('-');
			if(startTime && endTime){
				if(parseInt(start[0]) > parseInt(end[0])){
					return false;
				}else if(parseInt(start[1]) > parseInt(end[1])){
					return false;
				}else if(parseInt(start[2]) >= parseInt(end[2])){
					return false;
				}else{
					return true;
				}
			}else{
				return true;
			}
		}
	});

	return search;
},{
	requires:['mod/pagination','mod/calendar','mod/defender','mod/dialog']
});
/*---------------------------------专业需求-----------------------------------*/
KISSY.add('recruitList/degree',function(S){
	var
		$ = S.all, on = S.Event.on, delegate = S.Event.delegate,
		el = {
			addDegreeBtn:'.J_addDegreeBtn',//指向添加专业需求按钮
			delDegreeBtn:'.J_delDegreeBtn'//指向删除专业需求的按钮
		};

	function degree(param){
		this.init();
	}

	S.augment(degree,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击添加专业需求按钮*/
			on(el.addDegreeBtn,'click',function(evt){
				that._addDegree(evt.target);
			});
			/*点击删除专业需求按钮*/
			delegate(document,'click',el.delDegreeBtn,function(evt){
				that._delDegree(evt.currentTarget);
			});
		},
		/*添加专业需求*/
		_addDegree:function(target){
			var
				that = this,
				degree = $(target).parent(),
				addDegree = $(degree).clone(true,true);
			$(addDegree).insertAfter(degree);
			$(el.delDegreeBtn,addDegree).removeClass('none');
			$(el.addDegreeBtn,addDegree).addClass('none');
		},
		/*删除专业需求*/
		_delDegree:function(target){
			var
				that = this,
				degree = $(target).parent();
			$(degree).remove();
		}
	});

	return degree;
},{
	requires:['core']
});
/*-----------------------------------删除招聘信息----------------------------------*/
KISSY.add('recruitList/del',function(S){
	var
		$ = S.all, on = S.Event.on,
		Dialog = PW.mod.Dialog,
		RecruitHandler = PW.module.recruit.recruit,
		el = {
			delBatchBtn:'.J_delBatch',//指向批量删除的按钮
			recruitList:'#J_template'//指向招聘信息列表
		};

	function del(param){
		this.init();
	}

	S.augment(del,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击删除按钮*/
			on(el.delBatchBtn,'click',function(evt){
				that._delRecruit();
			});
		},
		/*删除招聘信息*/
		_delRecruit:function(){
			RecruitHandler.del(el);
		}
	});

	return del;
},{
	requires:['mod/dialog','module/recruit/recruit']
});
/*--------------------------------录入--------------------------------*/
KISSY.add('recruitList/enter',function(S){
	var
		$ = S.all, delegate = S.Event.delegate,
		RecruitHandler = PW.module.recruit.recruit,
		el = {
			enterBtn:'.J_enterBtn'//指向录入按钮
		};

	function enter(param){
		this.opts = param;
		this.init();
	}

	S.augment(enter,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击录入按钮*/
			delegate(document,'click',el.enterBtn,function(e){
				that._enter(e.currentTarget);
			});
		},
		/*录入*/
		_enter:function(target){
			RecruitHandler.enter(target);
		}
	});

	return enter;
},{
	requires:['module/recruit/recruit']
});
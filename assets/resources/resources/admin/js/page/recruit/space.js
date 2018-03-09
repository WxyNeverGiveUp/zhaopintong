/*-----------------------------------------------------------------------------
* @Description:     场地信息相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/recruit/space',function(S,search,suggest){
	PW.namespace('page.space');
	PW.page.space = function(param){
		new search(param);
		new suggest(param);
	};
},{
	requires:['space/search','space/suggest']
});
/*---------------------------------查询----------------------------------*/
KISSY.add('space/search',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM, query = DOM.query,
		SpaceIO = PW.io.recruit.space,
		Calendar = PW.mod.Calendar,
		Pagination = PW.mod.Pagination,
		Dialog = PW.mod.Dialog,
		el = {
			weekHolder:'.J_weekHolder',//指向周列表
			palceHolder:'.J_placeHolder',//指向地点的列表
			searchBtn:'.J_searchBtn',//指向查询按钮
			searchForm:'.J_searchForm',//指向查询表单
			spaceHolder:'#J_spaceHolder',//指向宣讲会
			prevWeekBtn:'.J_prevWeekBtn',//指向上周按钮
			nextWeekBtn:'.J_nextWeekBtn',//指向下周按钮
			weekBtn:'.J_weekBtn',//指向本周按钮
			titleHolder:'.J_titleHolder',//指向标题
			startTimeHolder:'.J_startTime',//指向开始时间
			endTimeHolder:'.J_endTime'//指向结束时间
		},
		BOTH_CLASS = 'lecture',
		INTERVIEW_CLASS = 'interview',
		WRITTEN_CLASS = 'written';
	function search(param){
		this.opts = param;
		this.init();
	}

	S.augment(search,{
		init:function(){
			//this._loadSpace(1);
			this._addCalendar();
			this._search();
			this._addEventListener();
		},
		/*加载场地*/
		_loadSpace:function(param){
			var
				that = this,
				td = $('td',el.spaceHolder),
				week = $('th',el.weekHolder),
				palce = $(el.palceHolder),
				weekList = that._getList(week,0),
				placeList = that._getList(palce,1);
			that._clearList(td);
			SpaceIO.load({weekList:weekList,placeList:placeList,weekType:param},function(rs,data,errMsg){
				if(rs){
					that._renderSpace(data,td);
				}else{
					S.log(errMsg);
				}
			});
		},
		/*获取列表*/
		_getList:function(list,flag){
			var
				that = this,
				listValue = [],
				value;
			S.each(list,function(i,o){
				if(flag == 0){
					value = $(i).text();
				}else{
					value = $(i).attr('data-id');
				}	
				if(value != ''){
					listValue.push(value);
				}
			});
			return listValue;
		},
		/*渲染场地信息*/
		_renderSpace:function(data,td){
			var
				that = this,
				interview = data.interview,
				written = data.written,
				both = data.both,
				th = $('th',el.weekHolder);
			//面试
			S.each(interview,function(i,o){
				$(td[i]).addClass(INTERVIEW_CLASS);
			});
			//笔试
			S.each(written,function(i,o){
				$(td[i]).addClass(WRITTEN_CLASS);
			});
			//面试、笔试
			S.each(both,function(i,o){
				$(td[i]).addClass(BOTH_CLASS);
			});
			S.each(data.weekList,function(i,o){
				$(th[o+1]).html(i);
			});
		},
		/*添加日历*/
		_addCalendar:function(){
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
		},
		/*分页*/
		_paging:function(param){
			var
				that = this,
				opts = that.opts,
				extraParam = S.merge(opts,{extraParam:param});
			Pagination.client(extraParam);
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击查询按钮*/
			on(el.searchBtn,'click',function(evt){
				that._search();
			});
			/*点击上周按钮*/
			// on(el.prevWeekBtn,'click',function(evt){
			// 	that._loadSpace(0);
			// 	that._changeTitle(evt.target,0);
			// });
			/*点击本周按钮*/
			// on(el.weekBtn,'click',function(evt){
			// 	that._loadSpace(1);
			// 	that._changeTitle(evt.target,1);
			// });
			/*点击下周按钮*/
			// on(el.nextWeekBtn,'click',function(evt){
			// 	that._loadSpace(2);
			// 	that._changeTitle(evt.target,2);
			// });
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
				Dialog.alert('开始时间需小于结束时间！');
			}
		},
		/*表单序列化*/
		_serialize:function(){
			var
				info = {};
			info = DOM.serialize(el.searchForm);
			return info;
		},
		/*改变场地安排的头部*/
		_changeTitle:function(target,flag){
			var
				that = this,
				title = $(target).text();
			$(el.titleHolder).html(title);
			$(target).hide();
			switch(flag){
				case 0:
						$(el.weekBtn).show();
						$(el.nextWeekBtn).show();
						break;
				case 1:
						$(el.prevWeekBtn).show();
						$(el.nextWeekBtn).show();
						break;
				case 2:
						$(el.prevWeekBtn).show();
						$(el.weekBtn).show();
						break;
				default:
						break;
			}
		},
		/*清空表格*/
		_clearList:function(td){
			S.each(td,function(i){
				$(i).removeAttr('class');
			});
		},
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
	requires:['io/recruit/space','mod/calendar','mod/pagination','mod/dialog']
});
/*----------------------------------suggest----------------------------------------*/
KISSY.add('space/suggest',function(S){
	var
		suggest = PW.module.suggest;
	function suggest(param){
		new suggest(param);
	}

	return suggest;
},{
	requires:['module/suggest']
});
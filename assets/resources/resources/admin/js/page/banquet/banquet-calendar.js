/*-----------------------------------------------------------------------------
* @Description:     宴请日历页面相关js(banquet-calendar.js)
* @Version:         1.0.0
* @author:          chenke(396985267@qq.com)
* @date             2014.9.30
* ==NOTES:=============================================
* v1.0.0(2014.9.30):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/banquet/banquet-calendar',function(S,calendar){
	PW.namespace('page.banquetCalendar');
	PW.page.banquetCalendar = function(param){
		new calendar(param);
	}
},{
	requires:['banquetCalendar/calendar']
});
/*--------------------------------点击日历刷新分页-----------------------------------------*/
KISSY.add('banquetCalendar/calendar',function(S){
	var
		$ = S.all, DOM = S.DOM, get = DOM.get, on = S.Event.on,
		Calendar = PW.mod.Calendar,
		Pagination = PW.mod.Pagination;

	function calendar(param){
		this.opts = param;
		this.init();
	}

	S.augment(calendar,{
		init:function(){
			var
				that = this,
				nowTime = new Date(),
				year = nowTime.getFullYear(),
				month = nowTime.getMonth()+1,
				day = nowTime.getDate(),
				nowDate = year + '-'+ month + '-' + day;
			that._paging({date:nowDate});
			that._addEventListener();		
		},
		_addEventListener:function(){
			var
				that = this,
			
			cal = Calendar.client({
				renderTo: '.J_calendar', //默认只获取第一个
                select: {
                    rangeSelect: false, //是否允许区间选择
                    dateFmt: 'YYYY-MM-DD',
                    showTime: false //是否显示时间
                },
                style:{
                	popup:false
                }
			});
			/*点击日历查询宴请详情*/
			on(cal,'select',function(e){
				that._search(e);
			});
		},
		/*查询宴请详情*/
		_search:function(e){
			var
				that = this,
				time = e.date,
				year = time.getFullYear(),
				month = time.getMonth()+1,
				day = time.getDate(),
				date = year + '-'+ month + '-' + day;
			that._paging({date:date});
		},
		/*分页*/
		_paging:function(param){
			var
				that = this,
				opts = that.opts,
				extraParam = S.merge(opts,{extraParam:param});
			Pagination.client(extraParam);
		}
	});

	return calendar;
},{
	requires:['mod/pagination','mod/calendar']
});

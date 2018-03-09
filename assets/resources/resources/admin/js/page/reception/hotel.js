/*-----------------------------------------------------------------------------
* @Description:     接待管理部分宾馆管理相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.25
* ==NOTES:=============================================
* v1.0.0(2014.9.25):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/reception/hotel',function(S,search,selectAll,suggest,delBatch){
	PW.namespace('page.hotelList');
	PW.page.hotelList = function(param){
		new search(param);
		new selectAll(param);
		new suggest(param);
		new delBatch(param);
	}
},{
	requires:['hotelList/search','module/selectAll','hotelList/suggest','hotelList/delBatch']
});
/*------------------------------查询--------------------------------*/
KISSY.add('hotelList/search',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Pagination = PW.mod.Pagination,
		Calendar = PW.mod.Calendar,
		el = {
			searchForm:'.J_searchForm',//指向查询的表单
			searchBtn:'.J_searchBtn'//指向查询按钮
		};

	function search(param){
		this.opts = param;
		this.init();
	}

	S.augment(search,{
		init:function(){
			this._paging({});
			this._addCalendar();
			this._addEventListener();
		},
		/*分页*/
		_paging:function(param){
			var
				that = this,
				opts = that.opts,
				extraParam = S.merge(opts,{extraParam:param});
			Pagination.client(extraParam);
		},
		/*添加日历*/
		_addCalendar:function(){
			Calendar.client({
                renderTo: '.date', //默认只获取第一个
                select: {
                    rangeSelect: false, //是否允许区间选择
                    dateFmt: 'YYYY-MM-DD',
                    showTime: false //是否显示时间
                }
            });
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
		/*表单序列化*/
		_serialize:function(){
			var
				that = this,
				info = {};
			info = DOM.serialize(el.searchForm);
			return info;
		}
	});

	return search;
},{
	requires:['mod/pagination','mod/calendar']
});
/*-------------------------------suggest--------------------------------*/
KISSY.add('hotelList/suggest',function(S){
	var
		suggest = PW.module.suggest;

	function suggest(param){
		new suggest(param);
	}

	return suggest;
},{
	requires:['module/suggest']
});
/*------------------------------批量删除---------------------------------*/
KISSY.add('hotelList/delBatch',function(S){
	var
		$ = S.all, on = S.Event.on,
		HotelHandler = PW.module.reception.hotel,
		el = {
			hotelList:'#J_template',//指向宾馆列表
			delBatchBtn:'.J_delBatch'//指向删除按钮
		};

	function delBatch(param){
		this.opts = param;
		this.init();
	}

	S.augment(delBatch,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击删除按钮*/
			on(el.delBatchBtn,'click',function(evt){
				that._delHotel();
			});
		},
		/*删除宾馆*/
		_delHotel:function(){
			HotelHandler.del(el);
		}
	});

	return delBatch;
},{
	requires:['module/reception/hotel']
});
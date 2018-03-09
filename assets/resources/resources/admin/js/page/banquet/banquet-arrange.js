/*-----------------------------------------------------------------------------
* @Description:     宴请安排页面相关js(banquet-arrange.js)
* @Version:         1.0.0
* @author:          chenke(396985267@qq.com)
* @date             2014.9.25
* ==NOTES:=============================================
* v1.0.0(2014.9.25):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/banquet/banquet-arrange',function(S,suggest,search,selectAll){
	PW.namespace('page.banquetArrange');
	PW.page.banquetArrange = function(param){
		new suggest(param);
		new search(param);
		new selectAll(param);
	}
},{
	requires:['banquetArrange/suggest','banquetArrange/search','module/selectAll']
});
/*---------------------------------suggest------------------------------------*/
KISSY.add('banquetArrange/suggest',function(S){
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
KISSY.add('banquetArrange/search',function(S){
	var
		$ = S.all, DOM = S.DOM, query = DOM.query, on = S.Event.on,
		Calendar = PW.mod.Calendar,
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
	requires:['mod/pagination','mod/calendar']
});

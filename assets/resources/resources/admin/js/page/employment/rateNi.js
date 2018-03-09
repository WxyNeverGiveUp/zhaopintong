/*-----------------------------------------------------------------------------
* @Description: 就业管理--拟就业率 (rateNi.js)
* @Version: 	V1.0.0
* @author: 		xuyihong(597262617@qq.com)
* @date			2015.05.10
* ==NOTES:=============================================
* v2.0.0(2015.05.10):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/employment/rateNi',function(S,search, exportStu){
	PW.namespace('page.student');
	PW.page.rateNi = function(param){
		new search(param);
		new exportStu();
	}
},{
	requires:['rateNi/search','student/export']
});
/*---------------------------------查询------------------------------------*/
KISSY.add('rateNi/search',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,delegate = S.Event.delegate,
		Juicer = PW.mod.Juicer,
		StudentIO = PW.io.employment.student,
		Pagination = PW.mod.Pagination,
		Dialog = PW.mod.Dialog,
		el = {
			searchBtn:'.J_searchBtn',//指向查,按钮
			searchForm:'.J_searchForm',//
			searchCountEl: '.J_searchCount',
			//表头渲染结点
			thRender: '.J_thRender',
			thInfoRender: '.J_thInfoRender',
			//表体渲染结点
			tbodyRender: '#J_template',
			//表头模板
			thTpl: '#tpl-th',
			thInfoTpl: '#tpl-thInfo',
			//表体模板
			tbodyTpl: '#tpl'
		},
		//学生id属性
		DATA_STU = 'data-stu-id',
		//排序属性
		DAtA_ORDERBY = 'data-orderby';
	function search(param){
		this.opts = param;
		this.init();
	}

	S.augment(search,{
		init:function(){
			this._addEventListener();//注册事件
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击查询按钮*/
			on(el.searchBtn,'click',function(){
				// that._search(0, '');
				that._search();
			});
			delegate('#J_template','click',el.delBtn,function(e){
				that._del(e.target);
			});
			delegate(el.thInfoRender, 'click', 'a', function(e){
				that._orderBySearch(e.target);
			});
		},
		// _search:function(orderBy, id){
		_search:function(){
			var
				that = this,
				param = that._serialize();
				// param = that._serialize(orderBy, id);
			StudentIO.searchRateNi(param, function(code, data, errMsg){
				if(code){
					that._renderRateNis(data);
				}else{
					Dialog.alert(errMsg);
				}
			});
			// that._pagination(param);
		},
		_renderRateNis: function(data){
			var
				that = this;
			that._renderTh(data.dimension);
			that._renderTbody(data.dataList);
		},
		_renderTh: function(ths){
			var
				that = this,
				thTpl = DOM.html(el.thTpl),
				thInfoTpl = DOM.html(el.thInfoTpl),
				thHtml = Juicer(thTpl, {data: ths}),
				thInfoHtml = Juicer(thInfoTpl, {data: ths});
			$(el.thInfoRender).empty();
			$('[colspan=3]', el.thRender).remove();
			$(el.thRender).append(thHtml);
			$(el.thInfoRender).append(thInfoHtml);
		},
		_renderTbody: function(data){
			var
				that = this,
				tr = '';
			S.each(data, function(item){
				var 
					tbodyTpl = DOM.html(el.tbodyTpl),
					tbodyHtml = Juicer(tbodyTpl, {list: item.list});
				tr +='<tr><td>'+item.dataName+'</td>'+ tbodyHtml+'</tr>';
			});
			$(el.tbodyRender).empty();
			$(el.tbodyRender).append(tr);
		},
		_orderBySearch: function(target){
			var
				that = this,
				orderBy = DOM.attr(target, DAtA_ORDERBY),
				th = DOM.parent(target, 'th'),
				index = Math.floor((DOM.index(th) + 1) / 2),
				targetEL = $('th', el.thRender)[index],
				id = DOM.attr(targetEL, 'data-id'),
				info = DOM.serialize(el.searchForm);
			S.mix(info, {orderBy: orderBy, id: id});
			StudentIO.searchOrderbyRateNi(info, function(code, data, errMsg){
				if(code){
					that._renderRateNis(data);
				}else{
					Dialog.alert(errMsg);
				}
			});
		},
		_serialize:function(orderBy, id){
			var
				info = {};
			info = DOM.serialize(el.searchForm);
			// S.mix(info, {
			// 	orderBy: orderBy,
			// 	id: id
			// })
			return info;
		}
	});

	return search;
},{
	requires:['mod/dialog','io/employment/student', 'mod/juicer']
});

KISSY.add('student/export', function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		el = {
			exportBtn:'.J_export',
			searchForm:'.J_searchForm'
		};
	function exportStu(){
		this.init();
	}
	S.augment(exportStu, {
		init: function(){
			this.buildEvt();
		},
		/**
		 * 添加事件
		 **/
		buildEvt: function(){
			var
				that = this;
			on(el.exportBtn, 'click', function(e){
				that.exportHandler();
			});
		},
		exportHandler: function(){
			
			var
				that = this,
				info = S.IO.serialize(el.searchForm),
				href = DOM.attr(el.exportBtn, 'href').split('?')[0];
				href = href + '?' + info;
				DOM.attr(el.exportBtn, 'href', href);
		}
	});
	return exportStu;
},{
	requires: ['core']
})
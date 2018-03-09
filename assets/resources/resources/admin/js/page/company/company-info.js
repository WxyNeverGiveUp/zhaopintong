/*-----------------------------------------------------------------------------
* @Description: 单位管理--单位信息管理 (company-info.js)
* @Version: 	V1.0.0
* @author: 		chenke(396985267@qq.com)
* @date			2014.09.23
* ==NOTES:=============================================
* v1.0.0(2014.09.23):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/company-info',function(S,suggest,addCompanyInfo,linkage,checkAll,del){
	PW.namespace('page.companyInfo');
	PW.page.companyInfo = function(param){
		new suggest(param);
		new addCompanyInfo(param);
		new linkage(param);
		new checkAll(param);
		new del(param);
	}
},{
	requires:['recruitList/suggest','companyInfo/addCompanyInfo','module/linkage','module/selectAll','companyInfo/del']
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
/*----------------------------单位信息验证与日期---------------------------------------*/
KISSY.add('companyInfo/addCompanyInfo',function(S){
	var
		$ = S.all, DOM = S.DOM, query = DOM.query, on = S.Event.on,
		Calendar = PW.mod.Calendar,
		Dialog = PW.mod.Dialog,
		Pagination = PW.mod.Pagination,
		el = {
			searchBtn:'.J_searchBtn',//指向查询按钮
			searchForm:'.J_searchForm'//指向查询的表单
		},
		TIME_TIP = '起始时间不能晚于结束时间！';

	function addCompanyInfo(param){
		this.opts = param;
		this.init();
	}

	S.augment(addCompanyInfo,{
		init:function(){
			this._addEventListener();
			this._paging({});
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
				tStartTime = $('.J_typeInStartTime').val(),
				tEndTime = $('.J_typeInEndTime').val(),
				tV = that._validDate(tStartTime,tEndTime),
				inStartTime = $('.J_interviewStartTime').val(),
				inEndTime = $('.J_interviewEndTime').val(),
				inV = that._validDate(inStartTime,inEndTime);
			if(tV&&inV){
				that._paging(info);
			}else{
				Dialog.alert(TIME_TIP);
			}
		},
		/*验证时间，开始时间不能晚于结束时间*/
		_validDate:function(startTime,endTime){
			var
				that = this,
				start,end;
			if(startTime!= '' && endTime!= ''){
		    	start = startTime.split('-');
		    	end = endTime.split('-');
		    	if(parseInt(start[0]) > parseInt(end[0])){
		    		return false;
		    	}else if(parseInt(start[1]) > parseInt(end[1])){
		    		return false;
		    	}else if(parseInt(start[2]) > parseInt(end[2])){
		    		return false;
		    	}else{
		    		return true;
		    	}
		    }else{
		    	return true;
		    }
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

	return addCompanyInfo;
},{
	requires:['mod/calendar','mod/pagination','mod/dialog']
});
/*--------------------------------------------删除-------------------------------------------*/
KISSY.add('companyInfo/del',function(S){
	var
		$ = S.all, on = S.Event.on,
		el = {
			delBtn:'.J_delBatch',//指向删除按钮
			companyList:'#J_template'
		},
		CompanyHandler = PW.module.company.companyInfo;

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
			on(el.delBtn,'click',function(evt){
				that._del();
			});
		},
		_del:function(){
			CompanyHandler.delBatch(el.companyList);
		}
	});

	return del;
},{
	requires:['module/company/companyInfo']
});
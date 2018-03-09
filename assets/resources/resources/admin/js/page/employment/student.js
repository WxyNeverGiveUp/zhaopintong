/*-----------------------------------------------------------------------------
* @Description: 单位管理--单位信息管理 (company-info.js)
* @Version: 	V1.0.0
* @author: 		xuyihong(597262617@qq.com)
* @date			2015.05.10
* ==NOTES:=============================================
* v1.0.0(2014.05.10):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/employment/student',function(S,search, exportStu){
	PW.namespace('page.student');
	PW.page.student = function(param){
		new search(param);
		new exportStu();
	}
},{
	requires:['student/search', 'student/export']
});
/*---------------------------------查询------------------------------------*/
KISSY.add('student/search',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,delegate = S.Event.delegate,
		StudentIO = PW.io.employment.student,
		Pagination = PW.mod.Pagination,
		Dialog = PW.mod.Dialog,
		el = {
			searchBtn:'.J_searchBtn',//指向查,按钮
			searchForm:'.J_searchForm',//
			searchCountEl: '.J_searchCount',
			delBtn:'.J_del'
		},
		TIP = ['确定执行该操作吗？', '删除成功！'],
		//学生id属性
		DATA_STU = 'data-stu-id';
	function search(param){
		this.opts = param;
		this.init();
	}

	S.augment(search,{
		init:function(){
			this._pagination({});
			this._addEventListener();//注册事件
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击查询按钮*/
			on(el.searchBtn,'click',function(){
				that._search();
			});
			delegate('#J_template','click',el.delBtn,function(e){
				that._del(e.target);
			});
		},
		_pagination:function(param){
			var
				that = this,
				opts = that.opts,
				extraParam = S.merge(opts,{
					extraParam:param,
					afterDataLoad: function(me, data, page){
						DOM.html(el.searchCountEl, data.dataCount);
					}
				});
			that.Pagination = Pagination.client(extraParam);
		},
		_search:function(){
			var
				that = this,
				param = that._serialize();

			that.Pagination.reload({extraParam:param});
		},
		_serialize:function(){
			var
				info = {};
			info = DOM.serialize(el.searchForm);
			return info;
		},
		_del: function(e){
			var
				that = this,
				tr = DOM.parent(e, 'tr'),
				sid = DOM.attr(tr, DATA_STU);
			Dialog.confirm(TIP[0],function(){
				StudentIO.del({id: sid}, function(code, data, errMsg){
					if(code){
						that.Pagination.reload();
						Dialog.alert(TIP[1]);
					}else{
						Dialog.alert(errMsg);
					}
				})
			});
		}
	});

	return search;
},{
	requires:['mod/pagination','mod/dialog','io/employment/student']
});

KISSY.add('student/add', function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Juicer = PW.mod.Juicer,
		StudentIO = PW.io.employment.student,
		el = {
			addBtn:'.J_add' //
		};
	function add(){
		this.init();
	}
	S.augment(add, {
		init: function(){
			this.buildEvt();
		},
		/**
		 * 添加事件
		 **/
		buildEvt: function(){
			var
				that = this;
			on(el.addBtn, 'click', function(e){
				that.openDlg();
			});
		},
		openDlg: function(){
			var
				that = this,
				addStuTpl = Juicer(DOM.html('#addTpl'), {});
			PW.mod.Dialog.client({
				title: '添加学生',
				content: addStuTpl,
				footer:{
                    btns:[{
                        text: '确定',
                        clickHandler: function(e,me){
                            var data = DOM.serialize('.J_addStu');
                            StudentIO.add(data, function(code, data, errMsg){
                            	if(code){
                            		window.location.reload();
                            	}else{
                            		Dialog.alert(errMsg);
                            	}
                            })
                        }
                    },
                    {
                        text: '取消',
                        clickHandler: function(e,me){
                            me.close();
                        }
                    }]
                }
			})
		}
	});
	return add;
},{
	requires: ['mod/dialog','mod/juicer','io/employment/student']
})


KISSY.add('student/export', function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		el = {
			exportBtn:'.J_export' ,//
			checkBtn:'.J_check'
		};
	function exportStu(){
		this.init();
	}
	S.augment(exportStu, {
		init: function(){
			S.log('in');
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
			on(el.checkBtn, 'click', function(e){
				that.checkHandler();
			});
		},
		exportHandler: function(){
			S.log(123);
			var
				that = this,
				info = DOM.serialize(el.searchForm),
				href = DOM.attr(el.exportBtn, 'href').split('?')[0];
				href = href + '?' + info;
				DOM.attr(el.exportBtn, 'href', href);
		},
		checkHandler: function(){
			var
				that = this,
				info = DOM.serialize(el.searchForm),
				href = DOM.attr(el.checkBtn, 'href').split('?')[0];
				href = href + '?' + info;
				DOM.attr(el.checkBtn, href);
		}
	});
	return exportStu;
},{
	requires: ['core']
})
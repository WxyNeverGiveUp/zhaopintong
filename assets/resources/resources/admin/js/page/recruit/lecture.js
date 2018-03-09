/*-----------------------------------------------------------------------------
* @Description:     宣讲会相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/recruit/lecture',function(S,search,linkage,suggest,add,visitor){
	PW.namespace('page.lecture');
	PW.page.lecture = function(param){
		new search(param);
		new linkage(param);
		new suggest(param);
		new add(param);
		new visitor(param);
	};
},{
	requires:['lecture/search','module/linkage','lecture/suggest','lecture/add','lecture/visitor']
});
/*---------------------------------查询----------------------------------*/
KISSY.add('lecture/search',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM, query = DOM.query,
		LectureIO = PW.io.recruit.lecture,
		Calendar = PW.mod.Calendar,
		Pagination = PW.mod.Pagination,
		el = {
			weekHolder:'.J_weekHolder',//指向周列表
			palceHolder:'.J_placeHolder',//指向地点的列表
			searchBtn:'.J_searchBtn',//指向查询按钮
			searchForm:'.J_searchForm',//指向查询的表单
			lectureHolder:'#J_lectureHolder',//指向宣讲会
			prevWeekBtn:'.J_prevWeekBtn',//指向上周按钮
			nextWeekBtn:'.J_nextWeekBtn',//指向下周按钮
			weekBtn:'.J_weekBtn',//指向本周按钮
			titleHolder:'.J_titleHolder'//指向标题
		},
		LECTURE_CLASS = 'lecture';
	function search(param){
		this.opts = param;
		this.init();
	}

	S.augment(search,{
		init:function(){
			this._loadLecture(1);
			this._addCalendar();
			this._paging({});
			this._addEventListener();
		},
		_loadLecture:function(param){
			var
				that = this,
				td = $('td',el.lectureHolder),
				week = $('th',el.weekHolder),
				palce = $(el.palceHolder),
				weekList = that._getList(week,0),
				placeList = that._getList(palce,1);
			that._clearList(td);
			LectureIO.load({weekList:weekList,placeList:placeList,weekType:param},function(rs,data,errMsg){
				if(rs){
					that._renderLecture(data,td);
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
		_renderLecture:function(data,td){
			var
				that = this,
				th = $('th',el.weekHolder);
			S.each(data.num,function(i,o){
				$(td[i]).addClass(LECTURE_CLASS);
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
			/*添加查询按钮*/
			on(el.searchBtn,'click',function(evt){
				that._search();
			});
			/*点击上周按钮*/
			on(el.prevWeekBtn,'click',function(evt){
				that._loadLecture(0);
				that._changeTitle(evt.target,0);
			});
			/*点击本周按钮*/
			on(el.weekBtn,'click',function(evt){
				that._loadLecture(1);
				that._changeTitle(evt.target,1);
			});
			/*点击下周按钮*/
			on(el.nextWeekBtn,'click',function(evt){
				that._loadLecture(2);
				that._changeTitle(evt.target,2);
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
				info = {};
			info = DOM.serialize(el.searchForm);
			return info;
		},
		/*改变宣讲会安排的头部*/
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
		}
	});

	return search;
},{
	requires:['io/recruit/lecture','mod/calendar','mod/pagination']
});
/*----------------------------------suggest----------------------------------------*/
KISSY.add('lecture/suggest',function(S){
	var
		suggest = PW.module.suggest;
	function suggest(param){
		new suggest(param);
	}

	return suggest;
},{
	requires:['module/suggest']
});
/*-----------------------------------添加形象大使-------------------------------*/
KISSY.add('lecture/add',function(S){
	var
		$ = S.all, delegate = S.Event.delegate, DOM = S.DOM,
		Dialog = PW.mod.Dialog,
		Defender = PW.mod.Defender,
		LectureIO = PW.io.recruit.lecture,
		el = {
			addFrom:'.J_addFrom',//指向添加形象大使的表单
			addBtn:'.J_add',//指向添加按钮
			idHolder:'.J_idHolder',//指向id
			nameHolder:'.J_nameHolder',//指向名称
			phoneHolder:'.J_phoneHolder'//指向电话
		},
		ADD_HTML = '<form class="J_addFrom">'
						+'<input class="none J_idHolder" autocomplete="off"  type="text" name="recruitId">'
						+'<div class="control-area control-area-short">'
							+'<label>形象大使</label>'
							+'<textarea class="textTheme" name="ambassador"></textarea>'
						+'</div>'
					+'</form>';
	function add(param){
		this.init();
	}

	S.augment(add,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击添加形象大使按钮*/
			delegate(document,'click',el.addBtn,function(evt){
				that._showDialog(evt.currentTarget);
			});
		},
		/*显示弹出层*/
		_showDialog:function(target){
			var
				that = this,
				info = that._serialize(target);
			that.dialogContent = that._createDialogContent();
			that.dialogId = Dialog.open({
				title:'添加形象大使',
				width:860,
				content:that.dialogContent,
				afterOpen:function(){
					that.valid = Defender.client(el.addFrom,{
						theme:'inline',
						showTip:false
					});
					that._fillAddInfo(info);
				},
				footer:{
					btns:[
						{
							bid:1,
							text:'确定',
							clickHandler:function(e,me){
								that._ajaxAdd(me,target);
							}
						},
						{
							bid:2,
							text:'取消',
							clickHandler:function(e,me){
								me.close();
							}
						}
					]
				}
			});
		},
		/*生成对话框内容*/
		_createDialogContent:function(){
			return ADD_HTML;
		},
		/*序列化形象大使信息*/
		_serialize:function(target){
			var
				that = this,
				tr = $(target).parent().parent(),
				td = $('td',tr),
				info = {};
			S.each(td,function(i,o){
				var
					me = i,
					name;
				name = $(me).attr('name');
				if(name != '' && name != null){
					info[name] = $(me).html();
				}
			});
			return info;
		},
		/*填充添加形象大使表单*/
		_fillAddInfo:function(info){
			$(el.nameHolder).val(info.ambassador);
			$(el.idHolder).val(info.id);
		},
		/*使用ajax添加形象大使*/
		_ajaxAdd:function(dialog,target){
			var
				that = this,
				tr = $(target).parent().parent();
				v = that.valid,
				info = that._serializeForm();
			v.validAll();
			if(v.getValidResult('bool')){
				LectureIO.add(info,function(rs,errMsg){
					if(rs){
						$(el.nameHolder,tr).text(info.ambassador);
						dialog.close();
					}else{
						Dialog.alert(errMsg);
					}
				});
			}
		},
		/*表单序列化*/
		_serializeForm:function(){
			var
				info = {};
			info = DOM.serialize(el.addFrom);
			return info;
		}
	});

	return add;
},{
	requires:['mod/dialog','mod/defender','io/recruit/lecture']
});
/*----------------------------------添加来访人------------------------------------*/
KISSY.add('lecture/visitor',function(S,Juicer){
	var
		$ = S.all, delegate = S.Event.delegate, DOM = S.DOM,
		Dialog = PW.mod.Dialog,
		Defender = PW.mod.Defender,
		LectureIO = PW.io.recruit.lecture,
		el = {
			idField:'.J_idField',//指向招聘会id
			visitorBtn:'.visitor',//指向来访人
			visitorHolder:'.J_visitorHolder',//指向来访人
			addBtn:'.J_addVisitor'//指向添加来访人按钮
		},
		DIALOG_HTML = '<form class="clearfix J_addVisitorForm">'
						+'<input class="none J_idHolder" autocomplete="off"  type="text" name="recruitId">'
						+'<div class="control-area control-area-short">'
							+'<label>来访人姓名</label>'
							+'<input name="name" class="textTheme" autocomplete="off" />'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>来访人单位</label>'
							+'<input name="company" class="textTheme" autocomplete="off" type="text" />'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>来访人职务</label>'
							+'<input name="job" class="textTheme J_leaderPostHolder" autocomplete="off" type="text" />'
						+'</div>'
						+'</form>',
		EDIT_DIALOG_HTML = '<form class="clearfix J_EditVisitorForm">'
						+'<input class="none" autocomplete="off"  type="text" name="id" value="&{id}">'
						+'<div class="control-area control-area-short">'
							+'<label>来访人姓名</label>'
							+'<input name="name" class="textTheme" autocomplete="off" value="&{name}" />'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>来访人单位</label>'
							+'<input name="company" class="textTheme" autocomplete="off" type="text" value="&{company}" />'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>来访人职务</label>'
							+'<input name="job" class="textTheme J_leaderPostHolder" autocomplete="off" type="text"  value="&{job}" />'
						+'</div>'
						+'</form>';
		function visitor(param){
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
				delegate(document,'click',el.addBtn,function(evt){
					that._showDialog(evt.currentTarget);
				});
				/*点击来访人修改来访人信息*/
				delegate(document,'click',el.visitorBtn,function(evt){
					that._showEditDialog(evt.currentTarget);
				});
			},
			/*显示弹出层*/
			_showDialog:function(target){
				var
					that = this;
				that.dialogContent = that._createDialogContent();
				that.dialogId = Dialog.open({
					title:'添加来访人',
					content:that.dialogContent,
					afterOpen:function(){
						var
							tr = $(target).parent().parent(),
							id = $(el.idField,tr).html();
						$('.J_idHolder').val(id);
					},
					width:860,
					footer:{
						btns:[
							{
								bid:1,
								text:'确定',
								clickHandler:function(e,me){
									that._ajaxAdd(me,target);
								}
							},
							{
								bid:2,
								text:'取消',
								clickHandler:function(e,me){
									me.close();
								}
							}
						]
					}
				});
			},
			/*显示编辑对话框*/
			_showEditDialog:function(target){
				var
					that = this;
				that.dialogContent = that._createEditDialogContent(target);
				that.dialogId = Dialog.open({
					title:'修改来访人',
					content:that.dialogContent,
					width:860,
					footer:{
						btns:[
							{
								bid:1,
								text:'确定',
								clickHandler:function(e,me){
									that._ajaxEdit(me,target);
								}
							},
							{
								bid:2,
								text:'删除',
								clickHandler:function(e,me){
									that._ajaxDel(me,target);
								}
							},
							{
								bid:3,
								text:'取消',
								clickHandler:function(e,me){
									me.close();
								}
							}
						]
					}
				});
			},
			/*生成弹出层内容*/
			_createDialogContent:function(){
				return DIALOG_HTML;
			},
			/*生成编辑弹出层的内容*/
			_createEditDialogContent:function(target){
				var
					span = $('span',target),
					info = {},name;
				S.each(span,function(i,o){
					name = $(i).attr('name');
					if(name != ''){
						info[name] = $(i).html();
					}
				});

				return Juicer(EDIT_DIALOG_HTML,info);
			},
			/*使用ajax添加来访人*/
			_ajaxAdd:function(dialog,target){
				var
					that = this,
					info  = that._serialize('.J_addVisitorForm');
				LectureIO.addVisitor(info,function(rs,data,errMsg){
					if(rs){
						info.id = data.id;
						that._addVisitor(target,info);
						dialog.close();
					}else{
						Dialog.alert(errMsg);
					}
				});
			},
			/*使用ajax编辑来访人*/
			_ajaxEdit:function(dialog,target){
				var
					that = this,
					info = that._serialize('.J_EditVisitorForm');
				LectureIO.editVisitor(info,function(rs,data,errMsg){
					if(rs){
						that._editVisitor(target,info);
						dialog.close();
					}else{
						Dialog.alert(errMsg);
					}
				});
			},
			/*添加来访人到页面*/
			_addVisitor:function(target,info){
				var
					that = this,
					tr = $(target).parent().parent(),
					visitorHolder = $(el.visitorHolder,tr),
					resourceHtml = $(visitorHolder).html(),
					addHtml = '<a class="visitor" href="javaScript:;" title="编辑"><span name="name">'+info.name+'</span><span class="none" name="job">'+info.job+'</span><span class="none" name="company">'+info.company+'</span><span class="none J_visitorId" name="id">'+info.id+'</span></a>';
				if(resourceHtml == ''){
					$(visitorHolder).html(addHtml);
				}else{
					$(visitorHolder).html(resourceHtml+'<span>，</span>'+addHtml);
				}
			},
			/*修改来访人到页面*/
			_editVisitor:function(target,info){
				var
					that = this,
					html = '<span name="name">'+info.name+'</span><span class="none" name="job">'+info.job+'</span><span class="none" name="company">'+info.company+'</span><span class="none J_visitorId" name="id">'+info.id+'</span>';
				$(target).html(html);
			},
			/*表单序列化*/
			_serialize:function(formName){
				var
					info = DOM.serialize(formName);
				return info;
			},
			/*删除*/
			_ajaxDel:function(dialog,target){
				var
					that = this,
					pre,next,
					id = $('.J_visitorId',target).html();
				Dialog.confirm(
					'确定删除？',
					function(e,me){
						LectureIO.delVisitor({id:id},function(rs,data,errMsg){
							if(rs){
								pre = $(target).prev();
								next = $(target).next();
								if(pre && $(pre).html() == '，'){
									$(pre).remove();
								}else if(next && $(next).html() == '，'){
									$(next).remove();
								}
								$(target).remove();
								Dialog.alert('删除成功！');
								dialog.close();
							}else{
								Dialog.alert(errMsg);
							}
						});
					},
					function(e,me){},
					{
						position:'fixed'
					}
				);
			}
		});

		return visitor;
},{
	requires:['mod/juicer','mod/dialog','io/recruit/lecture']
});
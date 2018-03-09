/*-----------------------------------------------------------------------------
* @Description:     新增笔试信息相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.23
* ==NOTES:=============================================
* v1.0.0(2014.9.22):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/recruit/written-new',function(S,add,del){
	PW.namespace('page.recruit.writtenNew');
	PW.page.recruit.writtenNew = function(param){
		new add(param);
		new del(param);
	};
},{
	requires:['writtenNew/add','writtenNew/del']
});
/*------------------------------新增笔试信息-------------------------------------*/
KISSY.add('writtenNew/add',function(S){
	var
		$ = S.all, DOM = S.DOM, query = DOM.query, on = S.Event.on,
		Calendar = PW.mod.Calendar,
		Defender = PW.mod.Defender,
		Pagination = PW.mod.Pagination,
		Dialog = PW.mod.Dialog,
		WrittenIO = PW.io.recruit.written,
		el = {
			writtenAddFrom:'.J_writtenAddForm',//指向添加面试信息的表单
			timeHolder:'.time',//指向时间的表单
			sureBtn:'.J_sureBtn',//指向确定按钮
			addWrittenBtn:'.J_addWrittenBtn',//指向添加面试时间按钮
			writtenHolder:'.J_writtenHolder',//指向面试信息
			btnHolder:'.J_btnHolder',
			startTime:'.J_startTime',//指向开始时间
			endTime:'.J_endTime',//指向结束时间
			remarkHolder:'.J_remarkHolder'
		},
		ADD_HTML = '<div class="J_writtenAddForm clearfix">'
						+'<div class="control-area control-area-short recruit-area-short">'
							+'<label>笔试地点</label>'
							+'<input class="textTheme" autocomplete="off" type="text" name="place" value=" ">'
						+'</div>'
						+'<div class="control-area control-area-short recruit-area-short">'
							+'<label>面试日期</label>'
							+'<input class="textTheme date" autocomplete="off" type="text" data-valid-rule="notNull" name="date">'
						+'</div>'
						+'<div class="control-area control-area-short recruit-area-short">'
							+'<label>起始时间</label>'
							+'<input class="textTheme time J_startTime" autocomplete="off" placeHolder="例(00:00)" type="text" name="startTime">'
						+'</div>'
						+'<div class="control-area control-area-short recruit-area-short">'
							+'<label>结束时间</label>'
							+'<input class="textTheme time J_endTime" autocomplete="off" placeHolder="例(00:00)" type="text" name="endTime">'
						+'</div>'
						+'<div class="control-area control-area-short recruit-area-short">'
							+'<label>个数</label>'
							+'<input class="textTheme" autocomplete="off" type="text" name="size" value=" ">'
						+'</div>'
						+'<div class="control-area control-area-short recruit-area-short">'
							+'<label>房间号</label>'
							+'<input class="textTheme" autocomplete="off" type="text" name="room" value=" ">'
						+'</div>'
						+'<div class="control-area control-area-short recruit-area-short">'
							+'<label>备用教室</label>'
							+'<input class="textTheme" autocomplete="off" type="text" name="backup" value=" ">'
						+'</div>'
						+'<div class="control-area control-area-short recruit-area-short">'
							+'<label>备注</label>'
							+'<input class="textTheme J_remarkHolder" autocomplete="off" type="text" name="remark" value=" ">'
						+'</div>'
						+'<a href="javaScript:;" class="del-btn J_delWrittenBtn">删除</a>'
					+'</div>';

	function add(param){
		this.opts = param;
		this.init();
	}

	S.augment(add,{
		init:function(){
			this._addEventListener();
			this.valid = Defender.client(el.writtenAddFrom,{
				showTip:false,
				items:[
					{
						queryName:el.timeHolder,
						pattern: function(input,shell,form){
							var 
								val = S.DOM.val(input);
								if(val != '' && /^(([1-9]{1})|([0-1][0-9])|([1-2][0-3])):([0-5][0-9])$/.test(val)){
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
			this._paging();
		},
		_addEventListener:function(){
			var
				that = this;
			that._addCalendar('.date');
			/*点击确定按钮*/
			on(el.sureBtn,'click',function(){
				that._formSubmit();
			});
			/*点击添加笔试时间按钮*/
			on(el.addWrittenBtn,'click',function(evt){
				that._addWritten();
			});
		},
		/*分页*/
		_paging:function(){
			var
				that = this,
				opts = that.opts;
			Pagination.client(opts);
		},
		/*添加日历*/
		_addCalendar:function(holder){
			var
				that = this;
			S.each(query(holder),function(i){
				if(!$(i).hasAttr('data-calendar-id')){
					that.calendar = Calendar.client({
						renderTo: i, //默认只获取第一个
		                select: {
		                    rangeSelect: false, //是否允许区间选择
		                    dateFmt: 'YYYY-MM-DD',
		                    showTime: false //是否显示时间
		                }
					});
				}
			});
		},
		/*表单提交*/
		_formSubmit:function(){
			var
				that = this,
				valid = that.valid;
			valid.refresh();
			valid.validAll();
			if(valid.getValidResult('bool')){
				that._validTime()
			}
		},
		/*添加笔试信息*/
		_addWritten:function(){
			var
				that = this,
				valid = that.valid,
				date;
			$(ADD_HTML).insertBefore(el.btnHolder);
			date = $('.date');
			that._addCalendar(date);
			valid.refresh();
		},
		/*验证开始时间与结束时间*/
		_validTime:function(){
			var
				that = this,
				startTime = $(el.startTime),
				remarkHolder = $(el.remarkHolder),
				endTimeHolder,endTime,end,start,j=0;
			S.each(startTime,function(i,o){
				endTimeHolder = $(i).parent().next();
				endTime = $(el.endTime,endTimeHolder).val();
				end = endTime.split(':');
				start = $(i).val().split(':');
				if(parseInt(start[0]) > parseInt(end[0])){
					j = j+1;
				}else if(parseInt(start[0]) == parseInt(end[0]) && parseInt(start[1]) >= parseInt(end[1])){
					j = j+1;
				}
			});
			if(j > 0){
				Dialog.error('开始时间需早于结束时间！');
			}else{
				S.each(remarkHolder,function(i,o){
					if($(i).val() == ''){
						$(i).val("暂无");
					}
				});
				jQuery(el.writtenAddFrom).submit();
			}
		},
		/*表单序列化*/
		_serialize:function(){
			var
				info = {};
			info = DOM.serialize(el.writtenAddFrom);
			return info;
		}
	});

	return add;
},{
	requires:['mod/calendar','mod/defender','mod/pagination','mod/dialog','io/recruit/written']
});
/*-----------------------------------删除面试信息-------------------------------------*/
KISSY.add('writtenNew/del',function(S){
	var
		$ = S.all, delegate = S.Event.delegate,
		el = {
			delWrittenBtn:'.J_delWrittenBtn'
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
			delegate(document,'click',el.delWrittenBtn,function(evt){
				that._delWritten(evt.currentTarget);
			});
		},
		/*删除笔试信息*/
		_delWritten:function(target){
			var
				that = this,
				writtenInfo = $(target).parent();
			$(writtenInfo).remove();
		}
	});

	return del;
},{
	requires:['core']
});
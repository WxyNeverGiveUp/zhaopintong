/*-----------------------------------------------------------------------------
* @Description:     接待管理部分车辆新增相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.25
* ==NOTES:=============================================
* v1.0.0(2014.9.25):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/reception/vehicle-new',function(S,add){
	PW.namespace('page.reception.vehicleNew');
	PW.page.vehicleNew = function(param){
		new add(param);
	};
},{	
	requires:['vehicleNew/add']
});
/*---------------------------------------添加车辆-----------------------------------*/
KISSY.add('vehicleNew/add',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM, query = DOM.query,
		Defender = PW.mod.Defender,
		Calendar = PW.mod.Calendar,
		el = {
			addForm:'.J_addForm',
			sureBtn:'.J_sureBtn',//指向确定按钮
			useSureBtn:'#J_useSureBtn',
			ambassadorHolder:'.J_ambassadorHolder'
		},
		AMBASSADOR_HTML = '<div class="control-area control-area-short J_ambassadorPhone">'
								+'<label>大使电话</label>'
								+'<input type="text" class="textTheme" data-valid-rule="notNull&isMobile" name="ambassadorCellphone">'
							+'</div>',
		AMBASSADOR_HTML_NO = '<div class="control-area control-area-short J_ambassadorPhone none">'
								+'<label>大使电话</label>'
								+'<input type="text" class="textTheme" name="ambassadorCellphone">'
							+'</div>';

	function add(param){
		this.opts = param;
		this.init();
	}

	S.augment(add,{
		init:function(){
			this.valid = Defender.client('form',{
				showTip:false,
				items:[
					{
						queryName:'.time',
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
			this._addCalendar();
			this._addEventListener();
		},
		/*添加日历*/
		_addCalendar:function(){
			S.each(query('.date'),function(i,o){
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
		_addEventListener:function(){
			var
				that = this,
				radio = $('input',el.ambassadorHolder);
			/*点击确定按钮*/
			on(el.sureBtn,'click',function(evt){
				that._formSubmit();
			});
			/*选择确认使用按钮*/
			on(el.useSureBtn,'change',function(evt){
				that._changeValid(evt.target);
			});
			/*点击是否需要大使按钮*/
			on(radio,'click',function(evt){
				that._showAmbassadorPhone(evt.target);
			});
		},
		/*表单提交*/
		_formSubmit:function(){
			var
				that = this,
				v = that.valid,
				val = $(el.useSureBtn).val();
			if(val == '1' || val == '0'){
				v.validAll();
				if(v.getValidResult('bool')){
					that._removeDisabled();
					jQuery(el.addForm).submit();
				}
			}else{
				jQuery(el.addForm).submit();
			}
		},
		/*去掉disabled属性*/
		_removeDisabled:function(){
			var
				that = this,
				input = $('select,input',el.addForm);
			S.each(input,function(i,o){
				if($(i).hasAttr('disabled')){
					$(i).removeAttr('disabled');
				}
			});
		},
		/*改变验证*/
		_changeValid:function(holder){
			var
				that = this,
				val = $(holder).val(),
				others = $(holder).parent().siblings();
			if(val == '1' || val == '0' || val == '3'){
				S.each(others,function(i,o){
					if(!$(i).hasClass('J_ambassadorPhone')){
						$(i).show();
					}
				});
			}else{
				S.each(others,function(i,o){
					$(i).hide();
				});
			}
		},
		_showAmbassadorPhone:function(target){
			var
				that = this,
				flag = $(target).attr('data-id'),
				v = that.valid;
			if(flag == '1'){
				that._addAmbassadorPhone();
			}else if($(el.ambassadorHolder).next().hasClass('J_ambassadorPhone')){
				$(el.ambassadorHolder).next().remove();
				$(AMBASSADOR_HTML_NO).insertAfter(el.ambassadorHolder);
				v.refresh();
			}
		},
		_addAmbassadorPhone:function(){
			var
				that = this,
				v = that.valid;
			$(el.ambassadorHolder).next().remove();
			$(AMBASSADOR_HTML).insertAfter(el.ambassadorHolder);
			v.refresh();
		}
	});

	return add;
},{
	requires:['mod/calendar','mod/defender','mod/dialog']
});

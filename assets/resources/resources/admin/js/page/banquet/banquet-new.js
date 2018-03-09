/*-----------------------------------------------------------------------------
* @Description:     宴请部分相关js
* @Version:         1.0.0
* @author:          daiql(1649500603@qq.com)
* @date             2014.9.22
* ==NOTES:=============================================
* v1.0.0(2014.9.22):
     初始生成
  v1.0.1(2014.10.29)
  	宴请与不宴请
* ---------------------------------------------------------------------------*/
KISSY.add('page/banquet/banquet-new',function(S,date,select,banquet){
	PW.namespace('page.banquetNew');
	PW.page.banquetNew = function(param){
		new date(param);
		new select(param);
		new banquet(param);
	};
},{
	requires:['banquetNew/date','banquetNew/select','banquetNew/banquet']
});
/*--------------------------------选择宴请日期-------------------------------*/
KISSY.add('banquetNew/date',function(S){
	var 
		query = S.query;
	function date(param){
		this.init();
	}

	S.augment(date,{
		init:function(){
			S.each(query('.date'),function(i){
				Calendar = PW.mod.Calendar;
				var calendar = Calendar.client({
					renderTo: i, //默认只获取第一个
					select: {
						rangeSelect: false, //是否允许区间选择
						dateFmt: 'YYYY-MM-DD',
						showTime: false //是否显示时间
					}
				});
			});
		}
	});
	return date;
},{
	requires:['mod/calendar']
});
/*--------------------------------选择参加人员-------------------------------*/
KISSY.add('banquetNew/select',function(S){
	var
		$ = S.all, on = S.Event.on, 
		Defender = PW.mod.Defender,
		Dialog = PW.mod.Dialog,
		selectHandler = PW.module.banquet.selectCheckbox,
		el = {
			banquetHolder:'.J_banquetHolder',//指向宴请选择的按钮
			hotelHolder:'.J_hotelHolder',//指向宾馆的多选按钮
			selectInfoIdHolder:'.J_selectInfoIdHolder',//指向隐藏的用于放置选择人员id的input
			selectBtn:'.J_selectBtn',//指向选择按钮
			timeHolder:'.time',//指向时间框
			noBanquetRadio:'#J_noBanquetRadio',
			sureBtn:'.J_sureBtn'//指向确定按钮
		};

	function select(param){
		this.opts = param;
		this.init();
	}
	S.augment(select,{
		init:function(){
			this._addEventListener();
			this.valid = Defender.client('.block-form',{
				showTip:false,
				items:[
					{
						queryName:el.timeHolder,
						pattern: function(input,shell,form){
							var 
								val = S.DOM.val(input);
								if(val != '' &&  /^(([1-9]{1})|([0-1][0-9])|([1-2][0-3])):([0-5][0-9])$/.test(val)){
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
		},
		_addEventListener:function(){
			var
				that = this,
				result = true,
				checked;
			/*点击选择按钮*/
			on(el.selectBtn,'click',function(evt){
				that._getPerson(evt.target);
			});
			/*点击确定按钮*/
			on(el.sureBtn,'click',function(evt){
				that._formSubmit();
			});
		},
		_getPerson:function(target){
			var
				attr= $(target).attr('data-attr');
			el.attr = attr,
			el.tar = target;
			selectHandler(el);
		},
		/*验证宾馆*/
		_validHotel:function(){
			var
				that = this,
				checkedBox = $('input',el.hotelHolder),
				checkedhotel = [];
			S.each(checkedBox,function(i,o){
				if($(i).attr('checked') == 'checked'){
					checkedhotel.push(i);
				}
			});
			if(checkedhotel.length > 0){
				return true;
			}else{
				return false;
			}
		},
		/*表单提交*/
		_formSubmit:function(){
			var
				that = this,
				v = that.valid,
				banquet = that._validBaquent();
			if(banquet){
				Dialog.confirm(
					'确定不宴请？',
					function(e,me){
						jQuery('form').submit();
					},
					function(e,me){
						
					},{

					}
				);
			}else{
				v.validAll();
				if(v.getValidResult('bool')){
					result = that._validHotel();
					if(result){
						jQuery('form').submit();
					}else{
						Dialog.error('没有选中任何宾馆！');
					}
				}
			}
		},
		/*验证是否宴请*/
		_validBaquent:function(){
			var
				that = this,
				flag = 0,
				radio = $('input:[type="radio"]',el.banquetHolder);
			S.each(radio,function(i,o){
				if($(i).attr('checked') == 'checked' && $(i).attr('data-id') == '1'){
					flag++;
				}
			});
			if(flag == 1){
				return true;
			}else{
				return false;
			}
		}
	});	

	return select;
},{
	requires:['module/banquet/select-checkbox','mod/defender','mod/dialog']
});
/*------------------------------------宴请--------------------------------------*/
KISSY.add('banquetNew/banquet',function(S){
	var
		$ = S.all, on = S.Event.on,
		BanquetIO = PW.io.banquet.select,
		el = {
			banquetHolder:'.J_banquetHolder',//指向宴请选择的按钮
			banquetInput:'.J_banquetInput'//指向宴请时要填写的表单
		};

	function banquet(param){
		this.init();
	}

	S.augment(banquet,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this,
				radio = $('input:[type="radio"]',el.banquetHolder);
			/*点击宴请选项*/
			on(radio,'click',function(evt){
				that._changeBanquetState(evt.target);
			});
			that._validStatus(radio);
		},
		/*改变宴请表单的状态*/
		_changeBanquetState:function(target){
			var
				that = this,
				flag = $(target).attr('data-id'),
				checked = $(target).attr('checked');
			that._removeDisable();
			if(flag == '1' && checked == 'checked'){
				$(el.banquetInput).hide();
			}else{
				$(el.banquetInput).show();
				if(flag == '2' && checked == 'checked'){
					that._selectInput();
					that._getUser();
				}
			}
		},
		/*当选择的是盒饭的时候*/
		_selectInput:function(){
			var
				that = this,
				item = $('input','.J_selectHolder'),
				flag;
			S.each(item,function(i,o){
				flag = $(i).attr('data-id');
				if(flag == '0'){
					$(i).attr('checked','checked');
				}else{
					$(i).attr('disabled','disabled');
					$(i).removeAttr('checked','checked');
				}
			});
		},
		/*清除disabled属性*/
		_removeDisable:function(){
			var
				that = this,
				item = $('input','.J_selectHolder');
			$('.J_userHolder').val('');
			S.each(item,function(i,o){
				$(i).removeAttr('disabled','disabled');
			});
		},
		/*获取盒饭负责人*/
		_getUser:function(){
			var
				that = this,
				userHolder = $('.J_userHolder');
			BanquetIO.getUser({roleId:5},function(rs,data,errorMes){
				if(rs){
					S.each(userHolder,function(i,o){
						$(i).val(data.name);
						$(i).parent().next().next().children("input").val(data.id);
					});
				}else{
					S.log(errorMes);
				}
			});
		},
		/*验证宴请*/
		_validStatus:function(radio){
			var
				that = this,
				status;
			S.each(radio,function(i,o){
				if($(i).attr('checked') == 'checked'){
					status = $(i).attr('data-id');
					if(status == '1'){
						$(el.banquetInput).hide();
					}else if(status == '2'){
						that._selectInput();
						that._getUser();
					}
				}
			});
		}
	});

	return banquet;
},{	
	requires:['io/banquet/select-checkbox','core']
});
/*-----------------------------------------------------------------------------
* @Description:     接待管理部分宾馆新增相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.25
* ==NOTES:=============================================
* v1.0.0(2014.9.25):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/reception/hotel-mod',function(S,info,result){
	PW.namespace('page.hotelMod');
	PW.page.hotelMod = function(param){
		new info(param);
		new result(param);
	};
},{
	requires:['hotelMod/info','hotelMod/result']
});
/*--------------------------------------宾馆信息-------------------------------*/
KISSY.add('hotelMod/info',function(S){
	var
		$ = S.all, on = S.Event.on,
		Calendar = PW.mod.Calendar,
		Defender = PW.mod.Defender,
		Dialog = PW.mod.Dialog,
		el = {
			infoForm:'.J_hotelInfoForm',//指向宾馆信息表单
			infoBtn:'.J_hotelInfoBtn'//指向确定按钮
		};

	function info(param){
		this.opts = param;
		this.init();
	}

	S.augment(info,{
		init:function(){
			this.valid = Defender.client(el.infoForm,{
				showTip:false
			});
			this._addCalendar();
			this._addEventListener();
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
			/*点击确定按钮*/
			on(el.infoBtn,'click',function(evt){
				that._formSubmit();
			});
		},
		/*表单提交*/
		_formSubmit:function(){
			var
				that = this,
				v = that.valid,
				result;
			v.validAll();
			if(v.getValidResult('bool')){
				that._validRoom();
			}
		},
		/*验证是否选择房间,且所选房间的房间号必须为数字*/
		_validRoom:function(){
			var
				that = this,
				roomCheck = $('input:[type="checkbox"]',el.infoForm),
				roomChecked = [],num = 0,roomNumInput,roomNum;
			S.each(roomCheck,function(i,o){
				if($(i).attr('checked') == 'checked'){
					roomChecked.push(i);
				}
			});
			if(roomChecked.length == 0){
				Dialog.alert('所选房间为空！');
			}else{
				S.each(roomChecked,function(i,o){
					roomNumInput = $(i).next();
					roomNum = $(roomNumInput).val();
					if(roomNum == '' || /^\d+$/.test(roomNum) == false){
						num++;
					}
				});
				if(num > 0){
					Dialog.alert('所选房间的，房间数量为空或是不为数字！')
				}else{
					jQuery(el.infoForm).submit();
				}
			}
		}
	});

	return info;
},{
	requires:['mod/defender','mod/calendar','mod/dialog']
});
/*------------------------------结果页--------------------------------*/
KISSY.add('hotelMod/result',function(S){
	var
		$ = S.all, on = S.Event.on,
		Defender = PW.mod.Defender,
		el = {
			resultBtn:'.J_resultBtn',
			resultForm:'.J_resultForm'
		};

	function result(param){
		this.init();
		this.valid = Defender.client(el.resultForm,{
			showTip:false
		});
	}

	S.augment(result,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击确定按钮*/
			on(el.resultBtn,'click',function(evt){
				that._formSubmit();
			});
		},
		_formSubmit:function(){
			var
				that = this,
				v = that.valid;
			v.validAll();
			if(v.getValidResult('bool')){
				jQuery(el.resultForm).submit();
			}
		}
	});

	return result;
},{
	requires:['mod/defender','thirdparty/jquery']
});
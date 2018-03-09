/*-----------------------------------------------------------------------------
* @Description:     编辑教育经历页面相关js
* @Version:         1.0.0
* @author:          daiqiaoling
* @date             2015.7.9
* ==NOTES:=============================================
* v1.0.0(2015.7.9):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/education' , function(S,submit){
	PW.namespace('page.job_apply.education');
	PW.page.job_apply.education = function(param){
		new submit(param);
	}
},{
	requires:['education/submit']
});
/*---------------------------------表单提交---------------------------------*/
KISSY.add('education/submit',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Defender = PW.mod.Defender,
		Dialog = PW.mod.Dialog,
		MyHomeIO = PW.io.job_apply.my_home,
		el = {
			J_Form:'.info-form', //指向表单
			J_Save:'.save',//指向保存按钮
			J_Major : '.J_Major',
			J_Sub : '.J_Sub',
			J_start_year : '#start-year',
			J_end_year : '#end-year',
			J_start_month : '#start-month',
			J_end_month : '#end-month'
		};

	function submit(param){
		this.opts = param;
		this.init();
	}

	S.augment(submit,{
		init:function(){
			this._valid();
			this._addEventListener();
			this._setUnSelectedYear();
			// this._setUnSelectedMonth();
		},
		_valid: function(){
			this.valid = Defender.client(el.J_Form,{
				showTip: false
			});
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击保存按钮*/
			on(el.J_Save,'click',function(ev){
				that._formSubmit(ev);
			});	

			$(el.J_Major).on('change',function(ev){
				var
					id = $(ev.currentTarget).children('option:selected').val();
				if(id != 0)
					that._getMajor(false,id);
			});

			$(el.J_start_year).on('change',function(ev){
				var
					year = parseInt($(ev.currentTarget).children('option:selected').val(),10),
					endYearList = $(el.J_end_year).children('option');
				S.each(endYearList , function(i , o){
					$(i).removeAttr('disabled');
					if($(i).val() < year){
						$(i).attr('disabled','disabled');
					}
				})
			});

			$(el.J_start_month).on('change',function(ev){
				var
					startYear = parseInt($(el.J_start_year).children('option:selected').val(),10),
					endYear = parseInt($(el.J_end_year).children('option:selected').val(),10),
					startMonth = parseInt($(ev.currentTarget).children('option:selected').val(),10),
					endMonthList = $(el.J_end_month).children('option');
				S.each(endMonthList , function(i , o){
					$(i).removeAttr('disabled');
					if(startYear == endYear){
						if($(i).val() < startMonth){
							$(i).attr('disabled','disabled');
						}
					}
				})
			});

			$(el.J_end_year).on('change',function(ev){
				var
					startYear = parseInt($(el.J_start_year).children('option:selected').val(),10),
					endYear = parseInt($(el.J_end_year).children('option:selected').val(),10),
					startMonth = parseInt($(el.J_start_month).children('option:selected').val(),10),
					endMonthList = $(el.J_end_month).children('option');
				S.each(endMonthList , function(i , o){
					$(i).removeAttr('disabled','disabled');
					if(startYear == endYear){
						if($(i).val() < startMonth){
							$(i).attr('disabled','disabled');
						}
					}
				})
			});			
		},		
		
		/*表单提交*/
		_formSubmit:function(ev){
			var
				startMonth = parseInt($(el.J_start_month).children('option:selected').val(),10),
				startYear = parseInt($(el.J_start_year).children('option:selected').val(),10),
				endMonth = parseInt($(el.J_end_month).children('option:selected').val(),10),
				endYear = parseInt($(el.J_end_year).children('option:selected').val(),10),
				that = this,
				valid = that.valid,
				opts = that.opts;
			
			valid.validAll(function(rs){
				if(rs){
					if(startYear > endYear){
						ev.preventDefault();
						Dialog.alert("开始年份不能大于结束年份");
					}
					else if(startYear == endYear){
						if(startMonth > endMonth){
							ev.preventDefault();
							Dialog.alert("开始月份不能大于结合月份");
						}
					}
					else{
						jQuery(el.J_Form).submit();
						Dialog.alert("保存成功！");
					}
				}else{
					Dialog.alert("请重新确认信息是否无误！");
				}
			});
		},

		// _setInfo:function(){
		// 	var
		// 		that = this;
		// 	MyHomeIO.isEduEdited({},function(rs,data,msg){
		// 		if(rs){
		// 			that._getMajor(false,data.majorId);
		// 			$(el.J_Major).children('option').item(data.majorId).attr('selected','selected');
		// 			S.available('#J_Sub_Major',function(){
		// 				$(el.J_Sub).children('option').item(data.subId).attr('selected','selected');
		// 			})
		// 		}
		// 	})
		// },

		//获取专业
		_getMajor:function(isMajor,id){
				var
					i = 0,
					para,
					optionHtml;
				if(isMajor){
					para = {}
					$(el.J_Major).html('');
				}
				else{
					para = 'id='+id;
					$(el.J_Sub).html('');
				}
				MyHomeIO.getMajor(para,function(rs,data,msg){
					S.each(data,function(item){
						if(i < data.length - 1){
							optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
							if(isMajor){	
								$(el.J_Major).append(optionHtml);
							}
							else{
								$(el.J_Sub).append(optionHtml);
							}
						}
						else{
							optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
							lastCityOptionHtml = '<option id="J_Sub_Major" value='+item.id+'>'+item.name+'</option>';
							if(isMajor){	
								$(el.J_Major).append(optionHtml);
							}
							else{
								$(el.J_Sub).append(lastCityOptionHtml);
							}
						}
						i ++;
					})
					if(isMajor)
						$(el.J_Major).prepend('<option value=0>请选择</option>');
					else
						$(el.J_Sub).prepend('<option value=0>请选择</option>');
				})
		},

		//当页面为编辑页面时，根据start-year的值，设置end-year的可选项
		_setUnSelectedYear:function(){
			var 
				startYear = $(el.J_start_year).children('option:selected').val(),
				endYearList = $(el.J_end_year).children('option');
			S.each(endYearList , function(i , o){
				$(i).removeAttr('disabled');
				if($(i).val() < startYear){
					$(i).attr('disabled','disabled');
				}
			})
		}
		// //当页面为编辑页面时，当开始年份等于结束年份时，结束月份不可选。
		// _setUnSelectedMonth:function(){
		// 	var
		// 		startYear = $(el.J_start_year).children('option:selected').val(),
		// 		endYear = $(el.J_end_year).children('option:selected').val(),
		// 		startMonth = $(el.J_start_month).children('option:selected').val(),
		// 		endMonthList = $(el.J_end_month).children('option');
		// 	S.each(endMonthList , function(i , o){
		// 		if(startYear == endYear){
		// 			$(i).attr('disabled','disabled');
		// 		}
		// 	})
		// }		
	});

	return submit;
},{
	requires:['mod/defender','mod/dialog','io/job_apply/my_home']
});
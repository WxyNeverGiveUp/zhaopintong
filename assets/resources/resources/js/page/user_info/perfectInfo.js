/*-----------------------------------------------------------------------------
* @DescriSion: 完善信息相关js
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.09.01
* ==NOTES:=============================================
* v1.0.0(2015.09.01):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/user_info/perfectInfo',function(S,perfectInfoShow){
	PW.namespace('page.user_info.perfectInfo');
	PW.page.user_info.perfectInfo = function(){
		new perfectInfoShow();
	}
},{
	requires:['perfectInfo/perfectInfoShow']
})

/* ---------------------------------------------------------------------------*/
KISSY.add('perfectInfo/perfectInfoShow',function(S){
	var 
		$ = S.all,
		on = S.Event.on,
		Defender = PW.mod.Defender,
		userIO = PW.io.user.user,
		el = {
			J_form : '.complete-form',
			J_location_province : '.location-province',
			J_location_city : '.location-city',
			J_main_major : '.main-major',
			J_sub_major : '.sub-major',
			J_school_input : '.school-input',
			J_close_input : '.close-input',
			J_all_school : '.all-school',
			J_submit_complete_form : '.submit-complete-form',
			J_start_year : '.start-year',
			J_end_year : '.end-year'
		}

	function perfectInfoShow(){
		this.init();
		this._addEventListener();
	}

	S.augment(perfectInfoShow , {
		init:function(){
			this.valid = Defender.client(el.J_form , {
				showTip:false
			});
		},

		_addEventListener:function(){
			$(el.J_location_province).on('change',function(ev){
				var provinceId = $(ev.currentTarget).children('option:selected').val();
				userIO.getCity({id:provinceId} , function(rs,data,msg){
					if(rs){
						var optionHtml;
						$(el.J_location_city).html(' ');
						S.each(data,function(item){
							optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
							$(el.J_location_city).append(optionHtml);
						})
						optionHtml = '<option value="0">请选择所在市</option>';
						$(el.J_location_city).prepend(optionHtml);
					}
				})
			});

			$(el.J_main_major).on('change',function(ev){
				var majorId = $(ev.currentTarget).children('option:selected').val();
				userIO.getMajor({majorId:majorId} , function(rs,data,msg){
					if(rs){
						var optionHtml;
						$(el.J_sub_major).html(' ');
						S.each(data,function(item){
							optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
							$(el.J_sub_major).append(optionHtml);
						})
						optionHtml = '<option value="0">请选择</option>';
						$(el.J_location_city).prepend(optionHtml);
					}
				})
			});

			$(el.J_all_school).on('change',function(ev){
				var schoolId = $(ev.currentTarget).children('option:selected').val();
				if(schoolId == 'other'){
					$(el.J_school_input).show();
					$(el.J_all_school).hide();
					$(el.J_close_input).show();
					$(el.J_all_school).children('option').item(28).attr('selected',"selected");
				}
			});

			$(el.J_school_input).on('click',function(ev){
				$(ev.currentTarget).val('');
			})

			$(el.J_close_input).on('click',function(ev){
				$(el.J_school_input).hide();
				$(el.J_all_school).show();
				$(el.J_close_input).hide();
				$(el.J_school_input).val('请输入您的学校');
				$(el.J_all_school).children('option').item(0).attr('selected',"selected");
			});

			// $(el.J_submit_complete_form).on('click',function(ev){
			// 	ev.preventDefault();
			// 	if($(el.J_school_input).val().length != 0){
			// 		document.getElementById('J_complete_form').submit();
			// 	}
			// });

			$(el.J_start_year).on('change',function(ev){
				var 
					startYear = $(ev.currentTarget).children('option:selected').val(),
					endYearList = $(el.J_end_year).children('option');
				S.each(endYearList , function(i , o){
					$(i).removeAttr('disabled');
					if($(i).val() <= startYear){
						$(i).attr('disabled','disabled');
					}
				})

			})
		}
	})
	
	return perfectInfoShow;
},{
	requires:['event','mod/defender','io/user/user']
})
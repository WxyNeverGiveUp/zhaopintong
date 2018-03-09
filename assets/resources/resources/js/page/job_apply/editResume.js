/*-----------------------------------------------------------------------------
* @DescriSion: edit-resume页面相关js
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.07.21
* ==NOTES:=============================================
* v1.0.0(2015.07.21):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/editResume',function(S,basicInfo,contactInfo,educationExperience,
												 certificate,schoolPosition,schoolAward,
												 language,internship,itSkill,work ,project ,
												 train ,apply,attachment ,editedInfo){
	PW.namespace('page.job_apply.editResume');
	PW.page.job_apply.editResume = function(){
		new editedInfo();
		new basicInfo();
		new contactInfo();
		new educationExperience();
		new certificate();
		new schoolPosition();
		new schoolAward();
		new language();
		new internship();
		new itSkill();
		new work();
		new project();
		new train();
		new apply();
		new attachment();
	}
},{
	requires:['editResume/basicInfo','editResume/contactInfo','editResume/educationExperience',
	'editResume/certificate','editResume/schoolPosition','editResume/schoolAward' ,'editResume/itSkill', 
	'editResume/language','editResume/internship','editResume/work','editResume/project',
	'editResume/train' , 'editResume/apply' , 'editResume/attachment' , 'editResume/editedInfo']
})

/*----------------------------------编辑基本信息-------------------------------*/
KISSY.add('editResume/basicInfo',function(S){
	var
		$ = S.all,
		editResumeIO = PW.io.job_apply.editResume,
		el = {
			J_edit_basic : '.J_edit_basic',
			J_editing_basic_info : '.editing-basic-info',
			J_edited_basic_info : '.edited-basic-info',
			J_no_basic_info : '.no-basic-info',
			J_cancel_basic : '.cancel-basic',
			J_name : '.name',
			J_live_province : '.live-province',
			J_live_city : '.live-city',
			J_hukou_province : '.hukou-province',
			J_hukou_city : '.hukou-city',
			J_birth_year : '.birth-year',
			J_birth_month : '.birth-month',
			J_birth_day : '.birth-day',
			J_work_experience : '.work-experience',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index'
		},
		Defender = PW.mod.Defender,
		on = S.Event.on;
	function basicInfo(){
		this.init();
	}

	S.augment(basicInfo,{
		init:function(){
			this._addEventListener();
			this.valid = Defender.client(el.J_editing_basic_info,{
				showTip:false
			});
			// this._getCity(true,0,true);
			// this._getCity(true,0,false);
			// this._getYear();
		},

		_addEventListener:function(){
			var
				that = this;
			$(el.J_edit_basic).on('click',function(ev){
				that._setExistedInfo();
				$(el.J_editing_basic_info).show();
				$(el.J_pop_wrap).show();
				$(el.J_editing_basic_info).addClass(el.J_z_index);
			});

			$(el.J_cancel_basic).on('click',function(ev){
				$(el.J_editing_basic_info).hide();
				$(el.J_pop_wrap).hide();
				$(el.J_editing_basic_info).removeClass(el.J_z_index);
			});

			$(el.J_birth_month).on('change',function(ev){
				var
					year = $(el.J_birth_year).children('option:selected').text(),
					month = $(ev.currentTarget).children('option:selected').text();
				that._getDay(year,month);
			});

			$(el.J_birth_year).on('change',function(ev){
				var
					year = $(el.J_birth_year).children('option:selected').text(),
					month = $(ev.currentTarget).children('option:selected').text();
				that._getDay(year,month);
			});

			$(el.J_live_province).on('change',function(ev){
				var
					id = $(ev.currentTarget).children('option:selected').val();
				that._getCity(false,id,false);
			})

			$(el.J_hukou_province).on('change',function(ev){
				var
					id = $(ev.currentTarget).children('option:selected').val();
				that._getCity(false,id,true);
			})
		},

		_setExistedInfo:function(){
			var
				nowYear = new Date().getFullYear(),
				that = this;
			editResumeIO.getBasicInfo({},function(rs,data,msg){
				$(el.J_name).val(data.name);
				$(el.J_hukou_city).children().item(data.hukouCity).attr('selected','selected');
				$(el.J_live_city).children().item(data.liveCity).attr('selected','selected');
				$(el.J_hukou_province).children().item(data.hukouProvince).attr('selected','selected');
				$(el.J_live_province).children().item(data.liveProvince).attr('selected','selected');
				S.each($(el.J_birth_year).children('option') , function(i,o){
					if($(i).val() == data.birthYear){
						$(i).attr('selected','selected')
					}
				})
				S.each($(el.J_birth_month).children('option') , function(i,o){
					if($(i).val() == data.birthMonth){
						$(i).attr('selected','selected')
					}
				})
				S.each($(el.J_birth_day).children('option') , function(i,o){
					if($(i).val() == data.birthDay){
						$(i).attr('selected','selected')
					}
				})
				$(el.J_work_experience).children().item(data.workExperience).attr('selected','selected');
			})
		},

		// _getYear:function(){
		// 	editResumeIO.getYear({},function(rs,data,msg){
		// 		$(el.J_birth_year).html('');
		// 		S.each(data,function(item){
		// 			var
		// 				optionHtml = '<option>'+item+'</option>';
		// 			$(el.J_birth_year).append(optionHtml);
		// 		})
		// 	})
		// },

		_getCity:function(isProvince,id,isHokou){
			var
				i = 0,
				para,
				lastOptionHtml,
				optionHtml;
			if(isProvince){
				para = {};
				if(isHokou){
					$(el.J_live_province).html('');
					$(el.J_live_province).append('<option value=0>请选择</option>');
				}
				else{
					$(el.J_hukou_province).html('');
					$(el.J_hukou_province).append('<option value=0>请选择</option>');
				}
			}
			else{
				if(isHokou){
					para = 'id='+id;
					$(el.J_hukou_city).html('');
					$(el.J_hukou_city).append('<option value=0>请选择</option>');
				}
				else{
					para = 'id='+id;
					$(el.J_live_city).html('');
					$(el.J_live_city).append('<option value=0>请选择</option>')
				}
			}
			
			editResumeIO.getCity(para,function(rs,data,msg){
				S.each(data,function(item){
					optionHtml = '<option value="'+item.name+'"CId="'+item.id+'"">'+item.name+'</option>';
					lastHukouOptionHtml = '<option id="J_HuKou_City" value="'+item.name+'"CId="'+item.id+'"">'+item.name+'</option>';
					lastLiveOptionHtml = '<option id="J_Live_City" value="'+item.name+'"CId="'+item.id+'"">'+item.name+'</option>';
					i ++;
					if(isProvince){	
						if(isHokou){
							if(i<data.length)
								$(el.J_hukou_province).append(optionHtml);
							else
								$(el.J_hukou_province).append(lastOptionHtml);
						}
						else{
							if(i<data.length)
								$(el.J_live_province).append(optionHtml);
							else
								$(el.J_live_province).append(lastOptionHtml);
						}
					}
					else{
						if(isHokou){
							if(i<data.length)
								$(el.J_hukou_city).append(optionHtml);
							else
								$(el.J_hukou_city).append(lastHukouOptionHtml);
						}
						else{
							if(i<data.length)
								$(el.J_live_city).append(optionHtml);
							else
								$(el.J_live_city).append(lastLiveOptionHtml);
						}
					}
				})
			})
		},

		_getDay:function(year,month){
			var
				i = 0,
				optionHtml,
				para = 'month='+month+'&year='+year;
			editResumeIO.getDay(para,function(rs,data,msg){
				$(el.J_birth_day).html('');
				S.each(data,function(item){
					i ++;
					if(i < data.length)
						optionHtml = '<option>'+item+'</option>';
					else
						optionHtml = '<option id="J_Day">'+item+'</option>';
					$(el.J_birth_day).append(optionHtml);
				})
			})
		}
	})
	return basicInfo;
},{
	requires:['core','mod/defender','io/job_apply/editResume']
})

/*---------------------------------编辑联系信息-------------------------------------*/
KISSY.add('editResume/contactInfo',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		Defender = PW.mod.Defender,
		editResumeIO = PW.io.job_apply.editResume,
		el = {
			J_edit_contact : '.J_edit_contact',
			J_editing_contact_info : '.editing-contact-info',
			J_edited_contact_info : '.edited-contact-info',
			J_no_contact_info : '.no-contact-info',
			J_cancel_contact : '.cancel-contact',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index',
			J_contact_phone : '.contact-phone',
			J_contact_email : '.contact-email'
		};

	function contactInfo(){
		this.init();
	}

	S.augment(contactInfo , {
		init:function(){
			this._addEventListener();
			this.valid = Defender.client(el.J_editing_contact_info,{
				showTip:true
			});
		},

		_addEventListener:function(){
			var
				that = this;
			$(el.J_edit_contact).on('click',function(ev){
				$(el.J_editing_contact_info).show();
				$(el.J_pop_wrap).show();
				$(el.J_editing_contact_info).addClass(el.J_z_index);
				that._setExistedInfo();
			});

			$(el.J_cancel_contact).on('click',function(ev){
				$(el.J_editing_contact_info).hide();
				$(el.J_pop_wrap).hide();
				$(el.J_editing_contact_info).removeClass(el.J_z_index);
			})
		},

		_setExistedInfo:function(){
			editResumeIO.getContactInfo({},function(rs,data,msg){
				$(el.J_contact_phone).val(data.phone);
				$(el.J_contact_email).val(data.email);
			})
		}
	});
	return contactInfo;
},{
	requires:['core','mod/defender','io/job_apply/editResume']
})

/*-------------------------------编辑教育经历-------------------------------------*/
KISSY.add('editResume/educationExperience',function(S){
	var
		$ = S.all
		on = S.Event.on,
		Defender = PW.mod.Defender,
		Dialog = PW.mod.Dialog,
		MyHomeIO = PW.io.job_apply.my_home,
		editResumeIO = PW.io.job_apply.editResume,
		el = {
			J_editing_education_experience : '.editing-education-experience',
			J_editing_education_experience_out : '.editing-education-experience-out',
			J_edit_education_experience : '.edit-education-experience',
			J_insert_tr : '.insert-tr',
			J_edu_start_year : '.edu-start-year',
			J_edu_end_year : '.edu-end-year',
			J_edu_major : '.edu-major',
			J_edu_cancel : '.edu-cancel',
			J_del_edu_experience : '.delete-education-experience',
			J_add_education : '.J_add_education',
			J_no_edu_info : '.no-edu-info',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index',
			J_school : '.edu-school',
			J_edu_start_month : '.edu-start-month',
			J_edu_end_month : '.edu-end-month',
			J_major_name : '.major-name',
			J_degree : '.degree-in',
			J_GPA : '.GPA',
			J_ranking : '.ranking',
			J_major_description : '.major-description',
			J_edu_input : '.edu-input',
			J_Major : '.J_Major',
			J_Sub : '.J_Sub',
			J_submit_in : '.edu-submit-in',
			J_submit_out : '.edu-submit-out',
			J_added_experience_detail : '.added-experience-detail',
			J_edu_check : '.edu-check',
			J_check_hook : '.check-hook'

		};

	function educationExperience(){
		this.init();
	}

	S.augment(educationExperience,{
		init:function(){
			this._addEventListener();
			this.validIn = Defender.client(el.J_editing_education_experience,{
				showTip:false
			});
			this.validOut = Defender.client(el.J_editing_education_experience_out,{
				showTip:false
			});
		},

		_addEventListener:function(){
			var
				that = this;
			$(el.J_edit_education_experience).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id');
				that._setExistedInfo(id);
				$(el.J_insert_tr).show();
				$(el.J_insert_tr).addClass(el.J_z_index);
				$(el.J_edu_input).attr('value',id);
				$(el.J_pop_wrap).show();
			});

			$(el.J_edu_cancel).on('click',function(ev){
				$(el.J_insert_tr).hide();
				$(el.J_pop_wrap).hide();
				$(el.J_insert_tr).removeClass(el.J_z_index);
				$(el.J_editing_education_experience_out).removeClass(el.J_z_index);
				$(el.J_editing_education_experience_out).hide();
				$(el.J_edu_input).removeAttr('value');
				$(el.J_Sub).html('');
			});

			$(el.J_del_edu_experience).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id'),
					para = 'id='+id;
				editResumeIO.delEduExperience(para,function(rs,data,msg){
					if(rs){
						$(ev.currentTarget).parent().parent().remove();
						if($(el.J_added_experience_detail).children('tbody').children('tr').length == 1){
							$(el.J_added_experience_detail).remove();
							$(el.J_no_edu_info).show();
							$(el.J_edu_check).children('em').removeClass(el.J_check_hook);
							$(el.J_edu_check).children('em').text('未添加');
						}
					}
					else{
						alert(msg);
					}
				})
			});

			$(el.J_add_education).on('click',function(ev){
				$(el.J_editing_education_experience_out).show();
				$(el.J_editing_education_experience_out).addClass(el.J_z_index);
				$(el.J_pop_wrap).show();
			});

			$(el.J_Major).on('change',function(ev){
				var
					id = $(ev.currentTarget).children('option:selected').val();
				if(id != 0)
					that._getMajor(false,id);
			});

			$(el.J_submit_in).on('click',function(ev){
				that._formSubmit(ev , that.validIn ,'in');
			});

			$(el.J_submit_out).on('click',function(ev){
				that._formSubmit(ev , that.validOut ,'out');
			});			
		},

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
					if(isMajor)
						$(el.J_Major).append('<option value=0>请选择</option>');
					else
						$(el.J_Sub).append('<option value=0>请选择</option>');
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
							lastOptionHtml = '<option id="J_Sub_Major" value='+item.id+'>'+item.name+'</option>';
							if(isMajor){	
								$(el.J_Major).append(optionHtml);
							}
							else{
								$(el.J_Sub).append(lastOptionHtml);
							}
						}
						i ++;
					})
				})
		},

		//当用户点击编辑时，表单中的值为用户之前已输入的值
		_setExistedInfo:function(id){
			var
				that = this,
				nowYear = new Date().getFullYear(),
				para = 'id='+id;
			editResumeIO.getEduInfo(para,function(rs,data,msg){
				if(rs){
					that._getMajor(false,data.majorId);
					S.each($(el.J_edu_start_year).item(0).children('option') , function(i,o){
						if($(i).val() == data.startYear){
							$(i).attr('selected','selected');
						}
					});
					S.each($(el.J_edu_end_year).item(0).children('option') , function(i,o){
						if($(i).val() == data.endYear){
							$(i).attr('selected','selected');
						}
					});
					$(el.J_edu_start_month).item(0).children().item(data.startMonth-1).attr('selected','selected');
					$(el.J_edu_end_month).item(0).children().item(data.endMonth-1).attr('selected','selected');
					$(el.J_school).item(0).val(data.school);
					$(el.J_major_name).item(0).val(data.majorName);
					$(el.J_degree).item(0).children('option').item(data.degree).attr('selected','selected');
					$(el.J_GPA).item(0).val(data.GPA);
					$(el.J_ranking).item(0).children().item(data.ranking).attr('selected','selected');
					$(el.J_major_description).item(0).val(data.majorDescription);
					$(el.J_Major).item(0).children('option').item(data.majorId).attr('selected','selected');
					S.available('#J_Sub_Major',function(){
						$(el.J_Sub).item(0).children('option').item(data.subId).attr('selected','selected');
					})
				}
			})
		},

		_formSubmit:function(ev,valid,which){
			var 
				that = this,
				startYear,
				startMonth,
				endYear,
				endMonth;

			if(which == 'in'){
				startYear = parseInt($(el.J_edu_start_year).item(0).children('option:selected').val(),10);
				startMonth = parseInt($(el.J_edu_start_month).item(0).children('option:selected').val(),10);
				endYear = parseInt($(el.J_edu_end_year).item(0).children('option:selected').val(),10);
				endMonth = parseInt($(el.J_edu_end_month).item(0).children('option:selected').val(),10);
			}
			if(which == 'out'){
				if($(el.J_added_experience_detail).length == 0){
					startYear = parseInt($(el.J_edu_start_year).item(0).children('option:selected').val(),10);
					startMonth = parseInt($(el.J_edu_start_month).item(0).children('option:selected').val(),10);
					endYear = parseInt($(el.J_edu_end_year).item(0).children('option:selected').val(),10);
					endMonth = parseInt($(el.J_edu_end_month).item(0).children('option:selected').val(),10);
				}
				else{
					startYear = parseInt($(el.J_edu_start_year).item(1).children('option:selected').val(),10);
					startMonth = parseInt($(el.J_edu_start_month).item(1).children('option:selected').val(),10);
					endYear = parseInt($(el.J_edu_end_year).item(1).children('option:selected').val(),10);
					endMonth = parseInt($(el.J_edu_end_month).item(1).children('option:selected').val(),10);
				}
			}
			
			valid.validAll(function(rs){
				if(rs){
					if(startYear > endYear){
						ev.preventDefault();
						Dialog.alert('开始年份不能大于结束年份');
					}
					else if(startYear == endYear){
						if(startMonth > endMonth){
							ev.preventDefault();
							Dialog.alert('开始月份不能大于结束月份');
						}
					}
				}
			})
		}
	});
	return educationExperience;
},{
	requires:['core','mod/defender','io/job_apply/editResume','io/job_apply/my_home','mod/dialog']
})

/*----------------------证书-----------------------------------------------*/
KISSY.add('editResume/certificate' , function(S){
	var
		$ = S.all,
		on = S.Event.on,
		editResumeIO = PW.io.job_apply.editResume,
		delegate = S.Event.delegate,
		el = {
			J_all_certi : '.all-certi',
			J_edit_certi : '.J_edit_certi',
			J_certi_form : '.certi-form',
			J_selected_certi : 'selected-certi',
			J_del_certi : '.del-certi',
			J_acquired_certi : '.acquired-certi',
			J_certi_cancel : '.certi-cancel',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index',
			J_del_acquired_certi : '.del-acquired-certi',
			J_no_certi_info : '.no-certi-info',
			J_certi_check : '.certi-check',
			J_check_hook : '.check-hook'
		};
	function certificate(){
		this.init();
	}

	S.augment(certificate , {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var
				that = this;
			$(el.J_edit_certi).on('click',function(ev){
				$(el.J_certi_form).show();
				$(el.J_del_certi).show();
				$('em',el.J_acquired_certi).addClass(el.J_selected_certi);
				$(el.J_pop_wrap).show();
				$(el.J_certi_form).addClass(el.J_z_index);
				$(el.J_acquired_certi).addClass(el.J_del_acquired_certi);
			});

			$(el.J_del_certi).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().attr('id'),
					para = 'id='+id;
				editResumeIO.delCerti(para,function(rs,data,msg){
					if(rs){
						$(ev.currentTarget).parent().remove();
						if($(el.J_acquired_certi).children('em').length == 0){
							$(el.J_acquired_certi).hide();
							$(el.J_no_certi_info).show();
							$(el.J_certi_check).children('em').removeClass(el.J_check_hook);
							$(el.J_certi_check).children('em').text('未添加');
						}
					}
					else{
						alert(msg)
					}
				})
			});

			$(el.J_certi_cancel).on('click',function(ev){
				$(el.J_certi_form).hide();
				$('em',el.J_acquired_certi).removeClass(el.J_selected_certi);
				$(el.J_pop_wrap).hide();
				$(el.J_certi_form).removeClass(el.J_z_index);
				$(el.J_acquired_certi).removeClass(el.J_del_acquired_certi);
			})

		},

		_getCerti:function(){
			var
				optionHtml;
			editResumeIO.getCerti({},function(rs,data,msg){
				S.each(data,function(item){
					optionHtml = '<option id='+item.id+'>'+item.name+'</option>';
					$(el.J_all_certi).append(optionHtml);
				})
			})
		}
	});

	return certificate;
},{
	requires:['core' , 'io/job_apply/editResume']
})

/*--------------------------------校内职务--------------------------------------*/
KISSY.add('editResume/schoolPosition' , function(S){
	var
		$ = S.all,
		on = S.Event.on,
		editResumeIO = PW.io.job_apply.editResume,
		Defender = PW.mod.Defender,
	 	Dialog = PW.mod.Dialog,
		el = {
			J_add_posi : '.J_add_posi',
			J_no_posi_info : '.no-posi-info',
			J_edit_school_posi : '.edit-school-posi',
			J_delete_school_posi : '.delete-school-posi',
			J_editing_school_posi_out : '.editing-school-posi-out',
			J_editing_school_posi : '.editing-school-posi',
			J_posi_insert_tr : '.posi-insert-tr',
			J_posi_start_year : '.posi-start-year',
			J_posi_end_year : '.posi-end-year',
			J_posi_start_month : '.posi-start-month',
			J_posi_end_month : '.posi-end-month',
			J_school_posi : '.school-posi',
			J_school_in : '.school-in',
			J_school_out : '.school-out',
			J_posi_cancel : '.posi-cancel',
			J_achievement : '.achievement',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index',
			J_posi_input : '.posi-input',
			J_submit_in : '.posi-submit-in',
			J_submit_out : '.posi-submit-out',
			J_added_school_posi : '.added-school-posi',
			J_posi_check : '.posi-check',
			J_check_hook : '.check-hook',
			J_added_experience_detail : '.added-experience-detail',
			J_error_field : '.error-field'
 		};
	function schoolPosition(){
		this.init();
		this.validIn = Defender.client(el.J_editing_school_posi,{
				showTip:false
			});
		this.validOut = Defender.client(el.J_editing_school_posi_out,{
				showTip:false
			});
	}

	S.augment(schoolPosition , {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var
				that = this;
			$(el.J_edit_school_posi).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id');
				that._getSchool(true);
				that._setExistedInfo(id);
				$(el.J_posi_insert_tr).show();
				$(el.J_posi_insert_tr).addClass(el.J_z_index);
				$(el.J_posi_input).attr('value',id);
				$(el.J_pop_wrap).show();
			});

			$(el.J_posi_cancel).on('click',function(ev){
				$(el.J_posi_insert_tr).hide();
				$(el.J_pop_wrap).hide();
				$(el.J_posi_insert_tr).removeClass(el.J_z_index);
				$(el.J_editing_school_posi_out).removeClass(el.J_z_index);
				$(el.J_editing_school_posi_out).hide();
				$(el.J_posi_input).removeAttr('value');
			});

			$(el.J_delete_school_posi).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id'),
					para = 'id='+id;
				editResumeIO.delSchoolPosi(para,function(rs,data,msg){
					if(rs){
						$(ev.currentTarget).parent().parent().remove();
						if($(el.J_added_school_posi).children('tbody').children('tr').length == 1){
							$(el.J_added_school_posi).hide();
							$(el.J_no_posi_info).show();
							$(el.J_posi_check).children('em').removeClass(el.J_check_hook);
							$(el.J_posi_check).children('em').text('未添加');
						}
					}
					else{
						alert(msg);
					}
				})
			});

			$(el.J_add_posi).on('click',function(ev){
				if($(el.J_added_experience_detail).length == 0){
					Dialog.alert('请先填写教育经历，再填写校内职位！');
				}
				else{
					that._getSchool(false);
					$(el.J_editing_school_posi_out).show();
					$(el.J_editing_school_posi_out).addClass(el.J_z_index);
					$(el.J_pop_wrap).show();
				}
			});

			$(el.J_submit_in).on('click',function(ev){
				that._formSubmit(ev , that.validIn ,'in');
			});

			$(el.J_submit_out).on('click',function(ev){
				that._formSubmit(ev , that.validOut ,'out');
			});		
		},

		//当用户点击编辑时，表单中的值为用户之前已输入的值
		_setExistedInfo:function(id){
			var
				nowYear = new Date().getFullYear(),
				para = 'id='+id;
			editResumeIO.getPosiInfo(para,function(rs,data,msg){
				S.each($(el.J_posi_start_year).item(0).children('option') , function(i,o){
					if($(i).val() == data.startYear){
						$(i).attr('selected','selected');
					}
				});
				S.each($(el.J_posi_end_year).item(0).children('option') , function(i,o){
					if($(i).val() == data.endYear){
						$(i).attr('selected','selected');
					}
				});
				$(el.J_posi_start_month).item(0).children().item(data.startMonth-1).attr('selected','selected');
				$(el.J_posi_end_month).item(0).children().item(data.endMonth-1).attr('selected','selected');
				$(el.J_school_posi).item(0).val(data.schoolPosition);
				$(el.J_achievement).item(0).val(data.achievement);
				S.each($(el.J_school_in).children('option') , function(i,o){
					if($(i).text() == data.school){
						$(i).attr('selected','selected');
					}
				});
			})
		},

		_formSubmit:function(ev,valid,which){
			var 
				that = this,
				startYear,
				startMonth,
				endYear,
				endMonth;

			if(which == 'in'){
				startYear = parseInt($(el.J_posi_start_year).item(0).children('option:selected').val(),10);
				startMonth = parseInt($(el.J_posi_start_month).item(0).children('option:selected').val(),10);
				endYear = parseInt($(el.J_posi_end_year).item(0).children('option:selected').val(),10);
				endMonth = parseInt($(el.J_posi_end_month).item(0).children('option:selected').val(),10);
			}
			if(which == 'out'){
				if($(el.J_added_school_posi).length != 0){
					startYear = parseInt($(el.J_posi_start_year).item(1).children('option:selected').val(),10);
					startMonth = parseInt($(el.J_posi_start_month).item(1).children('option:selected').val(),10);
					endYear = parseInt($(el.J_posi_end_year).item(1).children('option:selected').val(),10);
					endMonth = parseInt($(el.J_posi_end_month).item(1).children('option:selected').val(),10);
				}
				else{
					startYear = parseInt($(el.J_posi_start_year).item(0).children('option:selected').val(),10);
					startMonth = parseInt($(el.J_posi_start_month).item(0).children('option:selected').val(),10);
					endYear = parseInt($(el.J_posi_end_year).item(0).children('option:selected').val(),10);
					endMonth = parseInt($(el.J_posi_end_month).item(0).children('option:selected').val(),10);
				}
			}	
			
			valid.validAll(function(rs){
				if(rs){
					if(startYear > endYear){
						ev.preventDefault();
						Dialog.alert('开始年份不能大于结束年份');
					}
					else if(startYear == endYear){
						if(startMonth > endMonth){
							ev.preventDefault();
							Dialog.alert('开始月份不能大于结束月份');
						}
					}
					if(which == 'in'){
						if($(el.J_school_in).children('option:selected').val() == 0){
							ev.preventDefault();
							$(el.J_school_in).addClass(el.J_error_field);
						}
					}
					if(which == 'out'){
						if($(el.J_school_out).children('option:selected').val() == 0){
							ev.preventDefault();
							$(el.J_school_out).addClass(el.J_error_field);
						}
					}
				}
			})
		},

		_getSchool:function(isEdit){
			var
				optionHtml;
			editResumeIO.getSchool({},function(rs,data,msg){
				if(rs){
					if(isEdit){
						$(el.J_school_in).html('');
					}
					else{
						$(el.J_school_out).html('');
					}
					S.each(data,function(item){
						optionHtml = '<option>'+item.school+'</option>';
						if(isEdit){
							$(el.J_school_in).append(optionHtml);
						}
						else{
							$(el.J_school_out).append(optionHtml);
						}
					})
					if(isEdit){
						$(el.J_school_in).prepend('<option value="0">请选择</option>');
					}
					else{
						$(el.J_school_out).prepend('<option value="0">请选择</option>');
					}
				}
			})
		}
	});
	return schoolPosition;
},{
	requires:['core','io/job_apply/editResume' ,'mod/defender' , 'mod/dialog']
})

/*--------------------------------校内奖励--------------------------------------*/
KISSY.add('editResume/schoolAward' , function(S){
	var
		$ = S.all,
		on = S.Event.on,
		editResumeIO = PW.io.job_apply.editResume,
		Defender = PW.mod.Defender,
		Dialog = PW.mod.Dialog,
		el = {
			J_add_award : '.J_add_award',
			J_edit_school_award : '.edit-school-award',
			J_delete_school_award : '.delete-school-award',
			J_editing_school_award_out : '.editing-school-award-out',
			J_editing_school_award : '.editing-school-award',
			J_award_insert_tr : '.award-insert-tr',
			J_award_year : '.award-year',
			J_award_month : '.award-month',
			J_school_award : '.school-award',//学校的奖励
			J_school_in : '.award-school_in',//颁奖的学校
			J_school_out : '.award-school_out',
			J_award_cancel : '.award-cancel',
			J_description : '.award-description',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index',
			J_award_input : '.award-input',
			J_added_school_award : '.added-school-award',
			J_award_check : '.award-check',
			J_check_hook : '.check-hook',
			J_added_experience_detail : '.added-experience-detail',
			J_no_award_info : '.no-award-info',
			J_added_experience_detail : '.added-experience-detail',
			J_submit_in : '.award-submit-in',
			J_submit_out : '.award-submit-out',
			J_error_field : '.error-field'
		};
	function schoolAward(){
		this.init();
		this.validIn = Defender.client(el.J_editing_school_award,{
				showTip:false
			});
		this.validOut = Defender.client(el.J_editing_school_award_out,{
				showTip:false
			});
	}

	S.augment(schoolAward , {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var
				that = this;
			$(el.J_edit_school_award).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id');
				that._getSchool(true);
				that._setExistedInfo(id);
				$(el.J_award_insert_tr).show();
				$(el.J_award_insert_tr).addClass(el.J_z_index);
				$(el.J_award_input).attr('value',id);
				$(el.J_pop_wrap).show();
			});

			$(el.J_award_cancel).on('click',function(ev){
				$(el.J_award_insert_tr).hide();
				$(el.J_pop_wrap).hide();
				$(el.J_award_insert_tr).removeClass(el.J_z_index);
				$(el.J_editing_school_award_out).removeClass(el.J_z_index);
				$(el.J_editing_school_award_out).hide();
				$(el.J_award_input).removeAttr('value');
			});

			$(el.J_delete_school_award).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id'),
					para = 'id='+id;
				editResumeIO.delSchoolAward(para,function(rs,data,msg){
					if(rs){
						$(ev.currentTarget).parent().parent().remove();
						if($(el.J_added_school_award).children('tbody').children('tr').length == 1){
							$(el.J_added_school_award).hide();
							$(el.J_no_award_info).show();
							$(el.J_award_check).children('em').removeClass(el.J_check_hook);
							$(el.J_award_check).children('em').text('未添加');
						}
					}
					else{
						alert(msg);
					}
				})
			});

			$(el.J_add_award).on('click',function(ev){
				if($(el.J_added_experience_detail).length == 0){
					Dialog.alert('请先填写教育经历，再填写校内奖励！');
				}
				else{
					that._getSchool(false);
					$(el.J_editing_school_award_out).show();
					$(el.J_editing_school_award_out).addClass(el.J_z_index);
					$(el.J_pop_wrap).show();
				}
			});

			$(el.J_submit_in).on('click',function(ev){
				that._formSubmit(ev,that.validIn ,'in');
			});

			$(el.J_submit_out).on('click',function(ev){
				that._formSubmit(ev,that.validOut ,'out');
			});
		},

		//当用户点击编辑时，表单中的值为用户之前已输入的值
		_setExistedInfo:function(id){
			var
				nowYear = new Date().getFullYear(),
				para = 'id='+id;
			editResumeIO.getAwardInfo(para,function(rs,data,msg){
				S.each($(el.J_award_year).item(0).children('option'),function(i,o){
					if($(i).val() == data.awardYear){
						$(i).attr('selected','selected');
					}
				});
				$(el.J_award_month).item(0).children().item(data.awardMonth-1).attr('selected','selected');
				$(el.J_school_award).item(0).val(data.schoolAward);
				$(el.J_description).item(0).val(data.description);
				S.available('#J_EduSchool',function(){
					S.each($(el.J_school_in).children('option'),function(i,o){
						if(data.school == $(i).text())
							$(i).attr('selected','selected');
					})
				});
			})
		},

		_getSchool:function(isEdit){
			var
				optionHtml;
			editResumeIO.getSchool({},function(rs,data,msg){
				if(rs){
					if(isEdit){
						$(el.J_school_in).html('');
					}
					else{
						$(el.J_school_out).html('');
					}
					S.each(data,function(item){
						optionHtml = '<option>'+item.school+'</option>';
						if(isEdit){
							$(el.J_school_in).append(optionHtml);
						}
						else{
							$(el.J_school_out).append(optionHtml);
						}
					})
					if(isEdit){
						$(el.J_school_in).prepend('<option value="0" id="J_EduSchool">请选择</option>');
					}
					else{
						$(el.J_school_out).prepend('<option value="0">请选择</option>');
					}
				}
			})
		},

		_formSubmit:function(ev,valid,which){
			valid.validAll(function(rs){
				if(rs){
					if(which == 'in'){
						if($(el.J_school_in).children('option:selected').val() == 0){
							ev.preventDefault();
							$(el.J_school_in).addClass(el.J_error_field);
						}
					}
					if(which == 'out'){
						if($(el.J_school_out).children('option:selected').val() == 0){
							ev.preventDefault();
							$(el.J_school_out).addClass(el.J_error_field);
						}
					}
				}
			})
		}
	});
	return schoolAward;
},{
	requires:['core','io/job_apply/editResume','mod/defender', 'mod/dialog']
})

/*---------------------------------语言能力--------------------------------------*/
KISSY.add('editResume/language' , function(S){
	var
		$ = S.all,
		on = S.Event.on,
		delegate = S.Event.delegate,
		DOM = S.DOM,
		editResumeIO = PW.io.job_apply.editResume,
		Defender = PW.mod.Defender,
		el = {
			J_add_language : '.J_add_language',
			J_edit_language : '.edit-language',
			J_delete_language : '.delete-language',
			J_editing_language_out : '.editing-language-out',
			J_editing_language : '.editing-language',
			J_language_insert_tr : '.language-insert-tr',
			J_language_cancel : '.language-cancel',
			J_languages : '.languages',
			J_talk_listen : '.talk-listen',
			J_read_write : '.read-write',
			J_exam : '.exam',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index',
			J_add_exam: '.add-exam',
			J_exam_tr:'.exam-tr',
			J_grade : '.grade',
			J_language_input : '.language-input',
			J_del_exam : '.del-exam',
			J_new : '.new',
			J_old : '.old',
			J_old_in : '.old-in',
			J_language_check : '.language-check',
			J_added_language : '.added-language',
			J_check_hook : '.check-hook',
			J_no_language_info : '.no-language-info'
		};
	function language(){
		this.init();
		this.valid = Defender.client(el.J_editing_language,{
				showTip:false
			});
		this.valid = Defender.client(el.J_editing_language_out,{
				showTip:false
			});
	}

	S.augment(language , {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var
				currentEditId,
				that = this;
			$(el.J_edit_language).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id'),
					Lid = $(ev.currentTarget).parent().prev().prev().prev().prev().attr('Lid');
				currentEditId = id;
				that._getExams(Lid);
				that._getLanguages(false,Lid);
				S.available('#J_Exam', function(){
					that._setExistedInfo(id);
				})
				$(el.J_language_insert_tr).show();
				$(el.J_language_insert_tr).addClass(el.J_z_index);
				$(el.J_language_input).attr('value',id);
				$(el.J_pop_wrap).show();
			});

			$(el.J_language_cancel).on('click',function(ev){
				$(el.J_language_insert_tr).hide();
				$(el.J_pop_wrap).hide();
				$(el.J_language_insert_tr).removeClass(el.J_z_index);
				$(el.J_editing_language_out).removeClass(el.J_z_index);
				$(el.J_editing_language_out).hide();
				$(el.J_language_input).removeAttr('value');
				$(el.J_exam_tr).slice(1,$(el.J_old_in).length).remove();
			});

			$(el.J_delete_language).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id'),
					para = 'id='+id;
				editResumeIO.delLanguage(para,function(rs,data,msg){
					if(rs){
						$(ev.currentTarget).parent().parent().remove();
						if($(el.J_added_language).children('tbody').children('tr').length == 1){
							$(el.J_added_language).hide();
							$(el.J_no_language_info).show();
							$(el.J_language_check).children('em').removeClass(el.J_check_hook);
							$(el.J_language_check).children('em').text('未添加');
						}
					}
					else{
						alert(msg);
					}
				})
			});

			$(el.J_add_language).on('click',function(ev){
				that._getLanguages(true,0);
				$(el.J_editing_language_out).show();
				$(el.J_editing_language_out).addClass(el.J_z_index);
				$(el.J_pop_wrap).show();
			});

			$(el.J_languages).on('change',function(ev){
				var
					id = $(ev.currentTarget).children('option:selected').val();
				$(el.J_exam).html('');
				that._getExams(id);
			});

			$(el.J_add_exam).on('click',function(ev){
				$(ev.currentTarget).parent().parent().prev().clone(true).addClass(el.J_new).insertBefore($(ev.currentTarget).parent().parent());
			});

			delegate(document,'click',el.J_del_exam,function(ev){
				var
					currentTarget = $(ev.currentTarget),
					examId = $(ev.currentTarget).parent().prev().prev().children('select').attr('examId'),
					para = 'examId='+examId+'&currentEditId='+currentEditId;
				if(currentTarget.parent().parent().hasClass(el.J_new)){
					currentTarget.parent().parent().remove();
				}
				else if(currentTarget.parent().parent().hasClass(el.J_old)){
					if($(el.J_old).length > 1)
						currentTarget.parent().parent().remove();
					else{
						currentTarget.parent().prev().children('input').val('');
						currentTarget.parent().prev().prev().children('select').children().item(0).attr('selected','selected');
					}
				}
				else if(currentTarget.parent().parent().hasClass(el.J_old_in)){
					if($(el.J_old_in).length > 1)
						currentTarget.parent().parent().remove();
					else{
						currentTarget.parent().prev().children('input').val('');
						currentTarget.parent().prev().prev().children('select').children().item(0).attr('selected','selected');
					}
				}
				// else{
				// 	editResumeIO.delExam(para,function(code,data,msg){
				// 		if(code == 0){
				// 			currentTarget.parent().parent().remove();
				// 		}
				// 		if(code == 1){
				// 			currentTarget.parent().prev().children('input').val('');
				// 			currentTarget.parent().prev().prev().children('select').children().item(0).attr('selected','selected');
				// 		};
				// 	})
				// }
			});
		},

		_getLanguages:function(isAll,id){
			var
				para,
				optionHtml,
				i = 1;
			if(isAll)
				para = {};
			else
				para = 'Lid='+id;
			editResumeIO.getLanguages(para,function(rs,data,msg){
				if(rs){
					$(el.J_languages).html('');
					if(isAll){
						S.each(data,function(item){
							optionHtml = '<option value='+i+'>'+item.name+'</option>';
							$(el.J_languages).append(optionHtml);
							i ++;
						})
						optionHtml = '<option value=0>请选择</option>';
						$(el.J_languages).prepend(optionHtml);
						console.log($(el.J_languages).children('option').item(0));
						$(el.J_languages).item(1).children('option').item(0).attr('selected','selected');
					}
					if(!isAll){
						optionHtml = '<option value='+i+'>'+data+'</option>';
						$(el.J_languages).append(optionHtml);
					}
				}				
			});
		},

		//当用户点击编辑时，表单中的值为用户之前已输入的值
		_setExistedInfo:function(id){
			var
				para = 'id='+id;
			editResumeIO.getLanguageInfo(para,function(rs,data,msg){
				$(el.J_languages).item(0).children().item(0).attr('selected','selected');
				$(el.J_talk_listen).item(0).children().item(data.talkListen).attr('selected','selected');
				$(el.J_read_write).item(0).children().item(data.readWrite).attr('selected','selected');
				for(var i=0; i<data.exam.length-1; i++){
					$(el.J_add_exam).parent().parent().prev().clone(true).insertBefore($(el.J_add_exam).parent().parent());
				}
				for(var i=0; i<data.exam.length; i ++){
					$(el.J_exam).item(i).children().item(data.exam[i]+1).attr('selected','selected');
					$(el.J_grade).item(i).val(data.grade[i]);
					$(el.J_exam).item(i).attr('examId',i);
				}
			})
		},

		_getExams:function(id){
			var
				i = 0,
				optionHtml,
				para = 'id='+id;
			editResumeIO.getExams(para,function(rs,data,msg){
				if(rs){
					$(el.J_exam).html('');
					optionHtml = '<option value="0">请选择</option>';
					$(el.J_exam).append(optionHtml);
					S.each(data,function(item){
						if(i < data.length-1)
							optionHtml = '<option value='+item.id+'>'+item.exam_name+'</option>';
						else
							optionHtml = '<option id=J_Exam value='+item.id+'>'+item.exam_name+'</option>';
						$(el.J_exam).append(optionHtml);
						i ++;
					})
				}
			})
		}
	});
	return language;
},{
	requires:['core','io/job_apply/editResume','mod/defender']
})

/*--------------------------------实习经历--------------------------------------*/
KISSY.add('editResume/internship' , function(S){
	var
		$ = S.all,
		on = S.Event.on,
		editResumeIO = PW.io.job_apply.editResume,
		Defender = PW.mod.Defender,
		el = {
			J_add_intern : '.J_add_intern',
			J_edit_intern : '.edit-intern',
			J_del_intern : '.del-intern',
			J_editing_intern_out : '.editing-intern-out',
			J_editing_intern : '.editing-intern',
			J_intern_insert_tr : '.intern-insert-tr',
			J_intern_start_year : '.intern-start-year',
			J_intern_end_year : '.intern-end-year',
			J_intern_start_month : '.intern-start-month',
			J_intern_end_month : '.intern-end-month',
			J_intern_company : '.intern-company',
			J_posi_name : '.intern-posi-name',
			J_intern_cancel : '.intern-cancel',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index',
			J_province : '.intern-province',
			J_city : '.intern-city',
			J_department : '.intern-department',
			J_industry : '.intern-industry',
			J_posi_type : '.intern-posi-type',
			J_salary : '.intern-salary',
			J_report_to : '.intern-report-to',
			J_description : '.intern-description',
			J_intern_input : '.intern-input',
			J_submit_in : '.intern-submit-in',
			J_submit_out : '.intern-submit-out',
			J_added_intern : '.added-intern',
			J_no_intern_info : '.no-intern-info',
			J_check_hook : '.check-hook',
			J_intern_check : '.intern-check'

		};
	function internship(){
		this.init();
		this.validIn = Defender.client(el.J_editing_intern,{
				showTip:false
			});
		this.validOut = Defender.client(el.J_editing_intern_out,{
				showTip:false
			});
	}

	S.augment(internship , {
		init:function(){
			this._addEventListener();
			// this._getYear();
			// this._getCity(true,0);
			// this._getIndustry();
			// this._getPosiType();
		},

		_addEventListener:function(){
			var
				that = this;
			$(el.J_edit_intern).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id');
				that._setExistedInfo(id);
				$(el.J_intern_insert_tr).show();
				$(el.J_intern_insert_tr).addClass(el.J_z_index);
				// $(el.J_intern_insert_tr).inserwtAfter($(ev.currentTarget).parent().parent());
				$(el.J_intern_input).attr('value',id);
				$(el.J_pop_wrap).show();
			});

			$(el.J_intern_cancel).on('click',function(ev){
				$(el.J_intern_insert_tr).hide();
				$(el.J_pop_wrap).hide();
				$(el.J_intern_insert_tr).removeClass(el.J_z_index);
				$(el.J_editing_intern_out).removeClass(el.J_z_index);
				$(el.J_editing_intern_out).hide();
				$(el.J_intern_input).removeAttr('value');
				$(el.J_city).html(' ');
			});

			$(el.J_del_intern).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id'),
					para = 'id='+id;
				editResumeIO.delIntern(para,function(rs,data,msg){
					if(rs){
						$(ev.currentTarget).parent().parent().remove();
						if($(el.J_added_intern).children('tbody').children('tr').length == 1){
							$(el.J_added_intern).hide();
							$(el.J_no_intern_info).show();
							$(el.J_intern_check).children('em').removeClass(el.J_check_hook);
							$(el.J_intern_check).children('em').text('未添加');
						}
					}
					else{
						alert(msg);
					}
				})
			});

			$(el.J_add_intern).on('click',function(ev){
				$(el.J_editing_intern_out).show();
				$(el.J_editing_intern_out).addClass(el.J_z_index);
				$(el.J_pop_wrap).show();
			});

			$(el.J_province).on('change',function(ev){
				var
					id = $(ev.currentTarget).children('option:selected').val();
				id = parseInt(id,10);
				that._getCity(false,id);
			});

			$(el.J_submit_in).on('click',function(ev){
				that._formSubmit(ev , that.validIn ,'in');
			});

			$(el.J_submit_out).on('click',function(ev){
				that._formSubmit(ev , that.validOut ,'out');
			});	
		},

		// _getYear:function(){
		// 	editResumeIO.getYear({},function(rs,data,msg){
		// 		S.each(data,function(item){
		// 			var
		// 				optionHtml = '<option>'+item+'</option>';
		// 			$(el.J_intern_start_year).append(optionHtml);
		// 			$(el.J_intern_end_year).append(optionHtml);
		// 		})
		// 	})
		// },

		//当用户点击编辑时，表单中的值为用户之前已输入的值
		_setExistedInfo:function(id){
			var
				that = this,
				nowYear = new Date().getFullYear(),
				para = 'id='+id;
			editResumeIO.getInternInfo(para,function(rs,data,msg){
				that._getCity(false,data.province);
				S.each($(el.J_intern_start_year).item(0).children('option') , function(i,o){
					if($(i).val() == data.startYear){
						$(i).attr('selected','selected');
					}
				})
				S.each($(el.J_intern_end_year).item(0).children('option') , function(i,o){
					if($(i).val() == data.endYear){
						$(i).attr('selected','selected');
					}
				})
				$(el.J_intern_start_month).item(0).children().item(data.startMonth-1).attr('selected','selected');
				$(el.J_intern_end_month).item(0).children().item(data.endMonth-1).attr('selected','selected');
				$(el.J_intern_company).item(0).val(data.internCompany);
				$(el.J_posi_name).item(0).val(data.posiName);
				$(el.J_province).item(0).children().item(data.province).attr('selected','selected');
				S.available('#J_intern_CITY',function(){
					$(el.J_city).item(0).children().item(data.city).attr('selected','selected');
				})
				$(el.J_department).item(0).val(data.department);
				$(el.J_industry).item(0).children().item(data.industry).attr('selected','selected');
				$(el.J_posi_type).item(0).children().item(data.posiType).attr('selected','selected');
				$(el.J_salary).item(0).val(data.salary);
				$(el.J_report_to).item(0).val(data.reportTo);
				$(el.J_description).item(0).val(data.description);
			});
		},

		_getCity:function(isProvince,id){
			var
				i = 0,
				para,
				optionHtml;
			if(isProvince){
				para = {}
				$(el.J_province).html('');
			}
			else{
				para = 'id='+id;
				$(el.J_city).html('');
			}
			editResumeIO.getCity(para,function(rs,data,msg){
				S.each(data,function(item){
					if(i < data.length - 1){
						optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
						if(isProvince){	
							$(el.J_province).append(optionHtml);
						}
						else{
							$(el.J_city).append(optionHtml);
						}
					}
					else{
						optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
						lastCityOptionHtml = '<option id="J_intern_CITY" value='+item.id+'>'+item.name+'</option>';
						if(isProvince){	
							$(el.J_province).append(optionHtml);
						}
						else{
							$(el.J_city).append(lastCityOptionHtml);
						}
					}
					i ++;
				})
				if(isProvince)
					$(el.J_province).prepend('<option value=0>请选择</option>');
				else
					$(el.J_city).prepend('<option value=0>请选择</option>');
			})
		},

		_formSubmit:function(ev,valid,which){
			var 
				that = this,
				startYear,
				startMonth,
				endYear,
				endMonth;

			if(which == 'in'){
				startYear = parseInt($(el.J_intern_start_year).item(0).children('option:selected').val(),10);
				startMonth = parseInt($(el.J_intern_start_month).item(0).children('option:selected').val(),10);
				endYear = parseInt($(el.J_intern_end_year).item(0).children('option:selected').val(),10);
				endMonth = parseInt($(el.J_intern_end_month).item(0).children('option:selected').val(),10);
			}
			if(which == 'out'){
				if($(el.J_added_intern).length != 0){
					startYear = parseInt($(el.J_intern_start_year).item(1).children('option:selected').val(),10);
					startMonth = parseInt($(el.J_intern_start_month).item(1).children('option:selected').val(),10);
					endYear = parseInt($(el.J_intern_end_year).item(1).children('option:selected').val(),10);
					endMonth = parseInt($(el.J_intern_end_month).item(1).children('option:selected').val(),10);
				}
				else{
					startYear = parseInt($(el.J_intern_start_year).item(0).children('option:selected').val(),10);
					startMonth = parseInt($(el.J_intern_start_month).item(0).children('option:selected').val(),10);
					endYear = parseInt($(el.J_intern_end_year).item(0).children('option:selected').val(),10);
					endMonth = parseInt($(el.J_intern_end_month).item(0).children('option:selected').val(),10);
				}
				
			}
			
			valid.validAll(function(rs){
				if(rs){
					if(startYear > endYear){
						ev.preventDefault();
						Dialog.alert('开始年份不能大于结束年份');
					}
					else if(startYear == endYear){
						if(startMonth > endMonth){
							ev.preventDefault();
							Dialog.alert('开始月份不能大于结束月份');
						}
					}
				}
			})
		}

		// _getIndustry:function(){
		// 	editResumeIO.getIndustry({},function(rs,data,msg){
		// 		var
		// 			optionHtml;
		// 		$(el.J_industry).html(' ');
		// 		S.each(data,function(item){
		// 			optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
		// 			$(el.J_industry).append(optionHtml);
		// 		});
		// 		$(el.J_industry).prepend('<option value=0>请选择</option>');
		// 	})
		// },

		// _getPosiType:function(){
		// 	editResumeIO.getPosiType({},function(rs,data,msg){
		// 		var
		// 			optionHtml;
		// 		$(el.J_posi_type).html('');
		// 		S.each(data,function(item){
		// 			optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
		// 			$(el.J_posi_type).append(optionHtml);
		// 		});
		// 		$(el.J_posi_type).prepend('<option value=0>请选择</option>');
		// 	})
		// }
	});
	return internship;
},{
	requires:['core','io/job_apply/editResume' ,'mod/defender']
})

/*------------------------------------it技能--------------------------------------*/
KISSY.add('editResume/itSkill' , function(S){
	var
		$ = S.all,
		on = S.Event.on,
		delegate = S.Event.delegate,
		DOM = S.DOM,
		editResumeIO = PW.io.job_apply.editResume,
		el = {
			J_it_pop : '.it-pop',
			J_choosed : '.choosed',
			J_submit_skills : '.submit-skills',
			J_cancel_skills : '.cancel-skills',
			J_pop_wrap : '.pop-wrap',
			J_edit_skill : '.J_edit_skill',
			J_all_skills : '.all-skills',
			J_close_skills : '.close-skills',
			J_skills : '.skills',
			J_skill_details : '.skill-details',
			J_old_skills : '.old-skills',
			J_no_it_info : '.no-it-info'
		};

	function itSkill(){
		this.init();
	}

	S.augment(itSkill , {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var
				selectedNameArray = new Array(),
				nameArray = new Array();

			$(el.J_edit_skill).on('click',function(ev){
				$(el.J_it_pop).show();
				$(el.J_pop_wrap).show();
				editResumeIO.getSelectedSkill({},function(rs,data,msg){
					if(rs){
						$(el.J_choosed).html('');
						S.each(data,function(item){
							var
								spanNode = $(DOM.create('<span></span>')),
								aNode = $(DOM.create('<a href="javascript:;">x</a>'));
							spanNode.attr('id',item.id);
							spanNode.text(item.name);
							spanNode.append(aNode);
							$(el.J_choosed).append(spanNode);
							selectedNameArray.push(item.name);
						})
					}
					else{
						alert(msg);
					}
				})

				editResumeIO.getDetail({},function(rs,data,msg){
					if(rs)
						$('textarea',el.J_it_pop).val(data);
					else
						alert(msg);
				})
			});

			$(el.J_choosed).delegate('click','a',function(ev){
				var
					currentTarget = $(ev.currentTarget),
					id = currentTarget.parent().attr('id'),
					para = 'id='+id;

				editResumeIO.delSkill(para,function(rs,data,msg){
					if(rs){
						var 
							itName = currentTarget.parent().text(),
							itName = itName.substring(0,itName.length-1);
						S.each(selectedNameArray , function(i,o){
							if(i == itName){
								selectedNameArray.splice(o,1);
							}
						})
						currentTarget.parent().remove();
					}
					else{
						alert(msg);
					}
				})
			});

			$('input',el.J_all_skills).on('change',function(ev){
				var
					isNew = true, 
					spanNode = $(DOM.create('<span></span>'));
				if($(ev.currentTarget).attr('checked') == 'checked'){
					S.each(selectedNameArray , function(item){
						if($(ev.currentTarget).attr('name') == item)
							isNew = false;
					})
					if(isNew){
						nameArray.push($(ev.currentTarget).attr('name'));
						spanNode.attr('name',$(ev.currentTarget).attr('name'));
						spanNode.text($(ev.currentTarget).parent().text());
						$(el.J_choosed).append(spanNode);
					}
				}
				else{
					nameArray.pop($(ev.currentTarget).attr('name'));
					S.each($('span',el.J_choosed) , function(i,o){
						if($(i).attr('name') == $(ev.currentTarget).attr('name'))
							$(i).remove();
					})
				}
			});

			$(el.J_submit_skills).on('click',function(ev){
				var
					detail = $('textarea',el.J_it_pop).val(),
					para = 'skills='+nameArray+'&detail='+detail;
				editResumeIO.putSkills(para,function(rs,data,msg){
					if(rs){
						window.location.reload(true);
					}
				})
			});

			$(el.J_cancel_skills).on('click',function(ev){
				$(el.J_pop_wrap).hide();
				$(el.J_it_pop).hide();
			});

			$(el.J_close_skills).on('click',function(ev){
				$(el.J_pop_wrap).hide();
				$(el.J_it_pop).hide();
			})
		}
	})
	return itSkill;
},{
	requires:['core','io/job_apply/editResume']
})

/*--------------------------------工作经历--------------------------------------*/
KISSY.add('editResume/work' , function(S){
	var
		$ = S.all,
		on = S.Event.on,
		editResumeIO = PW.io.job_apply.editResume,
		Defender = PW.mod.Defender,
		el = {
			J_add_work : '.J_add_work',
			J_edit_work : '.edit-work',
			J_del_work : '.del-work',
			J_editing_work_out : '.editing-work-out',
			J_editing_work : '.editing-work',
			J_work_insert_tr : '.work-insert-tr',
			J_work_start_year : '.work-start-year',
			J_work_end_year : '.work-end-year',
			J_work_start_month : '.work-start-month',
			J_work_end_month : '.work-end-month',
			J_work_company : '.work-company',
			J_posi_name : '.work-posi-name',
			J_work_cancel : '.work-cancel',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index',
			J_province : '.work-province',
			J_city : '.work-city',
			J_department : '.work-department',
			J_industry : '.work-industry',
			J_posi_type : '.work-posi-type',
			J_salary : '.work-salary',
			J_report_to : '.work-report-to',
			J_description : '.work-description',
			J_posi_level : '.posi-level',
			J_subordinate_num : '.subordinate-num',
			J_work_input : '.work-input',
			J_submit_in : '.work-submit-in',
			J_submit_out : '.work-submit-out',
			J_added_work : '.added-work',
			J_no_work_info : '.no-work-info',
			J_check_hook : '.check-hook',
			J_work_check : '.work-check'
		};
	function work(){
		this.init();
		this.validIn = Defender.client(el.J_editing_work,{
				showTip:false
			});
		this.validOut = Defender.client(el.J_editing_work_out,{
				showTip:false
			});
	}

	S.augment(work , {
		init:function(){
			this._addEventListener();
			// this._getYear();
			// this._getCity(true,0);
			// this._getIndustry();
			// this._getPosiType();
		},

		_addEventListener:function(){
			var
				that = this;
			$(el.J_edit_work).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id');
				that._setExistedInfo(id);
				$(el.J_work_insert_tr).show();
				$(el.J_work_insert_tr).addClass(el.J_z_index);
				// $(el.J_work_insert_tr).insertAfter($(ev.currentTarget).parent().parent());
				$(el.J_work_input).attr('value',id);
				$(el.J_pop_wrap).show();
			});

			$(el.J_work_cancel).on('click',function(ev){
				$(el.J_work_insert_tr).hide();
				$(el.J_pop_wrap).hide();
				$(el.J_work_insert_tr).removeClass(el.J_z_index);
				$(el.J_editing_work_out).removeClass(el.J_z_index);
				$(el.J_editing_work_out).hide();
				$(el.J_work_input).removeAttr('value');
				$(el.J_city).html('');
			});

			$(el.J_del_work).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id'),
					para = 'id='+id;
				editResumeIO.delWork(para,function(rs,data,msg){
					if(rs){
						$(ev.currentTarget).parent().parent().remove();
						if($(el.J_added_work).children('tbody').children('tr').length == 1){
							$(el.J_added_work).hide();
							$(el.J_no_work_info).show();
							$(el.J_work_check).children('em').removeClass(el.J_check_hook);
							$(el.J_work_check).children('em').text('未添加');
						}
					}
					else{
						alert(msg);
					}
				})
			});

			$(el.J_add_work).on('click',function(ev){
				$(el.J_editing_work_out).show();
				$(el.J_editing_work_out).addClass(el.J_z_index);
				$(el.J_pop_wrap).show();
			});

			$(el.J_province).on('change',function(ev){
				var
					id = $(ev.currentTarget).children('option:selected').val();
				id = parseInt(id,10);
				that._getCity(false,id);
			});

			$(el.J_submit_in).on('click',function(ev){
				that._formSubmit(ev , that.validIn ,'in');
			});

			$(el.J_submit_out).on('click',function(ev){
				that._formSubmit(ev , that.validOut ,'out');
			});	


		},

		// _getYear:function(){
		// 	editResumeIO.getYear({},function(rs,data,msg){
		// 		S.each(data,function(item){
		// 			var
		// 				optionHtml = '<option>'+item+'</option>';
		// 			$(el.J_work_start_year).append(optionHtml);
		// 			$(el.J_work_end_year).append(optionHtml);
		// 		})
		// 	})
		// },

		//当用户点击编辑时，表单中的值为用户之前已输入的值
		_setExistedInfo:function(id){
			var
				that = this,
				nowYear = new Date().getFullYear(),
				para = 'id='+id;
			editResumeIO.getWorkInfo(para,function(rs,data,msg){
				that._getCity(false,data.province);
				S.each($(el.J_work_start_year).item(0).children('option') , function(i,o){
					if($(i).val() == data.startYear){
						$(i).attr('selected','selected');
					}
				});
				S.each($(el.J_work_end_year).item(0).children('option') , function(i,o){
					if($(i).val() == data.endYear){
						$(i).attr('selected','selected');
					}
				});
				$(el.J_work_start_month).item(0).children().item(data.startMonth-1).attr('selected','selected');
				$(el.J_work_end_month).item(0).children().item(data.endMonth-1).attr('selected','selected');
				$(el.J_work_company).item(0).val(data.workCompany);
				$(el.J_posi_name).item(0).val(data.posiName);
				$(el.J_province).item(0).children().item(data.province).attr('selected','selected');
				S.available('#J_Work_CITY',function(){
					$(el.J_city).item(0).children().item(data.city).attr('selected','selected');
				})
				$(el.J_department).item(0).val(data.department);
				$(el.J_industry).item(0).children().item(data.industry).attr('selected','selected');
				$(el.J_posi_type).item(0).children().item(data.posiType).attr('selected','selected');
				$(el.J_salary).item(0).val(data.salary);
				$(el.J_report_to).item(0).val(data.reportTo);
				$(el.J_description).item(0).val(data.description);
				$(el.J_posi_level).item(0).children().item(data.posiLevel).attr('selected','selected');
				$(el.J_subordinate_num).item(0).val(data.subordinateNum)
			});
		},

		_getCity:function(isProvince,id){
			var
				i = 0,
				para,
				optionHtml;
			if(isProvince){
				para = {}
				$(el.J_province).html('');
			}
			else{
				para = 'id='+id;
				$(el.J_city).html('');
			}
			editResumeIO.getCity(para,function(rs,data,msg){
				S.each(data,function(item){
					if(i < data.length-1)
						optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
					else{
						optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
						lastOptionHtml = '<option id="J_Work_CITY" value='+item.id+'>'+item.name+'</option>';
					}
					if(isProvince){	
						$(el.J_province).append(optionHtml);
					}
					else{
						if(i < data.length-1)
							$(el.J_city).append(optionHtml);
						else
							$(el.J_city).append(lastOptionHtml);
					}

					i ++;
				})
				if(isProvince)
					$(el.J_province).prepend('<option value=0>请选择</option>');
				else
					$(el.J_city).prepend('<option value=0>请选择</option>');
			})
		},

		_formSubmit:function(ev,valid,which){
			var 
				that = this,
				startYear,
				startMonth,
				endYear,
				endMonth;

			if(which == 'in'){
				startYear = parseInt($(el.J_work_start_year).item(0).children('option:selected').val(),10);
				startMonth = parseInt($(el.J_work_start_month).item(0).children('option:selected').val(),10);
				endYear = parseInt($(el.J_work_end_year).item(0).children('option:selected').val(),10);
				endMonth = parseInt($(el.J_work_end_month).item(0).children('option:selected').val(),10);
			}
			if(which == 'out'){
				if($(el.J_added_work).length != 0){
					startYear = parseInt($(el.J_work_start_year).item(1).children('option:selected').val(),10);
					startMonth = parseInt($(el.J_work_start_month).item(1).children('option:selected').val(),10);
					endYear = parseInt($(el.J_work_end_year).item(1).children('option:selected').val(),10);
					endMonth = parseInt($(el.J_work_end_month).item(1).children('option:selected').val(),10);
				}
				else{
					startYear = parseInt($(el.J_work_start_year).item(0).children('option:selected').val(),10);
					startMonth = parseInt($(el.J_work_start_month).item(0).children('option:selected').val(),10);
					endYear = parseInt($(el.J_work_end_year).item(0).children('option:selected').val(),10);
					endMonth = parseInt($(el.J_work_end_month).item(0).children('option:selected').val(),10);
				}
			}
			
			valid.validAll(function(rs){
				if(rs){
					if(startYear > endYear){
						ev.preventDefault();
						Dialog.alert('开始年份不能大于结束年份');
					}
					else if(startYear == endYear){
						if(startMonth > endMonth){
							ev.preventDefault();
							Dialog.alert('开始月份不能大于结束月份');
						}
					}
				}
			})
		}

		// _getIndustry:function(){
		// 	editResumeIO.getIndustry({},function(rs,data,msg){
		// 		var
		// 			optionHtml;
		// 		$(el.J_industry).html(' ');
		// 		$(el.J_industry).append('<option value=0>请选择</option>');
		// 		S.each(data,function(item){
		// 			optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
		// 			$(el.J_industry).append(optionHtml);
		// 		})
		// 	})
		// },

		// _getPosiType:function(){
		// 	editResumeIO.getPosiType({},function(rs,data,msg){
		// 		var
		// 			optionHtml;
		// 		$(el.J_posi_type).html('');
		// 		$(el.J_posi_type).append('<option value=0>请选择</option>');
		// 		S.each(data,function(item){
		// 			optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
		// 			$(el.J_posi_type).append(optionHtml);
		// 		})
		// 	})
		// }
	});
	return work;
},{
	requires:['core','io/job_apply/editResume' ,'mod/defender']
})

/*--------------------------------项目经验--------------------------------------*/
KISSY.add('editResume/project' , function(S){
	var
		$ = S.all,
		on = S.Event.on,
		editResumeIO = PW.io.job_apply.editResume,
		Defender = PW.mod.Defender,
		el = {
			J_add_project : '.J_add_project',
			J_edit_project : '.edit-project',
			J_del_project : '.del-project',
			J_editing_project_out : '.editing-project-out',
			J_editing_project : '.editing-project',
			J_project_insert_tr : '.project-insert-tr',
			J_project_start_year : '.project-start-year',
			J_project_end_year : '.project-end-year',
			J_project_start_month : '.project-start-month',
			J_project_end_month : '.project-end-month',
			J_project_name : '.project-name',
			J_project_cancel : '.project-cancel',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index',
			J_team_size : '.team-size',
			J_project_sum : '.project-sum',
			J_project_role : '.project-role',
			J_project_achievement : '.project-achievement',
			J_project_input : '.project-input',
			J_submit_in : '.project-submit-in',
			J_submit_out : '.project-submit-out',
			J_added_project : '.added-project',
			J_no_project_info : '.no-project-info',
			J_check_hook : '.check-hook',
			J_project_check : '.project-check'

		};
	function project(){
		this.init();
		this.validIn = Defender.client(el.J_editing_project,{
				showTip:false
			});
		this.validOut = Defender.client(el.J_editing_project_out,{
				showTip:false
			});
	}

	S.augment(project , {
		init:function(){
			this._addEventListener();
			// this._getYear();
		},

		_addEventListener:function(){
			var
				that = this;
			$(el.J_edit_project).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id');
				that._setExistedInfo(id);
				$(el.J_project_insert_tr).show();
				$(el.J_project_insert_tr).addClass(el.J_z_index);
				// $(el.J_project_insert_tr).insertAfter($(ev.currentTarget).parent().parent());
				$(el.J_project_input).attr('value',id);
				$(el.J_pop_wrap).show();
			});

			$(el.J_project_cancel).on('click',function(ev){
				$(el.J_project_insert_tr).hide();
				$(el.J_pop_wrap).hide();
				$(el.J_project_insert_tr).removeClass(el.J_z_index);
				$(el.J_editing_project_out).removeClass(el.J_z_index);
				$(el.J_editing_project_out).hide();
				$(el.J_project_input).removeAttr('value');
			});

			$(el.J_del_project).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id'),
					para = 'id='+id;
				editResumeIO.delProject(para,function(rs,data,msg){
					if(rs){
						$(ev.currentTarget).parent().parent().remove();
						if($(el.J_added_project).children('tbody').children('tr').length == 1){
							$(el.J_added_project).hide();
							$(el.J_no_project_info).show();
							$(el.J_project_check).children('em').removeClass(el.J_check_hook);
							$(el.J_project_check).children('em').text('未添加');
						}
					}
					else{
						alert(msg);
					}
				})
			});

			$(el.J_add_project).on('click',function(ev){
				$(el.J_editing_project_out).show();
				$(el.J_editing_project_out).addClass(el.J_z_index);
				$(el.J_pop_wrap).show();
			});

			$(el.J_submit_in).on('click',function(ev){
				that._formSubmit(ev , that.validIn ,'in');
			});

			$(el.J_submit_out).on('click',function(ev){
				that._formSubmit(ev , that.validOut ,'out');
			});	
		},

		// _getYear:function(){
		// 	editResumeIO.getYear({},function(rs,data,msg){
		// 		S.each(data,function(item){
		// 			var
		// 				optionHtml = '<option>'+item+'</option>';
		// 			$(el.J_project_start_year).append(optionHtml);
		// 			$(el.J_project_end_year).append(optionHtml);
		// 		})
		// 	})
		// },

		//当用户点击编辑时，表单中的值为用户之前已输入的值
		_setExistedInfo:function(id){
			var
				that = this,
				nowYear = new Date().getFullYear(),
				para = 'id='+id;
			editResumeIO.getProjectInfo(para,function(rs,data,msg){
				S.each($(el.J_project_start_year).item(0).children('option') , function(i,o){
					if($(i).val() == data.startYear){
						$(i).attr('selected','selected');
					}
				});
				S.each($(el.J_project_end_year).item(0).children('option') , function(i,o){
					if($(i).val() == data.endYear){
						$(i).attr('selected','selected');
					}
				});
				$(el.J_project_start_month).item(0).children().item(data.startMonth-1).attr('selected','selected');
				$(el.J_project_end_month).item(0).children().item(data.endMonth-1).attr('selected','selected');
				$(el.J_project_name).item(0).val(data.projectName);
				$(el.J_team_size).item(0).children().item(data.teamSize).attr('selected','selected');
				$(el.J_project_sum).item(0).val(data.projectSum);
				$(el.J_project_achievement).item(0).val(data.projectAchievement);
				$(el.J_project_role).item(0).val(data.projectRole);
			});
		},

		_formSubmit:function(ev,valid,which){
			var 
				that = this,
				startYear,
				startMonth,
				endYear,
				endMonth;

			if(which == 'in'){
				startYear = parseInt($(el.J_project_start_year).item(0).children('option:selected').val(),10);
				startMonth = parseInt($(el.J_project_start_month).item(0).children('option:selected').val(),10);
				endYear = parseInt($(el.J_project_end_year).item(0).children('option:selected').val(),10);
				endMonth = parseInt($(el.J_project_end_month).item(0).children('option:selected').val(),10);
			}
			if(which == 'out'){
				if($(el.J_added_project).length != 0){
					startYear = parseInt($(el.J_project_start_year).item(1).children('option:selected').val(),10);
					startMonth = parseInt($(el.J_project_start_month).item(1).children('option:selected').val(),10);
					endYear = parseInt($(el.J_project_end_year).item(1).children('option:selected').val(),10);
					endMonth = parseInt($(el.J_project_end_month).item(1).children('option:selected').val(),10);
				}
				else{
					startYear = parseInt($(el.J_project_start_year).item(0).children('option:selected').val(),10);
					startMonth = parseInt($(el.J_project_start_month).item(0).children('option:selected').val(),10);
					endYear = parseInt($(el.J_project_end_year).item(0).children('option:selected').val(),10);
					endMonth = parseInt($(el.J_project_end_month).item(0).children('option:selected').val(),10);
				}

			}
			
			valid.validAll(function(rs){
				if(rs){
					if(startYear > endYear){
						ev.preventDefault();
						Dialog.alert('开始年份不能大于结束年份');
					}
					else if(startYear == endYear){
						if(startMonth > endMonth){
							ev.preventDefault();
							Dialog.alert('开始月份不能大于结束月份');
						}
					}
				}
			})
		}
		
	});
	return project;
},{
	requires:['core','io/job_apply/editResume' ,'mod/defender']
})

/*--------------------------------培训经历--------------------------------------*/
KISSY.add('editResume/train' , function(S){
	var
		$ = S.all,
		on = S.Event.on,
		editResumeIO = PW.io.job_apply.editResume,
		Defender = PW.mod.Defender,
		el = {
			J_add_train : '.J_add_train',
			J_edit_train : '.edit-train',
			J_del_train : '.del-train',
			J_editing_train_out : '.editing-train-out',
			J_editing_train : '.editing-train',
			J_train_insert_tr : '.train-insert-tr',
			J_train_start_year : '.train-start-year',
			J_train_end_year : '.train-end-year',
			J_train_start_month : '.train-start-month',
			J_train_end_month : '.train-end-month',
			J_train_name : '.train-name',
			J_train_cancel : '.train-cancel',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index',
			J_train_content : '.train-content',
			J_train_agency : '.train-agency',
			J_train_input : '.train-input',
			J_submit_in:'.train-submit-in',
			J_submit_out:'.train-submit-out',
			J_added_train : '.added-train',
			J_no_train_info : '.no-train-info',
			J_check_hook : '.check-hook',
			J_train_check : '.train-check'
		};
	function train(){
		this.init();
		this.validIn = Defender.client(el.J_editing_train,{
				showTip:false
			});
		this.validOut = Defender.client(el.J_editing_train_out,{
				showTip:false
			});
	}

	S.augment(train , {
		init:function(){
			this._addEventListener();
			// this._getYear();
		},

		_addEventListener:function(){
			var
				that = this;
			$(el.J_edit_train).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id');
				that._setExistedInfo(id);
				$(el.J_train_insert_tr).show();
				$(el.J_train_insert_tr).addClass(el.J_z_index);
				// $(el.J_train_insert_tr).insertAfter($(ev.currentTarget).parent().parent());
				$(el.J_train_input).attr('value',id);
				$(el.J_pop_wrap).show();
			});

			$(el.J_train_cancel).on('click',function(ev){
				$(el.J_train_insert_tr).hide();
				$(el.J_pop_wrap).hide();
				$(el.J_train_insert_tr).removeClass(el.J_z_index);
				$(el.J_editing_train_out).removeClass(el.J_z_index);
				$(el.J_editing_train_out).hide();
				$(el.J_train_input).removeAttr('value');
			});

			$(el.J_del_train).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id'),
					para = 'id='+id;
				editResumeIO.delTrain(para,function(rs,data,msg){
					if(rs){
						$(ev.currentTarget).parent().parent().remove();
						if($(el.J_added_train).children('tbody').children('tr').length == 1){
							$(el.J_added_train).hide();
							$(el.J_no_train_info).show();
							$(el.J_train_check).children('em').removeClass(el.J_check_hook);
							$(el.J_train_check).children('em').text('未添加');
						}
					}
					else{
						alert(msg);
					}
				})
			});

			$(el.J_add_train).on('click',function(ev){
				$(el.J_editing_train_out).show();
				$(el.J_editing_train_out).addClass(el.J_z_index);
				$(el.J_pop_wrap).show();
			});

			$(el.J_submit_in).on('click',function(ev){
				that._formSubmit(ev , that.validIn ,'in');
			});

			$(el.J_submit_out).on('click',function(ev){
				that._formSubmit(ev , that.validOut ,'out');
			});	
		},

		// _getYear:function(){
		// 	editResumeIO.getYear({},function(rs,data,msg){
		// 		S.each(data,function(item){
		// 			var
		// 				optionHtml = '<option>'+item+'</option>';
		// 			$(el.J_train_start_year).append(optionHtml);
		// 			$(el.J_train_end_year).append(optionHtml);
		// 		})
		// 	})
		// },

		//当用户点击编辑时，表单中的值为用户之前已输入的值
		_setExistedInfo:function(id){
			var
				that = this,
				nowYear = new Date().getFullYear(),
				para = 'id='+id;
			editResumeIO.getTrainInfo(para,function(rs,data,msg){
				S.each($(el.J_train_start_year).item(0).children('option') , function(i,o){
					if($(i).val() == data.startYear){
						$(i).attr('selected','selected');
					}
				});
				S.each($(el.J_train_end_year).item(0).children('option') , function(i,o){
					if($(i).val() == data.endYear){
						$(i).attr('selected','selected');
					}
				});
				$(el.J_train_start_month).item(0).children().item(data.startMonth-1).attr('selected','selected');
				$(el.J_train_end_month).item(0).children().item(data.endMonth-1).attr('selected','selected');
				$(el.J_train_name).item(0).val(data.trainName);
				$(el.J_train_content).item(0).val(data.trainContent);
				$(el.J_train_agency).item(0).val(data.trainAgency);
			});
		},

		_formSubmit:function(ev,valid,which){
			var 
				that = this,
				startYear,
				startMonth,
				endYear,
				endMonth;

			if(which == 'in'){
				startYear = parseInt($(el.J_train_start_year).item(0).children('option:selected').val(),10);
				startMonth = parseInt($(el.J_train_start_month).item(0).children('option:selected').val(),10);
				endYear = parseInt($(el.J_train_end_year).item(0).children('option:selected').val(),10);
				endMonth = parseInt($(el.J_train_end_month).item(0).children('option:selected').val(),10);
			}
			if(which == 'out'){
				if($(el.J_added_train).length != 0){
					startYear = parseInt($(el.J_train_start_year).item(1).children('option:selected').val(),10);
					startMonth = parseInt($(el.J_train_start_month).item(1).children('option:selected').val(),10);
					endYear = parseInt($(el.J_train_end_year).item(1).children('option:selected').val(),10);
					endMonth = parseInt($(el.J_train_end_month).item(1).children('option:selected').val(),10);
				}
				else{
					startYear = parseInt($(el.J_train_start_year).item(0).children('option:selected').val(),10);
					startMonth = parseInt($(el.J_train_start_month).item(0).children('option:selected').val(),10);
					endYear = parseInt($(el.J_train_end_year).item(0).children('option:selected').val(),10);
					endMonth = parseInt($(el.J_train_end_month).item(0).children('option:selected').val(),10);
				}
			}
			
			valid.validAll(function(rs){
				if(rs){
					if(startYear > endYear){
						ev.preventDefault();
						Dialog.alert('开始年份不能大于结束年份');
					}
					else if(startYear == endYear){
						if(startMonth > endMonth){
							ev.preventDefault();
							Dialog.alert('开始月份不能大于结束月份');
						}
					}
				}
			})
		}
		
	});
	return train;
},{
	requires:['core','io/job_apply/editResume' ,'mod/defender']
})

/*--------------------------------求职经历--------------------------------------*/
KISSY.add('editResume/apply' , function(S){
	var
		$ = S.all,
		on = S.Event.on,
		editResumeIO = PW.io.job_apply.editResume,
		Defender = PW.mod.Defender,
		el = {
			J_add_apply : '.J_add_apply',
			J_edit_apply : '.edit-apply',
			J_del_apply : '.del-apply',
			J_editing_apply_out : '.editing-apply-out',
			J_editing_apply : '.editing-apply',
			J_apply_insert_tr : '.apply-insert-tr',
			J_apply_year : '.apply-year',
			J_apply_month : '.apply-month',
			J_apply_company_name : '.apply-company-name',
			J_apply_cancel : '.apply-cancel',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index',
			J_apply_posi_name : '.apply-posi-name',
			J_apply_type : '.apply-type',
			J_apply_input : '.apply-input',
			J_added_apply : '.added-apply',
			J_no_apply_info : '.no-apply-info',
			J_check_hook : '.check-hook',
			J_apply_check : '.apply-check'
		};
	function apply(){
		this.init();
		this.valid = Defender.client(el.J_editing_apply,{
				showTip:false
			});
		this.valid = Defender.client(el.J_editing_apply_out,{
				showTip:false
			});
	}

	S.augment(apply , {
		init:function(){
			this._addEventListener();
			// this._getYear();
		},

		_addEventListener:function(){
			var
				that = this;
			$(el.J_edit_apply).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id');
				that._setExistedInfo(id);
				$(el.J_apply_insert_tr).show();
				$(el.J_apply_insert_tr).addClass(el.J_z_index);
				// $(el.J_apply_insert_tr).insertAfter($(ev.currentTarget).parent().parent());
				$(el.J_apply_input).attr('value',id);
				$(el.J_pop_wrap).show();
			});

			$(el.J_apply_cancel).on('click',function(ev){
				$(el.J_apply_insert_tr).hide();
				$(el.J_pop_wrap).hide();
				$(el.J_apply_insert_tr).removeClass(el.J_z_index);
				$(el.J_editing_apply_out).removeClass(el.J_z_index);
				$(el.J_editing_apply_out).hide();
				$(el.J_apply_input).removeAttr('value');
			});

			$(el.J_del_apply).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id'),
					para = 'id='+id;
				editResumeIO.delApply(para,function(rs,data,msg){
					if(rs){
						$(ev.currentTarget).parent().parent().remove();
						if($(el.J_added_apply).children('tbody').children('tr').length == 1){
							$(el.J_added_apply).hide();
							$(el.J_no_apply_info).show();
							$(el.J_apply_check).children('em').removeClass(el.J_check_hook);
							$(el.J_apply_check).children('em').text('未添加');
						}
					}
					else{
						alert(msg);
					}
				})
			});

			$(el.J_add_apply).on('click',function(ev){
				$(el.J_editing_apply_out).show();
				$(el.J_editing_apply_out).addClass(el.J_z_index);
				$(el.J_pop_wrap).show();
			});
		},

		// _getYear:function(){
		// 	editResumeIO.getYear({},function(rs,data,msg){
		// 		S.each(data,function(item){
		// 			var
		// 				optionHtml = '<option>'+item+'</option>';
		// 			$(el.J_apply_year).append(optionHtml);
		// 		})
		// 	})
		// },

		//当用户点击编辑时，表单中的值为用户之前已输入的值
		_setExistedInfo:function(id){
			var
				that = this,
				nowYear = new Date().getFullYear(),
				para = 'id='+id;
			editResumeIO.getApplyInfo(para,function(rs,data,msg){
				S.each($(el.J_apply_year).item(0).children('option') , function(i,o){
					if($(i).val() == data.applyYear){
						$(i).attr('selected','selected');
					}
				});
				$(el.J_apply_month).item(0).children().item(data.applyMonth-1).attr('selected','selected');
				$(el.J_apply_company_name).item(0).val(data.applyCompanyName);
				$(el.J_apply_posi_name).item(0).val(data.applyPosiName);
				$(el.J_apply_type).item(0).children('input').item(data.applyType).attr('checked','checked');
			});
		}
		
	});
	return apply;
},{
	requires:['core','io/job_apply/editResume' ,'mod/defender']
})

/*-------------------------------------附件------------------------------------------*/
KISSY.add('editResume/attachment',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		editResumeIO = PW.io.job_apply.editResume,
		el = {
			J_file_submit : '.file-submit',
			J_upload_cancel : '.upload-cancel',
			J_del_attachment : '.del-attachment',
			J_to_upload : '.to-upload',
			J_file_upload : '.file-upload',
			J_upload_tip : '.upload-tip',
			J_pop_wrap : '.pop-wrap',
			J_z_index : '.z-index',
			J_add_attach : '.J_add_attach',
			J_added_attachment : '.added-attachment',
			J_no_attachment_info : '.no-attachment-info',
			J_check_hook : '.check-hook',
			J_attachment_check : '.attachment-check'
		};

	function attachment(){
		this.init();
	}

	S.augment(attachment, {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){

			$(el.J_add_attach).on('click',function(ev){
				$(el.J_to_upload).show();
				$(el.J_pop_wrap).show();
				$(el.J_to_upload).addClass(el.J_z_index);
			})

			$(el.J_file_upload).on('change',function(){

				file = document.getElementById('upload-attachment-file').files;
				fileSize = file[0].size/1024;
				filePath = $(el.J_file_upload).val();
				suffix = filePath.substring(filePath.length-3 , filePath.length);

				if (fileSize > 5120) {
					$(el.J_upload_tip).text('文件不得大于5M');
					$(el.J_file_submit).attr('disabled','disabled');
				}else if(suffix =='exe'|| suffix=='txt'||suffix=='bat'||suffix=='xml'||suffix=='php'||suffix=='vbs'||suffix=='.js'
				         ||suffix=='jse'||suffix=='wsh'||suffix=='wsf')
				{
                    $(el.J_upload_tip).text('请上传正确的附件');
					$(el.J_file_submit).attr('disabled','disabled');
				}else{
					$(el.J_upload_tip).text('已选择'+filePath);
					$(el.J_file_submit).removeAttr('disabled','disabled');
				}

			})

			$(el.J_file_submit).on('click',function(ev){
				ev.preventDefault();
				if($(el.J_file_upload).val().length != 0){
					document.getElementById('attachment-file').submit();
				}
			})

			$(el.J_upload_cancel).on('click',function(ev){
				$(el.J_to_upload).hide();
				$(el.J_pop_wrap).hide();
				$(el.J_to_upload).removeClass(el.J_z_index);
			})

			$(el.J_del_attachment).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().parent().attr('id'),
					para = 'id='+id;
				editResumeIO.delAttachment(para,function(rs,data,msg){
					if(rs){
						$(ev.currentTarget).parent().parent().remove();
						if($(el.J_added_attachment).children('tbody').children('tr').length == 0){
							$(el.J_added_attachment).hide();
							$(el.J_no_attachment_info).show();
							$(el.J_attachment_check).children('em').removeClass(el.J_check_hook);
							$(el.J_attachment_check).children('em').text('未添加');
						}
					}
					else{
						alert(msg);
					}
				})
			});
		}
	})
	return attachment;
},{
	requires:['core','io/job_apply/editResume']
})

/*-------------------------------左侧导航栏已编辑好的信息的改变--------------------*/
KISSY.add('editResume/editedInfo',function(S){
	var
		$ = S.all,
		editResumeIO = PW.io.job_apply.editResume,
		el = {
			J_sketchy_title : '.sketchy-title',
			J_check_hook : 'check-hook'
		};

	function editedInfo(){
		this.init();
	}

	S.augment(editedInfo , {
		init:function(){
			this._checkHook();
		},

		_checkHook:function(){
			var
				lis = $('li',el.J_sketchy_title);
			editResumeIO.getEditedArray({},function(rs,data,msg){
				S.each(data,function(item){
					lis.item(item).children('em').text('');
					lis.item(item).children('em').addClass(el.J_check_hook);
				})
			})
		}
	})
	return editedInfo;
},{
	requires:['core','io/job_apply/editResume']
})
/*-----------------------------------------------------------------------------
* @Description:     编辑简历页面相关js
* @Version:         1.0.0
* @author:          daiqiaoling
* @date             2015.7.15
* @email			1649500603@qq.com
* ==NOTES:=============================================
* v1.0.0(2015.7.15):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/resume' , function(S,editResume,linkage){
	PW.namespace('page.job_apply.resume');
	PW.page.job_apply.resume = function(param){
		new editResume(param);
		new linkage(param);
	}
},{
	requires:['resume/editResume','module/linkage']
});
/*------------------------显示建议---------------------------------------*/
KISSY.add('resume/editResume',function(S){
	var $ = S.all, 
		on = S.Event.on,
		DOM = S.DOM,
		Defender = PW.mod.Defender,

		el = {
				J_EditBtn:'.J_edit',//指向编辑或添加按钮
				J_DefaultBox:'.J_default',//指向默认显示框
				J_Editform:'form',//指向编辑的表单
				J_FinishedBox:'.J_finished',//指向填写完成的框
				J_Save:'.save',//指向保存按钮
				J_Cancel:'.cancel',//指向取消按钮
				J_Submodule:'.submodule',//指向每一个子模块
				J_Nav:'.left_nav'//指向左侧导航
		};
		
	function editResume(param){
		this.opts = param;
		this.init();
	}

	S.augment(editResume,{
		init:function(){
			this._valid();
			this._editInfo();
		},
		_valid: function(form){
			var
				forms = $(el.J_Editform),
				length = forms.length,
				valid = {};
			S.each(forms, function(item, index){
				valid[index + 1] = Defender.client('#form' + [index + 1],{
					showTip: true
				});
			});
			this.valid = valid;
		},

		_editInfo:function(){
			var 
				that = this,
				opts = that.opts,
				$submodules = $(el.J_Submodule),
				$navLi = $(el.J_Nav).children('ul').children();

			$(el.J_Editform).hide();//刚打开页面，表单不显示	

			that._showInfo($navLi);//如果有填写完成的信息，则显示信息，否则显示默认框			
			
			//点击编辑或添加按钮时，对应的编辑表单显示，默认框和填写完的信息均隐藏
			on(el.J_EditBtn,'click',function(){
				var $h3 = $(this).parent();
				if($h3.next(el.J_DefaultBox)){
					$h3.next(el.J_DefaultBox).hide();
				}
				$h3.next(el.J_Editform).show();
				$h3.next(el.J_FinishedBox).hide();
			});

			/*点击保存按钮*/
			on(el.J_Save,'click',function(evt){
				that._formSubmit(evt.target,$navLi);
			});

			/*点击取消按钮*/
			on(el.J_Cancel,'click',function(evt){
				var 
					$currentform = $(evt.target).parent('form');

				$currentform.hide();
				that._showInfo($navLi);
			});
		},
		_showInfo:function($navLi){
			var 
				that = this,
				$FinishedBoxes = $(el.J_FinishedBox);

			S.each( $FinishedBoxes , function(elem,index){
				var 
					$tr_length = $(elem).children('table').children('tbody').children('tr').length/*
								 - $(elem).children('table').children('thead').children('tr').length*/,
					$li_length = $(elem).children('ul').children('li').length,
					$p_length = $(elem).children('p').length,
					$submodule_index = index,
					$length = $tr_length || $li_length ||$p_length;
				
				if( $length > 0){
					$(elem).show();
					if($(elem).prev(el.J_DefaultBox)){						
						$(elem).prev(el.J_DefaultBox).hide();
					}
					S.each($navLi,function(elem,index){
						if( index == $submodule_index ){
							$(elem).children('em').text('');
							$(elem).children('em').addClass('correct');
						}
					});

				}else{
					$(elem).hide();		
					if($(elem).prev(el.J_DefaultBox)){						
						$(elem).prev(el.J_DefaultBox).show();
					}

					S.each($navLi,function(elem,index){
						if( index == $submodule_index ){
							$(elem).children('em').removeClass('correct');
							$(elem).children('em').text('未添加');
						}
					});
				}
			});
		},

		_formSubmit:function(evt,$navLi){
			var
				that = this, 
				valid = that.valid,
				opts = that.opts,
				$J_form = $(evt).parent('form'),
				formId = $J_form.attr('id').slice(4);
			valid[formId].validAll(function(rs){
				if(rs){
					$J_form[0].submit();
					that._finishInfo($J_form , formId , $navLi);
					$J_form.hide();					
					$J_form.next().show();
				}
			});
		},

		_finishInfo:function(form , formId , $navLi){
			var	
				that = this,
				HTML;

			if(form.hasClass('J_Education')){
				var
					$degree_select_val = form.children('.J_DegreeType').children('.J_Degree').one(':selected').text(),
					$length = form.next('div').children('table').children('tbody').children('tr').length,
					$time = form.children('.time'),
					$id = 'edu' + $length;

				HTML = "<tr id='" + $id + "'>" 
					 + '<td>' + $('.J_SchoolName').val() + '</td>'
					 + '<td>' +  $time.children("#start-year").val() + '-' +  $time.children("#start-month").val() + '至' +  $time.children("#end-year").val() + '-' +  $time.children("#end-month").val() + '</td>'
					 + '<td class="td-short">' + $degree_select_val + '</td>'
					 + '<td class="td-short">' + $('.major').val() + '</td>'
					 + '<td class="td-shortest"><a href="javascript:;">编辑</a><a href="javascript:;" class="more-margin">删除</a></td>';

				form.next().children().children('tbody').append(HTML);

			}else if(form.hasClass('J_SchoolJob')){
				var
					$which_school_val = form.children('.J_WhichSchool').children('.J_School').one(':selected').text(),
					$length = form.next('div').children('table').children('tbody').children('tr').length,
					$time = form.children('.time'),
					$id = 'schooljob' + $length;
				
				HTML = "<tr id='" + $id + "'>" 
					 + '<td>' + $('.J_SchoolJobName').val() + '</td>'
					 + '<td>' +  $time.children("#start-year").val() + '-' +  $time.children("#start-month").val() + '至' +  $time.children("#end-year").val() + '-' + $time.children("#end-month").val() + '</td>'
					 + '<td class="td-short">' + $which_school_val + '</td>'
					 + '<td class="td-short">' + '</td>'
					 + '<td class="td-shortest"><a href="javascript:;">编辑</a><a href="javascript:;" class="more-margin">删除</a></td>';

				form.next().children().children('tbody').append(HTML);


			}else if(form.hasClass('J_Awards')){
				var
					$which_school_val = form.children('.J_WhichSchool').children('.J_School').one(':selected').text(),
					$time = form.children('.time'),
					$length = form.next('div').children('table').children('tbody').children('tr').length,
					$id = 'award' + $length;
				
				HTML = "<tr id='" + $id + "'>" 
					 + '<td>' + $('.J_AwardName').val() + '</td>'
					 + '<td>' + $time.children("#start-year").val() + '-' + $time.children("#start-month").val() + '</td>'
					 + '<td class="td-short">' + $which_school_val + '</td>'
					 + '<td class="td-short">' + '</td>'
					 + '<td class="td-shortest"><a href="javascript:;">编辑</a><a href="javascript:;" class="more-margin">删除</a></td>';

				form.next().children().children('tbody').append(HTML);

			}else if(form.hasClass('J_LanguageAbility')){
				var
					$language_ability = form.children('.language_ability_edit'),
					$language_val = $language_ability.children('.J_Language').children('.J_Type').one(':selected').text(),
					$listen_val = $language_ability.children('.J_Listen').children('.J_level').one(':selected').text(),
					$read_val = $language_ability.children('.J_Read').children('.J_level').one(':selected').text(),
					$exam_type = form.children('.J_Exams').children('.J_Exam').one(':selected').text(),
					$exam_val = form.children('.J_Exams').children('input').val(),
					$grade,
					$length = form.next('div').children('table').children('tbody').children('tr').length,
					$id = 'language' + $length;
				
				if(form.children('.J_Exams').children('.J_Exam').val() == 0){
					$grade = "";
				}else{
					$exam_val = '(' + $exam_val + '分)';
					$grade = $exam_type + $exam_val;
				}

				HTML = "<tr id='" + $id + "'>" 
					 + '<td class="td-short">' + $language_val + '</td>'
					 + '<td class="td-short">' + $listen_val + '</td>'
					 + '<td class="td-short">' + $read_val + '</td>'
					 + '<td class="td-long">' + $grade + '</td>'
					 + '<td class="td-shortest"><a href="javascript:;">编辑</a><a href="javascript:;" class="more-margin">删除</a></td>';

				form.next().children().children('tbody').append(HTML);

			}else if(form.hasClass('J_Practice')){
				var
					$time = form.children('.time'),
					$length = form.next('div').children('table').children('tbody').children('tr').length,
					$id = 'practice' + $length;
				
				HTML = "<tr id='" + $id + "'>" 
					 + '<td class="td-long">' + $('.J_PracticeCompany').val() + '</td>'
					 + '<td class="td-long">' +  $time.children("#start-year").val() + '-' +  $time.children("#start-month").val() + '至' +  $time.children("#end-year").val() + '-' + $time.children("#end-month").val() + '</td>'
					 + '<td class="td-short">' + $('.J_PracticeJob').val() + '</td>'
					 + '<td class="td-shortest"><a href="javascript:;">编辑</a><a href="javascript:;" class="more-margin">删除</a></td>';

				form.next().children().children('tbody').append(HTML);

			}else if(form.hasClass('J_Project')){
				var
					$time = form.children('.time'),
					$length = form.next('div').children('table').children('tbody').children('tr').length,
					$id = 'project' + $length;
				
				HTML = "<tr id='" + $id + "'>" 
					 + '<td class="td-long">' + $('.J_ProjectName').val() + '</td>'
					 + '<td class="td-long">' +  $time.children("#start-year").val() + '-' +  $time.children("#start-month").val() + '至' +  $time.children("#end-year").val() + '-' + $time.children("#end-month").val() + '</td>'
					 + '<td class="td-short">' + $('.J_Role').val() + '</td>'
					 + '<td class="td-shortest"><a href="javascript:;">编辑</a><a href="javascript:;" class="more-margin">删除</a></td>';

				form.next().children().children('tbody').append(HTML);

			}else if(form.hasClass('J_Work')){
				var
					$time = form.children('.time'),
					$length = form.next('div').children('table').children('tbody').children('tr').length,
					$id = 'work' + $length;
				
				HTML = "<tr id='" + $id + "'>" 
					 + '<td class="td-long">' + $('.J_WorkCompany').val() + '</td>'
					 + '<td class="td-long">' +  $time.children("#start-year").val() + '-' +  $time.children("#start-month").val() + '至' +  $time.children("#end-year").val() + '-' + $time.children("#end-month").val() + '</td>'
					 + '<td class="td-short">' + $('.J_Position').val() + '</td>'
					 + '<td class="td-shortest"><a href="javascript:;">编辑</a><a href="javascript:;" class="more-margin">删除</a></td>';

				form.next().children().children('tbody').append(HTML);

			}else if(form.hasClass('J_Train')){
				var
					$time = form.children('.time'),
					$length = form.next('div').children('table').children('tbody').children('tr').length,
					$id = 'train' + $length;
				
				HTML = "<tr id='" + $id + "'>" 
					 + '<td class="td-long">' + $('.J_TrainName').val() + '</td>'
					 + '<td class="td-long">' +  $time.children("#start-year").val() + '-' +  $time.children("#start-month").val() + '至' +  $time.children("#end-year").val() + '-' + $time.children("#end-month").val() + '</td>'
					 + '<td class="td-short">' + '</td>'
					 + '<td class="td-shortest"><a href="javascript:;">编辑</a><a href="javascript:;" class="more-margin">删除</a></td>';

				form.next().children().children('tbody').append(HTML);

			}else if(form.hasClass('J_Apply')){
				var
					$time = form.children('.time'),
					$length = form.next('div').children('table').children('tbody').children('tr').length,
					$id = 'apply' + $length;
				
				HTML = "<tr id='" + $id + "'>" 
					 + '<td class="td-long">' + $('.J_ApplyCompany').val() + '</td>'
					 + '<td class="td-short">' + $('.J_ApplyPosition').val() + '</td>'
					 + '<td class="td-short">' + $('.J_Type').val() + '</td>'
					 + '<td class="td-short">' + $time.children("#start-year").val() + '-' +  $time.children("#start-month").val() + '</td>'
					 + '<td class="td-shortest"><a href="javascript:;">编辑</a><a href="javascript:;" class="more-margin">删除</a></td>';

				form.next().children().children('tbody').append(HTML);

			}
			

			

			that._showInfoAgain(formId , $navLi);
			
		},
		_showInfoAgain:function(formId , $navLi){
			var 
				that = this;

			S.each($navLi,function(elem,index){
				index = index + 1;
				if( index == formId ){
					if(!$(elem).hasClass('correct')){
						$(elem).children('em').text('');
						$(elem).children('em').addClass('correct');
					}						
				}
			});
		},
			

	});	
	return editResume;
},{
	requires:['event','mod/defender']
});


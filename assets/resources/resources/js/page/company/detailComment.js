/*-----------------------------------------------------------------------------
* @Description:     detail-comment页面相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.09
* ==NOTES:=============================================
* v1.0.0(2015.07.09):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/detailComment',function(S,detailCommentShow){
	PW.namespace('page.company.detailComment');
	PW.page.company.detailComment = function(){
		new detailCommentShow();
	}
},{
	requires:['detailComment/detailCommentShow']
})

/*---------------------------------------------------------------------------*/
KISSY.add('detailComment/detailCommentShow',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		Defender = PW.mod.Defender,
		jobDetailIO = PW.io.company.jobDetail,
		el = {
			J_follow :'.follow',
			J_unfollow : '.unfollow',
			J_follow_number : ".follow-number",
			J_company_name:".company-name",
			J_company_id:"company-id",
			J_rate:'.rate',
			J_rate_level_1_hover:'rate-level-1-hover',
			J_rate_level_2_hover:'rate-level-2-hover',
			J_rate_level_3_hover:'rate-level-3-hover',
			J_rate_level_4_hover:'rate-level-4-hover',
			J_rate_level_5_hover:'rate-level-5-hover',
			J_rate_level_1:'rate-level-1',
			J_rate_level_2:'rate-level-2',
			J_rate_level_3:'rate-level-3',
			J_rate_level_4:'rate-level-4',
			J_rate_level_5:'rate-level-5',
			J_rate_tip:'.rate-tip',
			J_satisfy_degree:'.satisfy-degree',
			J_click_satisfy_degree:'.click_satisfy-degree',
			J_first_input:'.first-input',
			J_second_input:'.second-input',
			J_third_input:'.third-input',
			J_fourth_input:'.fourth-input',
			J_sub:'.sub',
			J_advise:'.advise',
			J_advise_tip:'.advise-tip',
			J_form:'#J_form'
		},
		myvar = {

		};

	function detailCommentShow(){
		this.init();
	}

	S.augment(detailCommentShow,{
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var
				that = this,
				isFollow ,
				isCollect;
			$(el.J_unfollow).on('click',function(ev){
				isFollow = myvar.no;
				$(el.J_unfollow).hide();
				$(el.J_follow).show();
				var para = 'companyId='+$(el.J_company_name).attr(el.J_company_id)+'&isFollow='+isFollow;
				jobDetailIO.getFollowNumberIO(para,function(code,data,msg){
					if(code == 0){
						$(el.J_follow_number).text(data.followNumber);
					}
				})
			});

			$(el.J_follow).on('click',function(ev){
				isFollow = myvar.yes;
				$(el.J_follow).hide();
				$(el.J_unfollow).show();
				var para = 'companyId='+$(el.J_company_name).attr(el.J_company_id)+'&isFollow='+isFollow;
				jobDetailIO.getFollowNumberIO(para,function(code,data,msg){
					if(code == 1){
						$(el.J_pop).show();
						$(el.J_login).show();
						$(el.J_register).hide();
					}
					if(code == 0){
						$(el.J_follow_number).text(data.followNumber);
					}
				})
			});

			$('a',el.J_rate).on('mouseenter',function(ev){
				if($(ev.currentTarget).attr('value') == 1){
					$(ev.currentTarget).parent().removeClass('rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover');
					$(ev.currentTarget).parent().addClass(el.J_rate_level_1_hover);
					$(ev.currentTarget).parent().next().text($(ev.currentTarget).text());
				}
				if($(ev.currentTarget).attr('value') == 2){
					$(ev.currentTarget).parent().removeClass('rate-level-1-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover');
					$(ev.currentTarget).parent().addClass(el.J_rate_level_2_hover);
					$(ev.currentTarget).parent().next().text($(ev.currentTarget).text());
				}
				if($(ev.currentTarget).attr('value') == 3){
					$(ev.currentTarget).parent().removeClass('rate-level-2-hover rate-level-1-hover rate-level-4-hover rate-level-5-hover');
					$(ev.currentTarget).parent().addClass(el.J_rate_level_3_hover);
					$(ev.currentTarget).parent().next().text($(ev.currentTarget).text());
				}
				if($(ev.currentTarget).attr('value') == 4){
					$(ev.currentTarget).parent().removeClass('rate-level-2-hover rate-level-3-hover rate-level-1-hover rate-level-5-hover');
					$(ev.currentTarget).parent().addClass(el.J_rate_level_4_hover);
					$(ev.currentTarget).parent().next().text($(ev.currentTarget).text());
				}
				if($(ev.currentTarget).attr('value') == 5){
					$(ev.currentTarget).parent().removeClass('rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-1-hover');
					$(ev.currentTarget).parent().addClass(el.J_rate_level_5_hover);
					$(ev.currentTarget).parent().next().text($(ev.currentTarget).text());
				}
			});

			$(el.J_rate).on('mouseleave',function(ev){
				$(el.J_rate).removeClass('rate-level-1-hover rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover');
				$(ev.currentTarget).next().text('');
			});

			$('a',el.J_rate).on('click',function(ev){
				$(ev.currentTarget).parent().next().next().next().hide();
				if($(ev.currentTarget).attr('value') == 1){
					$(ev.currentTarget).parent().removeClass('rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover rate-level-2 rate-level-3 rate-level-4 rate-level-5');
					$(ev.currentTarget).parent().addClass(el.J_rate_level_1);
					$(ev.currentTarget).parent().next().next().text($(ev.currentTarget).text());
					$(ev.currentTarget).parent().prev().val($(ev.currentTarget).attr('value'));
				}
				if($(ev.currentTarget).attr('value') == 2){
					$(ev.currentTarget).parent().removeClass('rate-level-1-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover rate-level-1 rate-level-3 rate-level-4 rate-level-5');
					$(ev.currentTarget).parent().addClass(el.J_rate_level_2);
					$(ev.currentTarget).parent().next().next().text($(ev.currentTarget).text());
					$(ev.currentTarget).parent().prev().val($(ev.currentTarget).attr('value'));
				}
				if($(ev.currentTarget).attr('value') == 3){
					$(ev.currentTarget).parent().removeClass('rate-level-2-hover rate-level-1-hover rate-level-4-hover rate-level-5-hover rate-level-2 rate-level-1 rate-level-4 rate-level-5');
					$(ev.currentTarget).parent().addClass(el.J_rate_level_3);
					$(ev.currentTarget).parent().next().next().text($(ev.currentTarget).text());
					$(ev.currentTarget).parent().prev().val($(ev.currentTarget).attr('value'));
				}
				if($(ev.currentTarget).attr('value') == 4){
					$(ev.currentTarget).parent().removeClass('rate-level-2-hover rate-level-3-hover rate-level-1-hover rate-level-5-hover rate-level-2 rate-level-3 rate-level-1 rate-level-5');
					$(ev.currentTarget).parent().addClass(el.J_rate_level_4);
					$(ev.currentTarget).parent().next().next().text($(ev.currentTarget).text());
					$(ev.currentTarget).parent().prev().val($(ev.currentTarget).attr('value'));
				}
				if($(ev.currentTarget).attr('value') == 5){
					$(ev.currentTarget).parent().removeClass('rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-1-hover rate-level-2 rate-level-3 rate-level-4 rate-level-1');
					$(ev.currentTarget).parent().addClass(el.J_rate_level_5);
					$(ev.currentTarget).parent().next().next().text($(ev.currentTarget).text());
					$(ev.currentTarget).parent().prev().val($(ev.currentTarget).attr('value'));
				}
			});

			$(el.J_sub).on('click',function(ev){
				that._formSubmit(ev);
			});

			$(el.J_advise).on('click',function(ev){
				$(el.J_advise_tip).hide();
				$(ev.currentTarget).removeAttr('placeholder');
			})

		},

		_formSubmit:function(ev){
			ev.preventDefault();

			var
				isSubmit = true, 
				adviseLength = $(el.J_advise).val().length,
				unSelected = [],				
				inputLength = 0,
				hiddenInput = $('input:[type="hidden"]');
			S.log(adviseLength);
			S.each(hiddenInput , function(i,o){
				if($(i).val())
					inputLength ++;
				else
					unSelected.push(i);
			});

			if(inputLength < 4){
				isSubmit = false;
				for(var i=0 ; i<unSelected.length ; i ++){
					$(unSelected[i]).next().next().next().next().show();
				}
			};

			if(adviseLength < 10){
				$(el.J_advise_tip).show();
				isSubmit = false;
			};

			if(isSubmit){
				document.getElementById("J_form").submit();
			}
		}
	});
	return detailCommentShow;
},{
	requires:['mod/defender','io/company/jobDetail']
})
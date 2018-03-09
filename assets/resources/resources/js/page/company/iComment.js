/*-----------------------------------------------------------------------------
* @Description:     i-comment页面相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.07
* ==NOTES:=============================================
* v1.0.0(2015.07.07):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/iComment',function(S,iComentShow){
	PW.namespace('page.company.iComment');
	PW.page.company.iComment = function(){
		new iComentShow();
	}
},{
	requires:['iComment/iComentShow']
})

/*---------------------------------------------------------------------------*/
KISSY.add('iComment/iComentShow',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		Defender = PW.mod.Defender,
		jobDetailIO = PW.io.company.jobDetail,
		el = {
			J_follow :'.follow',
			J_unfollow : '.unfollow',
			J_follow_number : ".follow-number",
			J_staff:'.staff',
			J_not_staff:'.not-staff',
			J_share_form:'.share-form',
			J_submit:'.submit',
			J_work_experience:'.work-experience',
			J_add_experience:'.add-experience',
			J_rate:'.rate',
			J_form:'#J_form',
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
			J_position_name:'.position-name',
			J_score:'.score',
			J_position_tip:'.position-tip',
			J_rate_tip:'.rate-tip',
			J_satisfy_degree:'.satisfy-degree',
			J_company_name:".company-name",
			J_company_id:"company-id"
		},
		myvar = {
			isStaff:true
		};

	function iCommentShow(){
		this.init();
	}

	S.augment(iCommentShow,{
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
			on(el.J_submit,'click',function(ev){
				that._formSubmit(ev);
			});

			on(el.J_staff,'click',function(ev){
				myvar.isStaff = true;
				$(el.J_position_name).removeAttr('disabled');
				$(el.J_work_experience).show();
				$(el.J_add_experience).hide();
			});

			on(el.J_not_staff,'click',function(ev){
				myvar.isStaff = false;
				$(el.J_position_name).attr('disabled','disabled');
				$(el.J_work_experience).hide();
				$(el.J_add_experience).show();
			});

			on(el.J_position_name,'click',function(ev){
				$(el.J_position_tip).hide();
			});

			$('a',el.J_rate).on('mouseenter',function(ev){
				$(el.J_rate_tip).hide();
				if($(ev.currentTarget).attr('value') == 1){
					$(el.J_rate).removeClass('rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover');
					$(el.J_rate).addClass(el.J_rate_level_1_hover);
					$(el.J_satisfy_degree).text($(ev.currentTarget).text());
				}
				if($(ev.currentTarget).attr('value') == 2){
					$(el.J_rate).removeClass('rate-level-1-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover');
					$(el.J_rate).addClass(el.J_rate_level_2_hover);
					$(el.J_satisfy_degree).text($(ev.currentTarget).text());
				}
				if($(ev.currentTarget).attr('value') == 3){
					$(el.J_rate).removeClass('rate-level-2-hover rate-level-1-hover rate-level-4-hover rate-level-5-hover');
					$(el.J_rate).addClass(el.J_rate_level_3_hover);
					$(el.J_satisfy_degree).text($(ev.currentTarget).text());
				}
				if($(ev.currentTarget).attr('value') == 4){
					$(el.J_rate).removeClass('rate-level-2-hover rate-level-3-hover rate-level-1-hover rate-level-5-hover');
					$(el.J_rate).addClass(el.J_rate_level_4_hover);
					$(el.J_satisfy_degree).text($(ev.currentTarget).text());
				}
				if($(ev.currentTarget).attr('value') == 5){
					$(el.J_rate).removeClass('rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-1-hover');
					$(el.J_rate).addClass(el.J_rate_level_5_hover);
					$(el.J_satisfy_degree).text($(ev.currentTarget).text());
				}
			});

			$(el.J_rate).on('mouseleave',function(ev){
				$(el.J_rate).removeClass('rate-level-1-hover rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover');
				$(el.J_satisfy_degree).text('');
			});

			$('a',el.J_rate).on('click',function(ev){
				var value = $(ev.currentTarget).attr('value');
				if($(ev.currentTarget).attr('value') == 1){
					$(el.J_rate).removeClass('rate-level-1-hover rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover rate-level-2 rate-level-3 rate-level-4 rate-level-5');
					$(el.J_rate).addClass(el.J_rate_level_1);
				}
				if($(ev.currentTarget).attr('value') == 2){
					$(el.J_rate).removeClass('rate-level-1-hover rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover rate-level-1 rate-level-3 rate-level-4 rate-level-5');
					$(el.J_rate).addClass(el.J_rate_level_2);
				}
				if($(ev.currentTarget).attr('value') == 3){
					$(el.J_rate).removeClass('rate-level-1-hover rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover rate-level-2 rate-level-1 rate-level-4 rate-level-5');
					$(el.J_rate).addClass(el.J_rate_level_3)
				}
				if($(ev.currentTarget).attr('value') == 4){
					$(el.J_rate).removeClass('rate-level-1-hover rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover rate-level-2 rate-level-3 rate-level-1 rate-level-5');
					$(el.J_rate).addClass(el.J_rate_level_4);
				}
				if($(ev.currentTarget).attr('value') == 5){
					$(el.J_rate).removeClass('rate-level-1-hover rate-level-2-hover rate-level-3-hover rate-level-4-hover rate-level-5-hover rate-level-2 rate-level-3 rate-level-4 rate-level-1');
					$(el.J_rate).addClass(el.J_rate_level_5);
				}

				$(el.J_score).val(value);
				$(el.J_rate_tip).hide();
			});
		},

		_formSubmit:function(ev){
			ev.preventDefault();

			var 
				length = 0,
				isPostionNameNull = true,
				isScore = true,
				radio = $('input[type="radio"]');
			S.each(radio,function(i,o){
				if($(i).attr('checked') == 'checked')
					length ++;
			});
			if(myvar.isStaff){
				var string = $(el.J_position_name).text();
				if(string.length == 0){
					S.log($(el.J_position_tip));
					$(el.J_position_tip).show();
					isPostionNameNull = false;
				}
			};
			if($(el.J_score).text().length == 0){
				isScore = false;
				$(el.J_rate_tip).show()
			};
			if(length == 3 && isScore && isPostionNameNull)
			 	document.getElementById("J_form").submit();
		}
	});
	return iCommentShow;
},{
	requires:['mod/defender','io/company/jobDetail']
})
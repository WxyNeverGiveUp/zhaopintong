/*-----------------------------------------------------------------------------
* @Description:     experience页面相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.03
* ==NOTES:=============================================
* v1.0.0(2015.07.03):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/experience',function(S,experienceShow){
	PW.namespace('page.company.experience');
	PW.page.company.experience = function(param){
		new experienceShow(param);
	}
},{
	requires:['experience/experienceShow']
});

/*--------------------------------------------------------------------------*/
KISSY.add('experience/experienceShow',function(S){
	var
		$ = S.all,
		experienceIO = PW.io.company.experience,
		jobDetailIO = PW.io.company.jobDetail,
		on = S.Event.on,
		delegate = S.Event.delegate,
		el = {
			J_follow :'.follow',
			J_unfollow : '.unfollow',
			J_follow_number : ".follow-number",
			J_experience : ".experience",
			J_template:"#J_template",
			J_experience_info:"#experience-info",
			J_look_all:".look-all",
			J_retract_all:".retract-all",
			J_whole_experience:".whole-experience",
			J_praise:".praise",
			J_unpraise:".unpraise",
			J_praise_num:".J_praise_num",
			J_company_name:".company-name",
			J_company_id:"company-id",
			J_restrain:".restrain",
			J_to_share:'.J_to_share',
			J_pop : '.pop',
			J_login : '.login',
			J_register : '.register'
		}
		myvar = {
			ellipsis : '...',
			yes:1,
			no:0
		}
		Pagination = PW.mod.Pagination;

	function experienceShow(param){
		this.opts = param;
		this.pagination;
		this.init();
	}

	S.augment(experienceShow,{
		init:function(){
			this._pagination();
			this._click();
		},

		_pagination:function(){
			var
				that = this,
				opts = that.opts;

			that.pagination = Pagination.client(opts);
		},

		_click:function(){
			var
				that = this,
				opts = that.opts,
				sortId = 0,
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
                var para = 'companyId='+$(el.J_company_name).attr(el.J_company_id)+'&isFollow='+isFollow;
                jobDetailIO.getFollowNumberIO(para,function(code,data,msg){
                    if(code == 1){
                        $(el.J_pop).show();
                        $(el.J_login).show();
                        $(el.J_register).hide();
                    }
                    if(code == 0){
                        $(el.J_follow_number).text(data.followNumber);
                        $(el.J_follow).hide();
                		$(el.J_unfollow).show();
                    }
                })
            });

			$(el.J_restrain).on('change',function(ev){
				sortId = $(ev.currentTarget).children('option:selected').val();
				S.mix(opts , {
								extraParam : {
									sortId:sortId
								}
				});
				that.pagination.reload(opts);
			});


			$(el.J_to_share).on('click',function(ev){
				$(el.J_pop).show();
				$(el.J_login).show();
				$(el.J_register).hide();
			})

			delegate(document,'click',el.J_look_all,function(ev){
					$(ev.currentTarget).hide();
					$(ev.currentTarget).next().show();
					$(ev.currentTarget).prev().show();
					$(ev.currentTarget).prev().prev().hide();
			});

			delegate(document,'click',el.J_retract_all,function(ev){
				$(ev.currentTarget).prev().show();
				$(ev.currentTarget).hide();
				$(ev.currentTarget).prev().prev().hide();
				$(ev.currentTarget).prev().prev().prev().show();
			});

			delegate(document,'click',el.J_praise,function(ev){
				var 
					praiseNum = 0,
					isPraise = myvar.yes,
					wantPraise = $(ev.currentTarget).parent(),
					cancelPraise = $(ev.currentTarget).parent().prev();
				
				var experienceId =  $(ev.currentTarget).parent().parent().parent().attr('experience-id'),
					para = "experienceId="+experienceId+"&isPraise="+isPraise;
				experienceIO.getPraiseNum(para,function(code,data,msg){
					if(code == 0){
						wantPraise.hide();
						cancelPraise.show();
						praiseNum = '('+data.praiseNum+')';
						cancelPraise.children().item(1).children().text(praiseNum);
					}
					if(code == 1){
						$(el.J_pop).show();
						$(el.J_login).show();
						$(el.J_register).hide();
					}
				})	
			});

			delegate(document,'click',el.J_unpraise,function(ev){
				var
					praiseNum = 0,
					isPraise = myvar.no,
					cancelPraise = $(ev.currentTarget).parent() ,
					wantPraise = $(ev.currentTarget).parent().next();
				
				var experienceId =  $(ev.currentTarget).parent().parent().parent().attr('experience-id'),
					para = "experienceId="+experienceId+"&isPraise="+isPraise;
				experienceIO.getPraiseNum(para,function(code,data,msg){
					if(code == 0){
						praiseNum = '('+data.praiseNum+')';
						wantPraise.children().item(1).children().text(praiseNum);
						cancelPraise.hide();
						wantPraise.show();	
					}
				})
				
			});
		}

	});
	return experienceShow;
},{
	requires:['mod/pagination','io/company/experience','io/company/jobDetail']
})
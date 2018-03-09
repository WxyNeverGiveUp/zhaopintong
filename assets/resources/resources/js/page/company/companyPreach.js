/*-----------------------------------------------------------------------------
* @Description:     company-preach与remote-interview页面相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.04
* ==NOTES:=============================================
* v1.0.0(2015.07.04):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/companyPreach',function(S,companyPreachShow){
	PW.namespace('page.company.companyPreach');
	PW.page.company.companyPreach = function(param){
		new companyPreachShow(param);
	}
},{
	requires:['companyPreach/companyPreachShow']
});

/*--------------------------------------------------------------------------*/
KISSY.add('companyPreach/companyPreachShow',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		delegate = S.Event.delegate,
		myvar = {
			yes:1,
			no:0,
		},
		el = {
			J_follow :'.follow',
			J_unfollow : '.unfollow',
			J_follow_number : ".follow-number",
			J_company_name:".company-name",
			J_company_id:"company-id",
			J_want_enroll:".want-enroll",
			J_cancel_enroll:".cancel-enroll",
			J_schedule:".schedule",
			J_list_body:".list-body",
			J_id:"id",
			J_template:"#J_template",
			J_pop : '.pop',
			J_login : '.login',
			J_register : '.register'
		},
		jobDetailIO = PW.io.company.jobDetail,
		companyPreachIO = PW.io.company.companyPreach,
		remoteInterviewIO = PW.io.company.remoteInterview,
		Pagination = PW.mod.Pagination;

	function companyPreachShow(param){
		this.opts = param;
		this.pagination;
		this.init();
	}

	S.augment(companyPreachShow,{
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
			var isCollect , isEnroll;
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

			$(el.J_template).delegate('click','a',function(ev){
				if($(ev.currentTarget).hasClass(el.J_want_enroll)){
					var 
						wantEnroll = $(ev.currentTarget),
						cancelEnroll = $(ev.currentTarget).next();
					isEnroll = myvar.yes;
					
					if($(ev.currentTarget).parent().parent().hasClass(el.J_schedule)){
						var para = 'remoteId='+wantEnroll.parent().parent().attr(el.J_id)+'&isEnroll='+isEnroll;
						remoteInterviewIO.isEnroll(para,function(code,data,msg){
							if(code == 0){
								wantEnroll.hide();
								cancelEnroll.show();
							}
							if(code == 1){
								$(el.J_pop).show();
								$(el.J_login).show();
								$(el.J_register).hide();
							}
						});
					}
						
					if($(ev.currentTarget).parent().parent().hasClass(el.J_list_body)){
						var para = 'preachId='+wantEnroll.parent().parent().attr(el.J_id)+'&isEnroll='+isEnroll;
						companyPreachIO.isEnroll(para,function(code,data,msg){
							if(code == 0){
								wantEnroll.hide();
								cancelEnroll.show();
							}
							if(code == 1){
								$(el.J_pop).show();
								$(el.J_login).show();
								$(el.J_register).hide();
							}
						});
					}

					
				};
				if($(ev.currentTarget).hasClass(el.J_cancel_enroll)){
					var 
						cancelEnroll = $(ev.currentTarget),
						wantEnroll = $(ev.currentTarget).prev();
					isEnroll = myvar.no;
					
					if($(ev.currentTarget).parent().parent().hasClass(el.J_schedule)){
						var para = 'remoteId='+cancelEnroll.parent().parent().attr(el.J_id)+'&isEnroll='+isEnroll;
						remoteInterviewIO.isEnroll(para,function(code,data,msg){
							if(code == 0){
								cancelEnroll.hide();
								wantEnroll.show();
							}
							if(code == 1){
								$(el.J_pop).show();
								$(el.J_login).show();
								$(el.J_register).hide();
							}
						});
					}
				

					if($(ev.currentTarget).parent().parent().hasClass(el.J_list_body)){
						var para = 'preachId='+cancelEnroll.parent().parent().attr(el.J_id)+'&isEnroll='+isEnroll;
						companyPreachIO.isEnroll(para,function(code,data,msg){
							if(code == 0){
								cancelEnroll.hide();
								wantEnroll.show();
							}
							if(code == 1){
								$(el.J_pop).show();
								$(el.J_login).show();
								$(el.J_register).hide();
							}
						});
					}
				}
				
			});
		}

	});
	return companyPreachShow;
},{
	requires:['mod/pagination','io/company/companyPreach','io/company/remoteInterview','io/company/jobDetail']
})
/*-----------------------------------------------------------------------------
* @Description: 远程面试详情页 
* @Version: 	V1.0.0
* @author: 		xuyihong(597262617@qq.com)
* @date			2015.08.01
* ==NOTES:=============================================
* v1.0.0(2014.05.10):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/detail',function(S,search,follow){
	PW.namespace('page.company');
	PW.page.company = function(param){
		new search(param);
		new follow();
	}
},{
	requires:['detail/search','detail/follow']
});
/*---------------------------------查询------------------------------------*/
KISSY.add('detail/search',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Pagination = PW.mod.Pagination,
		el = {
			searchCountEl: '.J_searchCount'
		};
	function search(param){
		this.opts = param;
		this.init();
	}

	S.augment(search,{
		init:function(){
			// this._check();
			this._pagination({});
		},
		_pagination:function(param){
			var
				that = this,
				opts = that.opts,
				extraParam = S.merge(opts,{
					extraParam:param,
					afterDataLoad: function(me, data, page){
						if(data.dataCount == -1){
							DOM.html(el.searchCountEl, 0);
						}else{
							DOM.html(el.searchCountEl, data.dataCount);
						}
					}
				});
			that.Pagination = Pagination.client(extraParam);
		}
	});

	return search;
},{
	requires:['mod/pagination']
});
/*----------------------------------------------------------------------------*/
KISSY.add('detail/follow',function(S){
	var
		$ = S.all ,
		on = S.Event.on,
		jobDetailIO = PW.io.company.jobDetail,
		remoteInterviewIO = PW.io.company.remoteInterview,
		el = {
			enterBtn :'.entor',
			unenterBtn : '.unentor',
			enter_name:".J_enter",
			enter_id:"enter-id",
			J_follow :'.follow',
			J_unfollow : '.unfollow',
			J_follow_number : ".follow-number",
			J_company_name:".company-name",
			J_company_id:"company-id",
			J_pop : '.pop',
			J_login : '.login',
			J_register : '.register',
			J_pop : '.pop',
			J_login : '.login',
			J_register : '.register',
			J_remote_entance : '.J_remote_entance'
		},
		myvar = {
			yes :1,
			no : 0
		}

	function companyIntroShow(){
		this.init();
	}

	S.augment(companyIntroShow , {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var
				isFollow ,
				isCollect;
			$(el.unenterBtn).on('click',function(ev){
				isFollow = myvar.no;
				$(el.unenterBtn).hide();
				$(el.enterBtn).show();
				var para = 'remoteId='+$(el.enter_name).attr(el.enter_id)+'&isEnter='+isFollow;
				remoteInterviewIO.isEnroll(para,function(code,data,msg){
					if(code == 0){
						window.location.reload();
					}
				})
			});

			$(el.enterBtn).on('click',function(ev){
				isFollow = myvar.yes;
				var para = 'remoteId='+$(el.enter_name).attr(el.enter_id)+'&isEnter='+isFollow;
				remoteInterviewIO.isEnroll(para,function(code,data,msg){
					if(code == 1){
						$(el.J_pop).show();
						$(el.J_login).show();
						$(el.J_register).hide();
					}
					if(code == 0){
						window.location.reload();
					}
				})
			});

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
						$(el.J_follow).hide();
						$(el.J_unfollow).show();
						$(el.J_follow_number).text(data.followNumber);
					}
				})
			});

			$(el.J_remote_entance).on('click',function(ev){
				var
					id = $(ev.currentTarget).attr('remoteId');
				remoteInterviewIO.isRightTime({id:id},function(code,data,msg){
					if(code == 0){
						window.location.href = data;
					}
					if(code == 1){
						$(el.J_pop).show();
						$(el.J_login).show();
						$(el.J_register).hide();
					}
					/*if(code == 2){
						alert('远程面试尚未开始，'+data+'，方可进入');
					}*/
				})
			})
		}
	})

	return companyIntroShow;
},{
	requires:['event' ,'io/company/jobDetail','io/company/remoteInterview']
})
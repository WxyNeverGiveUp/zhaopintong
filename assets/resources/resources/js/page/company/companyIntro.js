/*-----------------------------------------------------------------------------
* @Description:     company-introduction页面相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.09
* ==NOTES:=============================================
* v1.0.0(2015.07.09):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/companyIntro',function(S,companyIntroShow){
	PW.namespace('page.company.companyIntro');
	PW.page.company.companyIntro = function(){
		new companyIntroShow();
	}
},{
	requires:['companyIntro/companyIntroShow']
})

/*----------------------------------------------------------------------------*/
KISSY.add('companyIntro/companyIntroShow',function(S){
	var
		$ = S.all ,
		on = S.Event.on,
		jobDetailIO = PW.io.company.jobDetail,
		el = {
			J_follow :'.follow',
			J_unfollow : '.unfollow',
			J_follow_number : ".follow-number",
			J_company_name:".company-name",
			J_company_id:"company-id",
			J_pop : '.pop',
			J_login : '.login',
			J_register : '.register'
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
		}
	})

	return companyIntroShow;
},{
	requires:['event' ,'io/company/jobDetail']
})
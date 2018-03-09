/*-----------------------------------------------------------------------------
* @Description:     post页面相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.03
* ==NOTES:=============================================
* v1.0.0(2015.07.03):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/post',function(S,postShow){
	PW.namespace('page.company.post');
	PW.page.company.post = function(param){
		new postShow(param);
	}
},{
	requires:['post/postShow']
});

/*--------------------------------------------------------------------------*/
KISSY.add('post/postShow',function(S){
	var
		$ = S.all,
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
			J_pop : '.pop',
			J_login : '.login',
			J_register : '.register'
		}
		jobDetailIO = PW.io.company.jobDetail,
		Pagination = PW.mod.Pagination;

	function postShow(param){
		this.opts = param;
		this.pagination;
		this.init();
	}

	S.augment(postShow,{
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
			var isCollect;
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

	});
	return postShow;
},{
	requires:['mod/pagination','io/company/jobDetail']
})
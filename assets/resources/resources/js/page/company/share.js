/*-----------------------------------------------------------------------------
* @Description:     share页面相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.06
* ==NOTES:=============================================
* v1.0.0(2015.07.06):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/share',function(S,shareShow){
	PW.namespace('page.company.share');
	PW.page.company.share = function(){
		new shareShow();
	}
},{
	requires:['share/shareShow']
})

/*----------------------------------------------------------------------------*/
KISSY.add('share/shareShow',function(S){
	var 
		$ = S.all,
		on = S.Event.on,
		jobDetailIO = PW.io.company.jobDetail,
		el = {
			J_follow :'.follow',
			J_unfollow : '.unfollow',
			J_follow_number : ".follow-number",
			J_form:'.J_form',
			J_sub:".sub",
			J_chose_job:".chose-job",
			J_company_name:".company-name",
			J_company_id:"company-id",
			J_years : '.years',
			J_months : '.months',
			J_days : '.days'
		},
		myvar = {
			yes:1,
			no:0
		}
		Defender = PW.mod.Defender;

	function shareShow(){
		this.init();
	}

	S.augment(shareShow,{
		init:function(){
			this.valid = Defender.client(el.J_form,{
				showTip:true,
				trace:true
			});
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

			on(el.J_sub,'click',function(ev){
				that._formSubmit();
			});

			$(el.J_years).on('change',function(ev){
				var
					year = $(el.J_years).children('option:selected').val(),
					month = $(el.J_months).children('option:selected').val();
				jobDetailIO.getDay({year:year,month:month} , function(rs,data,msg){
					if(rs){
						var
							optionHtml;
						$(el.J_days).html('');
						S.each(data , function(item){
							optionHtml = '<option value='+item+'>'+item+'</option>';
							$(el.J_days).append(optionHtml);
						});
						$(el.J_days).prepend('<option value=0>--</option>');
					}
				})
			});

			$(el.J_months).on('change',function(ev){
				var
					year = $(el.J_years).children('option:selected').val(),
					month = $(el.J_months).children('option:selected').val();
				jobDetailIO.getDay({year:year,month:month} , function(rs,data,msg){
					if(rs){
						var
							optionHtml;
						$(el.J_days).html('');
						S.each(data , function(item){
							optionHtml = '<option value='+item+'>'+item+'</option>';
							$(el.J_days).append(optionHtml);
						});
						$(el.J_days).prepend('<option value=0>--</option>');
					}
				})
			});
		},

		_formSubmit:function(){
			var	
				that = this,
				valid = that.valid;		
			valid.validAll();
 		}

	})

	return shareShow;
},{
	requires:['mod/defender','io/company/jobDetail']
})
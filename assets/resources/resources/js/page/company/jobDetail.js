/*-----------------------------------------------------------------------------
* @Description:     job-detail及视频点播页面相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.03
* ==NOTES:=============================================
* v1.0.0(2015.07.03):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/jobDetail',function(S,jobDetailShow){
	PW.namespace('page.company.jobDetail');
	PW.page.company.jobDetail = function(){
		new jobDetailShow();
	}
},{
	requires:['jobDetail/jobDetailShow']
})

/*---------------------------------------------------------------------------*/
KISSY.add('jobDetail/jobDetailShow',function(S){
	var 
		on = S.Event.on,
		delegate = S.Event.delegate,
		$ = S.all,
		jobDetailIO = PW.io.company.jobDetail,
		myvar = {
			yes:1,
			no:0,
		},
		el = {
			J_follow :'.follow',
			J_unfollow : '.unfollow',
			J_follow_number : ".follow-number",
			J_uncollection : ".uncollection",
			J_collection : ".collection",
			J_company_name:".company-name",
			J_company_id:"company-id",
			J_job_name:".job-name",
			J_job_id:"job-id",
			J_apply_job:'.J_apply_job',
			J_pop : '.pop',
			J_login : '.login',
			J_register : '.register',
			J_video_description : '.video-description',
			J_intro_video : '.intro-video',
			J_show_QRCode : '.show-QRCode',
			J_qrCode_Img : '.qrCode-Img',
			J_none : '.none',
			J_activate_pop : '.activate-pop',
			J_message_order : '.message-order',
			J_message_pop_layer : '.message-pop-layer',
			J_message_pop : '.message-pop',
			J_sure_message : '.sure-message',
			J_vip_user_tip : '.vip-user-tip',
			J_activate_user_tip : '.activate-user-tip',
			J_position_order : '.position-order'
		};

	function jobDetailShow(){
		this.init();
	}

	S.augment(jobDetailShow,{
		init:function(){
			this._click();
		},

		_click:function(){
			var 
				isHide = true,
				isFollow ,
				isCollect;
			$(el.J_unfollow).on('click',function(ev){
                isFollow = myvar.no;
                var para = 'companyId='+$(el.J_company_name).attr(el.J_company_id)+'&isFollow='+isFollow;
                jobDetailIO.getFollowNumberIO(para,function(code,data,msg){
                    if(code == 0){
                        $(el.J_follow_number).text(data.followNumber);
                        $(el.J_unfollow).hide();
                		$(el.J_follow).show();
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

			$(el.J_uncollection).on('click',function(ev){
				isCollect = myvar.no;
				$(el.J_uncollection).hide();
				$(el.J_collection).show();
				var para = 'jobId='+$(el.J_job_name).attr(el.J_job_id)+'&isCollect='+isCollect;
				jobDetailIO.isCollect(para,function(rs,data,msg){});
			});

			$(el.J_collection).on('click',function(ev){
				isCollect = myvar.yes;
				var para = 'jobId='+$(el.J_job_name).attr(el.J_job_id)+'&isCollect='+isCollect;
				jobDetailIO.isCollect(para,function(rs,data,msg){
					if(rs){
						$(el.J_collection).hide();
						$(el.J_uncollection).show();
					}
					else{
						$(el.J_pop).show();
						$(el.J_login).show();
						$(el.J_register).hide();
					}
				});
			});

			$(el.J_apply_job).on('click',function(ev){
				$(el.J_pop).show();
				$(el.J_login).show();
				$(el.J_register).hide();
			});

			$(el.J_show_QRCode).on('click',function(ev){
				if($(el.J_qrCode_Img).hasClass(el.J_none)){
					$(el.J_qrCode_Img).show();
					$(el.J_qrCode_Img).removeClass(el.J_none);
				}
				else{
					$(el.J_qrCode_Img).hide();
					$(el.J_qrCode_Img).addClass(el.J_none);
				}
			});

			$(el.J_activate_pop).on('click',function(ev){
				$(el.J_message_pop_layer).show();
				$(el.J_message_pop).show();
				$(el.J_activate_user_tip).show();
			});

			$(el.J_sure_message).on('click',function(ev) {
				$(el.J_message_pop_layer).hide();
				$(el.J_message_pop).hide();
				$(el.J_vip_user_tip).hide();
				$(el.J_activate_user_tip).hide();
			});

			// $(el.J_intro_video).on('mouseenter',function(ev){
			// 	$(ev.currentTarget).siblings('span').hide();
			// });

			// $(el.J_intro_video).on('mouseleave',function(ev){
			// 	if(isHide){
			// 		$(ev.currentTarget).siblings('span').show();
			// 	}
			// })

			// $(el.J_intro_video).delegate('click','object',function(ev){
			// 	isHide = false;
			// 	$(ev.currentTarget).siblings('span').hide();
			// });
		}
	});
	return jobDetailShow;
},{
	requires:['event','io/company/jobDetail']
})
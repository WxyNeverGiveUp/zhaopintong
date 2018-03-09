/*-----------------------------------------------------------------------------
* @Description: rate.js 滚动条
* @Version: 	V1.0.0
* @author: 		xuyihong(597262617@qq.com)
* @date			2015.08.02
* ==NOTES:=============================================
* v1.0.0(2014.05.10):
* 	初始生成 
* ---------------------------------------------------------------------------*/

KISSY.add('page/company/rate' , function(S, rate ,follow,video){
	PW.namespace('page.company.rate');
	PW.page.company = function(param){
		new rate(param);
		new follow(param);
		new video(param);
	}
},{
	requires:['company/rate','company/follow','company/video']
});
/*--------------------------滚动条-------------------------------------------------*/
KISSY.add('company/rate',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		el = {
			disBtn:'.distr-con',
			numBtn:'.num',
			shuBtn:'.shu',
			largeBtn:'#J_large'
		};

	function rate(param){
		this.opts = param;
		this.init();
	}

	S.augment(rate,{
		init:function(){
			this._getwid();
		},
		_getwid:function(){
			var num = $(el.largeBtn).val();
			S.each($(el.shuBtn), function(i , o) {
			    var a = $(i).text()/num;
			    var c = a*100+"%";
			    var b = $(i).parent().children("span").children("em").css("width",c);
			});
		}
})
	return rate;
},{
	requires:['mod/pagination' , 'node','dom']
});
/*--------------------------------报名/取消报名--------------------------------------------*/
KISSY.add('company/follow',function(S){
	var
		$ = S.all ,
		on = S.Event.on,
		jobDetailIO = PW.io.company.jobDetail,
		preachIO = PW.io.preach.preach,
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
			J_show_QRCode : '.show-QRCode',
			J_qrCode_Img : '.qrCode-Img',
			J_none : '.none',
			J_live_entrance : '.J_live_entrance',
			J_video : '.J_video'
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
				var para = 'id='+$(el.enter_name).attr(el.enter_id)+'&isEnter='+isFollow;
				preachIO.enroll(para,function(code,data,msg){
					if(code == 0){
						$(el.unenterBtn).hide();
						$(el.enterBtn).show();
						window.location.reload();
					}
				})
			});

			$(el.enterBtn).on('click',function(ev){
				isFollow = myvar.yes;
				var para = 'id='+$(el.enter_name).attr(el.enter_id)+'&isEnter='+isFollow;
				preachIO.enroll(para,function(code,data,msg){
					if(code == 1){
						$(el.J_pop).show();
						$(el.J_login).show();
						$(el.J_register).hide();
					}
					if(code == 0){
						$(el.enterBtn).hide();
						$(el.unenterBtn).show();
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
						$(el.J_follow_number).text(data.followNumber);
						$(el.J_follow).hide();
						$(el.J_unfollow).show();
					}
				})
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

			$(el.J_live_entrance).on('click',function(ev){
				var
					id = $(ev.currentTarget).attr('liveId');
				preachIO.isRightTime({id:id},function(code,data,msg){
					if(code == 0){
						window.location.href = data;
					}
					if(code == 1){
						$(el.J_pop).show();
						$(el.J_login).show();
						$(el.J_register).hide();
					}
					if(code == 2){
						alert('直播尚未开始，'+data+'，方可进入');
					}
				})
			});

			$(el.J_video).on('click' , function(ev){
				var
					id = $(el.J_live_entrance).attr('liveId');
				preachIO.isRightTime({id:id},function(code,data,msg){
					if(code == 0){
						window.location.href = data;
					}
					if(code == 1){
						$(el.J_pop).show();
						$(el.J_login).show();
						$(el.J_register).hide();
					}
					if(code == 2){
						alert('直播尚未开始，'+data+'，方可进入');
					}
				})
			});
		}
	})

	return companyIntroShow;
},{
	requires:['event' ,'io/company/jobDetail','io/preach/preach']
})

/*---------------------------------视频------------------------------------*/
KISSY.add('company/video',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		el = {
			hideBtn : "#J_hide",
			imgBtn : ".J_img",
			videoBtn : ".J_video",
			J_preach_demand : '.J_preach_demand'
		};
	function video(param){
		this.opts = param;
		this.init();
	}

	S.augment(video,{
		init:function(){
			// this._check();
			this._play({});
			this._addEventListener();
		},
		_play:function(param){
			var
				that = this,
				opts = that.opts,
				index = $(el.hideBtn).val();

			if(index == 0){
				$(el.imgBtn).css("display","none");
			}else{
				$(el.videoBtn).css("display","none");
				$(el.imgBtn).on("click",function(){
					alert("该宣讲会无直播！")
				});
			}
		},
		_addEventListener:function(){
			$(el.J_preach_demand).on('click',function(ev){
				alert('该单位无宣讲点播！');
			})
		}
	});

	return video;
},{
	requires:['mod/dialog']
});
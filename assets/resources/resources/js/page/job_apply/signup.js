/*-----------------------------------------------------------------------------
* @Description:     报名宣讲会页面相关js
* @Version:         1.0.0
* @author:          daiqiaoling
* @date             2015.7.12
* ==NOTES:=============================================
* v1.0.0(2015.7.12):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/signup' , function(S,signup){
	PW.namespace('page.job_apply.signup');
	PW.page.job_apply.signup = function(param){
		new signup(param);
	}
},{
	requires:['signup/signup']
});
/*------------------------收藏职位---------------------------------------*/
KISSY.add('signup/signup' , function(S){
	var $ = S.all, 
		on = S.Event.on,
		delegate = S.Event.delegate,
		DOM = S.DOM,
		Pagination = PW.mod.Pagination,
		signup_preach = PW.io.job_apply.signup,

		el = {
				J_want_enroll:'.want-enroll',//指向“我要报名”按钮
				J_cancel_enroll:'.cancel-enroll',//指向“取消报名”按钮
				J_tp1_template:'#tpl-template',
			},

		myvar = {
			yes : 1,
			no : 0,
			enrollSuccess :'报名成功'
		};

	function signup(param){
		this.opts = param;
		this.init();
	}

	S.augment(signup,{
		init:function(){
			this._pagination();
			this._addEventListener();
		},

		_pagination:function(extraParam){
			var
				that = this,
				opts = that.opts;
			that.pagination = Pagination.client(opts);
		},
		
		_addEventListener:function(){
			var 
				that = this,
				opts = that.opts;

				$(el.J_tp1_template).delegate('click','a',function(ev){
					var 
						isEnroll , 
						parameter,
						currentClick = $(ev.currentTarget),
					    id = $(ev.currentTarget).parent().parent().attr('id');

					if(currentClick.hasClass(el.J_want_enroll)){
						isEnroll = myvar.yes;
						currentClick.addClass('none');
						currentClick.next().removeClass('none');
						parameter = 'id='+id+'&isEnroll='+isEnroll;
					    signup_preach.signupIO(parameter,function(rs,data,msg){});
					}

					if(currentClick.hasClass(el.J_cancel_enroll)){
						isEnroll = myvar.no;
						currentClick.addClass('none');
						currentClick.prev().removeClass('none');
						parameter = 'id='+id+'&isEnroll='+isEnroll;
					    signup_preach.signupIO(parameter,function(rs,data,msg){});
					}
				});
		},
	});
	return signup;
},{
	requires:['event','mod/pagination','io/job_apply/signup']
});
/*-----------------------------------------------------------------------------
* @DescriSion: user相关js
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.09.14
* ==NOTES:=============================================
* v1.0.0(2015.09.14):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/mine',function(S,mineShow){
	PW.namespace('page.job_apply.mine');
	PW.page.job_apply.mine = function(){
		new mineShow();
	}
},{
	requires:['mine/mineShow']
})

/*--------------------------------------------------------------------------------*/
KISSY.add('mine/mineShow',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		MyHomeIO = PW.io.job_apply.my_home,
		myvar = {
			wrongCode : '您的激活码输入错误，请重新输入',
			usedCode : '该激活码已用，请重新输入',
			activatedUser : '该生信息已通过学校认证'
		}
		el = {
			J_activate : '.activate-validation',
			J_pop : '.pop-background',
			J_validation : '.validation-code',
			J_submit_code : '.submit-code',
			J_cancel_code : '.cancel-code',
			J_improve : '.improve',
			J_light_up : '.light-up',
			J_valid_tip : '.valid-tip',
			J_unclick_pointer : '.unclick-pointer'

		};

	function mineShow(){
		this.init();
	}

	S.augment(mineShow , {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			$(el.J_activate).on('click',function(ev){
				$(el.J_pop).show();
				$(el.J_validation).show();
			});

			$(el.J_submit_code).on('click',function(ev){
				var
					validationCode = $('input' , el.J_validation).val();
				MyHomeIO.putValidCode({validationCode:validationCode},function(code,data,msg){
					if(code == 0){
						var
							name = $(el.J_name).text();
						$(el.J_validation).hide();
						$(el.J_pop).hide();
						$(el.J_improve).addClass(el.J_light_up);
						$(el.J_activate).text(myvar.activatedUser);
						$(el.J_activate).detach('click');
						$(el.J_activate).addClass(el.J_unclick_pointer);
						window.location.reload();
					}
					if(code == 1){
						$(el.J_valid_tip).text(myvar.wrongCode);
					}
					if(code == 2){
						$(el.J_valid_tip).text(myvar.usedCode);
					}
				})
			});

			$(el.J_cancel_code).on('click',function(ev){
				$(el.J_pop).hide();
				$(el.J_validation).hide();
			});

			$('input' , el.J_validation).on('click',function(ev){
				$('input', el.J_validation).val('');
				$(el.J_valid_tip).text('');
			})
		}
	})

	return mineShow;
},{
	requires:['core','io/job_apply/my_home']
})
/*-----------------------------------------------------------------------------
* @Description:     编辑关于我页面相关js
* @Version:         1.0.0
* @author:          daiqiaoling
* @date             2015.7.3
* ==NOTES:=============================================
* v1.0.0(2015.7.3):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/about' , function(S,showTip,submit){
	PW.namespace('page.job_apply.about');
	PW.page.job_apply.about = function(param){
		new showTip(param);
		new submit(param);
	}
},{
	requires:['about/showTip','about/submit']
});
/*------------------------显示建议---------------------------------------*/
KISSY.add('about/showTip',function(S){
	var $ = S.all, 
		on = S.Event.on,
		DOM = S.DOM,

		el = {
				J_Reference:'.reference',
				J_Examples:'.examples'
		};
		
	function showTip(param){
		this.opts = param;
		this.init();
	}

	S.augment(showTip,{
		init:function(){
			this._show();
		},
		_show:function(){
			var that = this,
				opts = that.opts;
			$(el.J_Reference).on("mouseover",function(){
				$(this).children("i").css("background-position","-10px -5px");
			});

			$(el.J_Reference).on("mouseout",function(){
				$(this).children("i").css("background-position","-10px 0px");
			});

			$(el.J_Reference).on("click",function(){
				if($(el.J_Examples).hasClass("none")){
					$(el.J_Examples).removeClass("none");
					$(this).children("i").css("background-position","-10px -5px");
				}else{
					$(el.J_Examples).addClass("none");
					$(this).children("i").css("background-position","-10px 0px");
				}
			});	
		}

	});	
	return showTip;
	},{
		requires:['event']
});
/*---------------------------------表单提交---------------------------------*/
KISSY.add('about/submit',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Dialog = PW.mod.Dialog,
		el = {
			J_Form:'form', //指向表单
			J_Save:'.save'//指向保存按钮
		};

	function submit(param){
		this.opts = param;
		this.init();
	}

	S.augment(submit,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击保存按钮*/
			on(el.J_Save,'click',function(evt){
				that._formSubmit();
			});	
		},

		/*表单提交*/
		_formSubmit:function(){
			var
				that = this,
				opts = that.opts,
				$txt_length = $('textarea',el.J_Form).val().length;
			//S.log($txt_length);
			if($txt_length >= 6 && $txt_length <= 500){
				jQuery(el.J_Form).submit();
				Dialog.alert("保存成功！");
			}else{
				Dialog.alert("描述信息不符合规则！");
			}
		}
	});

	return submit;
},{
	requires:['mod/dialog']
});
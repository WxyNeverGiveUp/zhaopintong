/*-----------------------------------------------------------------------------
* @Description:     编辑联系方式页面相关js
* @Version:         1.0.0
* @author:          daiqiaoling
* @date             2015.7.3
* ==NOTES:=============================================
* v1.0.0(2015.7.3):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/contact' , function(S,visibility,submit){
	PW.namespace('page.job_apply.contact');
	PW.page.job_apply.contact = function(param){
		new visibility(param);
		new submit(param);
	}
},{
	requires:['contact/visibility','contact/submit']
});
/*----------------------可见范围---------------------------------------*/
KISSY.add('contact/visibility',function(S){
	var $ = S.all, 
		on = S.Event.on,
		DOM = S.DOM,

		el = {
				J_Visibility:'.visible-range',
		};
		
	function visibility(param){
		this.opts = param;
		this.init();
	}

	S.augment(visibility,{
		init:function(){
			this._selectRange();
		},
		_selectRange:function(){
			var that = this,
				opts = that.opts;

			on(el.J_Visibility,"mouseenter",function(){
				$("p",this).addClass("focus");
			});	

			on(el.J_Visibility,"mouseleave",function(){
				$("p",this).removeClass("focus");
			});

			that._clickP();

			$("li",el.J_Visibility).on("mouseenter",function(){
				$(this).css("background","#f1f1f1");
			});

			$("li",el.J_Visibility).on("mouseleave",function(){
				$(this).css("background","#fff");
			});

			that._clickLi();
		},

		_clickP:function(){
			var 
				that = this;

			$("p",el.J_Visibility).on("click",function(){
				var 
					$ul = $(this).next();

				if($ul.hasClass("none")){
					$ul.removeClass("none");
					$(this).addClass("focus");
					on($ul,"mouseleave",function(){
						$(this).prev().removeClass("focus");
						if(!$(this).hasClass("none")){
							$(this).addClass("none");
						}
					});
				}else{
					$ul.addClass("none");
				}
			});
		},
		_clickLi:function(){	
			var 
				that = this;

			$("li",el.J_Visibility).on("click",function(ev){
				var 
					confidentialDegreeDegree,
					$current_i = $("i",this),
					$current_ul = $(this).parent(),
					inputName = $(this).parent().prev().attr('name'),
					$current_class = $current_i.attr("class"),
					$all_li = $(this).parent().children("li");
				if($current_class === "self"){
					$current_i.css("background-position","-18px 0");
					confidentialDegree = 0;
					$all_li.each(function(){
						var $other_i = $(this).children("i");
						if($other_i.attr("class") == "all"){
							$other_i.css("background-position","-26px 0");
						}
						else if($other_i.attr("class") == "schoolmate"){
							$other_i.css("background-position","-46px 0");
						}
					});
				}else if($current_class === "all"){
					$current_i.css("background-position","-36px 0");
					confidentialDegree = 2;
					$all_li.each(function(){
						var $other_i = $(this).children("i");
						if($other_i.attr("class") == "self"){
							$other_i.css("background-position","-10px 0");
						}
						else if($other_i.attr("class") == "schoolmate"){
							$other_i.css("background-position","-46px 0");
						}
					});
				}else if($current_class === "schoolmate"){
					$current_i.css("background-position","-58px 0");
					confidentialDegree = 1;
					$all_li.each(function(){
						var $other_i = $(this).children("i");
						if($other_i.attr("class") == "self"){
							$other_i.css("background-position","-10px 0");
						}
						else if($other_i.attr("class") == "all"){
							$other_i.css("background-position","-26px 0");
						}
					});
				}
				$current_ul.prev("p").html($(this).html() + '<span></span><input type="hidden" value="'+confidentialDegree+'"name="'+inputName+'">');
				$current_ul.addClass("none");
			});
		},

	});	
	return visibility;
},{
		requires:['event']
});
/*---------------------------------表单提交---------------------------------*/
KISSY.add('contact/submit',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Defender = PW.mod.Defender,
		Dialog = PW.mod.Dialog,
		el = {
			J_Form:'.info-form', //指向表单
			J_Save:'.save',//指向保存按钮
			J_ErrorTip: '.J_errorTip',//指向错误提示所在的容器
		};

	function submit(param){
		this.opts = param;
		this.init();
	}

	S.augment(submit,{
		init:function(){
			this._valid();
			this._addEventListener();
		},
		_valid: function(){
			this.valid = Defender.client(el.J_Form,{
				showTip: false
			});
		},
		_addEventListener:function(){
			var
				that = this;

			/*点击保存按钮*/
			on(el.J_Save,'click',function(evt){
				that._formSubmit();
			});	

			on('input','focus',function(evt){
				that._setTip(evt.target);
			});	

			on('input','blur',function(evt){
				that._setTip(evt.target);
			});

			on('input','keyup',function(evt){
				that._setTip(evt.target);
			});	
		},
		
		_setTip:function(e){
			var 
				that = this,
				$tip = $(e).next('span'),
				$errorTip = $(e).parent().next('p');
			$errorTip.append($tip);
		},

		/*表单提交*/
		_formSubmit:function(){
			var
				that = this,
				valid = that.valid,
				opts = that.opts;
			
			valid.validAll(function(rs){
				if(rs){
					jQuery(el.J_Form).submit();
					Dialog.alert("保存成功！");
				}else{
					that._showTip();
				}
			});
		},		
		
		//重新定义错误提示的显示样式
		_showTip:function(){
			var
				that = this,
				tips = $('.error-state'),
				errorTip;
			S.each(tips, function(tip){
				errorTip = $(tip).parent().next('p');
				$(errorTip).empty();
				$(errorTip).append($(tip));
			});
		}
	});

	return submit;
},{
	requires:['mod/defender','mod/dialog']
});
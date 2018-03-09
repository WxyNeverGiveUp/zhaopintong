/*-----------------------------------------------------------------------------
* @Description:     编辑我的特长页面相关js
* @Version:         1.0.0
* @author:          daiqiaoling
* @date             2015.7.4
* ==NOTES:=============================================
* v1.0.0(2015.7.4):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/specialty' , function(S,editSpecialty,scroll){
	PW.namespace('page.job_apply.specialty');
	PW.page.job_apply.specialty = function(param){
		new scroll(param);
		new editSpecialty(param);
	}
},{
	requires:['specialty/editSpecialty','specialty/scroll']
});
/*-----------------------添加特长功能实现--------------------------------------*/
KISSY.add('specialty/editSpecialty',function(S){
	var $ = S.all, 
		on = S.Event.on,
		delegate = S.Event.delegate,
		DOM = S.DOM,
		myHomeIO = PW.io.job_apply.my_home,		
		Dialog = PW.mod.Dialog,
		myvar = {
			addedSpecialty:[]
		}
		el = {
				J_AddItem:'.add-item',
				J_AddedItem:'#addedItem',
				J_Input:'.input-specialty',			
				J_SaveBtn:'.save',
				J_ErrorTip:'.error-tip'				
		};
		
	function editSpecialty(param){
		this.opts = param;
		this.init();
	}

	S.augment(editSpecialty,{
		init:function(){
			this._addEventListener();
			this._getAddedSpecialty();
		},
		_addEventListener:function(){
			var that = this,
				opts = that.opts,
				$spanHtml;
			
			//快速添加专长标签	
			$("a",el.J_AddItem).on("click",function(){
				var $quicklyAdd_val = $(this).next().text();
				that._specialtyAjax($quicklyAdd_val);	
			});

			//自定义添加专长标签
			$("input",el.J_Input).on("click",function(){
				$(el.J_ErrorTip).addClass("none");
			});

			$("input",el.J_Input).val("");

			$(el.J_SaveBtn).on("click",function(){
				$input_val = $("input",el.J_Input).val();
				if($input_val){
					that._specialtyAjax($input_val);
					$("input",el.J_Input).val("");
					// that._delSpecialty();	
				}else{
					$(el.J_ErrorTip).removeClass("none");
				}
			});

			$(el.J_AddedItem).delegate("click",'a',function(ev){
					var 
						currentTarget = $(ev.currentTarget),
						$del_id = currentTarget.parent().children("b").attr("id"),
						info = currentTarget.parent().children("b").text();
					myHomeIO.delSpecialty({"specialtyId":$del_id},function(rs,data,msg){
						if(rs){
							var
								i = 0;
							currentTarget.parent("li").html("");
							S.each(myvar.addedSpecialty,function(item){
								if(item == info)
									myvar.addedSpecialty.splice(i,1);
								i ++;
							})
						}
						
					});	
									
			});
		},

		//添加特长时，发送Ajax请求
		_specialtyAjax:function(info){
			var 
				isIn = true,
				that = this,
				opts = that.opts;
			S.each(myvar.addedSpecialty , function(item){
				if(item == info)
					isIn = false;
			})
			if(isIn){
				myHomeIO.addSpecialty({"specialtyContent":info},function(code,data,msg){
				if(code == 0){
					$spanHtml = '<li><span><b id=' + data.specialtyId + ' title='+data.content+'>' + data.content + '</b><a href="javascript:;"></a></span></li>';
					$(el.J_AddedItem).prepend($spanHtml);
					myvar.addedSpecialty.push(info);
				}else{
					Dialog.alert("操作失败！");
				}				
			});
			}
			
			
		},
		_getAddedSpecialty:function(){
			var
				addedSpecialty = new Array();
			S.each($('b',el.J_AddedItem),function(i,o){
				addedSpecialty.push($(i).text());
			})
			myvar.addedSpecialty = addedSpecialty;
		}
		
	});	
	return editSpecialty;
	},{
		requires:['event','mod/dialog','io/job_apply/my_home']
});
/*-----------------------左右滚动效果实现--------------------------------------*/
KISSY.add('specialty/scroll',function(S){
	var $ = S.all, 
		on = S.Event.on,
		DOM = S.DOM,

		el = {
				J_Box:'.box-content',
				J_Left:'.left',
				J_Right:'.right',				
		};
		
	function scroll(param){
		this.opts = param;
		this.init();
	}

	S.augment(scroll,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var that = this,
				opts = that.opts,
				/*$width = Number($("li",el.J_Box).css("width").slice(0,-2)),
				$x_padding = Number(2 * $("li",el.J_Box).css("padding-left").slice(0,-2)),
				$x_offset = $width + $x_padding;
				console.log($x_offset);*/
				$ul_left = $("ul",el.J_Box).css("left").slice(0,-2);
			$(el.J_Right).on("click",function(){
				if($ul_left == "-1400"){
					$(this).css("background-position","-29px -13px");
				}else{
					$(el.J_Left).css("background-position","-42px -13px");
					$ul_left -= 700;
				}
				if($ul_left == "-1400"){
					$(this).css("background-position","-29px -13px");
				}
				$("ul",el.J_Box).css("left",$ul_left + 'px');
			});

			$(el.J_Left).on("click",function(){
				if($ul_left == "0"){
					$(this).css("background-position","-17px -13px");
				}else{
					$(this).css("background-position","-42px -13px");
					$ul_left += 700;
				}
				if($ul_left !== "-1400"){
					$(el.J_Right).css("background-position","-56px -13px");
				}
				if($ul_left == "0"){
					$(this).css("background-position","-17px -13px");
				}
				$("ul",el.J_Box).css("left",$ul_left + 'px');
			});
		}

	});	
	return scroll;
	},{
		requires:['event']
});
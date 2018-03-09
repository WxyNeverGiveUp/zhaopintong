/*-----------------------------------------------------------------------------
* @Description:     宴请部分相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.11.13
* ==NOTES:=============================================
* v1.0.0(2014.11.13):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/banquet/banquet-communicate',function(S,date,banquet){
	PW.namespace('page.banquetCommunicate');
	PW.page.banquetCommunicate = function(param){
		new date(param);
		new banquet(param);
	};
},{
	requires:['banquetCommunicate/date','banquetCommunicate/banquet']
});
/*--------------------------------选择宴请日期-------------------------------*/
KISSY.add('banquetCommunicate/date',function(S){
	var 
		query = S.query;
	function date(param){
		this.init();
	}

	S.augment(date,{
		init:function(){
			S.each(query('.date'),function(i){
				Calendar = PW.mod.Calendar;
				var calendar = Calendar.client({
					renderTo: i, //默认只获取第一个
					select: {
						rangeSelect: false, //是否允许区间选择
						dateFmt: 'YYYY-MM-DD',
						showTime: false //是否显示时间
					}
				});
			});
		}
	});
	return date;
},{
	requires:['mod/calendar']
});
/*------------------------------------宴请--------------------------------------*/
KISSY.add('banquetCommunicate/banquet',function(S){
	var
		$ = S.all, on = S.Event.on,
		Defender = PW.mod.Defender,
		Dialog = PW.mod.Dialog,
		BanquetIO = PW.io.banquet.feast,
		el = {
			sureBtn:'.J_sureBtn',//指向确定按钮
			banquetHolder:'.J_banquetHolder',//指向宴请选择的按钮
			banquetInput:'.J_banquetInput',//指向宴请时要填写的表单
			feastId:'#J_feastId'//指向宴请id隐藏域
		};

	function banquet(param){
		this.valid = Defender.client('.block-form',{
			showTip:false,
			items:[
				{
					queryName:'.time',
					pattern: function(input,shell,form){
						var 
							val = S.DOM.val(input);
							if(val != '' && /^(([1-9]{1})|([0-1][0-9])|([1-2][0-3])):([0-5][0-9])$/.test(val)){
								return true;
							}else{
								return false;
							}
					},
					tip:'|',
					showEvent:'focus',
					vldEvent:'keyup'
				}
			]
		});
        this.opts = param;
		this.init();

	}

	S.augment(banquet,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*改变宴请*/
			on(el.banquetHolder,'change',function(evt){
				that._changeBanquetState(evt.target);
			});
			/*点击确定按钮*/
			on(el.sureBtn,'click',function(evt){
				that._submit();
			});
		},
		/*改变宴请表单的状态*/
		_changeBanquetState:function(target){
			var
				that = this,
				flag = $(target).val();
			if(flag == "1" || flag == "3"){
				//宴请
				$(el.banquetInput).show();
			}else{
				//不宴请
				$(el.banquetInput).hide();
			}
		},
		/*表单提交*/
		_submit:function(){
			var
				that = this,
				v = that.valid,
                opts = that.opts,
				banquet = $(el.banquetHolder).val(),
				feastId = $(el.feastId).val();
			if(banquet == "1" || banquet == "3"){
				v.validAll();
				if(v.getValidResult('bool')){
                    if(opts.pageFlag == 1){//contact_communicate_new.jsp页面
                        BanquetIO.communication({feastId:feastId},function(rs,data,errorMes){
                            if(rs){
                                jQuery('form').submit();
                            }else{
                                Dialog.confirm(
                                    data.msg,
                                    function(){
                                        jQuery('form').submit();
                                    },
                                    function(){

                                    },{
                                        position:'fixed',
                                        width:'400',
                                        maskColor:'#343434'
                                    }
                                );
                            }
                        });
                    }else if(opts.pageFlag == 2){//general_office_new_before.jsp页面
                        jQuery('form').submit();
                    }
				}
			}else{
                if(opts.pageFlag == 1){//contact_communicate_new.jsp页面
                    BanquetIO.communication({feastId:feastId},function(rs,data,errorMes){
                        if(rs){
                            jQuery('form').submit();
                        }else{
                            Dialog.confirm(
                                data.msg,
                                function(){
                                    jQuery('form').submit();
                                },
                                function(){

                                },{
                                    position:'fixed',
                                    width:'400',
                                    maskColor:'#343434'
                                }
                            );
                        }
                    });
                }else if(opts.pageFlag == 2){//general_office_new_before.jsp页面
                    jQuery('form').submit();
                }
			}
		}
	});

	return banquet;
},{	
	requires:['io/banquet/feast','mod/defender','mod/dialog','thirdparty/jquery']
});
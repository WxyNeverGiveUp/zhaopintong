/*-----------------------------------------------------------------------------
* @Description: 编辑基本信息页面相关js (company-info-new.js)
* @Version: 	V1.0.0
* @author: 		chenke(396985267@qq.com)
* @date			2014.09.15
* ==NOTES:=============================================
* v1.0.0(2014.09.15):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/edit_info',function(S,submit){
	PW.namespace('page.job_apply.editInfo');
	PW.page.job_apply.editInfo = function(param){
		new submit(param);
	};
},{
	requires:['editInfo/submit']
});
/*---------------------------------表单提交---------------------------------*/
KISSY.add('editInfo/submit',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Defender = PW.mod.Defender,
		Dialog = PW.mod.Dialog,
		MyHomeIO = PW.io.job_apply.my_home,
		el = {
			J_Form:'form', //指向表单
			J_Save:'.save',//指向保存按钮
			J_proviceList:'.J_proviceList',
			J_cityList:'.J_cityList'
		};

	function submit(param){
		this.opts = param;
		this.init();
	}

	S.augment(submit,{
		init:function(){
			this._valid();
			this._addEventListener();
			this._getCity(true,0);
			this._setInfo();
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

			$(el.J_proviceList).on('change',function(ev){
				var
					id = $(ev.currentTarget).children('option:selected').val();
				that._getCity(false,id);
			})	
		},
		_setInfo:function(){
			var
				that = this;
			MyHomeIO.isEdited({},function(rs,data,msg){
				if(rs){
					that._getCity(false,data.provinceId);
					$(el.J_proviceList).children('option').item(data.provinceId).attr('selected','selected');
					S.available('#J_live_CITY',function(){
						$(el.J_cityList).children('option').item(data.cityId).attr('selected','selected');
					})
				}
			})
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
					Dialog.alert("请重新确认信息是否无误！");
				}
			});
		},
		_getCity:function(isProvince,id){
				var
					i = 0,
					para,
					optionHtml;
				if(isProvince){
					para = {}
					$(el.J_proviceList).html('');
				}
				else{
					para = 'id='+id;
					$(el.J_cityList).html('');
				}
				MyHomeIO.getCity(para,function(rs,data,msg){
					S.each(data,function(item){
						if(i < data.length - 1){
							optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
							if(isProvince){	
								$(el.J_proviceList).append(optionHtml);
							}
							else{
								$(el.J_cityList).append(optionHtml);
							}
						}
						else{
							optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
							lastCityOptionHtml = '<option id="J_live_CITY" value='+item.id+'>'+item.name+'</option>';
							if(isProvince){	
								$(el.J_proviceList).append(optionHtml);
							}
							else{
								$(el.J_cityList).append(lastCityOptionHtml);
							}
						}
						i ++;
					})
					if(isProvince)
						$(el.J_proviceList).prepend('<option value=0>请选择</option>');
					else
						$(el.J_cityList).prepend('<option value=0>请选择</option>');
				})
			}
	});

	return submit;
},{
	requires:['mod/defender','mod/dialog','io/job_apply/my_home']
});
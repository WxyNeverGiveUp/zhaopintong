/*-----------------------------------------------------------------------------
* @Description:     弹出层中下拉列表相关的js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.18
* ==NOTES:=============================================
* v1.0.0(2014.9.18):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/select',function(S){
	var
		$ = S.all, delegate = S.Event.delegate,
		SelectIO = PW.io.module.select,
		el = {
			selectHolder:'.J_selectHolder'
		};

	function select(param){
		this.opts = S.merge(el,param);
		this.init();
	}

	S.augment(select,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this,
				opts = that.opts;
			/*点击下拉列表*/
			delegate(document,'focus',opts.selectHolder,function(evt){
				that._ajaxGetOption(evt.currentTarget);
			});
		},
		/*使用ajax获取下拉列表*/
		_ajaxGetOption:function(target){
			var
				that = this,
				opts = that.opts,
				type = $(target).attr('data-type'),
				info;
			if(opts.extraParam){
				info = S.merge({type:type},opts.extraParam);
			}else{
				info = {type:type};
			}
			SelectIO.getOption(info,function(rs,data,errorMes){
				if(rs){
					that._updateSelect(data,target);
				}else{
					S.log(errorMes);
				}
			});
		},
		/*更新下来列表*/
		_updateSelect:function(data,selectHolder){
			var
				that = this,
				optionHtml = '<option value="0">请选择</option>';
			S.each(data,function(d,o){
				optionHtml = optionHtml + '<option value="'+d.id+'">'+d.name+'</option>';
			});
			$(selectHolder).html(optionHtml);
		}
	});

	return select;
},{
	requires:['io/module/select/select']
});
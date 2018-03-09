/*-----------------------------------------------------------------------------
* @Description:     全选部分相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.16
* ==NOTES:=============================================
* v1.0.0(2014.9.16):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/selectAll',function(S){
	var
		$ = S.all, on = S.Event.on,
		el = {
			selectAllBtn:'#J_selectAll',//指向全选按钮
			dataList:'#J_template'//指向数据列表
		};

	function selectAll(param){
		this.opts = param;
		this.init();
	}
	S.augment(selectAll,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击全选按钮*/
			on(el.selectAllBtn,'click',function(evt){
				that._selectAll(evt.target);
			});
		},
		/*全选*/
		_selectAll:function(holder){
			var
				that = this,
				checkbox = $('input:[type=checkbox]',el.dataList),
				checked = $(holder).attr('checked');
			if(checked == 'checked'){
				that._checkedAll(checkbox);
			}else{
				that._unCheckedAll(checkbox);
			}
		},
		/*全选*/
		_checkedAll:function(checkbox){
			S.each(checkbox,function(c,o){
				if($(c).attr('disabled') != 'disabled'){
					$(c).attr('checked','checked');
				}
			});
		},
		/*取消全选*/
		_unCheckedAll:function(checkbox){
			S.each(checkbox,function(c,o){
				$(c).removeAttr('checked');
			});
		}
	});

	return selectAll;
},{
	requires:['core']
});
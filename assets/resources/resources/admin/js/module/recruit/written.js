/*-----------------------------------------------------------------------------
* @Description:     招聘信息部分笔试信息相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/recruit/written',function(S,del){
	PW.namespace('module.recruit.written');
	PW.module.recruit.written = {
		del:function(param){
			new del(param);
		}
	};
},{
	requires:['written/del']
});
/*--------------------------------删除笔试信息-------------------------------*/
KISSY.add('written/del',function(S){
	var
		$ = S.all, on = S.Event.on,
		el = {

		},
		Dialog = PW.mod.Dialog,
		WrittenIO = PW.io.recruit.written,
		DEL_TIP = '确定删除该条笔试信息？';

	function del(param){
		this.opts = param;
		this.init();
	}

	S.augment(del,{
		init:function(){
			this._openTip();
		},
		/*显示提示信息*/
		_openTip:function(){
			var
				that = this;
			Dialog.confirm(
				DEL_TIP,
				function(){
					that._ajaxDelWritten();
				},
				function(){

				},{
					position:'fixed',
					width:'400',
					maskColor:'#343434'
				}
			);
		},
		/*使用ajax删除笔试信息*/
		_ajaxDelWritten:function(){
			var
				that = this,
				opts = that.opts,
				parent = $(opts).parent(),
				written = $(parent).next(),
				id = $(written).attr('data-id');
			WrittenIO.del({id:id},function(rs,data,errMsg){
				if(rs){
					$(written).html('<div class="control-area control-area-short"><span>无</span></div>');
					$(opts).addClass('none');
				}else{
					Dialog.error(errMsg);
				}
			});
		}
	});

	return del;
},{
	requires:['io/recruit/written','mod/dialog']
});
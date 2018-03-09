/*-----------------------------------------------------------------------------
* @Description:     招聘信息部分面试信息相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/recruit/interview',function(S,del){
	PW.namespace('module.recruit.interview');
	PW.module.recruit.interview = {
		del:function(param){
			new del(param);
		}
	};
},{
	requires:['interview/del']
});
/*--------------------------------删除面试信息-------------------------------*/
KISSY.add('interview/del',function(S){
	var
		$ = S.all, on = S.Event.on,
		el = {

		},
		Dialog = PW.mod.Dialog,
		InterviewIO = PW.io.recruit.interview,
		DEL_TIP = '确定删除该条面试信息？';

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
					that._ajaxDelInterview();
				},
				function(){

				},{
					position:'fixed',
					width:'400',
					maskColor:'#343434'
				}
			);
		},
		/*使用ajax删除面试信息*/
		_ajaxDelInterview:function(){
			var
				that = this,
				opts = that.opts,
				parent = $(opts).parent(),
				interview = $(parent).next(),
				id = $(interview).attr('data-id');
			InterviewIO.del({id:id},function(rs,data,errMsg){
				if(rs){
					$(interview).html('<div class="control-area control-area-short"><span>无</span></div>');
					$(opts).addClass('none');
				}else{
					Dialog.error(errMsg);
				}
			});
		}
	});

	return del;
},{
	requires:['io/recruit/interview','mod/dialog']
});
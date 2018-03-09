/*-----------------------------------------------------------------------------
* @Description: edit-subscribe.js编辑订阅页面js
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.07.31
* ==NOTES:=============================================
* v1.0.0(2015.07.31):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/editSubscribe',function(S,editSubscribeShow){
	PW.namespace('page.job_apply.editSubscribe');
	PW.page.job_apply.editSubscribe = function(){
		new editSubscribeShow();
	}
},{
	requires:['editSubscribe/editSubscribeShow']
})

/*------------------------------编辑职位订阅-----------------------------------*/
KISSY.add('editSubscribe/editSubscribeShow',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		editSubscribeIO = PW.io.job_apply.editSubscribe,
		el = {
			J_del_sub : '.del-sub'
		};
	function editSubscribeShow(){
		this.init();
	}

	S.augment(editSubscribeShow,{
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			$(el.J_del_sub).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().attr('id'),
					para = 'id='+id;

				editSubscribeIO.delSub(para,function(rs,data,msg){
					if(rs)
						$(ev.currentTarget).parent().remove();
				})
			})
		}
	})
	return editSubscribeShow;
},{
	requires:['event','io/job_apply/editSubscribe']
})
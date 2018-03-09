/*-----------------------------------------------------------------------------
* @Description:     地点维护删除相关js
* @Version:         1.0.0
* @author:          chenke(396985267@qq.com)
* @date             2014.10.01
* ==NOTES:=============================================
* v1.0.0(2014.10.01):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/system/place',function(S,del){
	PW.namespace('module.system.place');
	PW.module.system.place = {
		del:function(param){
			new del(param);
		}
	};
},{
	requires:['system/place/del']
});
/*------------------------------删除------------------------------*/
KISSY.add('system/place/del',function(S){
	var
		$ = S.all,
		Dialog = PW.mod.Dialog,
		PlaceIO = PW.io.system.place,
		el = {},
		DEL_TIP = '确定删除该地点信息？';
	function del(param){
		this.e = param;
		this.init();
	}

	S.augment(del,{
		init:function(){
			var
				that = this,
				e = that.e;
			this._showDialog(e);
		},
		/*显示提示信息
		e指向删除按钮的a标签
		*/
		_showDialog:function(e){
			var
				that = this;
			Dialog.confirm(
				DEL_TIP,
				function(){
					that._ajaxDelPlace(e);
				},
				function(){

				},{
					position:'fixed',
					width:'400',
					maskColor:'#343434'
				}
			);
		},
		/*发送ajax删除地点
		  e指向删除按钮的a标签
		*/
		_ajaxDelPlace:function(e){
			var
				that = this,
				placeTr = $(e).parent().parent(),
				inputHidden = $(e).siblings('input:[type="hidden"]'),
				id = $(inputHidden).attr('data-id');
			PlaceIO.del({id:id},function(rs,data,errMsg){
				if(rs){
					that._delPlaceList(e);
				}else{
					Dialog.error(errMsg);
				}
			});
		},
		_delPlaceList:function(evt){
			var
				that = this,
				placeTr = $(evt).parent().parent();
			$(placeTr).remove();
		}
	});

	return del;
},{
	requires:['io/system/place','mod/dialog']
});
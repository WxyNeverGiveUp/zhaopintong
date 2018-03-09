/*-----------------------------------------------------------------------------
* @Description:     接待管理部分宾馆管理相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.25
* ==NOTES:=============================================
* v1.0.0(2014.9.25):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/reception/hotel',function(S,del){
	PW.namespace('module.reception.hotel');
	PW.module.reception.hotel = {
		del:function(param){
			new del(param);
		}
	};
},{
	requires:['reception/hotel/del']
});
/*----------------------------------删除-----------------------------------*/
KISSY.add('reception/hotel/del',function(S){
	var
		$ = S.all,
		Dialog = PW.mod.Dialog,
		HotelIO = PW.io.reception.hotel,
		el = {

		},
		DEL_TIP = '确定删除该宾馆信息？';

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
					that._ajaxDelHotel();
				},
				function(){

				},{
					position:'fixed',
					width:'400',
					maskColor:'#343434'
				}
			);
		},
		/*使用ajax删除宾馆*/
		_ajaxDelHotel:function(){
			var
				that = this,
				opts = that.opts,
				checkbox = $('input[type="checkbox"]',opts.hotelList),
				id = [],checkedBox = [];
			S.each(checkbox,function(i,o){
				if($(i).attr('checked') == 'checked'){
					id.push($(i).attr('data-id'));
					checkedBox.push(i);
				}
			});
			if(checkedBox.length == 0){
				Dialog.alert('没有选中任何信息！');
			}else{
				HotelIO.delBatch({ids:id},function(rs,data,errMsg){
					if(rs){
						that._removeHotel(checkedBox);
					}else{
						Dialog.error(errMsg);
					}
				});
			}
		},
		/*删除宾馆信息*/
		_removeHotel:function(checkedBox){
			var
				that = this,
				tr;
			S.each(checkedBox,function(i,o){
				tr = $(i).parent().parent();
				$(tr).remove();
			});
		}
	});

	return del;
},{
	requires:['mod/dialog','io/reception/hotel']
});
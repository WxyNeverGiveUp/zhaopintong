/*-----------------------------------------------------------------------------
* @Description:     接待管理部分车辆管理相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/reception/vehicle',function(S,del){
	PW.namespace('module.reception.vehicle');
	PW.module.reception.vehicle = {
		del:function(param){
			new del(param);
		}
	};
},{
	requires:['reception/vehicle/del']
});
/*----------------------------------删除-----------------------------------*/
KISSY.add('reception/vehicle/del',function(S){
	var
		$ = S.all,
		Dialog = PW.mod.Dialog,
		VehicleIO = PW.io.reception.vehicle,
		el = {

		},
		DEL_TIP = '确定删除该车辆信息？';

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
					that._ajaxDelVehicle();
				},
				function(){

				},{
					position:'fixed',
					width:'400',
					maskColor:'#343434'
				}
			);
		},
		/*使用ajax删除车辆*/
		_ajaxDelVehicle:function(){
			var
				that = this,
				opts = that.opts,
				checkbox = $('input[type="checkbox"]',opts.vehicleList),
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
				VehicleIO.delBatch({ids:id},function(rs,data,errMsg){
					if(rs){
						that._removeVehicle(checkedBox);
					}else{
						Dialog.error(errMsg);
					}
				});
			}
		},
		/*删除车辆信息*/
		_removeVehicle:function(checkedBox){
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
	requires:['mod/dialog','io/reception/vehicle']
});
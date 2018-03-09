/*-----------------------------------------------------------------------------
* @Description:     接待管理部分车辆管理相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/recruit/recruit',function(S,del,enter){
	PW.namespace('module.recruit.recruit');
	PW.module.recruit.recruit = {
		del:function(param){
			new del(param);
		},
		enter:function(param){
			new enter(param);
		}
	};
},{
	requires:['recruit/recruit/del','recruit/recruit/enter']
});
/*----------------------------------删除-----------------------------------*/
KISSY.add('recruit/recruit/del',function(S){
	var
		$ = S.all, on = S.Event.on,
		Dialog = PW.mod.Dialog,
		RecruitIO = PW.io.recruit.recruit,
		el = {
			
		},
		DEL_TIP = '确定删除该条招聘信息？';

	function del(param){
		this.opts = param;
		this.init();
	}

	S.augment(del,{
		init:function(){
			this._showTip();
		},
		/*显示提示信息*/
		_showTip:function(){
			var
				that = this;
			Dialog.confirm(
				DEL_TIP,
				function(){
					that._ajaxDelRecruit();
				},
				function(){

				},{
					position:'fixed',
					width:'400',
					maskColor:'#343434'
				}
			);
		},
		/*是呀ajax删除招聘信息*/
		_ajaxDelRecruit:function(){
			var
				that = this,
				opts = that.opts,
				checkbox = $('input[type="checkbox"]',opts.recruitList),
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
				RecruitIO.delBatch({ids:id},function(rs,data,errMsg){
					if(rs){
						window.location.reload();
					}else{
						Dialog.error(errMsg);
					}
				});
			}
		}
	});

	return del;
},{
	requires:['mod/dialog','io/recruit/recruit']
});
/*---------------------------录入-------------------------*/
KISSY.add('recruit/recruit/enter',function(S){
	var
		$ = S.all,
		RecruitIO = PW.io.recruit.recruit,
		el = {};

	function enter(param){
		this.opts = param;
		this.init();
	}

	S.augment(enter,{
		init:function(){
			this._ajaxEnter();
		},
		/*使用ajax录入*/
		_ajaxEnter:function(){
			var
				that = this,
				opts = that.opts,
				tr = $(opts).parent().parent(),
				recruitId = $('input',tr).attr('data-id');
			RecruitIO.enter({recruitId:recruitId},function(rs,data,errMsg){
				if(rs){
					that._changeState();
				}else{
					S.log(errMsg);
				}
			});
		},
		/*改变信息状态*/
		_changeState:function(){
			var
				that = this,
				opts = that.opts,
				td = $(opts).parent();
			$(td).html('已处理');
		}
	});

	return enter;
},{
	requires:['io/recruit/recruit']
});
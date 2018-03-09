/*-----------------------------------------------------------------------------
* @Description:     用户部分面试信息相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.28
* ==NOTES:=============================================
* v1.0.0(2014.9.28):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/system/user',function(S,lock,unlock,delBatch){
	PW.namespace('module.system.user');
	PW.module.system.user = {
		lock:function(param){
			new lock(param);
		},
		unlock:function(param){
			new unlock(param);
		},
		delBatch:function(param){
			new delBatch(param);
		}
	};
},{
	requires:['system/user/lock','system/user/unlock','system/user/delBatch']
});
/*--------------------------------锁定-----------------------------------*/
KISSY.add('system/user/lock',function(S){
	var
		$ = S.all,
		UserIO = PW.io.system.user,
		el = {
			lockHolder:'.J_lockHolder',//指向状态
			lockBtn:'.lcok',//指向封锁按钮
			unlockBtn:'.unlock',//直接解锁按钮
			modBtn:'.mod'//指向修改按钮
		};

	function lock(param){
		this.opts = param;
		this.init();
	}

	S.augment(lock,{
		init:function(){
			this._ajaxLock();
		},
		_ajaxLock:function(){
			var
				that = this,
				opts = that.opts,
				td = $(opts).parent(),
				tr = $(td).parent(),
				id = $(tr).attr('data-id');
			UserIO.lock({id:id},function(rs,data,errMsg){
				if(rs){
					that._changeLockHolder(tr,opts);
				}
			});
		},
		_changeLockHolder:function(tr,target){
			var
				that = this,
				lockHolder = $(el.lockHolder,tr),
				unlockBtn = $(el.unlockBtn,tr),
				modBtn = $(el.modBtn,tr);
			/*一旦加锁，不能修改*/
			$(lockHolder).text('封锁');
			$(target).addClass('none');
			$(unlockBtn).removeClass('none');
			$(modBtn).addClass('none');
		}
	});

	return lock;
},{
	requires:['io/system/user']
});
/*--------------------------------解锁-----------------------------------*/
KISSY.add('system/user/unlock',function(S){
	var
		$ = S.all,
		UserIO = PW.io.system.user,
		el = {
			lockHolder:'.J_lockHolder',//指向状态
			lockBtn:'.lcok',//指向封锁按钮
			unlockBtn:'.unlock',//直接解锁按钮
			modBtn:'.mod'//指向修改按钮
		};

	function unlock(param){
		this.opts = param;
		this.init();
	}

	S.augment(unlock,{
		init:function(){
			this._ajaxUnlock();
		},
		_ajaxUnlock:function(){
			var
				that = this,
				opts = that.opts,
				td = $(opts).parent(),
				tr = $(td).parent(),
				id = $(tr).attr('data-id');
			UserIO.unlock({id:id},function(rs,data,errMsg){
				if(rs){
					that._changeLockHolder(tr,opts);
				}
			});
		},
		_changeLockHolder:function(tr,target){
			var
				that = this,
				lockHolder = $(el.lockHolder,tr),
				lockBtn = $(el.lockBtn,tr),
				modBtn = $(el.modBtn,tr);
			/*一旦解锁，可以修改*/
			$(lockHolder).text('解锁');
			$(target).addClass('none');
			$(lockBtn).removeClass('none');
			$(modBtn).removeClass('none');
		}
	});

	return unlock;
},{
	requires:['io/system/user']
});
/*------------------------------批量删除------------------------------*/
KISSY.add('system/user/delBatch',function(S){
	var
		$ = S.all,
		Dialog = PW.mod.Dialog,
		UserIO = PW.io.system.user,
		el = {},
		DEL_TIP = '确定删除该用户信息？';
	function delBatch(param){
		this.opts = param;
		this.init();
	}

	S.augment(delBatch,{
		init:function(){
			this._showDialog();
		},
		/*显示提示信息*/
		_showDialog:function(){
			var
				that = this;
			Dialog.confirm(
				DEL_TIP,
				function(){
					that._ajaxDelBatch();
				},
				function(){

				},{
					position:'fixed',
					width:'400',
					maskColor:'#343434'
				}
			);
		},
		/*ajax批量删除*/
		_ajaxDelBatch:function(){
			var
				that = this,
				checkedInfo = that._getCheckInfo();
			if(checkedInfo.checkedInfo.length > 0){
				UserIO.delBatch({ids:checkedInfo.checkedId},function(rs,data,errMsg){
					if(rs){
						that._delUser(checkedInfo.checkedInfo);
					}else{
						Dialog.error(errMsg);
					}
				});
			}else{
				Dialog.alert('您当前没有选中任何信息！');
			}
		},
		/*获取选中的人*/
		_getCheckInfo:function(){
			var
				that = this,
				opts = that.opts,
				userList = opts.userList,
				checkedBox = $('input:[type="checkbox"]',userList),
				checkedId = [],checkedInfo = [];
			S.each(checkedBox,function(i,o){
				if($(i).attr('checked') == 'checked'){
					checkedId.push($(i).attr('data-id'));
					checkedInfo.push(i);
				}
			});

			return {checkedId:checkedId,checkedInfo:checkedInfo};
		},
		/*删除用户*/
		_delUser:function(checkedInfo){
			var
				that = this,
				tr;
			S.each(checkedInfo,function(i,o){
				tr = $(i).parent().parent();
				tr.remove();
			});
		}
	});

	return delBatch;
},{
	requires:['io/system/user','mod/dialog']
});
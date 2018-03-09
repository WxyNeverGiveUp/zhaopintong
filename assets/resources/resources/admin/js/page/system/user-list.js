/*-----------------------------------------------------------------------------
* @Description:     用列表相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.28
* ==NOTES:=============================================
* v1.0.0(2014.9.28):
     初始生成
* --------------------------------------------------*/
KISSY.add('page/system/user-list',function(S,core,lock,delBatch,selectAll){
	PW.namespace('page.userList');
	PW.page.userList = function(param){
		new core(param);
		new lock(param);
		new delBatch(param);
		new selectAll(param);
	};
},{
	requires:['userList/core','userList/lock','userList/delBatch','module/selectAll']
});
/*-----------------------------------列表------------------------------------*/
KISSY.add('userList/core',function(S){
	var
		$ = S.all,
		Pagination = PW.mod.Pagination,
		el = {};

	function core(param){
		this.opts = param;
		this.init();
	}

	S.augment(core,{
		init:function(){
			this._paging();
		},
		_paging:function(){
			var
				that = this,
				opts = that.opts;
			Pagination.client(opts);
		}
	});

	return core;
},{
	requires:['mod/pagination']
});
/*---------------------------------加锁-----------------------------------*/
KISSY.add('userList/lock',function(S){
	var
		$ = S.all, delegate = S.Event.delegate,
		userHandler = PW.module.system.user,
		el = {
			lockBtn:'.lcok',//指向锁定按钮
			unlockBtn:'.unlock'//指向解锁按钮
		};

	function lock(param){
		this.init();
	}

	S.augment(lock,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击锁定按钮*/
			delegate(document,'click',el.lockBtn,function(evt){
				that._lock(evt.currentTarget);
			});
			/*点击解锁按钮*/
			delegate(document,'click',el.unlockBtn,function(evt){
				that._unlock(evt.currentTarget);
			});
		},
		/*锁定*/
		_lock:function(holder){
			userHandler.lock(holder);
		},
		/*解锁*/
		_unlock:function(holder){
			userHandler.unlock(holder);
		}
	});

	return lock;
},{
	requires:['module/system/user']
});
/*-------------------------------------批量删除-------------------------------------*/
KISSY.add('userList/delBatch',function(S){
	var
		$ = S.all, on = S.Event.on,
		userHandler = PW.module.system.user,
		el = {
			userList:'#J_template',//指向用户列表
			delBatchBtn:'.J_delBatchBtn'
		};

	function delBatch(param){
		this.init();
	}

	S.augment(delBatch,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击删除按钮*/
			on(el.delBatchBtn,'click',function(evt){
				that._delUserInfo();
			});
		},
		/*删除用户*/
		_delUserInfo:function(){
			userHandler.delBatch(el);
		}
	});

	return delBatch;
},{
	requires:['module/system/user']
});
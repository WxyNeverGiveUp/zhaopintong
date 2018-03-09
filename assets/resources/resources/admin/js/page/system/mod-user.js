/*-----------------------------------------------------------------------------
* @Description:     修改用户相关js
* @Version:         1.0.0
* @author:          chenke(396985267@qq.com)
* @date             2014.10.04
* ==NOTES:=============================================
* v1.0.0(2014.10.04):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/system/mod-user',function(S,core){
	PW.namespace('page.modUser');
	PW.page.modUser = function(param){
		new core;
	}
},{
	requires:['modUser/core']
});
/*-------------------------------表单提交-----------------------------*/
KISSY.add('modUser/core',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Defender = PW.mod.Defender,
		Dialog = PW.mod.Dialog,
		UserIO = PW.io.system.user,
		el = {
			submitBtn:'.J_submitBtn',//指向提交按钮
		};
	function core(param){
		this.init();
	}

	S.augment(core,{
		init:function(){
			this.valid = Defender.client('form',{
				showTip:false
			});
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*表单提交*/
			on(el.submitBtn,'click',function(e){
				that._submit();
			});
		},
		_submit:function(){
			var
				that = this,
				v = that.valid;
			v.validAll();
			if(v.getValidResult('bool')){
				var
					info = that._serialize();
				UserIO.modValid(info,function(rs,data,errMsg){
					if(rs){
						jQuery('form').submit();
					}else{
						Dialog.error(errMsg);
					}
				});
			}
		},
		/*序列化添加走访信息的表单*/
		_serialize:function(){
			var
				info = {};
			info = DOM.serialize('form');
			return info;
		}
	});

	return core;
},{
	requires:['io/system/user','mod/defender','mod/dialog']
});
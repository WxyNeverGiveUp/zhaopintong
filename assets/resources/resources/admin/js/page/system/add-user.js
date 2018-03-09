/*-----------------------------------------------------------------------------
* @Description:     新增用户相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.28
* ==NOTES:=============================================
* v1.0.0(2014.9.28):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/system/add-user',function(S,core){
	PW.namespace('page.addUser');
	PW.page.addUser = function(param){
		new core;
	}
},{
	requires:['addUser/core']
});
/*-------------------------------表单提交-----------------------------*/
KISSY.add('addUser/core',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Defender = PW.mod.Defender,
		Dialog = PW.mod.Dialog,
		UserIO = PW.io.system.user,
		el = {
			submitBtn:'.J_submitBtn',//指向提交按钮
			username:'#J_username'//指向用户名表单
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
				v = that.valid,
				result;
			v.validAll();
			if(v.getValidResult('bool')){
				result = that._validRole();
				if(result){
					var
						username;
					username = $(el.username).val();
					UserIO.addValid({username:username},function(rs,data,errMsg){
						if(rs){
							jQuery('form').submit();
						}else{
							Dialog.error(errMsg);
						}
					});
				}else{
					Dialog.error('没有选中任何角色！');
					return false;
				}
			}
		},
		/*验证角色*/
		_validRole:function(){
			var
				that = this,
				checkBox = $('input:[type="checkbox"]','form'),
				checkedBox = [];
			S.each(checkBox,function(i,o){
				if($(i).attr('checked') == 'checked'){
					checkedBox.push(i);
				}
			});
			if(checkedBox.length > 0){
				return true;
			}else{
				return false;
			}
		}
	});

	return core;
},{
	requires:['io/system/user','mod/defender','mod/dialog']
});
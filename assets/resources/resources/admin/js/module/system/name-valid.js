/*-----------------------------------------------------------------------------
* @Description:     名称验重相关js
* @Version:         1.0.0
* @author:          chenke(396985267@qq.com)
* @date             2014.10.02
* ==NOTES:=============================================
* v1.0.0(2014.10.02):
     初始生成
* ---------------------------------------------------------------------------*/
/*--------------------------------名称验证-----------------------------------*/
KISSY.add('module/system/name-valid',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Dialog = PW.mod.Dialog,
		Defender = PW.mod.Defender,
		PlaceIO = PW.io.system.place,
		el = {
			submitBtn:'.J_submitBtn'//指向确定按钮
		};

	function nameValid(param){
		this.opts = param;
		this.init();
		this.valid = Defender.client('form',{
			showTip:false
		});
	}

	S.augment(nameValid,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			on(el.submitBtn,'click',function(){
				that._addName();
			});
		},
		_addName:function(){
			var
				that = this,
				v = that.valid;
				v.validAll();
			if(v.getValidResult('bool')){
				var
					info = that._serialize();
				PlaceIO.nameValid(info,function(rs,data,errMsg){
					if(rs){
						jQuery('form').submit();
					}else{
						Dialog.error(errMsg);
					}
				});
			}
		},
		/*序列化添加需求信息的表单*/
		_serialize:function(){
			var
				info = {};
			info = DOM.serialize('form');
			return info;
		}
	});

	return nameValid;
},{
	requires:['io/system/place','mod/dialog']
});

/*-----------------------------------------------------------------------------
* @Description:     宴请部分综合办相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.26
* ==NOTES:=============================================
* v1.0.0(2014.9.26):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/banquet/office-new',function(S,add){
	PW.namespace('page.officeNew');
	PW.page.officeNew = function(param){
		new add(param);
	};
},{
	requires:['officeNew/add']
});
/*-----------------------------------添加---------------------------------*/
KISSY.add('officeNew/add',function(S){
	var
		$ = S.all, on = S.Event.on,
		Defender = PW.mod.Defender,
		el = {
			modBtn:'.J_modBtn'//修改按钮
		};

	function add(param){
		this.opts = param;
		this.init();
	}

	S.augment(add,{
		init:function(){
			this.valid = Defender.client('form',{
				showTip:false,
				items:[
					{
						queryName:'.time',
						pattern: function(input,shell,form){
							var 
								val = S.DOM.val(input);
								if(val != '' && /^(([1-9]{1})|([0-1][0-9])|([1-2][0-3])):([0-5][0-9])$/.test(val)){
									return true;
								}else{
									return false;
								}
						},
						tip:'|',
						showEvent:'focus',
						vldEvent:'keyup'
					}
				]
			});
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击修改按钮*/
			on(el.modBtn,'click',function(evt){
				that._modTime(evt.target);
			});
		},
		/*修改时间*/
		_modTime:function(target){
			var
				that = this,
				timeHolder = $(target).prev();
			$(timeHolder).removeAttr('readOnly');
		}
	});

	return add;
},{
	requires:['mod/defender']
});
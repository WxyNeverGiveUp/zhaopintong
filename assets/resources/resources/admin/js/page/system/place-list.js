/*-----------------------------------------------------------------------------
* @Description:     地点列表相关js
* @Version:         1.0.0
* @author:          chenke(396985267@qq.com)
* @date             2014.10.01
* ==NOTES:=============================================
* v1.0.0(2014.10.01):
     初始生成
* --------------------------------------------------*/
KISSY.add('page/system/place-list',function(S,delPlace,nameValid){
	PW.namespace('page.placeList');
	PW.page.placeList = function(param){
		new delPlace(param);
		new nameValid(param);
	};
},{
	requires:['placeList/delPlace','module/system/name-valid']
});
/*-----------------------------------删除地点------------------------------------*/
KISSY.add('placeList/delPlace',function(S){
	var
		$ = S.all, on = S.Event.on,
		Defender = PW.mod.Defender,
		placeHandler = PW.module.system.place,
		el = {
			delBtn:'.J_delete'//指向列表删除按钮
		};

	function delPlace(param){
		this.opts = param;
		this.init();
	}

	S.augment(delPlace,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击删除按钮*/
			on(el.delBtn,'click',function(evt){
				S.log(evt.currentTarget);
				that._delPlace(evt.currentTarget);

			});
		},
		/*发送ajax删除地点
		  e指向删除按钮的a标签
		*/
		_delPlace:function(e){
			placeHandler.del(e);
		},
		/*删除列表当中要删除的地点行
		evt 指向删除按钮的a标签
		*/
		_delPlaceList:function(evt){
			var
				that = this,
				placeTr = $(evt).parent().parent();
			$(placeTr).remove();
		}
	});

	return delPlace;
},{
	requires:['mod/defender','module/system/place']
});
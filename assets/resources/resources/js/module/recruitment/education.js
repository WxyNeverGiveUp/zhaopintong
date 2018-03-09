/*-----------------------------------------------------------------------------
* @Description:     教育部分相关js
* @Version:         1.0.0
* @author:          xuyihong(597262617@qq.com)
* @date             2015.6.19
* ==NOTES:=============================================
* v1.0.0(2014.9.28):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/recruitment/education',function(S,collection,uncollection){
	PW.namespace('module.recruitment.education');
	PW.module.recruitment.education = {
		collection:function(param){
			new collection(param);
		},
		uncollection:function(param){
			new uncollection(param);
		}
	}
},{
	requires:['recruitment/education/collection','recruitment/education/uncollection']
});
/*--------------------------------收藏-----------------------------------*/
KISSY.add('recruitment/education/collection',function(S){
	var 
		$ = S.all,
		collectionIO = PW.io.recruitment.education,
		el = {
			collectionkBtn:'.collection',//指向收藏按钮
			uncollectionBtn:'.uncollection'//直接取消收藏按钮
		};
	function collection(param){
		this.opts = param;
		this.init();
	}

	S.augment(collection,{
		init:function(){
			this._ajaxCollection();
		},
		_ajaxCollection:function(){
			var
				that = this,
				opts = that.opts,
				div = $(opts).parent(),
				li = $(div).parent(),
				id = $(li).attr('data-id');
			collectionIO.collection({id:id},function(rs,data,errMsg){
				if(rs){
					that._changeCollectionHolder(tr,opts);
				}
			});
		},
		_changeCollectionHolder:function(tr,target){
			var
				that = this,
				uncollectionBtn = $(el.uncollectionBtn,tr);
			/*一旦加锁，不能修改*/
			$(target).addClass('none');
			$(uncollectionBtn).removeClass('none');
		}
	});

	return collection;
},{
	requires:['io/recruitment/education']
});

/*--------------------------------取消收藏-----------------------------------*/
KISSY.add('recruitment/education/uncollection',function(S){
	var
		$ = S.all,
		collectionIO = PW.io.recruitment.education,
		el = {
			collectionkBtn:'.collection',//指向收藏按钮
			uncollectionBtn:'.uncollection'//直接取消收藏按钮
		};

	function uncollection(param){
		this.opts = param;
		this.init();
	}

	S.augment(uncollection,{
		init:function(){
			this._ajaxUncollection();
		},
		_ajaxUncollection:function(){
			var
				that = this,
				opts = that.opts,
				div = $(opts).parent(),
				li = $(div).parent(),
				id = $(li).attr('data-id');
			collectionIO.uncollection({id:id},function(rs,data,errMsg){
				if(rs){
					that._changeCollectionHolder(tr,opts);
				}
			});
		},
		_changeCollectionHolder:function(tr,target){
			var
				that = this,
				collectionBtn = $(el.collectionBtn,tr);
			/*一旦解锁，可以修改*/
			$(target).addClass('none');
			$(lockBtn).removeClass('none');
		}
	});

	return uncollection;
},{
	requires:['io/recruitment/education']
});
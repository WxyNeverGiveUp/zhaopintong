/*-----------------------------------------------------------------------------
* @Description:     招聘信息部分需求信息相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.15
* ==NOTES:=============================================
* v1.0.0(2014.9.15):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/recruit/need',function(S,add,del){
	PW.namespace('module.recruit.need');
	PW.module.recruit.need = {
		add:function(param){
			new add(param);
		},
		del:function(param){
			new del(param);
		}
	};
},{
	requires:['need/add','need/del']
});
/*--------------------------------增加需求信息-------------------------------*/
KISSY.add('need/add',function(S,Juicer){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Dialog = PW.mod.Dialog,
		Defender = PW.mod.Defender,
		NeedIO = PW.io.recruit.need,
		el = {
			majorHolder:'#J_majorHolder',
			educationHolder:'.J_educationHolder'
		},
		DIALOG_HTML　= '<form class="clearfix J_addNeedForm">' 
						+'<div class="control-area control-area-short">'
							+'<label>需求专业</label>'
							+'<select name="majorDemandId" class="textTheme J_select" autocomplete="off" data-valid-rule="scale(0,100000,0)" id="J_majorHolder" data-type="major">'
							+'</select>'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>学历</label>'
							+'<select name="education" class="textTheme J_educationHolder" autocomplete="off" data-valid-rule="scale(0,100000,0)">'
								+'<option value="0">请选择</option>'
								+'<option value="1">本科</option>'
								+'<option value="2">硕士</option>'
								+'<option value="3">博士</option>'
								+'<option value="4">仅限本科</option>'
								+'<option value="5">仅限专科</option>'
								+'<option value="6">仅限硕士</option>'
								+'<option value="7">仅限博士</option>'
								+'<option value="8">本科及以上</option>'
								+'<option value="9">硕士及以上</option>'
								+'<option value="10">专科及以上</option>'
							+'</select>'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>人数</label>'
							+'<input name="num" class="textTheme" autocomplete="off" type="text" data-valid-rule="notNull&isNumber">'
						+'</div>'
					+'</form>',
		NEED_INFO = '<div class="info-divied clearfix" data-id="&{jobRequestId}">'
						+'<input name="jobRequestId" value="&{jobRequestId}" type="hidden" class="J_jobRequestHolder">'
						+'<a href="javaScript:;" class="del-btn J_needDelBtn">删除</a>'
						+'<div class="control-area control-area-short">'
							+'<label>需求专业</label>'
							+'<span>&{majorValue}</span>'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>学历</label>'
							+'<span>&{education}</span>'
						+'</div>'
						+'<div class="control-area control-area-short">'
							+'<label>人数</label>'
							+'<span>&{num}</span>'
						+'</div>'
						+'</div>';

	function add(param){
		this.opts = param;
		this.init();
	}

	S.augment(add,{
		init:function(){
			this._showDialog();
			this.valid = Defender.client('.J_addNeedForm',{
				showTip:false
			});
		},
		_showDialog:function(){
			var
				that = this,
				opts = that.opts;
			that.dialogContent = that._createDialogHtml();
			that.dialogId = Dialog.open({
				title:'添加需求信息',
				width:930,
				content:that.dialogContent,
				footer:{
					btns:[
						{
							bid:1,
							text:'确定',
							clickHandler:function(e,me){
								that._ajaxAddNeed(me);
							}
						},{
							bid:2,
							text:'取消',
							clickHandler:function(e,me){
								me.close();
							}
						}
					]
				}
			});
		},
		/*生成对话框内容*/
		_createDialogHtml:function(){
			return DIALOG_HTML;
		},
		/*ajax添加需求信息*/
		_ajaxAddNeed:function(dialog){
			var
				that = this,
				valid = that.valid,
				info = {},
				majorValue = '';
			valid.validAll();
			if(valid.getValidResult('bool')){
				info = that._serialize();
				NeedIO.add(info,function(rs,data,errorMes){
					if(rs){
						info.jobRequestId = data.jobRequestId;
						majorValue = that._getSelectValue(info.majorDemandId,el.majorHolder);
						education = that._getSelectValue(info.education,el.educationHolder);
						info.majorValue = majorValue;
						info.education = education;
						that._addNeedInfo(info);
						dialog.close();
					}else{
						Dialog.error(errorMes);
					}
				});
			}
		},
		/*序列化添加需求信息的表单*/
		_serialize:function(){
			var
				info = {};
			info = DOM.serialize('.J_addNeedForm');
			return info;
		},
		/*把需求信息写回页面*/
		_addNeedInfo:function(info){
			var
				that = this,
				opts = that.opts,
				needInfoHtml = Juicer(NEED_INFO,info);
			$(opts.needInfoHolder).append(needInfoHtml);
		},
		/*获取下拉列表的值*/
		_getSelectValue:function(major,holder){
			var
				options = $('option',holder),
				majorValue = '';
			S.each(options,function(i,o){
				if($(i).val() == major){
					majorValue = $(i).text();
				}
			});
			return majorValue;
		}
	});

	return add;
},{
	requires:['mod/juicer','mod/dialog','mod/defender','io/recruit/need']
});
/*--------------------------------删除需求信息-------------------------------*/
KISSY.add('need/del',function(S){
	var
		$ = S.all, on = S.Event.on,
		el = {

		},
		Dialog = PW.mod.Dialog,
		NeedIO = PW.io.recruit.need,
		DEL_TIP = '确定删除该条需求信息？';

	function del(param){
		this.opts = param;
		this.init();
	}

	S.augment(del,{
		init:function(){
			this._showDialog();
		},
		/*显示提示信息*/
		_showDialog:function(){
			var
				that = this,
				opts = that.opts,
				needInfoHolder = $(opts).parent(),
				id = $(needInfoHolder).attr('data-id');
			Dialog.confirm(
				DEL_TIP,
				function(e,me){
					that._ajaxDelNeed(needInfoHolder,id);
				},
				function(e,me){

				},
				{
					position:'fixed',
					width:'400',
					maskColor:'#343434'
				}
			);
		},
		/*根据id删除需求信息*/
		_ajaxDelNeed:function(needInfoHolder,id){
			NeedIO.del({id:id},function(rs,data,errorMes){
				if(rs){
					$(needInfoHolder).remove();
				}else{
					Dialog.error(errorMes);
				}
			});
		}
	});

	return del;
},{
	requires:['mod/dialog','io/recruit/need']
});
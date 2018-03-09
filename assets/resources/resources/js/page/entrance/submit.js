/*-----------------------------------------------------------------------------
* @Description: 上传表单 (upload.js)
* @Version: 	V1.0.0
* @author: 		xuyihong(597262617@qq.com)
* @date			2015.07.27
* ==NOTES:=============================================
* v1.0.0(2014.05.21):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/entrance/submit',function(S, linkage, submit){
	PW.namespace('page.submit');
	PW.page.submit = function(param){
		new linkage(param);
		new submit();
	};
},{
	requires:['module/linkage','submit/submit']
});
/*---------------------------------表单提交---------------------------------*/
KISSY.add('submit/submit',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM, get = DOM.get, delegate = S.Event.delegate,
		Defender = PW.mod.Defender, Pagination = PW.mod.Pagination,
		Calendar = PW.mod.Calendar,
		Dialog = PW.mod.Dialog,
		// PhoneIO = PW.io.entrance.phone,
		el = {
			uploadForm:'.J_uploadForm', //指向添加单位信息的表单
			submitBtn:'.J_submit',//指向确定提交按钮
			delBtn:'.J_del',
			J_file_form:".file-form",
			J_daima:'#daima',
			J_zhizhao:'#zhizhao',
			J_daima_tip:".daima-tip",
			J_zhizhao_tip:'.zhizhao-tip'
		},
		TIP = ['确定执行该操作吗？', '删除成功！'],
		DATA_STU = 'data-stu-id';
	function submit(param){
		this.init();
	}

	S.augment(submit,{
		init:function(){
			this.valid = Defender.client(el.uploadForm,{
				showTip:false
			});
			/*this.initCalander();*/
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击确定按钮*/
			on(el.submitBtn,'click',function(evt){
				that._formSubmit();
			});
			delegate('#J_template','click',el.delBtn,function(e){
				that._del(e.target);
			});
		},
		/*表单提交*/
		_formSubmit:function(){
			var
				that = this,
				valid = that.valid,
				result = true;
			/*result = that._validCheckbox();*///验证复选框是否为空
			valid.validAll(function(rs){
				if(rs){
					if($(el.uploadForm).hasClass(el.J_file_form)){
						var
							isSubmit = true,
							daimaFilePath = $(el.J_daima).val(),
							zhizhaoFilePath = $(el.J_zhizhao).val(),
							daimaSuffix = daimaFilePath.substring(daimaFilePath.length-3 , daimaFilePath.length),
							zhizhaoSuffix = zhizhaoFilePath.substring(zhizhaoFilePath.length-3 , zhizhaoFilePath.length);

						if(daimaSuffix != 'jpg' && daimaSuffix != 'pdf'){
							$(el.J_daima_tip).text('请上传jpg格式或pdf格式的组织机构代码');
							isSubmit = false;
						}
						if(zhizhaoSuffix != 'jpg' && zhizhaoSuffix != 'pdf'){
							$(el.J_zhizhao_tip).text('请上传jpg格式或pdf格式的营业执照');
							isSubmit = false;
						}
						if(isSubmit){
							get(el.uploadForm).submit();
						}
					}
					else{
						get(el.uploadForm).submit();
					}
				}
			});
		},
		_del: function(e){
			var
				that = this,
				div = DOM.parent(e, 'div'),
				sid = DOM.attr(div, DATA_STU);
			Dialog.confirm(TIP[0],function(){
				PhoneIO.del({id: sid}, function(code, data, errMsg){
					if(code){
						that.Pagination.reload();
						Dialog.alert(TIP[1]);
					}else{
						Dialog.alert(errMsg);
					}
				})
			});
		}
	})
	return submit;
},{
	requires:['mod/defender','mod/dialog']
});
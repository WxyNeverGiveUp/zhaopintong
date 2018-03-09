/*-----------------------------------------------------------------------------
* @Description: 就业管理--学生信息添加 (student-info.js)
* @Version: 	V1.0.0
* @author: 		xuyihong(597262617@qq.com)
* @date			2015.05.21
* ==NOTES:=============================================
* v1.0.0(2014.05.21):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/employment/add-student',function(S,linkage, submit,famous,img,teacher){
	PW.namespace('page.addStudent');
	PW.page.addStudent = function(param){
		new linkage(param);
		new submit();
		new famous();
		new img();
		new teacher();
	};
},{
	requires:['module/linkage','addStudent/submit','addStudent/famous','addStudent/img','addStudent/teacher']
});
/*---------------------------------表单提交---------------------------------*/
KISSY.add('addStudent/submit',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM, get = DOM.get,
		Defender = PW.mod.Defender,
		Calendar = PW.mod.Calendar,
		Dialog = PW.mod.Dialog,
		el = {
			studentForm:'.J_studentForm', //指向添加单位信息的表单
			submitBtn:'.J_submit',//指向确定提交按钮
			J_postionForm : '.J_postionForm',
			J_companyForm : '.J_companyForm',
			J_loginForm : '.login-from'
		}

	function submit(param){
		this.init();
	}

	S.augment(submit,{
		init:function(){
			this.valid = Defender.client(el.studentForm,{
				showTip:false
			});
			this.validPosition = Defender.client(el.J_postionForm,{
				showTip:false
			});
			this.validCompany = Defender.client(el.J_companyForm,{
				showTip:false
			});
			this.validPreach = Defender.client(el.J_loginForm,{
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
		},
		/*表单提交*/
		_formSubmit:function(){
			var
				that = this,
				valid = that.valid,
				result = true;
			//result = that._validCheckbox();//验证复选框是否为空
			valid.validAll(function(rs){
				if(rs){
					get(el.studentForm).submit();
				}
			});
		}
		/*initCalander:function(){
			Calendar.client({
				renderTo: '.date', //默认只获取第一个
                    select: {
                        rangeSelect: false, //是否允许区间选择
                        dateFmt: 'YYYY-MM-DD',
                        showTime: false //是否显示时间
                    }
			});
		}*/
	})
	return submit;
},{
	requires:['mod/calendar','mod/defender','mod/dialog']
});

/*--------------------------------是否名校名企：xuyihong---------------------------------*/
KISSY.add('addStudent/famous',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM, get = DOM.get,
		Defender = PW.mod.Defender,
		Calendar = PW.mod.Calendar,
		delegate = S.Event.delegate,
		Dialog = PW.mod.Dialog,
		el = {
			famousBtn:'#J_famous',
			upBtn:'.J_up',
			logoBtn:'.J_logo'
		}

	function famous(param){
		this.opts = S.merge(el,param);
		this.init();
	}

	S.augment(famous,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this,
				opts = that.opts;
			/*当名校名企改变时*/
			delegate(document,'change',opts.famousBtn,function(evt){
				that._formFamous(evt.target);
			});
		},
		_formFamous:function(famousBtn){
			var
				that = this,
				proviceId,proviceName,
				opts = that.opts,
				selectOption = '<option value="0">请选择</option>';
			proviceId = $(famousBtn).val();
			proviceName = $(famousBtn).one('option:selected').text();

			if(proviceId > 0){
				$(el.upBtn).css("display","inline-block");
				$(el.logoBtn).css("display","inline-block");
			}else{
				$(el.upBtn).css("display","none");
				$(el.logoBtn).css("display","none");
			}
		}
	})
	return famous;
},{
	requires:['mod/calendar','mod/defender','mod/dialog']
});

/*--------------------------------上传图片格式---------------------------------*/
KISSY.add('addStudent/img',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM, get = DOM.get,
		Defender = PW.mod.Defender,
		Calendar = PW.mod.Calendar,
		delegate = S.Event.delegate,
		Dialog = PW.mod.Dialog,
		el = {
			imgBtn:'#FileUpload1',
			textBtn:'#hint'
		}

	function img(param){
		this.opts = S.merge(el,param);
		this.init();
	}

	S.augment(img,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this,
				opts = that.opts;
			/*当上传图片改变时*/
			delegate(document,'change',opts.imgBtn,function(evt){
				that._isImg(evt.target);
			});
		},
		_isImg:function(imgBtn){

            var filename = $(el.imgBtn).val();
			var extend = filename.substring(filename.lastIndexOf(".") + 1);

            // 上传文件提示信息
            var hint = $(el.textBtn);

            if ((extend != "png") && (extend != "jpg") && (extend != "gif")) {
                // 清空上传文件控件的值
                var file = $(el.imgBtn) ;
				file.after(file.clone().val("")); 
				file.remove(); 

            }
		}
	})
	return img;
},{
	requires:['mod/calendar','mod/defender','mod/dialog']
});


/*--------------------------------是否教师：xuyihong---------------------------------*/
KISSY.add('addStudent/teacher',function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM, get = DOM.get,
		Defender = PW.mod.Defender,
		Calendar = PW.mod.Calendar,
		delegate = S.Event.delegate,
		Dialog = PW.mod.Dialog,
		el = {
			teaBtn:".yes",
			noteaBtn:".no",
			isBtn:'.J_isTeacher',
			noBtn:'.J_notTeacher'
		}

	function teacher(param){
		this.opts = S.merge(el,param);
		this.init();
	}

	S.augment(teacher,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this,
				opts = that.opts;

			on(opts.teaBtn,'click',function(){
				$(el.isBtn).css("display","inline-block");
				$(el.noBtn).css("display","none");
			});
			on(opts.noteaBtn,'click',function(){
				$(el.isBtn).css("display","none");
				$(el.noBtn).css("display","inline-block");
			});
		}
	})
	return teacher;
},{
	requires:['mod/calendar','mod/defender','mod/dialog']
});
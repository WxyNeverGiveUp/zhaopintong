/*-----------------------------------------------------------------------------
* @Description: 单位管理--单位信息管理 (company-info.js)
* @Version: 	V1.0.0
* @author: 		xuyihong(597262617@qq.com)
* @date			2015.05.10
* ==NOTES:=============================================
* v1.0.0(2014.05.10):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/employment/add_student',function(S,add){
	PW.namespace('page.student');
	PW.page.student = function(param){
		new add();
	}
},{
	requires:['student/add']
});
/*---------------------------------添加------------------------------------*/

KISSY.add('student/add', function(S){
	var
		$ = S.all, on = S.Event.on, DOM = S.DOM,
		Juicer = PW.mod.Juicer,
		StudentIO = PW.io.employment.student,
		el = {
			addBtn:'.J_add' //
		};
	function add(){
		this.init();
	}
	S.augment(add, {
		init: function(){
			this.buildEvt();
		},
		/**
		 * 添加事件
		 **/
		buildEvt: function(){
			var
				that = this;
			on(el.addBtn, 'click', function(e){
				that.openDlg();
			});
		},
		openDlg: function(){
			var
				that = this,
				addStuTpl = Juicer(DOM.html('#addTpl'), {});
			PW.mod.Dialog.client({
				title: '添加学生',
				content: addStuTpl,
				footer:{
                    btns:[{
                        text: '确定',
                        clickHandler: function(e,me){
                            var data = DOM.serialize('.J_addStu');
                            StudentIO.add(data, function(code, data, errMsg){
                            	if(code){
                            		window.location.reload();
                            	}else{
                            		Dialog.alert(errMsg);
                            	}
                            })
                        }
                    },
                    {
                        text: '取消',
                        clickHandler: function(e,me){
                            me.close();
                        }
                    }]
                }
			})
		}
	});
	return add;
},{
	requires: ['mod/dialog','mod/juicer','io/employment/student']
})
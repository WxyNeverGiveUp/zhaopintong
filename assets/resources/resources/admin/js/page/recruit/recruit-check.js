/*-----------------------------------------------------------------------------
* @Description:     招聘信息查看相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/recruit/recruit-check',function(S,interview,written){
	PW.namespace('page.recruitCheck');
	PW.page.recruitCheck = function(param){
		new interview(param);
		new written(param);
	}
},{
	requires:['recruitCheck/interview','recruitCheck/written']
});
/*--------------------------------删除面试信息------------------------------------*/
KISSY.add('recruitCheck/interview',function(S){
	var
		$ = S.all, on = S.Event.on,
		Interview = PW.module.recruit.interview,
		el = {
			delInterviewBtn:'.J_delInterview'//指向删除面试信息的按钮
		};
	function interview(param){
		this.init();
	}

	S.augment(interview,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击删除按钮*/
			on(el.delInterviewBtn,'click',function(evt){
				that._delInterview(evt.target);
			});
		},
		/*删除面试信息*/
		_delInterview:function(target){
			Interview.del(target);
		}
	});

	return interview;
},{
	requires:['module/recruit/interview']
});
/*--------------------------------删除笔试信息------------------------------------*/
KISSY.add('recruitCheck/written',function(S){
	var
		$ = S.all, on = S.Event.on,
		Written = PW.module.recruit.written,
		el = {
			delWrittenBtn:'.J_delWritten'//指向删除笔试信息按钮
		};
	function written(param){
		this.init();
	}

	S.augment(written,{
		init:function(){
			this._addEventListener();
		},
		_addEventListener:function(){
			var
				that = this;
			/*点击删除笔试信息按钮*/
			on(el.delWrittenBtn,'click',function(evt){
				that._delWritten(evt.target);
			});
		},
		/*删除笔试信息*/
		_delWritten:function(target){
			Written.del(target);
		}
	});

	return written;
},{
	requires:['module/recruit/written']
});
/*-----------------------------------------------------------------------------
* @DescriSion: resume-preview页面相关js
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.08.01
* ==NOTES:=============================================
* v1.0.0(2015.08.01):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/resumePreview',function(S,resumePreviewShow){
	PW.namespace('page.job_apply.resumePreview');
	PW.page.job_apply.resumePreview = function(){
		new resumePreviewShow();
	}
},{
	requires:['resumePreview/resumePreviewShow']
})

/*------------------------------编辑职位订阅-----------------------------------*/
KISSY.add('resumePreview/resumePreviewShow',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		el = {
			J_add_letter : '.add-letter',
			J_letter_form : '.letter-form',
			J_submit_letter : '.submit-letter',
			J_cancel_letter : '.cancel-letter',
			J_set_which_letter : '.set-which-letter'

		};
	function resumePreviewShow(){
		this.init();
	}

	S.augment(resumePreviewShow,{
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			$(el.J_add_letter).on('click',function(ev){
				$(el.J_letter_form).show();
			});

			$(el.J_cancel_letter).on('click',function(ev){
				$(el.J_letter_form).hide();
			});

			$(el.J_set_which_letter).on('change',function(ev){
				if($(ev.currentTarget).val() == 0){
					$(el.J_add_letter).show();
					$(el.J_letter_form).show();
				}
				else{
					$(el.J_add_letter).hide();
					$(el.J_letter_form).hide();
				}
			})
		}
	})
	return resumePreviewShow;
},{
	requires:['event']
})
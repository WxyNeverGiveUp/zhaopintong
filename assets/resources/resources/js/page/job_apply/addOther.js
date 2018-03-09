KISSY.add('page/job_apply/addOther',function(S,addOtherShow){
	PW.namespace('page.job_apply.addOther');
	PW.page.job_apply.addOther = function(){
		new addOtherShow();
	}
},{
	requires:['addOther/addOtherShow']
})
	

	/*-----------------------------------------------------*/

KISSY.add('addOther/addOtherShow',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		Defender = PW.mod.Defender,
		jobDetailIO = PW.io.company.jobDetail,
		el = {
			J_letter_title :'.letter-title',
			J_title_tip : '.title-tip',
			J_form : '#J_form',
			J_context : '.context',
			J_context_tip : '.context-tip',
			J_save : '.save'
		},
		myvar = {
			isStaff:true
		};

	function addOtherShow(){
		this.init();
	}

	S.augment(addOtherShow,{
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var that = this;
			$(el.J_save).on('click',function(ev){
				that._formSubmit(ev);
			})	
		},

		_formSubmit:function(ev){
			ev.preventDefault();
			var 
				letterTitle = $(el.J_letter_title).val(),
				context = $(el.J_context).val(),
				isLetterTitleNull = true,
				isContext = true;
			S.log(context);
			if(letterTitle.length == 0){
				isLetterTitleNull = false;
				$(el.J_title_tip).show();
			}
			if(context.length <= 10){
				isContext = false;
				S.log(1);
				$(el.J_context_tip).show();
				S.log($(el.J_context_tip));
			}
			if(isLetterTitleNull && isContext)
				document.getElementById("J_form").submit();
			
		}		
	});
	return addOtherShow;
},{
	requires:['mod/defender','io/company/jobDetail']
})
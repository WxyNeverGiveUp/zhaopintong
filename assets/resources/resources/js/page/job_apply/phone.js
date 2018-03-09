/*-----------------------------------------------------------------------------
* @DescriSion: 添加电话，修改电话页面相关js
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.08.01
* ==NOTES:=============================================
* v1.0.0(2015.08.01):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/phone',function(S,phoneShow){
	PW.namespace('page.job_apply.phone');
	PW.page.job_apply.phone = function(){
		new phoneShow();
	}
},{
	requires:['phone/phoneShow']
})

/*-----------------------------------------------------------------*/
KISSY.add('phone/phoneShow',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		Defender = PW.mod.Defender,
		phoneIO = PW.io.job_apply.phone,
		el = {
			J_phone_form : '.phone-form',
			J_change_form : '.change-form',
			J_change_phone : '.change-phone',
			J_del_phone : '.del-phone',
			J_change_border : 'change-border',
			J_phone_number : '.phone-number',
			J_submit_phone : '.submit-phone',
			J_cancel_phone : '.cancel-phone',
			J_pw_tip : '.pw-tip'
 		};
	function phoneShow(){
		this.init();
		this.valid = Defender.client(el.J_phone_form,{
				showTip:false
		});
		this.valid = Defender.client(el.J_change_form,{
				showTip:true
		});
	}

	S.augment(phoneShow,{
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var 
				value = new Array();

			S.each($(el.J_phone_number),function(i,o){
				value.push($(i).val());
			});

			$(el.J_del_phone).on('click',function(ev){
				var
					id = $(ev.currentTarget).parent().attr('id'),
					para = 'id='+id;
				phoneIO.delPhone(para,function(rs,data,msg){
					if(rs)
						$(ev.currentTarget).parent().remove();
				})
			});

			$(el.J_change_phone).on('click',function(ev){
				$(ev.currentTarget).parent().children('input').addClass(el.J_change_border);
				$(ev.currentTarget).parent().children('input').removeAttr('readonly');
				$('div',el.J_change_form).show();
			})

			$(el.J_cancel_phone).on('click',function(ev){
				S.each($(el.J_phone_number),function(i,o){
					$(i).val(value[o]);
				})
				$(el.J_phone_number).removeClass(el.J_change_border);
				$(el.J_phone_number).attr('readonly','readonly');
				$('div',el.J_change_form).hide();
				$(el.J_pw_tip).text('');
			})
		}
	})
	return phoneShow;
},{
	requires:['event','mod/defender','io/job_apply/phone']
})
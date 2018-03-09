/*-----------------------------------------------------------------------------
* @Description:     收藏职位页面相关js
* @Version:         1.0.0
* @author:          daiqiaoling
* @date             2015.7.10
* ==NOTES:=============================================
* v1.0.0(2015.7.10):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/collect' , function(S,collect){
	PW.namespace('page.job_apply.collect');
	PW.page.job_apply.collect = function(param){
		new collect(param);
	}
},{
	requires:['collect/collect']
});
/*------------------------收藏职位---------------------------------------*/
KISSY.add('collect/collect',function(S){
	var $ = S.all, 
		on = S.Event.on,
		DOM = S.DOM,
		Pagination = PW.mod.Pagination,
		collectIO = PW.io.job_apply.collect,
		delegate = S.Event.delegate,
		el = {
				J_Default:'.no-collect-position',//指向未收藏职位时，显示的内容
				J_Number:'.collect-number',//指向收藏信息总数
				J_CollectBtn:'.collect',//指向收藏按钮
				J_CancelBtn:'.cancel-collect',//指向取消收藏按钮
				J_DeliverBtn:'.deliver-resume',//指向投递简历按钮
				J_PostInfo:'#PostInfo_template',
				J_pop : '.pop',
				J_login : '.login',
				J_register : '.register',
				J_publish_position : '.publish-position',
				J_activate_pop : '.activate-pop',
				J_message_order : '.message-order',
				J_message_pop_layer : '.message-pop-layer',
				J_message_pop : '.message-pop',
				J_sure_message : '.sure-message',
				J_vip_user_tip : '.vip-user-tip',
				J_activate_user_tip : '.activate-user-tip',
				J_position_order : '.position-order'
		};
		
	function collect(param){
		this.opts = param;
		this.init();
	}

	S.augment(collect,{
		init:function(){
			this._pagination();
			this._addEventListener();
		},

		_pagination:function(extraParam){
			var
				that = this,
				opts = that.opts;
			that.pagination = Pagination.client(opts);
		},
		
		_addEventListener:function(){
			var 
				that = this,
				opts = that.opts;
		
			//点击收藏按钮
			delegate(document,'click',el.J_CollectBtn,function(ev){
				var 
					currentTarget = $(ev.currentTarget),
					$postId = currentTarget.prev('div').attr('id'),
					para = 'postId='+$postId+'&isEnroll='+1;

				collectIO.collectIO(para,function(code,data,msg){
					if(code == 0){
						currentTarget.addClass('none');
						currentTarget.siblings('a').removeClass('none');
						$('span',el.J_Number).text(data);	
						$(el.J_Default).hide();
						$(el.J_publish_position).hide();
						$(el.J_Number).show();
					}
					if(code == 1){
						$(el.J_pop).show();
						$(el.J_login).show();
						$(el.J_register).hide();
					}
				});
				
			});

			//点击取消收藏按钮
			delegate(document,'click',el.J_CancelBtn,function(ev){
				var 
					currentTarget = $(ev.currentTarget),
					$postId = currentTarget.prev('div').attr('id'),
					para = 'postId='+$postId+'&isEnroll='+0; 

				collectIO.collectIO(para,function(code,data,msg){
					if(code == 0){
						currentTarget.addClass('none');
						currentTarget.next().next().addClass('none');
						currentTarget.next().removeClass('none');
						$('span',el.J_Number).text(data);
						if(data == 0){
							window.location.reload();
						}
					}
					if(code == 1){
						$(el.J_pop).show();
						$(el.J_login).show();
						$(el.J_register).hide();
					}
				});
			});

			delegate(document,'click',el.J_activate_pop,function(ev){
				$(el.J_message_pop_layer).show();
				$(el.J_message_pop).show();
				$(el.J_activate_user_tip).show();
			});

			$(el.J_sure_message).on('click',function(ev) {
				$(el.J_message_pop_layer).hide();
				$(el.J_message_pop).hide();
				$(el.J_vip_user_tip).hide();
				$(el.J_activate_user_tip).hide();
			});
		}
	});	
	return collect;
},{
	requires:['event','mod/pagination','io/job_apply/collect']
});

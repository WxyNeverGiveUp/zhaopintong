/*-----------------------------------------------------------------------------
* @Description:     comment页面相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.07
* ==NOTES:=============================================
* v1.0.0(2015.07.07):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/comment',function(S,commentShow){
	PW.namespace('page.company.comment');
	PW.page.company.comment = function(param){
		new commentShow(param);
	}
},{
	requires:['comment/commentShow']
});

/*--------------------------------------------------------------------------*/
KISSY.add('comment/commentShow',function(S){
	var
		$ = S.all,
		commentIO = PW.io.company.comment,
		jobDetailIO = PW.io.company.jobDetail,
		on = S.Event.on,
		delegate = S.Event.delegate,
		el = {
			J_follow :'.follow',
			J_unfollow : '.unfollow',
			J_follow_number : ".follow-number",
			J_comment_info:"#comment-info",
			J_look_all:".look-all",
			J_retract_all:".retract-all",
			J_part_comment:".part-comment",
			J_whole_comment:".whole-comment",
			J_praise:".praise",
			J_unpraise:".unpraise",
			J_praise_num:".J_praise_num",
			J_all_comment:".all-comment",
			J_employee_comment:".employ-comment",
			J_non_employee_comment:".non-employee-comment",
			J_sort:'.sort',
			J_subnav:".subnav",
			J_pitch:"pitch",
			J_company_name:".company-name",
			J_company_id:"company-id",
			J_to_comment : '.J_to_comment',
			J_pop : '.pop',
			J_login : '.login',
			J_register : '.register'
		}
		myvar = {
			ellipsis : '...',
			yes:1,
			no:0
		}
		Pagination = PW.mod.Pagination;

	function commentShow(param){
		this.opts = param;
		this.pagination;
		this.init();
	}

	S.augment(commentShow,{
		init:function(){
			this._pagination();
			this._click();
		},

		_pagination:function(){
			var
				that = this,
				opts = that.opts;

			that.pagination = Pagination.client(opts);
		},

		_click:function(){
			var
				that = this,
				opts = that.opts,
				sortId = 0,
				commentTypeId = 1,
				isFollow ,
				isCollect;
			$(el.J_unfollow).on('click',function(ev){
				isFollow = myvar.no;
				$(el.J_unfollow).hide();
				$(el.J_follow).show();
				var para = 'companyId='+$(el.J_company_name).attr(el.J_company_id)+'&isFollow='+isFollow;
				jobDetailIO.getFollowNumberIO(para,function(code,data,msg){
					if(code == 0){
						$(el.J_follow_number).text(data.followNumber);
					}
				})
			});

			$(el.J_follow).on('click',function(ev){
				isFollow = myvar.yes;
				var para = 'companyId='+$(el.J_company_name).attr(el.J_company_id)+'&isFollow='+isFollow;
				jobDetailIO.getFollowNumberIO(para,function(code,data,msg){
					if(code == 1){
						$(el.J_pop).show();
						$(el.J_login).show();
						$(el.J_register).hide();
					}
					if(code == 0){
						$(el.J_follow_number).text(data.followNumber);
						$(el.J_follow).hide();
						$(el.J_unfollow).show();
					}
				})
			});

			// KISSY.available(el.J_comment_info,function(){
			// 	var comment = $(el.J_part_comment);
			// 	for(var i=0 ; i<comment.length ; i ++){
			// 		comment.item(i).text(comment.item(i).text().substring(0,100)+myvar.ellipsis);
			// 	};
			// });

			delegate(document,'click',el.J_look_all,function(ev){
				$(ev.currentTarget).hide();
				$(ev.currentTarget).next().show();
				$(ev.currentTarget).prev().show();
				$(ev.currentTarget).prev().prev().hide();
			});

			delegate(document,'click',el.J_retract_all,function(ev){
				$(ev.currentTarget).prev().show();
				$(ev.currentTarget).hide();
				$(ev.currentTarget).prev().prev().hide();
				$(ev.currentTarget).prev().prev().prev().show();
			});

			delegate(document,'click',el.J_praise,function(ev){
				var 
					praiseNum = 0,
					isPraise = myvar.yes,
					wantPraise = $(ev.currentTarget).parent(),
					cancelPraise = $(ev.currentTarget).parent().prev();

				var commentId =  $(ev.currentTarget).parent().parent().parent().attr('comment-id'),
					para = "commentId="+commentId+"&isPraise="+isPraise;
				commentIO.getPraiseNum(para,function(code,data,msg){
					if(code == 0){
						praiseNum = '('+data.praiseNum+')';
						cancelPraise.children().item(1).children().text(praiseNum);
						wantPraise.hide();
						cancelPraise.show();
					}
					if(code == 1){
						$(el.J_pop).show();
           				$(el.J_login).show();
            			$(el.J_register).hide();
					}
				})	
			});

			delegate(document,'click',el.J_unpraise,function(ev){
				var
					praiseNum = 0,
					isPraise = myvar.no,
					cancelPraise = $(ev.currentTarget).parent() ,
					wantPraise = $(ev.currentTarget).parent().next();
				cancelPraise.hide();
				wantPraise.show();

				var commentId =  $(ev.currentTarget).parent().parent().parent().attr('comment-id'),
					para = "commentId="+commentId+"&isPraise="+isPraise;
				commentIO.getPraiseNum(para,function(rs,data,msg){
					praiseNum = '('+data.praiseNum+')';
					wantPraise.children().item(1).children().text(praiseNum);	
				})
				
			});
			
			$('a',el.J_subnav).on('click',function(ev){
				commentTypeId = $(ev.currentTarget).attr('id');
				$('a',el.J_subnav).removeClass(el.J_pitch);
				$(ev.currentTarget).addClass(el.J_pitch);
				S.mix(opts, {
					extraParam: {
						sortId:sortId,
						commentTypeId:commentTypeId
					}
				});
				that.pagination.reload(opts);
				// KISSY.available(el.J_comment_info,function(){
				// 	var comment = $(el.J_part_comment);
				// 	for(var i=0 ; i<comment.length ; i ++){
				// 		comment.item(i).text(comment.item(i).text().substring(0,100)+myvar.ellipsis);
				// 	};
				// });
			});

			$(el.J_sort).on('change',function(ev){
				sortId = $(ev.currentTarget).children('option:selected').attr('value');
				S.mix(opts, {
					extraParam: {
						sortId:sortId,
						commentTypeId:commentTypeId
					}
				});
				that.pagination.reload(opts);
			});

			$(el.J_to_comment).on('click',function(ev){
				$(el.J_pop).show();
                $(el.J_login).show();
                $(el.J_register).hide();
			})
		}

	});
	return commentShow;
},{
	requires:['mod/pagination','io/company/comment','io/company/jobDetail']
})
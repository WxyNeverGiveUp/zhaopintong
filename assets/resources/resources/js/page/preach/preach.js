/*-----------------------------------------------------------------------------
* @Description:     preach页面相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.6.30
* ==NOTES:=============================================
* v1.0.0(2015.6.30):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/preach/preach',function(S,preachShow){
	PW.namespace('page.preach.preach');
	PW.page.preach.preach = function(param){
		new preachShow(param);
	}
},{
	requires:['preach/preachShow']
});

/*---------------------------------------------------------------------------*/

KISSY.add('preach/preachShow',function(S){
	var $ = S.all,
		on = S.Event.on,
		preachIO = PW.io.preach.preach,
		el = {
			J_search : '.J_search',
			J_preach_input : '.preach-input',
			J_time : '.J_time',
			J_industry : '.J_industry',
			J_preach_type : '.J_preach_type',
			J_total :'.total',
			J_branch:'.branch',
			J_want_enroll:'.want-enroll',
			J_cancel_enroll:'.cancel-enroll',
			J_tpl_template:'#tpl-template',
			J_pop_layer:'.pop-layer',
			J_show_layer:'.show-layer',
			J_okay :'.okay',
			J_cancel:'.cancel',
			J_select_what:'.J_select_what',
			J_selected_what:'.J_selected_what',
			J_follow_company:".follow-company",
			J_follow : ".J_follow",
			J_pop : '.pop',
			J_login : '.login',
			J_register : '.register',
			J_tpl_template:'#tpl-template',
			J_preach_table:'.preach-table'
		},
		myvar = {
			other : 'other',
			yes : 1,
			no : 0,
			enrollSuccess :'报名成功',
			chinIndustry:'行业'
		}
		Pagination = PW.mod.Pagination;

	function preachShow(param){
		this.opts = param;
		this.pagination;
		this.init();
	}

	S.augment(preachShow , {
		init:function(){
			this._pagination();
			this._click();
		},
		_pagination:function(extraParam){
			var
				that = this,
				opts = that.opts;
			that.pagination = Pagination.client(opts);
		},
		_click:function(){
			var 
				that = this,
				opts = that.opts,
				searchWord = "",
				timeId = 0,
				industryId = 0,
				isFollow = 0,
				preachTypeId = 0;

			$(el.J_search).on('click', function(){
				searchWord = $(el.J_search).prev().val();
				S.mix(opts, {
					extraParam: {
						searchWord: searchWord,
						timeId : timeId,
						industryId : industryId,
						preachTypeId : preachTypeId,
						isFollow : isFollow
					}
				});
				that.pagination.reload(opts);
			});

			$(el.J_preach_input).on('keypress',function(ev){
				if(ev.keyCode == 13){
					searchWord = $(el.J_preach_input).val();
					S.mix(opts, {
						extraParam: {
							searchWord: searchWord,
							timeId : timeId,
							industryId : industryId,
							preachTypeId : preachTypeId,
							isFollow : isFollow
						}
					});
					that.pagination.reload(opts);
				}
			});

			$(el.J_preach_input).on('change',function(ev){
				searchWord = $(el.J_preach_input).val();
			})

			$(el.J_time).on('change',function(ev){
				timeId = $(ev.currentTarget).children('option:selected').attr('value');

				S.mix(opts,{
								extraParam:{
												searchWord: searchWord,
												timeId : timeId,
												industryId : industryId,
												preachTypeId : preachTypeId,
												isFollow : isFollow
											}
				});

				that.pagination.reload(opts);
			});

			$(el.J_industry).on('change',function(ev){
				
				if($(ev.currentTarget).children('option:selected').attr('value') == myvar.other){
					var name;
					var currentTarget = $(ev.currentTarget); 
					myvar.Id = myvar.other;

					$(el.J_selected_what).text(myvar.blank);
					$(el.J_pop_layer).show();
					$(el.J_show_layer).show();
					$(el.J_branch).hide();
					$(el.J_select_what).text(myvar.chinIndustry);
					that._createIO('total');
					
					$(el.J_total).delegate('click','li',function(ev){
						name = $(ev.currentTarget).text();
						industryId = $(ev.currentTarget).attr('id');
						$(el.J_selected_what).text(name);
					});
					
					$(el.J_okay).on('click',function(ev){
						if(name != undefined){
								that._appendOption($(el.J_industry));
								currentTarget.children('option').item(0).attr('selected','selected')
								currentTarget.children('option').item(0).attr('value',industryId)
								currentTarget.children('option').item(0).text(name);
								S.mix(opts, {
												extraParam: {
																searchWord: searchWord,
																timeId : timeId,
																industryId : industryId,
																preachTypeId : preachTypeId,
																isFollow : isFollow
															}
								});
								that.pagination.reload(opts);
							
						}; 
						$(el.J_pop_layer).hide();
						$(el.J_show_layer).hide();
						$(el.J_okay).detach('click');
						$(el.J_total).undelegate('click','li'); 

						S.mix(opts,{
								extraParam:{	
												searchWord: searchWord,
												timeId : timeId,
												industryId : industryId,
												preachTypeId : preachTypeId
											}
						});

						that.pagination.reload(opts);
					});

					$(el.J_cancel).on('click',function(ev){
						$(el.J_pop_layer).hide();
						$(el.J_show_layer).hide();
						$(el.J_total).undelegate('click','li');
						$(el.J_industry).children('option').item(0).attr('selected','selected');
					});
				}
				else{
					industryId = $(ev.currentTarget).children('option:selected').attr('value');
					S.mix(opts,{
								extraParam:{
												searchWord: searchWord,
												timeId : timeId,
												industryId : industryId,
												preachTypeId : preachTypeId,
												isFollow : isFollow
											}
					});

					that.pagination.reload(opts);
				}
			});

			$(el.J_preach_type).on('change',function(ev){
				preachTypeId = $(ev.currentTarget).children('option:selected').attr('value');

				S.mix(opts,{
								extraParam:{
												searchWord: searchWord,
												timeId : timeId,
												industryId : industryId,
												preachTypeId : preachTypeId,
												isFollow : isFollow	
											}
				});

				that.pagination.reload(opts);
			});

			$('input',el.J_follow_company).on('change',function(ev){
				if($(el.J_follow).test(':checked')){
					isFollow = myvar.yes;
					S.mix(opts,{
								extraParam:{
												searchWord: searchWord,
												timeId : timeId,
												industryId : industryId,
												preachTypeId : preachTypeId,
												isFollow : isFollow	
											}
					});
					that.pagination.reload(opts);
				}

				else{
					isFollow = myvar.no;
					S.mix(opts,{
								extraParam:{
												searchWord: searchWord,
												timeId : timeId,
												industryId : industryId,
												preachTypeId : preachTypeId,
												isFollow : isFollow	
											}
					});

					that.pagination.reload(opts);
				}
			});
			
			$(el.J_tpl_template).delegate('click','a',function(ev){
				var 
					isEnroll , parameter,
					currentClick = $(ev.currentTarget),
				    id = $(ev.currentTarget).parent().parent().attr('id');

				if(currentClick.hasClass(el.J_want_enroll)){
					isEnroll = myvar.yes;
					parameter = 'id='+id+'&isEnroll='+isEnroll;
					preachIO.enroll(parameter,function(code,data,msg){
						if(code == 0){
							currentClick.hide();
							currentClick.next().show();
						}
						if(code == 1){
							$(el.J_pop).show();
							$(el.J_login).show();
							$(el.J_register).hide();
						}
					});
				}

				if(currentClick.hasClass(el.J_cancel_enroll)){
					isEnroll = myvar.no;
					parameter = 'id='+id+'&isEnroll='+isEnroll;
					preachIO.enroll(parameter,function(code,data,msg){
						if(code == 0){
							currentClick.hide();
							currentClick.prev().show();
						}
					});
				}
			});

		},
		_createLi:function(mark,data){
			if(mark == 'total'){
				$(el.J_total).empty();
				$(el.J_branch).empty();
				S.each(data,function(item){
					var liHtml = '<li id="'+item.id+'">'+item.name+'</li>';
					$(el.J_total).append(liHtml);
				});
			}
			
			if(mark == 'branch'){
				$(el.J_branch).empty();
				S.each(data,function(item){
					var liHtml = '<li id="'+item.id+'">'+item.name+'</li>';
					$(el.J_branch).append(liHtml);
				});
			}

		},
		// limark区分行业与地点 ，tbmark区分total与branch
		_createIO:function(tbmark){
			var that = this;
			var parameter = 'Id='+myvar.Id;
			
			preachIO.createIndustryIO(parameter,function(rs,data,msg){
				that._createLi(tbmark,data);
			});
		},
		//ie8不支持option的display:none属性,用js插入节点，而不是将原先隐藏的节点显示
		_appendOption:function(node){
			var 
				optionHtml = '<option value="0">不限</option>';
			if(node.children('option').length == 2){
				node.append(optionHtml)
			}
		}
	});
	return preachShow;
},{
	requires:['sizzle','event' , 'mod/pagination','io/preach/preach']
})

/*---------------------------------------------------------------------------*/

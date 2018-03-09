/*-----------------------------------------------------------------------------
* @Description: education页面相关js (education.js)
* @Version: 	V1.0.0
* @author: 		zhaokaikang(597262617@qq.com)
* @date			2015.06.27
* ==NOTES:=============================================
* v1.0.0(2015.06.27):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/recruitment/education' , function(S, jobShow){
	PW.namespace('page.recruitment.education');
	PW.page.recruitment.education = function(param){
		new jobShow(param);
	}
},{
	requires:['recruitment/jobShow']
});
/*---------------------------------------------------------------------------*/
KISSY.add('recruitment/jobShow',function(S){
	var $ = S.all, 
		on = S.Event.on,
		delegate = S.Event.delegate,
		DOM = S.DOM,
		Juicer = PW.mod.Juicer,
		recruitmentIO = PW.io.recruitment.education,
		Pagination = PW.mod.Pagination,
		urls = PW.Env.url.recruitment.education,
		myvar = {
				 Id : 0,
			     isCollect:0,
			     yes:1,
			     no:0,
			     positionTypeBool:true,
			     majorBool:true,
			     propertyBool:true,
			     locationBool:true,
			     PTmore:'PTmore',
			     Pmore:'Pmore',
			     Mmore:'Mmore',
			     Lmore:'Lmore',
			     chinPositionType:'职位类别',
			     chinProperty:'单位性质',
			     chinMajor:'专业',
			     chinLocation:'工作地点',
			     blank:'  ',
		},
		el = {
				left_nav:'.left-nav' ,
				J_after_click:'.after-click',
				after_click:'after-click',
				J_degree : '.J_degree',
				J_position_type:'.J_position_type',
				J_major : '.J_major',
				J_property:'.J_property',
				J_message_source:'.J_message_source',
				J_location:'.J_location',
				J_select_section:'.select-section',
				J_time: '.recruit-time',
				J_heat:'.green-arrow',
				J_check:'.check',
				J_collection:'.collection',
				J_uncollection:'.uncollection',
				J_template :'#J_template ',
				J_total :'.total',
				J_branch:'.branch',
				J_pop_layer:'.pop-layer',
				J_show_layer:'.show-layer',
				J_okay :'.okay',
				J_cancel:'.cancel',
				J_select_what:'.J_select_what',
				J_selected_what:'.J_selected_what',
				J_search:'.J_search',
				J_input:'.J_input',
				J_pop : '.pop',
				J_login : '.login',
				J_register : '.register',
				J_current_sort_way:'.current-sort-way',
				J_recruit_meeting : '.J_recruit_meeting'
		};
		
	function jobShow(param){
		this.opts = param;
		this.pagination;
		this.init();
	}

	S.augment(jobShow,{
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
		_click: function(){
			var
				that = this, 
				opts = that.opts,
				degreeId = 0,
				searchWord = "",
			    majorId = 0 ,
			    positionTypeId = 0,
			    propertyId = 0,
			    messageSourceId = 0,
			    locationId = 0,
			    timeSort = 1,
			    isJoinBigRecruitment=0,
			    heatSort = 0;

			$(el.J_search).on('click', function(){
				searchWord = $(el.J_input).val();
				S.mix(opts, {
					extraParam: {
						searchWord: searchWord,
						locationId: locationId,
						majorId: majorId,
						propertyId: propertyId,
						positionTypeId: positionTypeId,
						messageSourceId:messageSourceId,
						degreeId:degreeId,
						timeSort: timeSort,
						heatSort: heatSort,
						isJoinBigRecruitment: isJoinBigRecruitment
					}
				});
				that.pagination.reload(opts);
			});

			$(el.J_input).on('keypress',function(ev){
				if(ev.keyCode == 13){
					searchWord = $(el.J_input).val();
					S.mix(opts, {
						extraParam: {
							searchWord: searchWord,
							locationId: locationId,
							majorId: majorId,
							propertyId: propertyId,
							positionTypeId: positionTypeId,
							messageSourceId:messageSourceId,
							degreeId:degreeId,
							timeSort: timeSort,
							heatSort: heatSort,
							isJoinBigRecruitment: isJoinBigRecruitment
						}
					});
					that.pagination.reload(opts);
				}
			});

			$(el.J_input).on('change',function(ev){
				searchWord = $(el.J_input).val();
			});

			$('a', el.left_nav).on('click', function(ev){
				var context = $(ev.target).parent().parent().children();
				// 点击职位类别导航栏下的更多时，弹出的职位类别选择框
				if($(ev.target).parent().attr('positionTypeId') == myvar.PTmore){
					myvar.positionTypeBool = true;
					myvar.locationBool = false;
					myvar.propertyBool = false;
					myvar.majorBool = false;
					var name;
					var positionTypeMore = $(ev.target).parent();
					myvar.Id = myvar.PTmore;

					$(el.J_select_what).text(myvar.chinPositionType);
					$(el.J_selected_what).text(myvar.blank);
					$(el.J_pop_layer).show();
					$(el.J_show_layer).show();
					$(el.J_branch).hide();
					
					that._createIO('positionType','total');

					$(el.J_total).delegate('click','li',function(ev){
						if(myvar.positionTypeBool){
							name = $(ev.currentTarget).text();
							positionTypeId = $(ev.currentTarget).attr('id');
							$(el.J_selected_what).text(name);
						}
					});
					
					
					$(el.J_okay).on('click',function(ev){
						if(name != undefined && myvar.positionTypeBool){
								$(el.J_after_click, context).removeClass(el.after_click);
								positionTypeMore.prev().children().addClass(el.after_click).text(name);
								positionTypeMore.prev().attr({"positionTypeId" : positionTypeId});
			
							    S.mix(opts, {
												extraParam: {
																searchWord:searchWord,
																locationId: locationId,
																majorId: majorId,
																propertyId: propertyId,
																positionTypeId: positionTypeId,
																messageSourceId:messageSourceId,
																degreeId:degreeId,
																timeSort: timeSort,
																heatSort: heatSort,
																isJoinBigRecruitment: isJoinBigRecruitment
															}
									});
								that.pagination.reload(opts);
						};
						$(el.J_pop_layer).hide();
						$(el.J_show_layer).hide();
						$(el.J_okay).detach('click');
						$(el.J_total).undelegate('click','li'); 
					});

					$(el.J_cancel).on('click',function(ev){
						$(el.J_pop_layer).hide();
						$(el.J_show_layer).hide();
						$(el.J_total).undelegate('click','li'); 
					});
				}
				// 点击单位性质导航栏下的更多时，弹出的单位性质选择框
				else if($(ev.target).parent().attr('propertyId') == myvar.Pmore){
					myvar.positionTypeBool = false;
					myvar.locationBool = false;
					myvar.propertyBool = true;
					myvar.majorBool = false;
					
					var name;
					var propertyMore = $(ev.target).parent();
					myvar.Id = myvar.Pmore;

					$(el.J_select_what).text(myvar.chinProperty);
					$(el.J_selected_what).text(myvar.blank);
					$(el.J_pop_layer).show();
					$(el.J_show_layer).show();
					$(el.J_branch).hide();
					
					that._createIO('property','total');
					
					$(el.J_total).delegate('click','li',function(ev){
						if(myvar.propertyBool){
							name = $(ev.currentTarget).text();
							propertyId = $(ev.currentTarget).attr('id');
							$(el.J_selected_what).text(name);
						}
						
					});
					
					$(el.J_okay).on('click',function(ev){
						if(name != undefined && myvar.propertyBool){
								$(el.J_after_click, context).removeClass(el.after_click);
								propertyMore.prev().children().addClass(el.after_click).text(name);
								propertyMore.prev().attr({"propertyId" : propertyId});
			
							    S.mix(opts, {
												extraParam: {
																searchWord: searchWord,
																locationId: locationId,
																majorId: majorId,
																propertyId: propertyId,
																positionTypeId: positionTypeId,
																messageSourceId:messageSourceId,
																degreeId:degreeId,
																timeSort: timeSort,
																heatSort: heatSort,
																isJoinBigRecruitment: isJoinBigRecruitment
															}
									});
								that.pagination.reload(opts);
						};
						$(el.J_pop_layer).hide();
						$(el.J_show_layer).hide();
						$(el.J_okay).detach('click');
						$(el.J_total).undelegate('click','li'); 
					});

					$(el.J_cancel).on('click',function(ev){
						$(el.J_pop_layer).hide();
						$(el.J_show_layer).hide();
						$(el.J_total).undelegate('click','li');
					});
				}
				// 点击专业导航栏下的更多时，弹出的专业选择框
				else if($(ev.target).parent().attr('majorId') == myvar.Mmore){
					myvar.positionTypeBool = false;
					myvar.locationBool = false;
					myvar.propertyBool = false;
					myvar.majorBool = true;
					
					var name;
					var majorMore = $(ev.target).parent();
					myvar.Id = myvar.Mmore;

					$(el.J_select_what).text(myvar.chinMajor);
					$(el.J_selected_what).text(myvar.blank);
					$(el.J_pop_layer).show();
					$(el.J_show_layer).show();
					$(el.J_branch).hide();
					
					that._createIO('major','total');
					
					$(el.J_total).delegate('click','li',function(ev){
						if(myvar.majorBool){
							name = $(ev.currentTarget).text();
							majorId = $(ev.currentTarget).attr('id');
							$(el.J_selected_what).text(name);
						}
					});
					
					$(el.J_okay).on('click',function(ev){
						if(name != undefined && myvar.majorBool){
								$(el.J_after_click, context).removeClass(el.after_click);
								majorMore.prev().children().addClass(el.after_click).text(name);
								majorMore.prev().attr({"majorId" : majorId});
			
							    S.mix(opts, {
												extraParam: {
																searchWord: searchWord,
																locationId: locationId,
																majorId: majorId,
																propertyId: propertyId,
																positionTypeId: positionTypeId,
																messageSourceId:messageSourceId,
																degreeId:degreeId,
																timeSort: timeSort,
																heatSort: heatSort,
																isJoinBigRecruitment: isJoinBigRecruitment
															}
									});
								that.pagination.reload(opts);
						};
						$(el.J_pop_layer).hide();
						$(el.J_show_layer).hide();
						$(el.J_okay).detach('click');
						$(el.J_total).undelegate('click','li'); 		
					});

					$(el.J_cancel).on('click',function(ev){
						$(el.J_pop_layer).hide();
						$(el.J_show_layer).hide();
						$(el.J_total).undelegate('click','li');
					});
				}
				else{
					var context = $(ev.target).parent().parent().children();
					$(el.J_after_click, context).removeClass(el.after_click);
					$(this).addClass(el.after_click);
						
					if($(this).parent().hasAttr('positionTypeId'))
						positionTypeId = $(this).parent().attr('positionTypeId');
					
					if($(this).parent().hasAttr('propertyId'))
						propertyId = $(this).parent().attr('propertyId');

					if($(this).parent().hasAttr('majorId'))
						majorId = $(this).parent().attr('majorId');
					S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												majorId: majorId,
												propertyId: propertyId,
												positionTypeId: positionTypeId,
												messageSourceId:messageSourceId,
												degreeId:degreeId,
												timeSort: timeSort,
												heatSort: heatSort,
												isJoinBigRecruitment: isJoinBigRecruitment
											}
					});
					that.pagination.reload(opts);
				};
			});

			$('select' , el.J_select_section).on('change',function(ev){
				if($(ev.currentTarget).hasClass(el.J_degree)){
					degreeId = $(ev.currentTarget).children('option:selected').val();
					S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												majorId: majorId,
												propertyId: propertyId,
												positionTypeId: positionTypeId,
												messageSourceId:messageSourceId,
												degreeId:degreeId,
												timeSort: timeSort,
												heatSort: heatSort,
												isJoinBigRecruitment: isJoinBigRecruitment
											}
				});
				that.pagination.reload(opts);
				}
				if($(ev.currentTarget).hasClass(el.J_message_source)){
					messageSourceId = $(ev.currentTarget).children('option:selected').val();
					S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												majorId: majorId,
												propertyId: propertyId,
												positionTypeId: positionTypeId,
												messageSourceId:messageSourceId,
												degreeId:degreeId,
												timeSort: timeSort,
												heatSort: heatSort,
												isJoinBigRecruitment: isJoinBigRecruitment	
											}
				});
				that.pagination.reload(opts);
				}
				if($(ev.currentTarget).hasClass(el.J_location)){
					if($(ev.currentTarget).children('option:selected').val() == myvar.Lmore){
						myvar.positionTypeBool = false;
						myvar.locationBool = true;
						myvar.propertyBool = false;
						myvar.majorBool = false;
						var name;
						var location = $(ev.currentTarget).children('option:selected').prev(); 
						myvar.Id = myvar.Lmore;

						$(el.J_selected_what).text(myvar.blank);
						$(el.J_pop_layer).show();
						$(el.J_show_layer).show();
						$(el.J_branch).show();
						$(el.J_select_what).text(myvar.chinLocation);
						that._createIO('location','total');
						
						$(el.J_total).delegate('click','li',function(ev){
							myvar.Id = $(ev.currentTarget).attr('id');
							that._createIO('location','branch');
						});

						$(el.J_branch).delegate('click','li',function(ev){
							if (myvar.locationBool) {
								name = $(ev.currentTarget).text();
								locationId = $(ev.currentTarget).attr('id');
								$(el.J_selected_what).text(name);
							};
						});
						
						$(el.J_okay).on('click',function(ev){
							if(name != undefined && myvar.locationBool){
								that._appendOption($(el.J_location));
								location.text(name);
								location.val(locationId);
							}; 
							S.mix(opts, {
									extraParam: {
													searchWord: searchWord,
													locationId: locationId,
													majorId: majorId,
													propertyId: propertyId,
													positionTypeId: positionTypeId,
													messageSourceId:messageSourceId,
													degreeId:degreeId,
													timeSort: timeSort,
													heatSort: heatSort,
													isJoinBigRecruitment: isJoinBigRecruitment	
												}
							});
							that.pagination.reload(opts);
							$(el.J_pop_layer).hide();
							$(el.J_show_layer).hide();
							$(el.J_okay).detach('click');
							$(el.J_total).undelegate('click','li');
						})

						$(el.J_cancel).on('click',function(ev){
							$(el.J_pop_layer).hide();
							$(el.J_show_layer).hide();
							$(el.J_total).undelegate('click','li');
						})

						location.attr('selected','selected');
					}
					else{
						locationId = $(ev.currentTarget).children('option:selected').attr('value');
						S.mix(opts, {
									extraParam: {
													searchWord: searchWord,
													locationId: locationId,
													majorId: majorId,
													propertyId: propertyId,
													positionTypeId: positionTypeId,
													messageSourceId:messageSourceId,
													degreeId:degreeId,
													timeSort: timeSort,
													heatSort: heatSort,
													isJoinBigRecruitment: isJoinBigRecruitment	
												}
						});
						that.pagination.reload(opts);
					}
				}
			});

			$('a', el.J_select_section).on('click',function(ev){
				if($(this).hasClass(el.J_time)){
					timeSort = myvar.yes;
					heatSort = myvar.no;
					$(el.J_time).addClass(el.J_current_sort_way);
					$(el.J_heat).removeClass(el.J_current_sort_way);
					S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												majorId: majorId,
												propertyId: propertyId,
												positionTypeId: positionTypeId,
												messageSourceId:messageSourceId,
												degreeId:degreeId,
												timeSort: timeSort,
												heatSort: heatSort,
												isJoinBigRecruitment: isJoinBigRecruitment	
											}
					});
					that.pagination.reload(opts);
				};
				if($(this).hasClass(el.J_heat)){
					heatSort = myvar.yes;
					timeSort = myvar.no;
					$(el.J_time).removeClass(el.J_current_sort_way);
					$(el.J_heat).addClass(el.J_current_sort_way);
					S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												majorId: majorId,
												propertyId: propertyId,
												positionTypeId: positionTypeId,
												messageSourceId:messageSourceId,
												degreeId:degreeId,
												timeSort: timeSort,
												heatSort: heatSort,
												isJoinBigRecruitment: isJoinBigRecruitment	
											}
					});
					that.pagination.reload(opts);
				};
			});

			$(el.J_recruit_meeting).on('change',function(ev){
				if($(el.J_recruit_meeting).test(':checked')){
					isJoinBigRecruitment = myvar.yes;
				}
				else {
					isJoinBigRecruitment = myvar.no;
				}
				S.mix(opts, {
							extraParam: {
											searchWord: searchWord,
											locationId: locationId,
											majorId: majorId,
											propertyId: propertyId,
											positionTypeId: positionTypeId,
											messageSourceId:messageSourceId,
											degreeId:degreeId,
											timeSort: timeSort,
											heatSort: heatSort,
											isJoinBigRecruitment: isJoinBigRecruitment	
										}
				});
				that.pagination.reload(opts);
			})

			$(el.J_template).delegate('click','a',function(ev){

				if($(ev.currentTarget).hasClass(el.J_collection)){
					myvar.isCollect = 1;

					var collection = $(ev.currentTarget);
					var uncollection = $(ev.currentTarget).prev();
					var jobId = collection.parent().attr('id');
					var urlParam = 'jobId='+jobId+'&isCollect='+myvar.isCollect;
					recruitmentIO.isCollectIO(urlParam,function(code,data,msg){
						if(code == 0){
							collection.hide();
							uncollection.show();
						}
						if(code == 1){
							$(el.J_pop).show();
							$(el.J_login).show();
							$(el.J_register).hide();
						}
					});
					
				}
				if($(ev.currentTarget).hasClass(el.J_uncollection)){
					myvar.isCollect = 0;

					var uncollection = $(ev.currentTarget);
					var collection = $(ev.currentTarget).next();
					var jobId = collection.parent().attr('id');
					var urlParam = 'jobId='+jobId+'&isCollect='+myvar.isCollect;
					recruitmentIO.isCollectIO(urlParam,function(code,data,msg){
						if(code == 0){
							uncollection.hide();
							collection.show();
						}
					})
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
		//dpmlmark区分学历，单位与专业，地点，tbmark区分total与branch
		_createIO:function(dpmlmark,tbmark){
			var that = this;
			var parameter = 'Id='+myvar.Id;
			if(dpmlmark == 'positionType'){
				recruitmentIO.createPositionTypeIO(parameter,function(rs,data,msg){
					that._createLi(tbmark,data);
				});
			};
			if(dpmlmark == 'major'){
				recruitmentIO.createMajorIO(parameter,function(rs,data,msg){
					that._createLi(tbmark,data);
				});
			};
			if(dpmlmark == 'property'){
				recruitmentIO.createPropertyIO(parameter,function(rs,data,msg){
					that._createLi(tbmark,data);
				});
			};
			if(dpmlmark == 'location'){
				recruitmentIO.createLocationIO(parameter,function(rs,data,msg){
					that._createLi(tbmark,data);
				});
			};
		},

		//ie8不支持option的display:none属性,用js插入节点，而不是将原先隐藏的节点显示
		_appendOption:function(node){
			var 
				optionHtml = '<option value="0">不限</option>';
			if(node.children('option').length == 2){
				node.append(optionHtml);
				node.children('option').item(0).attr('selected','selected');
			}
		}

		
	});
	return jobShow;
},{
	requires:['sizzle' , 'mod/pagination' , 'io/recruitment/education']
});
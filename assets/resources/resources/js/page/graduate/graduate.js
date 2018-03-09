/*-----------------------------------------------------------------------------
* @DescriSion: education页面相关js (education.js)
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.06.27
* ==NOTES:=============================================
* v1.0.0(2015.06.27):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/graduate/graduate' , function(S, graduateShow){
	PW.namespace('page.graduate.graduate');
	PW.page.graduate.graduate = function(param){
		new graduateShow(param);
	}
},{
	requires:['graduate/graduateShow']
});
/*---------------------------------------------------------------------------*/
KISSY.add('graduate/graduateShow',function(S){
	var $ = S.all, 
		on = S.Event.on,
		delegate = S.Event.delegate,
		Juicer = PW.mod.Juicer,
		graduateIO = PW.io.graduate.graduate,
		Pagination = PW.mod.Pagination,
		urls = PW.Env.url.recruitment.education,
		myvar = {
				 Id : 0,
			     yes:1,
			     no:0,
			     schoolBool:true,
			     majorBool:true,
			     industryBool:true,
			     locationBool:true,
			     Smore:'Smore',
			     Imore:'Imore',
			     Mmore:'Mmore',
			     Lmore:'Lmore',
			     chinSchool:'学校',
			     chinIndustry:'所属行业',
			     chinMajor:'所学专业',
			     blank:'  ',
		},
		el = {
				left_nav:'.left-nav' ,
				J_after_click:'.after-click',
				after_click:'after-click',
				J_degree : '.J_degree',
				J_school:'.J_school',
				J_major : '.J_major',
				J_industry:'.J_industry',
				J_year:'.J_year',
				J_location:'.J_location',
				J_select_section:'.select-section',
				J_time: '.graduate-time',
				J_heat:'.yellow-arrow',
				J_check:'.check',
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
				J_matching_number:'.matching-number',
				J_search_number:'#search-number'
		};
		
	function graduateShow(param){
		this.opts = param;
		this.pagination;
		this.init();
	}

	S.augment(graduateShow,{
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
				schoolName = 0,
				searchWord = "",
			    majorId = 0 ,
			    industryId = 0,
			    degreeId = 0,
			    year = 0,
			    locationId = 0,
			    timeSort = 0,
			    heatSort = 0;

			$(el.J_search).on('click', function(){
				searchWord = $(el.J_input).val();
				S.mix(opts, {
					extraParam: {
						searchWord: searchWord,
						locationId: locationId,
						majorId: majorId,
						year: year,
						schoolName: schoolName,
						industryId: industryId,
						degreeId: degreeId,
						timeSort: timeSort,
						heatSort: heatSort
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
							year: year,
							schoolName: schoolName,
							industryId: industryId,
							degreeId: degreeId,
							timeSort: timeSort,
							heatSort: heatSort
						}
					});
					that.pagination.reload(opts);
				}
			});

			$(el.J_input).on('change',function(ev){
				searchWord = $(el.J_input).val();
			})
			
			$('a', el.left_nav).on('click', function(ev){
				var context = $(ev.target).parent().parent().children();
				// 点击学校导航栏下的更多时，弹出的选择框
				if($(ev.target).parent().attr('schoolId') == myvar.Smore){
					myvar.schoolBool = true;
					myvar.locationBool = false;
					myvar.industryBool = false;
					myvar.majorBool = false;
					var name;
					var schoolMore = $(ev.target).parent();
					myvar.Id = myvar.Smore;

					$(el.J_select_what).text(myvar.chinSchool);
					$(el.J_selected_what).text(myvar.blank);
					$(el.J_pop_layer).show();
					$(el.J_show_layer).show();
					$(el.J_branch).hide();
					
					that._createIO('school','total');

					$(el.J_total).delegate('click','li',function(ev){
						if(myvar.schoolBool){
							name = $(ev.currentTarget).text();
							schoolName = name;
							$(el.J_selected_what).text(name);
						}
					});
					
					
					$(el.J_okay).on('click',function(ev){
						if(name != undefined && myvar.schoolBool){
								$(el.J_after_click, context).removeClass(el.after_click);
								schoolMore.prev().children().addClass(el.after_click).text(name);
			
							    S.mix(opts, {
												extraParam: {
																searchWord: searchWord,
																locationId: locationId,
																majorId: majorId,
																year: year,
																schoolName: schoolName,
																industryId: industryId,
																degreeId: degreeId,
																timeSort: timeSort,
																heatSort: heatSort
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
				// 点击行业导航栏下的更多时，弹出的行业选择框
				// else if($(ev.target).parent().attr('industryId') == myvar.Imore){
				// 	myvar.schoolBool = false;
				// 	myvar.locationBool = false;
				// 	myvar.industryBool = true;
				// 	myvar.majorBool = false;
					
				// 	var name;
				// 	var industryMore = $(ev.target).parent();
				// 	myvar.Id = myvar.Imore;

				// 	$(el.J_select_what).text(myvar.chinIndustry);
				// 	$(el.J_selected_what).text(myvar.blank);
				// 	$(el.J_pop_layer).show();
				// 	$(el.J_show_layer).show();
				// 	$(el.J_branch).show();

				// 	that._createIO('industry','total');
					
				// 	$(el.J_total).delegate('click','li',function(ev){
				// 		myvar.Id = $(ev.currentTarget).attr('id');
				// 		that._createIO('industry','branch');
						
				// 	});	

				// 	$(el.J_branch).delegate('click','li',function(ev){
				// 			if(myvar.industryBool) {
				// 				name = $(ev.currentTarget).text();
				// 				industryId = $(ev.currentTarget).attr('id');
				// 				$(el.J_selected_what).text(name);
				// 			};
				// 		});
					
				// 	$(el.J_okay).on('click',function(ev){

				// 		if(name != undefined && myvar.industryBool){
				// 				$(el.J_after_click, context).removeClass(el.after_click);
				// 				industryMore.prev().children().addClass(el.after_click).text(name);
				// 				industryMore.prev().attr({"industryId" : industryId});
			
				// 			    S.mix(opts, {
				// 								extraParam: {
				// 												locationId: locationId,
				// 												majorId: majorId,
				// 												year: year,
				// 												schoolName: schoolName,
				// 												industryId: industryId,
				// 												degreeId: degreeId,
				// 												timeSort: timeSort,
				// 												heatSort: heatSort
				// 											}
				// 					});
				// 				that.pagination.reload(opts);
				// 		};
				// 		$(el.J_pop_layer).hide();
				// 		$(el.J_show_layer).hide();
				// 		$(el.J_okay).detach('click');
				// 		$(el.J_total).undelegate('click','li'); 
				// 	});

				// 	$(el.J_cancel).on('click',function(ev){
				// 		$(el.J_pop_layer).hide();
				// 		$(el.J_show_layer).hide();
				// 		$(el.J_total).undelegate('click','li');
				// 	});

					
				// }
				// 点击专业导航栏下的更多时，弹出的专业选择框
				else if($(ev.target).parent().attr('majorId') == myvar.Mmore){
					myvar.schoolBool = false;
					myvar.locationBool = false;
					myvar.industryBool = false;
					myvar.majorBool = true;
					
					var name;
					var majorMore = $(ev.target).parent();
					myvar.Id = myvar.Mmore;

					$(el.J_select_what).text(myvar.chinMajor);
					$(el.J_selected_what).text(myvar.blank);
					$(el.J_pop_layer).show();
					$(el.J_show_layer).show();
					$(el.J_branch).show();
					
					that._createIO('major','total');
					
					$(el.J_total).delegate('click','li',function(ev){
						myvar.Id = $(ev.currentTarget).attr('id');
						that._createIO('major','branch');
					});

					$(el.J_branch).delegate('click','li',function(ev){
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
			
							    S.mix(opts, {
												extraParam: {
																searchWord: searchWord,
																locationId: locationId,
																majorId: majorId,
																year: year,
																schoolName: schoolName,
																industryId: industryId,
																degreeId: degreeId,
																timeSort: timeSort,
																heatSort: heatSort
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
						
					if($(this).parent().hasAttr('schoolName'))
						schoolName = $(this).parent().attr('schoolName');
					
					if($(this).parent().hasAttr('industryId'))
						industryId = $(this).parent().attr('industryId');

					if($(this).parent().hasAttr('majorId'))
						majorId = $(this).parent().attr('majorId');
					S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												majorId: majorId,
												year: year,
												schoolName: schoolName,
												industryId: industryId,
												degreeId: degreeId,
												timeSort: timeSort,
												heatSort: heatSort
											}
					});
					that.pagination.reload(opts);
					
				};
			});

			$('select',el.J_select_section).on('change',function(ev){
				if($(ev.currentTarget).hasClass(el.J_degree)){
					degreeId = $(ev.currentTarget).children('option:selected').val();
					S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												majorId: majorId,
												year: year,
												schoolName: schoolName,
												industryId: industryId,
												degreeId: degreeId,
												timeSort: timeSort,
												heatSort: heatSort
											}
					});
					that.pagination.reload(opts);

					$(el.J_matching_number).text($(el.J_search_number).text());
				}
				if($(ev.currentTarget).hasClass(el.J_year)){
					year = $(ev.currentTarget).children('option:selected').text();
					S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												majorId: majorId,
												year: year,
												schoolName: schoolName,
												industryId: industryId,
												degreeId: degreeId,
												timeSort: timeSort,
												heatSort: heatSort
											}
					});
					that.pagination.reload(opts);
					$(el.J_matching_number).text($(el.J_search_number).text());
				}
				if($(ev.currentTarget).hasClass(el.J_location)){
					if($(ev.currentTarget).children('option:selected').val() == myvar.Lmore){
						myvar.schoolBool = false;
						myvar.locationBool = true;
						myvar.industryBool = false;
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
													year: year,
													schoolName: schoolName,
													industryId: industryId,
													degreeId: degreeId,
													timeSort: timeSort,
													heatSort: heatSort
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
						
						$(el.J_matching_number).text($(el.J_search_number).text());
						location.attr('selected','selected');
					}
					else{
						locationId = $(ev.currentTarget).children('option:selected').val();
						S.mix(opts, {
									extraParam: {
													searchWord: searchWord,
													locationId: locationId,
													majorId: majorId,
													year: year,
													schoolName: schoolName,
													industryId: industryId,
													degreeId: degreeId,
													timeSort: timeSort,
													heatSort: heatSort
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
					S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												majorId: majorId,
												year: year,
												schoolName: schoolName,
												industryId: industryId,
												degreeId: degreeId,
												timeSort: timeSort,
												heatSort: heatSort
											}
					});
					that.pagination.reload(opts);

					$(el.J_matching_number).text($(el.J_search_number).text());
				};
				if($(this).hasClass(el.J_heat)){
					heatSort = myvar.yes;
					timeSort = myvar.no;
					S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												majorId: majorId,
												year: year,
												schoolName: schoolName,
												industryId: industryId,
												degreeId: degreeId,
												timeSort: timeSort,
												heatSort: heatSort
											}
					});
					that.pagination.reload(opts);

					$(el.J_matching_number).text($(el.J_search_number).text());
				};
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
			if(dpmlmark == 'school'){
				graduateIO.createSchoolIO(parameter,function(rs,data,msg){
					that._createLi(tbmark,data);
				});
			};
			if(dpmlmark == 'major'){
				graduateIO.createMajorIO(parameter,function(rs,data,msg){
					that._createLi(tbmark,data);
				});
			};
			if(dpmlmark == 'industry'){
				graduateIO.createIndustryIO(parameter,function(rs,data,msg){
					that._createLi(tbmark,data);
				});
			};
			if(dpmlmark == 'location'){
				graduateIO.createLocationIO(parameter,function(rs,data,msg){
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
	return graduateShow;
},{
	requires:['sizzle' , 'mod/pagination' , 'io/graduate/graduate']
});
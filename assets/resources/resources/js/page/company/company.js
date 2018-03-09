/*-----------------------------------------------------------------------------
* @Description:     company页面相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.6.16
* ==NOTES:=============================================
* v1.0.0(2015.6.16):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/company' , function(S, companyShow){
	PW.namespace('page.company.company');
	PW.page.company.company = function(param){
		new companyShow(param);
	}
},{
	requires:['company/companyShow']
});
/*---------------------------------------------------------------------------*/
KISSY.add('company/companyShow',function(S){
	var $ = S.all, 
		on = S.Event.on,
		delegate = S.Event.delegate,
		companyIO = PW.io.company.company,
		Pagination = PW.mod.Pagination,
		urls = PW.Env.url.company.company,
		myvar = {
				 Id : 0,
				 isFollow:0,
			     yes:1,
			     no:0,
			     Lmore:'Lmore',
			     Imore:'Imore',
			     industryBool:true,
			     locationBool:true,
			     chinLocation:'城市',
			     chinIndustry:'行业',
			     blank:'  ',
		},
		el = {
				J_login : '.login',
				J_register : '.register',
				J_stu_login : '.stu-login',
				J_pop : '.pop',
				left_nav:'.left-nav' ,
				J_after_click:'.after-click',
				after_click:'after-click',
				location : '.J_location',
				industry : '.J_industry',
				J_Select : '.J_select',
				J_Elite_Firm : '.J_Elite_Firm',
				J_Elite_School : '.J_Elite_School',
				J_select_section:'.select-section',
				J_time: '.time',
				J_heat:'.blue-arrow',
				J_check:'.check',
				J_follow:'.follow',
				J_unfollow:'.unfollow',
				J_JobInfo_template :'#JobInfo_template ',
				J_total :'.total',
				J_branch:'.branch',
				J_pop_layer:'.pop-layer',
				J_show_layer:'.show-layer',
				J_okay :'.okay',
				J_cancel:'.cancel',
				J_select_what:'.J_select_what',
				J_selected_what:'.J_selected_what',
				J_search:'.J_search',
				J_company_input:'.company-input',
				J_current_sort_way:'.current-sort-way'
		};
		
	function companyShow(param){
		this.opts = param;
		this.pagination;
		this.init();
	}

	S.augment(companyShow,{
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
				locationId = 0,
				searchWord = "",
			    industryId = 0 ,
			    propertyId = 0 ,
			    isEliteSchool=0,
			    isEliteFirm = 0,
			    timeSort = 1,
			    heatSort = 0;

			$(el.J_search).on('click', function(){
				searchWord = $(el.J_company_input).val();
				S.mix(opts, {
					extraParam: {
						searchWord: searchWord,
						locationId: locationId,
						industryId: industryId,
						propertyId: propertyId,
						isEliteSchool: isEliteSchool,
						isEliteFirm: isEliteFirm,
						timeSort: timeSort,
						heatSort: heatSort
					}
				});
				that.pagination.reload(opts);
			});

			$(el.J_company_input).on('keypress',function(ev){
				if(ev.keyCode == 13){
					searchWord = $(el.J_company_input).val();
					S.mix(opts, {
						extraParam: {
							searchWord: searchWord,
							locationId: locationId,
							industryId: industryId,
							propertyId: propertyId,
							isEliteSchool: isEliteSchool,
							isEliteFirm: isEliteFirm,
							timeSort: timeSort,
							heatSort: heatSort
						}
					});
					that.pagination.reload(opts);
				}
			});

			$(el.J_company_input).on('change',function(ev){
				searchWord = $(el.J_company_input).val();
			});
			
			$('a', el.left_nav).on('click', function(ev){
				var context = $(ev.target).parent().parent().children();
				// 点击地点导航栏下的更多时，弹出的地点选择框
				if($(ev.target).parent().attr('locationId') == myvar.Lmore){
					myvar.locationBool = true;
					myvar.industryBool = false;
					var name;
					var locationMore = $(ev.target).parent(); 
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
						if(myvar.locationBool){
							name = $(ev.currentTarget).text();
							locationId = $(ev.currentTarget).attr('id');
							$(el.J_selected_what).text(name);
						}
					});
					
					$(el.J_okay).on('click',function(ev){
						if(name != undefined & myvar.locationBool){
								$(el.J_after_click, context).removeClass(el.after_click);
								locationMore.prev().children().addClass(el.after_click).text(name);
								locationMore.prev().attr({"locationId" : locationId});
								
								S.mix(opts, {
												extraParam: {
																searchWord: searchWord,
																locationId: locationId,
																industryId: industryId,
																propertyId: propertyId,
																isEliteSchool: isEliteSchool,
																isEliteFirm: isEliteFirm,
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
					})

					$(el.J_cancel).on('click',function(ev){
						$(el.J_pop_layer).hide();
						$(el.J_show_layer).hide();
						$(el.J_total).undelegate('click','li');
					})
				}
				// 点击行业导航栏下的更多时，弹出的行业选择框
				else if($(ev.target).parent().attr('industryId') == myvar.Imore){
					myvar.locationBool = false;
					myvar.industryBool = true;
					var name;
					var industryMore = $(ev.target).parent();
					myvar.Id = myvar.Imore;

					$(el.J_select_what).text(myvar.chinIndustry);
					$(el.J_selected_what).text(myvar.blank);
					$(el.J_pop_layer).show();
					$(el.J_show_layer).show();
					$(el.J_branch).hide();
					that._createIO('industry','total');
					
					$(el.J_total).delegate('click','li',function(ev){
						if(myvar.industryBool){
							name = $(ev.currentTarget).text();
							industryId = $(ev.currentTarget).attr('id');
							$(el.J_selected_what).text(name);
						}
					});
					
					$(el.J_okay).on('click',function(ev){
						if(name != undefined & myvar.industryBool){
								$(el.J_after_click, context).removeClass(el.after_click);
								industryMore.prev().children().addClass(el.after_click).text(name);
								industryMore.prev().attr({"industryId" : industryId});
			
							    S.mix(opts, {
												extraParam: {
																searchWord: searchWord,
																locationId: locationId,
																industryId: industryId,
																propertyId: propertyId,
																isEliteSchool: isEliteSchool,
																isEliteFirm: isEliteFirm,
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
					$(el.J_after_click, context).removeClass(el.after_click);
					$(this).addClass(el.after_click);
						
					if($(el.J_after_click).length == 1) {
						if($(el.J_after_click).item(0).parent().hasAttr('locationId')){
							locationId = $(el.J_after_click).item(0).parent().attr('locationId');
						}
						if($(el.J_after_click).item(0).parent().hasAttr('industryId')){
							industryId = $(el.J_after_click).item(0).parent().attr('industryId');
						}
					};

					if($(el.J_after_click).length == 2) {
						try{
							if($(el.J_after_click).item(0).parent().hasAttr('locationId'))
								locationId = $(el.J_after_click).item(0).parent().attr('locationId');
						}catch(e){
							return;
						}

						try{
							if($(el.J_after_click).item(1).parent().hasAttr('locationId'))
								locationId = $(el.J_after_click).item(1).parent().attr('locationId');
						}catch(e){
							return;
						}

						try{
							if($(el.J_after_click).item(0).parent().hasAttr('industryId'))
								industryId = $(el.J_after_click).item(0).parent().attr('industryId');
						}catch(e){
							return;
						}

						try{
							if($(el.J_after_click).item(1).parent().hasAttr('industryId'))
								industryId = $(el.J_after_click).item(1).parent().attr('industryId');
						}catch(e){
							return;
						}
					}

					S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												industryId: industryId,
												propertyId: propertyId,
												isEliteSchool: isEliteSchool,
												isEliteFirm: isEliteFirm,
												timeSort: timeSort,
												heatSort: heatSort
											}
					});
					that.pagination.reload(opts);
				};
			});

			$(el.J_Select).on('change',function(ev){
				propertyId  = $(ev.currentTarget).children('option:selected').val();

				S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												industryId: industryId,
												propertyId: propertyId,
												isEliteSchool: isEliteSchool,
												isEliteFirm: isEliteFirm,
												timeSort: timeSort,
												heatSort: heatSort
											}
				});
				that.pagination.reload(opts);
			});

			$('input[type="checkbox"]').on('change',function(ev){
				if($(el.J_Elite_Firm).test(':checked'))
					isEliteFirm = myvar.yes;
				else
					isEliteFirm = myvar.no;
				if($(el.J_Elite_School).test(':checked'))
					isEliteSchool = myvar.yes;
				else
					isEliteSchool = myvar.no;

				S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												industryId: industryId,
												propertyId: propertyId,
												isEliteSchool: isEliteSchool,
												isEliteFirm: isEliteFirm,
												timeSort: timeSort,
												heatSort: heatSort
											}
				});
				that.pagination.reload(opts);
			});

			$('a',el.J_select_section).on('click',function(ev){
				if($(this).hasClass(el.J_time)){
					timeSort = myvar.yes;
					heatSort = myvar.no;
					$(el.J_time).addClass(el.J_current_sort_way);
					$(el.J_heat).removeClass(el.J_current_sort_way);
					S.mix(opts, {
								extraParam: {
												searchWord: searchWord,
												locationId: locationId,
												industryId: industryId,
												propertyId: propertyId,
												isEliteSchool: isEliteSchool,
												isEliteFirm: isEliteFirm,
												timeSort: timeSort,
												heatSort: heatSort
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
												industryId: industryId,
												propertyId: propertyId,
												isEliteSchool: isEliteSchool,
												isEliteFirm: isEliteFirm,
												timeSort: timeSort,
												heatSort: heatSort
											}
					});
					that.pagination.reload(opts);
				};
			});

			$('#JobInfo_template').delegate('click','a',function(ev){
				if($(ev.currentTarget).hasClass(el.J_follow)){
					myvar.isFollow = 1;

					var follow = $(ev.currentTarget);
					var unfollow = $(ev.currentTarget).next();
					var companyId = follow.parent().attr('id');
					var urlParam = 'companyId='+companyId+'&isFollow='+myvar.isFollow;

					companyIO.getFollowNumber(urlParam , function(code,data,msg){
						if(code == 0){
							follow.hide();
							unfollow.show();
							follow.next().next().children().text(data.followNumber);
						}
						if(code == 1){
							$(el.J_pop).show();
							$(el.J_login).show();
							$(el.J_register).hide();
						}
					});
					
				}
				if($(ev.currentTarget).hasClass(el.J_unfollow)){
					myvar.isFollow = 0;
					var unfollow = $(ev.currentTarget);
					var follow = $(ev.currentTarget).prev();
					var companyId = unfollow.parent().attr('id');
					var urlParam = 'companyId='+companyId+'&isFollow='+myvar.isFollow;

					companyIO.getFollowNumber(urlParam , function(rs,data,msg){
						unfollow.next().children().text(data.followNumber);
					});

					unfollow.hide();
					follow.show();
				}
			});

			// $(el.J_pop_layer).on('click',function(ev){
			// 	$(el.J_pop_layer).hide();
			// 	$(el.J_show_layer).hide();
			// });

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
		_createIO:function(limark,tbmark){
			var that = this;
			var parameter = 'Id='+myvar.Id;
			if(limark == 'location'){
				companyIO.createLocationIO(parameter,function(rs,data,msg){
					that._createLi(tbmark,data);
				});
			};
			if(limark == 'industry'){
				companyIO.createIndustryIO(parameter,function(rs,data,msg){
					that._createLi(tbmark,data);
				});
			};
		}

		
	});
	return companyShow;
},{
	requires:['sizzle' , 'mod/pagination' , 'io/company/company']
});

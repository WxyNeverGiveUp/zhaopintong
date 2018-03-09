/*-----------------------------------------------------------------------------
* @DescriSion: index页面相关js
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.07.10
* ==NOTES:=============================================
* v1.0.0(2015.07.10):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/index/index',function(S,indexShow){
	PW.namespace('page.index.index');
	PW.page.index.index = function(){
		new indexShow();
	}
},{
	requires:['index/indexShow']
})

/*---------------------------------------------------------------------------*/
KISSY.add('index/indexShow',function(S){
	var 
		$ = S.all ,
		on = S.Event.on ,
		delegate = S.Event.delegate,
		Calendar = S.Calendar,
		Switchable = S.Switchable,
		indexIO = PW.io.index.index,
		el = {
			J_qq : '.qq',
			J_qq_service : '.qq-service',
			J_phone : '.phone',
			J_phone_service : '.phone-service',
			J_app_download : '.app',
			J_app_service : '.app-service',
			J_public_number : '.public-number',
			J_public_num_service : '.public-num-service',
			J_left : '.left',
			J_right : '.right',
			J_carousel : '.carousel',
			J_calendar :'.calendar',
			J_ks_dbd : '.ks-dbd' ,
			J_ks_null : '.ks-null',
			J_preach_day : 'preach-day',
			J_ks_disabled : 'ks-disabled',
			J_ks_next : '.ks-next' ,
			J_ks_prev : '.ks-prev',
			J_ks_title : '.ks-title',
			J_ks_cal_hd : 'ks-cal-hd',
			J_ok : '.ok',
			J_Slide : '#J_Slide',
			J_educational : '.educational',
			J_non_educational : '.non-educational',
			J_teacher_recruitment : '.teacher-recruitment',
			J_current_selected : 'current-selected',
			K_educational : '.J_educational',
			K_non_educational : '.J_non_educational',
			K_teacher_recruitment : '.J_teacher_recruitment',
			J_edu_more : '.edu-more',
			J_none_edu_more	: '.none-edu-more',
			J_teacher_more : '.teacher-more',
			J_right_bar : '.right-bar',
			J_enlarge : '.enlarge',
			J_preach_live_img : '.preach-live-img',
			J_preach_video_img : '.preach-video-img',
			J_preach_live_list : '.preach-live-list',
			J_preach_video_list : '.preach-video-list'	
	};

	function indexShow(){
		this.init();
	}

	S.augment(indexShow , {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var
				that = this,
				year = new Date().getFullYear(),
				month = new Date().getMonth()+1;
			that._carousel();
			that._carlendar();
			that._getPreachDay(year, month);
			that._qqService();
			that._phoneService();
			that._appService();
			that._publicNumService();
			that._currentSelect();

			$(el.J_educational).addClass(el.J_current_selected);

			// delegate(document, 'click', el.J_ks_prev , function(){
			// 	var
			// 		string = $(el.J_ks_title).text();
			// 	S.log(1);
			// 	year = string.substring(0,4);
			// 	if(string.length == 7)
			// 		month = string.substring(5,6);
			// 	if(string.length == 8)
			// 		month = string.substring(5,7);
			// 	that._getPreachDay(year , month);
			// });

			// delegate(document, 'click', el.J_ok , function(){
			// 	var
			// 		string = $(el.J_ks_title).text();

			// 	year = string.substring(0,4);
			// 	if(string.length == 7)
			// 		month = string.substring(5,6);
			// 	if(string.length == 8)
			// 		month = string.substring(5,7);
			// 	that._getPreachDay(year , month);
			// });

			// delegate(document, 'click', el.J_ks_next , function(){
			// 	var
			// 		string = $(el.J_ks_title).text();

			// 	year = string.substring(0,4);
			// 	if(string.length == 7)
			// 		month = string.substring(5,6);
			// 	if(string.length == 8)
			// 		month = string.substring(5,7);
			// 	that._getPreachDay(year , month);
			// });

			// delegate(document , 'click' , 'a' , function(ev){
			// 	if($(ev.currentTarget).parent().hasClass(el.J_ks_dbd)){
			// 		var
			// 			string = $(el.J_ks_title).text();

			// 		year = string.substring(0,4);
			// 		if(string.length == 7)
			// 			month = string.substring(5,6);
			// 		if(string.length == 8)
			// 			month = string.substring(5,7);
			// 		that._getPreachDay(year , month);
			// 	}
			// });

			$(el.J_enlarge).on('mouseenter',function(ev){
				$(el.J_right_bar).show();
				$(el.J_enlarge).hide();
			})

			// $(el.J_right_bar_whole).on('mouseenter',function(ev){
			// 	$(el.J_right_bar).show();
			// });

			$(el.J_right_bar).on('mouseleave',function(ev){
				$(el.J_right_bar).hide();
				$(el.J_enlarge).show();
			});

			$(el.J_right_bar).on('mouseenter',function(ev){
				$(el.J_right_bar).show();
			});

			// $(el.J_right_bar_whole).on('mouseleave',function(ev){
			// 	$(el.J_right_bar).hide();
			// });

			$(el.J_preach_live_img).on('mouseenter',function(ev){
				$(el.J_preach_live_img).hide();
				$(el.J_preach_live_list).show();
			});

			$(el.J_preach_video_img).on('mouseenter',function(ev){
				$(el.J_preach_video_img).hide();
				$(el.J_preach_video_list).show();
			});

			$(el.J_preach_video_list).on('mouseleave',function(ev){
				$(el.J_preach_video_img).show();
				$(el.J_preach_video_list).hide();
			});

			$(el.J_preach_live_list).on('mouseleave',function(ev){
				$(el.J_preach_live_img).show();
				$(el.J_preach_live_list).hide();
			});

			$(el.J_carousel).on('mouseleave',function(ev){
				$(el.J_left).hide();
				$(el.J_right).hide();
			});

			$(el.J_carousel).on('mouseenter',function(ev){
				$(el.J_left).show();
				$(el.J_right).show();
			})
		},

		_carousel:function(){
			var 
				i = 0 ,
				imgs = $('img' , el.J_carousel),
				imgsLength = imgs.length,
				s = new Switchable.Slide('#J_Slide',{		
					effect:'fade',
					easing:'fadeOut',
					duration:'0.5',
					interval:'2'
				});
			$(el.J_left).on('click' , function(ev){
				s.prev();
			});

			$(el.J_right).on('click' , function(ev){
				s.next();
			});
		},

		_carlendar:function(){
			var
				that = this,
				c = new Calendar(el.J_calendar,{
					pages:1,
					popup:false
				});

			// c.on('select' , function(ev){
			// 	var
			// 		year = ev.date.getFullYear(),
			// 		month = ev.date.getMonth(),
			// 		day = ev.date.getDate();
			// 	if(month < 10){
			// 		month = '0'+month;
			// 	}
			// 	if(day < 10){
			// 		day = '0'+day;
			// 	}
			// 	window.location.href = 'careerTalk/careerTalk/calList/date/'+year+'-'+month+'-'+day; 
			// });

			c.on('monthChange',function(ev){
				S.available('#preach-calendar', function(){
					var
						string = $(el.J_ks_title).text();

					year = string.substring(0,4);
					if(string.length == 7)
						month = string.substring(5,6);
					if(string.length == 8)
						month = string.substring(5,7);
					that._getPreachDay(year , month);
				})
			});
		},

		_getPreachDay:function(year , month){
			var
				that = this,
				para = 'year='+year+'&month='+month,
				dayLinks = $('a' , el.J_ks_dbd),
				nullLength = $(el.J_ks_null).length,
				subDayLinks = dayLinks.slice(nullLength,dayLinks.length);

			S.each(subDayLinks,function(i,o){
				$(i).addClass(el.J_ks_disabled);
			});

			indexIO.getPreachDayIO(para , function(rs,data,msg){
				S.each(data,function(item){
					subDayLinks.item(item.day - 1).removeClass(el.J_ks_disabled);
					subDayLinks.item(item.day - 1).attr('href',item.href);
					subDayLinks.item(item.day - 1).addClass(el.J_preach_day);
				})
			});
		},

		_qqService:function(){
			$(el.J_qq).on('mouseenter',function(ev){
				$(el.J_qq_service).show();
			});
			$(el.J_qq).on('mouseleave',function(ev){
				$(el.J_qq_service).hide();
			});

			$(el.J_qq_service).on('mouseenter',function(ev){
				$(el.J_qq_service).show();
			});
			$(el.J_qq_service).on('mouseleave',function(ev){
				$(el.J_qq_service).hide();
			});
		},

		_phoneService:function(){
			$(el.J_phone).on('mouseenter',function(ev){
				$(el.J_phone_service).show();
			});
			$(el.J_phone).on('mouseleave',function(ev){
				$(el.J_phone_service).hide();
			});

			$(el.J_phone_service).on('mouseenter',function(ev){
				$(el.J_phone_service).show();
			});
			$(el.J_phone_service).on('mouseleave',function(ev){
				$(el.J_phone_service).hide();
			});
		},

		_appService:function(){
			$(el.J_app_download).on('mouseenter',function(ev){
				$(el.J_app_service).show();
			});
			$(el.J_app_download).on('mouseleave',function(ev){
				$(el.J_app_service).hide();
			});

			$(el.J_app_service).on('mouseenter',function(ev){
				$(el.J_app_service).show();
			});
			$(el.J_app_service).on('mouseleave',function(ev){
				$(el.J_app_service).hide();
			});
		},

		_publicNumService:function(){
			$(el.J_public_number).on('mouseenter',function(ev){
				$(el.J_public_num_service).show();
			});
			$(el.J_public_number).on('mouseleave',function(ev){
				$(el.J_public_num_service).hide();
			});

			$(el.J_public_num_service).on('mouseenter',function(ev){
				$(el.J_public_num_service).show();
			});
			$(el.J_public_num_service).on('mouseleave',function(ev){
				$(el.J_public_num_service).hide();
			});
		},

		_currentSelect:function(){
			$(el.J_educational).on('click',function(ev){
				$(el.K_educational).show();
				$(el.K_non_educational).hide();
				$(el.K_teacher_recruitment).hide();
				$(el.J_educational).addClass(el.J_current_selected);
				$(el.J_non_educational).removeClass(el.J_current_selected);
				$(el.J_teacher_recruitment).removeClass(el.J_current_selected);
				$(el.J_edu_more).show();
				$(el.J_none_edu_more).hide();
				$(el.J_teacher_more).hide();
			})

			$(el.J_non_educational).on('click',function(ev){
				$(el.K_non_educational).show();
				$(el.K_educational).hide();
				$(el.K_teacher_recruitment).hide();
				$(el.J_non_educational).addClass(el.J_current_selected);
				$(el.J_educational).removeClass(el.J_current_selected);
				$(el.J_teacher_recruitment).removeClass(el.J_current_selected);
				$(el.J_edu_more).hide();
				$(el.J_none_edu_more).show();
				$(el.J_teacher_more).hide();
			})

			$(el.J_teacher_recruitment).on('click',function(ev){
				$(el.K_teacher_recruitment).show();
				$(el.K_educational).hide();
				$(el.K_non_educational).hide();
				$(el.J_teacher_recruitment).addClass(el.J_current_selected);
				$(el.J_educational).removeClass(el.J_current_selected);
				$(el.J_non_educational).removeClass(el.J_current_selected);
				$(el.J_edu_more).hide();
				$(el.J_none_edu_more).hide();
				$(el.J_teacher_more).show();
			})
		}

	});
	return indexShow;

},{
	requires:['core','calendar','io/index/index','switchable']
})
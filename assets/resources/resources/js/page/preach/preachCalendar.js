/*-----------------------------------------------------------------------------
* @Description:     preachCalendar页面相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.01
* ==NOTES:=============================================
* v1.0.0(2015.07.01):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/preach/preachCalendar',function(S,preachCalendarShow){
	PW.namespace('page.preach.preachCalendar');
	PW.page.preach.preachCalendar = function(param){
		new preachCalendarShow(param);
	}
},{
	requires:['preach/preachCalendarShow']
})

/* ---------------------------------------------------------------------------*/
KISSY.add('preach/preachCalendarShow',function(S){
	var 
		$ = S.all,
		on = S.Event.on,
		delegate = S.Event.delegate,
		Calendar = S.Calendar,
		preachCalIO = PW.io.preach.preachCalendar,
		el = {
			J_calendar:'.calendar',
			J_year : '.year',
			J_month : '.month',
			J_day : '.day',
			J_ks_dbd : '.ks-dbd' ,
			J_ks_null : '.ks-null',
			J_preach_day : 'preach-day',
			J_ks_disabled : 'ks-disabled',
			J_ks_next : '.ks-next' ,
			J_ks_prev : '.ks-prev',
			J_ks_title : '.ks-title',
			J_ok : '.ok',
			J_ks_cal_hd : 'ks-cal-hd',
			J_content : '.content ',
			J_tpl_template:'#tpl-template',
			J_ul_wrapper:'.ul-wrapper',
			J_grey_border:'.grey-border'
		},
		myvar = {
			data : 0,
			calId : 0
		}
		Pagination = PW.mod.Pagination;

	function preachCalendarShow(param){
		this.opts = param;
		this.pagination;
		this.init();
	}

	S.augment(preachCalendarShow , {
		init:function(){
			this._pagination();
			this._calendar();
			this._addEventListener();
			// this._getId();
		},
		// _getId:function(){
		// 	myvar.calId = $(el.J_content).children('div').item(0).attr('id');
		// 	myvar.calId = '#'+myvar.calId;
		// 	S.log(myvar.calId);
		// },
		_pagination:function(extraParam){
			var
				that = this,
				opts = that.opts;
			that.pagination = Pagination.client(opts);
			if($(el.J_tpl_template).children('li').length == 0){
				$(el.J_tpl_template).hide();
				$(el.J_ul_wrapper).append('<p class="no-preach-tip">今日无宣讲会</p>');
				$(el.J_grey_border).hide();
			}
			S.available('#preach-detail',function(){
				$(el.J_tpl_template).show();
				$('.no-preach-tip').remove();
				$(el.J_grey_border).show();
			})
		},

		_addEventListener:function(){
			var	
				firstList , secondList,
				that = this,
				firstMonth , secondMonth ,
				firstString = $(el.J_ks_title).item(0).text(),
				firstYear = firstString.substring(0,4),

				secondString = $(el.J_ks_title).item(1).text(),
				secondYear = secondString.substring(0,4),

				firstAllLength = $(el.J_ks_dbd).item(0).children('a').length,
				secondAllLength = $(el.J_ks_dbd).item(1).children('a').length,

				firstNullLength = $(el.J_ks_dbd).item(0).children(el.J_ks_null).length,
				secondNullLength = $(el.J_ks_dbd).item(1).children(el.J_ks_null).length;

			firstList = $(el.J_ks_dbd).item(0).children('a').slice(firstNullLength, firstAllLength);
			secondList = $(el.J_ks_dbd).item(1).children('a').slice(secondNullLength, secondAllLength);
				
				if(firstString.length == 7)
					firstMonth = firstString.substring(5,6);
				if(firstString.length == 8)
					firstMonth = firstString.substring(5,7);

				if(secondString.length == 7)
					secondMonth = secondString.substring(5,6);
				if(secondString.length == 8)
					secondMonth = secondString.substring(5,7);
			that._getPreachDay(firstYear,firstMonth,firstList);
			that._getPreachDay(secondYear,secondMonth,secondList);

			// delegate(document, 'click', el.J_ks_prev , function(){
			// 	var
			// 		firstMonth , secondMonth ,
			// 		firstString = $(el.J_ks_title).item(0).text(),
			// 		firstYear = firstString.substring(0,4),

			// 		secondString = $(el.J_ks_title).item(1).text(),
			// 		secondYear = secondString.substring(0,4),

			// 		firstAllLength = $(el.J_ks_dbd).item(0).children('a').length,
			// 		secondAllLength = $(el.J_ks_dbd).item(1).children('a').length,

			// 		firstNullLength = $(el.J_ks_dbd).item(0).children(el.J_ks_null).length,
			// 		secondNullLength = $(el.J_ks_dbd).item(1).children(el.J_ks_null).length;

			// 	firstList = $(el.J_ks_dbd).item(0).children('a').slice(firstNullLength, firstAllLength);
			// 	secondList = $(el.J_ks_dbd).item(1).children('a').slice(secondNullLength, secondAllLength);
					
			// 		if(firstString.length == 7)
			// 			firstMonth = firstString.substring(5,6);
			// 		if(firstString.length == 8)
			// 			firstMonth = firstString.substring(5,7);

			// 		if(secondString.length == 7)
			// 			secondMonth = secondString.substring(5,6);
			// 		if(secondString.length == 8)
			// 			secondMonth = secondString.substring(5,7);

			// 	that._getPreachDay(firstYear,firstMonth,firstList);
			// 	that._getPreachDay(secondYear,secondMonth,secondList);
			// });

			// delegate(document, 'click', el.J_ok , function(){
			// 	var
			// 		firstMonth , secondMonth ,
			// 		firstString = $(el.J_ks_title).item(0).text(),
			// 		firstYear = firstString.substring(0,4),

			// 		secondString = $(el.J_ks_title).item(1).text(),
			// 		secondYear = secondString.substring(0,4),

			// 		firstAllLength = $(el.J_ks_dbd).item(0).children('a').length,
			// 		secondAllLength = $(el.J_ks_dbd).item(1).children('a').length,

			// 		firstNullLength = $(el.J_ks_dbd).item(0).children(el.J_ks_null).length,
			// 		secondNullLength = $(el.J_ks_dbd).item(1).children(el.J_ks_null).length;

			// 	firstList = $(el.J_ks_dbd).item(0).children('a').slice(firstNullLength, firstAllLength);
			// 	secondList = $(el.J_ks_dbd).item(1).children('a').slice(secondNullLength, secondAllLength);
					
			// 		if(firstString.length == 7)
			// 			firstMonth = firstString.substring(5,6);
			// 		if(firstString.length == 8)
			// 			firstMonth = firstString.substring(5,7);

			// 		if(secondString.length == 7)
			// 			secondMonth = secondString.substring(5,6);
			// 		if(secondString.length == 8)
			// 			secondMonth = secondString.substring(5,7);

			// 	that._getPreachDay(firstYear,firstMonth,firstList);
			// 	that._getPreachDay(secondYear,secondMonth,secondList);
			// });

			// delegate(document, 'click', el.J_ks_next , function(){
			// 	var
			// 		firstMonth , secondMonth ,
			// 		firstString = $(el.J_ks_title).item(0).text(),
			// 		firstYear = firstString.substring(0,4),

			// 		secondString = $(el.J_ks_title).item(1).text(),
			// 		secondYear = secondString.substring(0,4),

			// 		firstAllLength = $(el.J_ks_dbd).item(0).children('a').length,
			// 		secondAllLength = $(el.J_ks_dbd).item(1).children('a').length,

			// 		firstNullLength = $(el.J_ks_dbd).item(0).children(el.J_ks_null).length,
			// 		secondNullLength = $(el.J_ks_dbd).item(1).children(el.J_ks_null).length;

			// 	firstList = $(el.J_ks_dbd).item(0).children('a').slice(firstNullLength, firstAllLength);
			// 	secondList = $(el.J_ks_dbd).item(1).children('a').slice(secondNullLength, secondAllLength);
					
			// 		if(firstString.length == 7)
			// 			firstMonth = firstString.substring(5,6);
			// 		if(firstString.length == 8)
			// 			firstMonth = firstString.substring(5,7);

			// 		if(secondString.length == 7)
			// 			secondMonth = secondString.substring(5,6);
			// 		if(secondString.length == 8)
			// 			secondMonth = secondString.substring(5,7);

			// 	that._getPreachDay(firstYear,firstMonth,firstList);
			// 	that._getPreachDay(secondYear,secondMonth,secondList);
			// });

			// delegate(document, 'click' ,'a' ,function(ev){
			// 	if($(ev.currentTarget).parent().hasClass(el.J_ks_dbd)){
			// 		var
			// 			firstMonth , secondMonth ,
			// 			firstString = $(el.J_ks_title).item(0).text(),
			// 			firstYear = firstString.substring(0,4),

			// 			secondString = $(el.J_ks_title).item(1).text(),
			// 			secondYear = secondString.substring(0,4),

			// 			firstAllLength = $(el.J_ks_dbd).item(0).children('a').length,
			// 			secondAllLength = $(el.J_ks_dbd).item(1).children('a').length,

			// 			firstNullLength = $(el.J_ks_dbd).item(0).children(el.J_ks_null).length,
			// 			secondNullLength = $(el.J_ks_dbd).item(1).children(el.J_ks_null).length;

			// 		firstList = $(el.J_ks_dbd).item(0).children('a').slice(firstNullLength, firstAllLength);
			// 		secondList = $(el.J_ks_dbd).item(1).children('a').slice(secondNullLength, secondAllLength);
						
			// 			if(firstString.length == 7)
			// 				firstMonth = firstString.substring(5,6);
			// 			if(firstString.length == 8)
			// 				firstMonth = firstString.substring(5,7);

			// 			if(secondString.length == 7)
			// 				secondMonth = secondString.substring(5,6);
			// 			if(secondString.length == 8)
			// 				secondMonth = secondString.substring(5,7);

			// 		that._getPreachDay(firstYear,firstMonth,firstList);
			// 		that._getPreachDay(secondYear,secondMonth,secondList);
			// 	}
			// })	
		},

		_calendar:function(){
			var 
				selectDate = new Date(),
				that = this,
				c = {},
				year = $(el.J_year).text(),
				month = $(el.J_month).text(),
				day = $(el.J_day).text();

			var showdate = function(n,d){
				var uom = new Date(d - 0 + n * 86400000);
				uom = uom.getFullYear() + "/" + (uom.getMonth() + 1) + "/" + uom.getDate();
				return new Date(uom);
			};

			year = parseInt(year,10);
			month = parseInt(month,10);
			day = parseInt(day,10);

			selectDate.setFullYear(year, month-1, day);

			c = new Calendar(el.J_calendar,{
				date:selectDate,
				pages:2,
				selected:showdate(0,selectDate),
				popup:false
			});

			c.on('select' , function(ev){
				var
					opts = that.opts;  
				year = ev.date.getFullYear(),
				month = ev.date.getMonth(),
				day = ev.date.getDate(); 
				// if(navigator.userAgent.indexOf("MSIE")>0 && navigator.userAgent.indexOf("MSIE 8.0")>0){
				// 	month = month+1;
				// 	if(month < 10){
				// 		month = '0'+month;
				// 	}
				// 	if(day < 10){
				// 		day = '0'+day;
				// 	}
				// 	window.location.href = 'http://'+document.domain + "/careerTalk/careerTalk/calList/date/"+year+'-'+month+'-'+day;
				// }
				$(el.J_tpl_template).show();
				$('p',el.J_ul_wrapper).hide();
				$(el.J_grey_border).show();
				S.mix(opts,{
						 extraParam:{
						 	year:year,
						 	month:month,
						 	day:day
						 }
				});
				that.pagination.reload(opts);
				S.available('#preach-calendar',function(){
					var	
						firstList , secondList,
						firstMonth , secondMonth ,
						firstString = $(el.J_ks_title).item(0).text(),
						firstYear = firstString.substring(0,4),

						secondString = $(el.J_ks_title).item(1).text(),
						secondYear = secondString.substring(0,4),

						firstAllLength = $(el.J_ks_dbd).item(0).children('a').length,
						secondAllLength = $(el.J_ks_dbd).item(1).children('a').length,

						firstNullLength = $(el.J_ks_dbd).item(0).children(el.J_ks_null).length,
						secondNullLength = $(el.J_ks_dbd).item(1).children(el.J_ks_null).length;

					firstList = $(el.J_ks_dbd).item(0).children('a').slice(firstNullLength, firstAllLength);
					secondList = $(el.J_ks_dbd).item(1).children('a').slice(secondNullLength, secondAllLength);
						
						if(firstString.length == 7)
							firstMonth = firstString.substring(5,6);
						if(firstString.length == 8)
							firstMonth = firstString.substring(5,7);

						if(secondString.length == 7)
							secondMonth = secondString.substring(5,6);
						if(secondString.length == 8)
							secondMonth = secondString.substring(5,7);
					that._getPreachDay(firstYear,firstMonth,firstList);
					that._getPreachDay(secondYear,secondMonth,secondList);
				})										
			});

			c.on('monthChange',function(ev){
				S.available('#preach-calendar',function(){
					var	
						firstList , secondList,
						firstMonth , secondMonth ,
						firstString = $(el.J_ks_title).item(0).text(),
						firstYear = firstString.substring(0,4),

						secondString = $(el.J_ks_title).item(1).text(),
						secondYear = secondString.substring(0,4),

						firstAllLength = $(el.J_ks_dbd).item(0).children('a').length,
						secondAllLength = $(el.J_ks_dbd).item(1).children('a').length,

						firstNullLength = $(el.J_ks_dbd).item(0).children(el.J_ks_null).length,
						secondNullLength = $(el.J_ks_dbd).item(1).children(el.J_ks_null).length;

					firstList = $(el.J_ks_dbd).item(0).children('a').slice(firstNullLength, firstAllLength);
					secondList = $(el.J_ks_dbd).item(1).children('a').slice(secondNullLength, secondAllLength);
						
						if(firstString.length == 7)
							firstMonth = firstString.substring(5,6);
						if(firstString.length == 8)
							firstMonth = firstString.substring(5,7);

						if(secondString.length == 7)
							secondMonth = secondString.substring(5,6);
						if(secondString.length == 8)
							secondMonth = secondString.substring(5,7);
					that._getPreachDay(firstYear,firstMonth,firstList);
					that._getPreachDay(secondYear,secondMonth,secondList);
				})
			});
		},

		_getPreachDay:function(year , month ,list){
			var
				that = this,
				para = 'year='+year+'&month='+month;
			S.each(list,function(i,o){
				$(i).addClass(el.J_ks_disabled);
			});
			preachCalIO.getPreachDay(para , function(rs,data,msg){
				myvar.data = data;
				S.each(data,function(item){
					list.item(item.day - 1).removeClass(el.J_ks_disabled);
					list.item(item.day - 1).addClass(el.J_preach_day);
				})
			});
		}
	});
	return preachCalendarShow	
},{
	requires:['mod/pagination','calendar','io/preach/preachCalendar']
})
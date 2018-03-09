/*-----------------------------------------------------------------------------
* @Description:     index相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.30
* ==NOTES:=============================================
* v1.0.0(2014.9.30):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/index/index',function(S,core){
	PW.namespace('page.Index');
	PW.page.Index = function(param){
		new core(param);
	};
},{
	requires:['Index/core']
});
/*--------------------------------------index-------------------------------*/
KISSY.add('Index/core',function(S){
	var
		$ = S.all, on = S.Event.on,
		Calendar = PW.mod.Calendar,
		Defender = PW.mod.Defender,
		IndexIO = PW.io.Index.Index,
		el = {
			calendarHeader:'.J_canlendarHeader',
			calendarDay:'.J_calendarDay'
		},
		LINE_HEIGHT_CLASS = 'line-height';

	function core(param){
		this.opts = param;
		this.init();
	}

	S.augment(core,{
		init:function(){
			var
				that = this;
			// that._addNowDate();
			// that._addCalendar();
			// that._addEventListener();
			that._randerLectureDate();
			that._getLectureDate();
		},
		/*添加日历*/
		// _addCalendar:function(){
		// 	var
		// 		that = this,
		// 		date = $('.date').val().split('-');
		// 	that.date = Calendar.client({
		// 		renderTo: '.date', //默认只获取第一个
  //               select: {
  //                   rangeSelect: false, //是否允许区间选择
  //                   dateFmt: 'YYYY-MM-DD',
  //                   showTime: false, //是否显示时间
  //                   //minDate:new Date(parseInt(date[0]),parseInt(date[1])-1,parseInt(date[2])),
  //               }
		// 	});
		// },
		//把当前时间显示在日历文本框中
		// _addNowDate:function(nowDate){
		// 	var
		// 		that = this,
		// 		nowTime = new Date(),
		// 		year = nowTime.getFullYear(),
		// 		month = nowTime.getMonth()+1,
		// 		day = nowTime.getDate(),
		// 		nowDate = year + '-'+ month + '-' + day;
		// 	$('.date').val(nowDate);
		// 	that._ajaxGetInfo({date:nowDate});
		// },
		// _addEventListener:function(){
		// 	var
		// 		that = this,
		// 		date = that.date;
		// 	// 点击时间
		// 	on(date,'select',function(e){
		// 		that._selectDate(e);
		// 	});
		// },
		// _selectDate:function(e){
		// 	var
		// 		that = this,
		// 		time = e.date,
		// 		year = time.getFullYear(),
		// 		month = time.getMonth()+1,
		// 		day = time.getDate(),
		//         date = year+'-'+month+'-'+day;

		//     that._ajaxGetInfo({date:date});
		// },
		// _ajaxGetInfo:function(param){
		// 	var
		// 		that = this;
		// 	IndexIO.getInfo(param,function(rs,data,errMsg){
		//     	if(rs){
		//     		that._randerInfo(data);
		//     	}else{
		//     		S.log(errMsg);
		//     	}
		//     });
		// },
		/*渲染数据*/
		// _randerInfo:function(data){
		// 	var
		// 		that = this;
		// 	$('.J_recruitInfo').text(data.recruitCount);
		// 	$('.J_lectureInfo').text(data.meetCount);
		// 	$('.J_writtenInfo').text(data.writtenCount);
		// 	$('.J_companyInfo').text(data.companyCount);
		// 	$('.J_interViewInfo').text(data.interview);
		// },
		_getLectureDate:function(){
			var
				that = this,date = $('a',el.calendarDay),
				opts = that.opts,currentDate,url;
			IndexIO.getLectureDate({},function(rs,data,errMsg){
				if(rs){
					S.each(data,function(i,o){
						currentDate = $(date[i.pos]);
						url = opts.lectureListUrl + '?lectureDate=' +i.date;
						currentDate.addClass(LINE_HEIGHT_CLASS);
						currentDate.attr('href',url);
					});
				}else{
					S.log(errMsg);
				}
			});
		},
		_randerLectureDate:function(){
			var
				that = this,
				today = new Date(),
				year = today.getFullYear(),
				month = today.getMonth()+1,
				day = today.getDate(),
				week = today.getDay(),
				date = year+'年'+month+'月';
			//渲染宣讲会日历的头部
			$(el.calendarHeader).text(date);
			that._randerDay(year,month,day,week);
		},
		_randerDay:function(year,month,day,week){
			var
				that = this,
				maxDay = 30,
				dayHtml = '',
				i = 1,d= day,w = 0,lastDay = day;
			if(month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12){
				maxDay = 31;
			}else if(month == 2){
				//判断是否为闰年
				if(year%100 == 0 && year%400 == 0){
					maxDay = 29;
				}else if(year%100 != 0 && year%4 == 0){
					maxDay = 29;
				}else{
					maxDay = 28;
				}
			}else{
				maxDay = 30;
			}
			//周的显示
			for(w;w<week;w++){
				dayHtml = dayHtml+'<a href="javaScript:;"></a>';
			}
			//本月的显示
			for(d;d<=maxDay;d++){
				dayHtml = dayHtml+'<a href="javaScript:;">'+d+'</a>';
			}
			//下一个的显示
			if(day == 31 || day == 30){
				if(month == 3|| month == 5 || month == 8 || month == 10 ){
					lastDay = 30;
				}else if(month == 1){
					if(year%100 == 0 && year%400 == 0){
						lastDay = 29;	
					}else if(year%100 != 0 && year%4 == 0){
						lastDay = 29;
					}else{
						lastDay = 28;
					}
				}
			}
			for(i;i<=lastDay;i++){
				dayHtml = dayHtml+'<a href="javaScript:;">'+i+'</a>';
			}
			$(el.calendarDay).html(dayHtml);
		}
	});

	return core;
},{
	requires:['io/index/index','mod/defender','mod/calendar']
});
$(function(){
	var now = new Date(); //当前时间
	var year = now.getFullYear();
	var month = now.getMonth()+1;
	var date = now.getDate();
	var weekArray = ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'];
	var week = weekArray[now.getDay()];
	var hour = now.getHours();
	var minute = now.getMinutes();
	
	var addSelectBox = ''; 
	addSelectBox += '<ul class="date-box"><li class="year-prev"><a href="javascript:;"><</a></li><li class="year"></li><li class="year-next"><a href="javascript:;">></a></li><li class="month-prev"><a href="javascript:;"><</a></li><li class="month"></li><li class="month-next"><a href="javascript:;">></a></li><li class="today"><a href="javascript:;">今天</a></li><li>日</li><li>一</li><li>二</li><li>三</li><li>四</li><li>五</li><li>六</li>';
	addSelectBox += '<li class="hour-prev"><a href="javascript:;"></a></li><li class="hour"></li><li class="hour-next"><a href="javascript:;"></a></li><li class="minute-prev"><a href="javascript:;"></a></li><li class="minute"></li><li class="minute-next"><a href="javascript:;"></a></li><li class="clear" style="float:right;">清空</li></ul>';
	$(".J_selectDate").after(addSelectBox);  //页面添加静态日历框  
	
	$(".J_selectDate").on('click',function(e){ //点击input框触发日历框
		changeDate();
		stopPropagation(e);
		$(this).next().show();
		$(".year").html(year+"年");
		$(".month").html(month+"月");
		//一下if语句在不需要时分的时间框中不显示时分
		if($(this).attr('class') == 'J_selectDate select-date J_selectDateSmall'||$(this).attr('class') == 'J_selectDate select-date J_selectDateBig') {
			$(".hour-prev a").html("<");
			$(".hour").html(hour+"时");
			$(".hour-next a").html(">");
			$(".minute-prev a").html("<");
			$(".minute").html(minute+"分");
			$(".minute-next a").html(">");
		} else {
			$(".hour-prev a").html("");
			$(".hour").html("");
			$(".hour-next a").html("");
			$(".minute-prev a").html("");
			$(".minute").html("");
			$(".minute-next a").html("");
		}
		
		
	});
	
	$(document).bind('click',function(){ //点击页面其他地方日历框隐藏
		$(".date-box").hide();
	});
	
	function stopPropagation(e) {     //取消冒泡
		if(e.stopPropagation) {         
			e.stopPropagation();
		} else {
			e.cancelBubble = true;
		}
	}
	
	function changeDate() {        //改变日期
		$(".date").remove();
		$(".J_prevDate").remove();
		$(".J_nextDate").remove();
		var otherNow = new Date(year,month-1,1);  //更改后的日期
		var otherNowPrev = new Date(year,month,1);  
		var otherNowNext = new Date(year,month+1,1);  
		var otherMonth = otherNow.getMonth();   
		var otherMonthPrev = otherNowPrev.getMonth();   
		var otherMonthNext = otherNowNext.getMonth();   
		
		if(otherMonth == 1||otherMonth == 3||otherMonth == 5||otherMonth == 7||otherMonth == 8||otherMonth == 10||otherMonth == 12) {	//获取每月的天数
			otherDay = 31;
		} else if(otherMonth == 4||otherMonth == 6||otherMonth == 9||otherMonth == 11) {
			otherDay = 30;
		} else if(otherMonth == 2) {
			if(year%400 == 0 || (year%100!=0 && year%4==0)) { //判断闰年
				otherDay = 29;
			} else {
				otherDay = 28;
			}
		} 
		if(otherMonthPrev == 1||otherMonthPrev == 3||otherMonthPrev == 5||otherMonthPrev == 7||otherMonthPrev == 8||otherMonthPrev == 10||otherMonthPrev == 12) {
			otherDayPrev = 31;
		} else if(otherMonthPrev == 4||otherMonthPrev == 6||otherMonthPrev == 9||otherMonthPrev == 11) {
			otherDayPrev = 30;
		} else if(otherMonthPrev == 2) {
			if(year%400 == 0 || (year%100!=0 && year%4==0)) { //判断闰年
				otherDayPrev = 29;
			} else {
				otherDayPrev = 28;
			}
		} 
		if(otherMonthNext == 1||otherMonthNext == 3||otherMonthNext == 5||otherMonthNext == 7||otherMonthNext == 8||otherMonthNext == 10||otherMonthNext == 12) {
			otherDayNext = 31;
		} else if(otherMonthNext == 4||otherMonthNext == 6||otherMonthNext == 9||otherMonthNext == 11) {
			otherDayNext = 30;
		} else if(otherMonthNext == 2) {
			if(year%400 == 0 || (year%100!=0 && year%4==0)) { //判断闰年
				otherDayNext = 29;
			} else {
				otherDayNext = 28;
			}
		} 
		
		var otherweek = otherNow.getDay();	//通过获取每月1日星期几判断起始点
		var otherweekPrev = otherNowPrev.getDay();
		var otherweekNext = otherNowNext.getDay();
		var showDate ="";
		var j=0,n=0;
		for(var i=0;i<42;i++) {
			if(i<=otherDay+otherweek-1&&i>=otherweek) {
				j++;	
				if(j==date) {
					showDate += "<li class='date date-today'><a href='javascript:;' class='date-today-link'>"+j+"</a></li>"; //当前日期
				} else {
					showDate += "<li class='date'><a href='javascript:;'>"+j+"</a></li>";
				}
			} else if(i<otherweek) {
				p = otherDayPrev+i-otherweek+1;
				showDate += "<li class='J_prevDate date-prev'><a href='javascript:;'>"+p+"</a></li>";
			} else {
				n++;
				showDate += "<li class='J_nextDate date-next'><a href='javascript:;'>"+n+"</a></li>";
			}
		};
		$(".hour-prev").before(showDate);
	}
	
	$(".year-prev").on('click',function(e){	//年
		year--;
		$(".year").html(year+"年");
		changeDate();
		stopPropagation(e);
	});
	$(".year-next").on('click',function(e){
		year++;
		$(".year").html(year+"年");
		changeDate();
		stopPropagation(e);
	})
	
	$(".month-prev").on('click',function(e){	//月
		if(month>1) {
			month--;
			$(".month").html(month+"月");
		} else {
			month = 12;
			year--;
			$(".month").html(month+"月");
			$(".year").html(year+"年");
		}
		changeDate();
		stopPropagation(e);
	});
	$(".month-next").on('click',function(e){
		if(month<12) {
			month++;
			$(".month").html(month+"月");
		} else {
			month = 1;
			year++;
			$(".month").html(month+"月");
			$(".year").html(year+"年");
		}
		changeDate();
		stopPropagation(e);
	});
	
	$(".hour-prev").on('click',function(e){	//时
		if(hour>0) {
			hour--;
			$(".hour").html(hour+"时");
		} else {
			hour = 23;
			$(".hour").html(hour+"时");
		}
		stopPropagation(e);
	});
	$(".hour-next").on('click',function(e){
		if(hour<23) {
			hour++;
			$(".hour").html(hour+"时");
		} else {
			hour = 0;
			$(".hour").html(hour+"时");
		}
		stopPropagation(e);
	});
	
	$(".minute-prev").on('click',function(e){	//分
		if(minute>0) {
			minute--;
			$(".minute").html(minute+"分");
		} else {
			minute = 59;
			$(".minute").html(minute+"分");
		}
		stopPropagation(e);
	});
	$(".minute-next").on('click',function(e){
		if(minute<59) {
			minute++;
			$(".minute").html(minute+"分");
		} else {
			minute = 0;
			$(".minute").html(minute+"分");
		}
		stopPropagation(e);
	})
	
	$(".today").on('click',function(){	//今天
		now = new Date();
		year = now.getFullYear();
		month = now.getMonth()+1;
		date = now.getDate();
		hour = now.getHours();
		minute = now.getMinutes();
		$(".year").html(year+"年");
		$(".month").html((month+1)+"月");
		$(".hour").html(hour+"时");
		$(".minute").html(minute+"分");
		changeDate();
		normalDate($(this));
		$(this).parent().prev().val(today);
	})
	
	$(".date-box").on('click','.date',function(){	//日
		date = $(this).text();
		normalDate($(this));
		$(this).parent().prev().val(today);
	})
	$(".date-box").on('mouseover','.date',function(){
		$(this).children().css({"color":"#fff","text-decoration":"none"});
		$(this).css("background-color","#1A84ED");
	})
	$(".date-box").on('mouseout','.date',function(){
		$(this).children().css("color","#1A84ED");
		$(this).css("background-color","#fff");
	})
	
	$(".date-box").on('click','.J_prevDate',function(e){	//灰色日期
		date = $(this).text();
		if(month == 1) {
			year--;
			month=12;
			normalDate($(this));
			$(this).parent().prev().val(today);
		} else {
			month--;
			normalDate($(this));
			$(this).parent().prev().val(today);
		}
	})
	$(".date-box").on('click','.J_nextDate',function(){
		date = $(this).text();
		if(month == 12) {
			year++;
			month=1;
			normalDate($(this));
			$(this).parent().prev().val(today);
		} else {
			month++;
			normalDate($(this));
			$(this).parent().prev().val(today);
		}
	})
	function normalDate(e) {	//规格化日期
		if(month<10)	month="0"+month; 
		if(date<10)		date="0"+date; 
		if(hour<10)		hour="0"+hour; 
		if(minute<10)	minute="0"+minute; 
		if(e.parent().prev().attr('class') == 'J_selectDate select-date J_selectDateSmall'||e.parent().prev().attr('class') == 'J_selectDate select-date J_selectDateBig') {
			// today = year+"年"+month+"月"+date+"日"+hour+"时"+minute+"分";
			today = year+"-"+month+"-"+date+" "+hour+":"+minute+":00";
		} else {
			// today = year+"年"+month+"月"+date+"日";
			today = year+"-"+month+"-"+date;
		}
		month = Number(month);
		date = Number(date);
		hour = Number(hour);
		minute = Number(minute);
	}
	
	 
	// $(".sure").on('click',function(){	//确定 
	// 	$(".date-box").hide();
	// })
		
	
	$(".clear").on('click',function(){	//清空
		$(this).parent().prev().val("");
	})
})

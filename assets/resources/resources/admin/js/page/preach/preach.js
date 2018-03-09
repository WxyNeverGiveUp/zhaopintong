
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

$(function() {
	var el = {
		// 上传图片	 
		J_img_box: '.img-box', // 照片盒子
		J_upload_box: '#upload-box', // 预览照片的容器
		J_choose: '.file-pick', // 选择按钮
		J_upload: '.upload', // 上传按钮
		J_delete: '.img-delete', // 删除按钮
		J_uncommitted: '.uncommitted', // 未提交的图片
		J_select_img: '.select-img', // 选择的图片
	};
	//添加需求专业
	$(".J_addMajor").on('click',function(){
		$(".add-major-box").show();
		$(".cover").show();
	})
	$(".J_cancel").on('click',function(){
		$(this).parent().parent().hide();
		$(".cover").hide();
	})
	$(".J_confirm").on('click',function(){
		if($(this).parent().parent().attr('class') == 'add-major-box'){   //判断确定按钮对应的框是添加需求专业框
			if($(".add-major-detail").val() == 0) {
				alert("请填写需求专业！");
			} else if($(".education").val() == 0) {
				alert("请填写学历！");
			} else if($(".add-major-number").val() == "" ) {
				alert("请填写人数！");
			} else {
				$(".add-major-box").hide();
				$(".cover").hide();
				var major = $(".add-major-detail").val();
				var education = $(".education").val();
				var number = $(".add-major-number").val();
				var addMajor ='<tr class="line4"><td><span>需求专业：</span><span class="line4-edu">'+major+
								'</span><input type="hidden" id="majorVal" name="major_names[]" value="'+major+
								'" /><span>学历：</span><span>'+education+
								'</span><input type="hidden" id="educationVal" name="major_degrees[]" value="'+education+
								'" /></td><td><span>人数：</span><span>'+number+
								'</span><input type="hidden" id="numberVal" name="major_nums[]" value="'+number+
								'" /><a href="javascript:;" class="J_addMajorDelete">删除</a></td></tr>';
				$(".job-info").append(addMajor);
			}
		} else if($(this).parent().parent().attr('class') == 'visits-box') { ////判断确定按钮对应的框是添加来访人框
				$(".visits-box").hide();
				$(".cover").hide();
				$(".visits-info").show();
				var name = $(".visits-name").val();
				var job = $(".visits-job").val();
				var phone = $(".visits-phone").val();
				var visitsInfo = '<tr><td>'+name+
									'<input type="hidden" id="nameVal" name="visitor_names[]" value="'+name+
									'" /></td><td>'+job+
									'<input type="hidden" id="jobVal" name="visitor_jobs[]" value="'+job+
									'" /></td><td>'+phone+
									'<input type="hidden" id="phoneVal" name="visitor_phones[]" value="'+phone+
									'" /></td><td class="visits-delete J_visitsDelete">删除</td></tr>';
				$(".visits-info").append(visitsInfo);
		}
		
	})
	$(".job-info").on('click','.J_addMajorDelete',function(){
		$(this).parent().parent().remove();
	})

	//是否需要笔试面试场地
	$("#J_writtenNo").on('click',function(){
		$(".written-box").slideUp();
	})
	$("#J_writtenYes").on('click',function(){
		$(".written-box").slideDown();
	})

	$("#J_auditionNo").on('click',function(){
		$(".audition-box").slideUp();
	})
	$("#J_auditionYes").on('click',function(){
		$(".audition-box").slideDown();
	})

	//添加笔试面试场地
	$(".J_addPlace").click(function(){
		var siblingsLast = $(this).siblings("div:last");
		siblingsLast.clone(true).insertAfter(siblingsLast);
	})
	//删除笔试面试场地
	$(".J_writtenClose").on('click',function(e) {
		if($(this).parent().siblings(".written-place-info").length != 0) {		//判断填写框是否是唯一一个
			$(this).parent().remove();
		} else {
			$(this).parent().parent().prev().children("input").eq(1).prop("checked",true).prev().prev().prop("checked",false);
			$(this).parent().parent().slideUp();
		}
	})


	//限定只能输入数字
	$(".J_number").keyup(function(){
		var value = $(this).val().replace(/[^\d]/g,'');
		$(this).val(value);
	})

	//场地使用时间比较大小
	$(".date-box").on('click','.date,.J_prevDate,.J_nextDate',function(e){
		var val = $(this).parent().prev("input");
		if($(val).attr('class') == 'J_selectDate select-date J_selectDateSmall'||$(val).attr('class') == 'J_selectDate select-date J_selectDateSmall slvzr-first-child') {
			var small = $(this).parent().prev().val();
			var big = $(this).parent().parent().siblings("p").children("input").val();
			if(small > big&&big != "") {
			alert("请选择小于结束时间的日期！");
			$(this).parent().show();
			// stopPropagation(e);
			$(this).parent().prev().val("");
			// $(this).parent().prev().trigger('click',e);      //无法触发该事件使日历框重新触发刷新
			}
		}
		if($(val).attr('class') == 'J_selectDate select-date J_selectDateBig'||$(val).attr('class') == 'J_selectDate select-date J_selectDateBig slvzr-first-child') {  
			var big = $(this).parent().prev().val();
			var small = $(this).parent().parent().siblings("p").children("input").val();
			if(small > big&&small != "") {
			alert("请选择大于开始时间的日期！");
			$(this).parent().show();
			// stopPropagation(e);
			$(this).parent().prev().val("");
			}
		}
	})


	//场地使用规模单选框
	$(".scale-of-use input[type='radio']").on('click',function(){
		var siblings = $(this).siblings("input");
		$(siblings).attr("checked",false);
		var number = $(this).val();
		$(this).parent().children(".scale-number").attr("value",number);
	})

	//点击选择联系人按钮、选择领队人按钮
	$(".J_contacts").click(function(){
		$(".J_contactsBox").show();
		$(".cover").show();
	})
	$(".J_leaders").click(function(){
		$(".J_leadersBox").show();
		$(".cover").show();
	})

	// 选择联系人
	$(".J_conSelected").click(function() {
		$(this).parent().parent().parent().parent().parent().hide();
		$(".cover").hide();
		var name = $(this).prev().prev().prev().text();
		var job = $(this).prev().prev().text();
		var tele = $(this).prev().text();
		$(".contact-name").val(name);
		$(".contact-job").val(job);
		$(".contact-tele").val(tele);
	})
	// 选择领队人
	$(".J_leadSelected").click(function() {
		$(this).parent().parent().parent().parent().parent().hide();
		$(".cover").hide();
		var name = $(this).prev().prev().prev().text();
		var job = $(this).prev().prev().text();
		var tele = $(this).prev().text();
		$(".leader-name").val(name);
		$(".leader-job").val(job);
		$(".leader-tele").val(tele);
	})

	//添加来访人
	$(".J_visits").click(function(){
		$(".visits-box").show();
		$(".cover").show();
	})
	$(".visits-info").on('click','.J_visitsDelete',function(){
		$(this).parent().remove();
		if($(".visits-info tbody").text() == "") {
			$(".visits-info").hide();
		}
	})
	//关闭弹出框
	$(".J_close").click(function(){
		$(this).parent().parent().hide();
		$(".cover").hide();
	})
	$(".J_close").hover(function() {
		$(this).css("color","#f00");
	},function() {
		$(this).css("color","#fff");
	})
	var flag1;
	//宣讲地点下拉框验空
	$(".submit").click(function(){
//		$(".telephone").trigger('blur');
		locationSuggest();
		$(".J_preachLocation").change(function(){
			locationSuggest();
		})
		
	})
	function locationSuggest() {
		var preachLocation = $(".J_preachLocation").find("option:selected").val();
		if(preachLocation == 0) {
			$(".J_preachLocation").addClass("red");
			flag1=1;
		} else {
			$(".J_preachLocation").removeClass("red");
			flag1=0;
		}
	}
	//表单提交验证
	$("form.apply-preach").on('submit',function() {
		if(flag1) {
			return false;
		}
	})
	//正则表达式验证
	// $(".telephone").blur(function(){
	// 	$(this).parent().children(".teleMsg").remove();
	// 	var tele=$(this).val();
	// 	var reg=/^((\(\d{2,3}\))|(\d{3}\-))?1[3,8,5]{1}\d{9}$/;	
	// 	if(tele=='') {
	// 		$(this).after("<span style='color:#f00;' class='teleMsg'>请填写手机号。<span>")
	// 	}
	// 	else if(!reg.test(tele)) {
	// 		$(this).after("<span style='color:#f00;' class='teleMsg'>手机号格式不正确,请重新填写。<span>")
	// 	} 
	// })

	

	// 上传
	_upload();
		// 图片上传
			function _upload() {
				var id = 0; // 照片盒子的id
				var times = 0; // 上传次数
				var box = document.getElementById('upload-box'); // 获取容器盒子
				$(el.J_upload).addClass('none'); // 默认隐藏继续上传

				// 获取文件file
				function getObjectURL(file) {
					var url = null ;
					if (window.createObjectURL!=undefined) { // basic
						url = window.createObjectURL(file) ;
					} else if (window.URL!=undefined) { // mozilla(firefox)
						url = window.URL.createObjectURL(file) ;
					} else if (window.webkitURL!=undefined) { // webkit or chrome
						url = window.webkitURL.createObjectURL(file) ;
					}
					return url ;
				}

				// 预览功能
				$(el.J_img_box).on('change', el.J_choose, function(event) {
					event.preventDefault();
					// 获取一个file的input的files对象
				    var file = document.getElementsByName("talk_file[]")[0].files;
				    
				  
				    	// 实现多张上传预览
				    	for(var i=0;i<file.length;i++){	
				    		var imgObjPreview = document.getElementById("img"+i);
				    		var reg = /(\.*.jpg$)|(\.*.png$)|(\.*.jpeg$)|(\.*.gif$)|(\.*.bmp$)/; 
				    		// 判断是否上传的是图片
				    		if(!reg.test(this.value)) {
				    			box.innerHTML += "<li class='uncommitted' id='" + id + "'><div class='select-img' id='img" + id + '-' + i + "' data-num='"+ id + "'></div><img src='../../../assets/resources/recruitment_resources/img/preach/someTxt.png' /><span>"+this.files[i].name+"</span><a href='javascript:;' class='img-delete'>删除</a></li>";
				    			$('#img'+id + '-'+i).attr('src', getObjectURL(this.files[i])); // 或 this.files[0] this->input
				    		} else {
				    			box.innerHTML += "<li class='uncommitted' id='" + id + "'><img class='select-img' id='img" + id + '-' + i + "' data-num='"+ id + "'/><span>"+this.files[i].name+"</span><a href='javascript:;' class='img-delete'>删除</a></li>";
				    			$('#img'+id + '-'+i).attr('src', getObjectURL(this.files[i])); // 或 this.files[0] this->input
				    		}
				    		
				    	}
					$('.operate .file-pick').eq(0).hide();
					$(el.J_upload).removeClass('none');
				});

				// 继续选择
				$(el.J_upload).click(function() {
					id += 1;
					$('.operate').prepend('<input type="file" class="file-pick" name="talk_file[]" value="请选择文件..." data-num="'+ id +'">');
				});

				$(el.J_upload_box).off("mouseenter","li").on('mouseenter', 'li', function(ev) {
					var deleteBtn = $(this).children('a');
					var deleteNum = $('.uncommitted').index($(this)); //未提交照片的索引
					deleteBtn.off('click'); // 事件解绑
					deleteBtn.on('click',function(ev) {
						deleteBtn.parent().remove();// 删除预览图片
						var dataNum = deleteBtn.prev().prev().attr('data-num');
						$('.operate input[data-num="' + dataNum +'"]').remove();
					});
					// 查看大图
					var popPic = $(this).children('img');
					popPic.off('click');
					popPic.on('click',function(ev) {
						$(el.J_pop).removeClass('none');
						var imgSrc = $(this).attr('src'); 
						$(el.J_pop).children('img').attr('src',imgSrc);
					})
				});
				// 隐藏
				$(el.J_close).click(function(ev) {
					$(this).parent().addClass('none');
				});
			};
	// 短信验证触发器
	$('#scms_yzcode').blur(function() {
		giveyz(document.getElementById('scms_yzcode').value);
	});
	$('#Submitbfs').click(function() {
		duanxin();
	});

	// 短信验证函数
	function duanxin() {
		var pram = document.getElementById('scms_gettel').value;
		giveduanxin(pram);
		// updateinfo(pram);
	};
	function qingkong() { //清空提示函数
		updateinfo();
	}
	var wait = 90; //停留时间
	function updateinfo(tel) {
		var regex = /^(?:13\d|15\d|18[123456789])-?\d{5}(\d{3}|\*{3})$/;
		if (wait == 0 || tel == "" || !regex.exec(tel)) {
			document.getElementById('Submitbfs').value = "获取验证码";
			document.getElementById('Submitbfs').disabled = "";
			wait = 90 //还原重发时的初始值
		}
		else {
			document.getElementById('Submitbfs').disabled = "disabled"; //防止关闭层后，又激活了
			document.getElementById('Submitbfs').value = "等待 " + wait + " 秒";
			wait--;
			setTimeout("updateinfo()", 1000);
		}

	}
	//   验证两次的验证码
	function giveyz(scms_yzcode) {
		var chk = true;
		var divid = document.getElementById("show_statu");
		var regex = /[0-9]$/

		divid.style.display = 'block';

		if (scms_yzcode == "") {
			divid.innerHTML = "<span style='color:red'>请填写您收到的短信校验码！</span>";
		} else if (document.getElementById('right_yzcode').value != scms_yzcode) {
			divid.innerHTML = "<span style='color:red'>您填写的短信校验码不正确！</span>";
		} else if (document.getElementById('right_yzcode').value == scms_yzcode) {
			divid.innerHTML = "<span style='color:green'>验证成功！</span>";
			//divid.style.display='none';
		}
	}

	var xmlHttp;

	function giveduanxin(tel) {
		var chk = true;
		var divid = document.getElementById("show_statu");
		var regex = /^(?:13\d|15\d|18[123456789])-?\d{5}(\d{3}|\*{3})$/;

		divid.style.display = '';
		if (tel == "") {
			divid.innerHTML =  "<span style='color:red'>请填写手机号码！</span>";
		} else if (!regex.exec(tel)) {
			divid.innerHTML = "<span style='color:red'>手机号码格式不正确！</span>";
		} else {

			xmlHttp = GetXmlHttpObject()
			if (xmlHttp == null) {
				alert("抱歉，浏览器不支持")
				return
			}

			var url = "http://www.dsjyw.net//recruitEntrance/recruitmentInfo/duanXin/sendCode"
			url = url + "?action=chk&tel=" + tel
			url = url + "&sid=" + Math.random()
			xmlHttp.onreadystatechange = stateChanged
			xmlHttp.open("GET", url, true)
			xmlHttp.send(null)
		}

		function stateChanged() {
			if (xmlHttp.readyState == 4 || xmlHttp.readyState == "complete") {

				var give_strs = new Array(); //定义一数组
				give_strs = xmlHttp.responseText.split("|"); //字符分割

				if (give_strs[0] == "right") {
					// document.getElementById('Submitbfs').style.visibility='hidden';
					//closeWindow();
					divid.innerHTML = "<span style='color:green'>验证码已发送,请查收！</span>";
					//document.getElementById('codeshows').innerHTML=yzm;
					document.getElementById('Submitbfs').disabled = "disabled";//立即失效，并开始提示下面是2秒后换提示内容并开始倒数
					document.getElementById('right_yzcode').value = give_strs[1];//回传发到短信的校验码
					setTimeout("qingkong()", 2000);//1秒后提示，重新发送
				} else {
					divid.innerHTML = xmlHttp.responseText;
				}
			}
		}

		// document.getElementById('Submitbfs').disabled="disabled";

	}

	function GetXmlHttpObject() {
		var xmlHttp = null;
		try {
			// Firefox, Opera 8.0+, Safari
			xmlHttp = new XMLHttpRequest();
		}
		catch (e) {
			// Internet Explorer
			try {
				xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e) {
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
		return xmlHttp;

	}
})


/*登录注册页面的js
吴晓阳*/
// 入口函数
$(document).ready(function() {
	// 变量声明
	// 对象盒子
	var el = {
		J_require_text: '.require-text', // 必填文本
		J_require_option: '.require-option', // 必填下拉框
		J_submit: '.sub-btn', // 提交按钮 
		J_tip: '.tip', // 消息提示
		J_phone: '.phone', // 用户名
		// 联系人基本信息
		J_psw: '.psw', // 密码
		J_psw2: '.psw2', // 确认密码
		J_img_code_input: '#img-code-input', // 图片验证码输入框
		J_img_code: '#img-code', // 图片验证码
	};
	

	// 加载函数
	_verify();
	_imgCode();

	// pop框的隐藏于显示
	var register = $(".register-btn");
	$(register).click(function() {
		$(".login").addClass("none");
		$(".register-choose").removeClass("none");
	});
	var forget_password = $(".forget-password");
	$(forget_password).click(function() {
		$(".login").addClass("none");
		$(".register").removeClass("none");
	});
	
	// 验证函数 (私有函数_)
	function _verify() {
		var flag1, // 文本框
			flag2; // 下拉框
		$(el.J_submit).click(function(event) {
			// 必填文本框
			$(el.J_require_text).each(function() {
				var textVal = $(this).val();
				if (textVal.length == 0) {
					flag1 = true;
					$(this).css('border','1px solid #F61E1E');
					$(this).next().removeClass('none');
					// console.log('error:文本框未填写'); // 调试提醒
				} else{
					$(this).css('border','1px solid #ccc');
				}
			});
			$(el.J_require_text).change(function() {
				var textVal = $(this).val();
				$(this).next().addClass('none');
				if (textVal.length == 0) {
					flag1 = true;
					$(this).css('border','1px solid #F61E1E');
					$(this).next().removeClass('none');
					// console.log('error:文本框未填写'); // 调试提醒
				} else{
					$(this).css('border','1px solid #ccc');
				}
			});

			// 阻止表单提交
			if (flag1 || flag2) {
				$("body").animate({scrollTop:0}, 500);
				return false;
			}else{
				return true;
			}
		});
		// 登录密码
		$(el.J_psw).blur(function() {
			var reg = /^[a-zA-Z0-9]{6,16}$/;
			if(!(reg.test($(el.J_psw).val())) && ($(el.J_psw).val().length != 0)){
				$(el.J_psw).css('border','1px solid #F61E1E');
				$(el.J_psw).next().removeClass('none');
			}else{
				$(el.J_psw).css('border','1px solid #ccc');
				$(el.J_psw).next().addClass('none');
			}
		});

		// 重复密码
		$(el.J_psw2).blur(function() {
			if(!($(el.J_psw2).val() == $(el.J_psw).val()) && ($(el.J_psw2).val().length != 0)){
				$(el.J_psw2).css('border','1px solid #F61E1E');
				$(el.J_psw2).next().removeClass('none');
			}else{
				$(el.J_psw2).css('border','1px solid #ccc');
				$(el.J_psw2).next().addClass('none');
			}
		});
		// 用户名验证
		$(el.J_phone).blur(function() {
			var reg = /^1[3|4|5|7|8][0-9]{9}$/;
			if(!(reg.test($(el.J_phone).val())) && ($(el.J_phone).val().length!=0)){
				$(el.J_phone).css('border','1px solid #F61E1E');
				$(el.J_phone).next().removeClass('none');
			}else{
				$(el.J_phone).css('border','1px solid #ccc');
				$(el.J_phone).next().addClass('none');
			}
		});
	};

	// 图片验证码
	function _imgCode() {
		// 点击刷新验证码
		$(el.J_img_code).click(function() {
			$(el.J_img_code).attr('src','http://new_dsjyw.dev/util/Captcha/Getcode'); // 点击刷新
		});

		// 失焦验证
		$(el.J_img_code_input).blur(function() {
			var code = $(el.J_img_code_input).val();
			$.ajax({
				url: 'http://new_dsjyw.dev/util/captcha/check?',
				type: 'get',
				dataType: 'json',
				data: {
					code: code
				},
				success: function (data) {
					if (data.code == 0) { // 返回0成功
						$(el.J_img_code_input).parent().next('p').removeClass('none').css('color', 'green');
						$(el.J_img_code_input).parent().next('p').text(data.ext);
					}
					else if(data.code == 1){ // 返回1失败
						$(el.J_img_code_input).parent().next('p').removeClass('none').css('color', '#E72F2F');
						$(el.J_img_code_input).parent().next('p').text(data.ext);
					}
					else{ // 返回2需要刷新
						$(el.J_img_code_input).parent().next('p').removeClass('none').css('color', '#E72F2F');
						$(el.J_img_code_input).parent().next('p').text(data.ext);
					}
					
				},
				error: function (data,errorMsg) {
					console.log('error');
				}
			})
		});
	}

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
		var regex = /^(?:13\d|15\d|17\d|18[123456789])-?\d{5}(\d{3}|\*{3})$/;
		if (wait == 0 || tel == "" || !regex.exec(tel)) {
			document.getElementById('Submitbfs').value = "获取验证码";
			document.getElementById('Submitbfs').disabled = "";
			document.getElementById("show_statu").innerHTML = "<span style=color:blue>如果您没有收到短信校验码，您现在可以重新获取！</span>";
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
		var regex =  /^(?:13\d|15\d|17\d|18[123456789])-?\d{5}(\d{3}|\*{3})$/;

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
	
});

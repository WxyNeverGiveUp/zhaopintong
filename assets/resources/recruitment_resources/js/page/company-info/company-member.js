/*公司信息-单位员工
吴晓阳*/
$(document).ready(function() {
	// 变量声明
	// 对象盒子
	var el = {
		J_require_text: '.require-text', // 必填文本
		J_query_type: '#query-type', // 类别筛选
		J_name: '.name', // 姓名
		J_phone: '.phone', // 手机号
		J_telephone: '.telephone', // 手机号
		J_email: '.email', // email
		J_select: '.info-select', // 下拉框
		J_address: '.address', // 校友填写项中的通讯地址(唯一必填文本框)
		J_new_cancel: '.new-cancel', // 取消按钮
		J_update_cancel: '.update-cancel', // 取消按钮
		J_new: '.new-linkman', // 新建联系人
		J_new_pop: '.new-pop', //弹出层
		J_update_pop: '.update-pop', //弹出层
		J_img_code_input: '.img-code-input', // 填写图片验证码
		J_img_code: '#img-code', // 图片验证码
		J_update_info: '.update-info', // 修改
		J_delete: '.delete', // 删除
		J_submit1: '.new-sub', // 添加联系人提交按钮
		J_submit2: '.update-sub' // 修改联系人提交按钮
	};
	
	// 加载函数
	_verify();
	_showOrhide();
	_transmit();
	_imgCode();

	// 局部的显示隐藏
	function _showOrhide() {
		// 添加全局键盘事件
		$(document).keyup(function(event) {
			if (event.keyCode == 27) {
				$(el.J_new_pop).addClass('none');
				$(el.J_update_pop).addClass('none');
			}
		});
		// 修改 (动态添加显示)
		$(el.J_update_cancel).click(function() {
			$(el.J_update_pop).addClass('none');
		});

		// 新建联系人
		$(el.J_new).click(function() {
			$(el.J_new_pop).removeClass('none');
		});
		$(el.J_new_cancel).click(function() {
			$(el.J_new_pop).addClass('none');
		});
	}
	
	// 验证函数 (私有函数_)
	function _verify() {
		var flag1, // 文本框
			flag2, // 下拉框
			flag3; // 联系人类别
		$(el.J_submit1).click(function(event) {
			// 必填文本框
			$('.new-pop .require-text').each(function() {
				var textVal = $(this).val();
				if (textVal == "") {
					flag1 = true;
					$(this).css('border','1px solid #F61E1E');
					$(this).siblings('p').removeClass('none');
					console.log('error:文本框未填写'); // 调试提醒
				} else{
					flag1 = false;
					$(this).css('border','1px solid #ccc');
				}
			});
			$('.new-pop .require-text').change(function() {
				var textVal = $(this).val();
				$(this).siblings('p').addClass('none');
				if (textVal == "") {
					flag1 = true;
					$(this).css('border','1px solid #F61E1E');
					$(this).siblings('p').removeClass('none');
					console.log('error:文本框未填写'); // 调试提醒
				} else{
					flag1 = false;
					$(this).css('border','1px solid #ccc');
				}
			});

			// 新建联系人别类是否选择
			if ($('.checkbox input').is(':checked')) {
				flag3 = false;
				$('.checkbox').siblings('p').addClass('none');
			}
			else{
				flag3 = true;
				$('.checkbox').siblings('p').removeClass('none');
			}

			// 阻止表单提交
			if (flag1 || flag2 || flag3) {
				console.log('error:信息未完成');
				return false;
			}else{
				return true;
			}
		});
		$(el.J_submit2).click(function(event) {
			// 必填文本框
			$('.update-pop .require-text').each(function() {
				var textVal = $(this).val();
				if (textVal.length == 0) {
					flag1 = true;
					$(this).css('border','1px solid #F61E1E');
					$(this).siblings('p').removeClass('none');
					console.log('error:文本框未填写'); // 调试提醒
				} else{
					flag1 = false;
					$(this).css('border','1px solid #ccc');
				}
			});
			$('.update-pop .require-text').change(function() {
				var textVal = $(this).val();
				$(this).siblings('p').addClass('none');
				if (textVal.length == 0) {
					flag1 = true;
					$(this).css('border','1px solid #F61E1E');
					$(this).siblings('p').removeClass('none');
					console.log('error:文本框未填写'); // 调试提醒
				} else{
					$(this).css('border','1px solid #ccc');
				}
			});

			// 修改联系人别类是否选择
			if ($('.checkbox input').is(':checked')) {
				flag3 = false;
				$('.checkbox').siblings('p').addClass('none');
			}
			else{
				flag3 = true;
				$('.checkbox').siblings('p').removeClass('none');
			}

			// 阻止表单提交
			if (flag1 || flag2 || flag3) {
				console.log('error:信息未完成');
				$("body").animate({scrollTop:0}, 500);
				return false;
			}else{
				return true;
			}
		});
		// 手机号
		$(el.J_phone).blur(function() {
			var reg = /^1[3|4|5|7|8][0-9]{9}$/;
			if(!(reg.test($(el.J_phone).val())) && ($(el.J_phone).val().length!=0)){
				$(el.J_phone).css('border','1px solid #F61E1E');
				$(el.J_phone).siblings('p').removeClass('none');
			}else{
				$(el.J_phone).css('border','1px solid #ccc');
				$(el.J_phone).siblings('p').addClass('none');
			}
		});

		// 邮箱验证
		$(el.J_email).blur(function() {
			var reg = /^(\w)+(\.\w+)*@(\w)+((\.\w{2,3}){1,3})$/;
			if(!(reg.test($(el.J_email).val())) && ($(el.J_email).val().length != 0)){
				$(el.J_email).css('border','1px solid #F61E1E');
				$(el.J_email).siblings('p').removeClass('none');
			}else{
				$(el.J_email).css('border','1px solid #ccc');
				$(el.J_email).siblings('p').addClass('none');
			}
		});
	};

	// 联系人修改传参数
	function _transmit() {
		// 类别筛选
		$(el.J_query_type).change(function(ev) {
			var value = $(el.J_query_type).children('option:selected').val(),
				path_url = _ajax.url.company_info.company_member.queryByType;
			$.ajax({
				url: path_url,
				type: 'get',
				dataType: 'json',
				data: {
					id: value,
				},
				success: function (data) {
					var code = data.code; // 返回值 0代表查询空为 1代表查询不为空
					if (code) { 
						$('.manage').next('p').addClass('none');
						$('.manage tbody tr').remove();
						$.each(data.data,function(i,o) {
							$('.manage tbody').append(
								'<tr>' +
								'<td>' + o.name + '</td>' +
								'<td>' + o.duty + '</td>' +
								'<td>' + o.type_id + '</td>' +
								'<td>' + o.phone + '</td>' +
								'<td><input type="hidden" value='+ o.id + '><a href="javascript:;" class="update-info">修改</a><a href="javascript:;" class="delete">删除</a></td>' +
								'</tr>');
						});
					}
					else{
						$('.manage tbody tr').remove();
						$('.manage').next('p').removeClass('none');
					}
				},
				error: function(data,errorMsg) {
                    console.log('查询失败');
				}
			})
		});
		// 联系人修改传参 动态节点添加操作
		$('.manage').on('click', el.J_update_info, function(ev) {
			ev.preventDefault();
			$(el.J_update_pop).removeClass('none');
			var target = ev.target;
			var id = $(target).prev('input').val();
			$.ajax({
				url: 'userJson',
				type: 'get',
				dataType: 'json',
				data: {
					id: id
				},
				success: function(data) {
                    $('.user-id').val(data.data.id);//添加ID
					$('.name').val(data.data.name); // 添加姓名
					$('.change-phone').val(data.data.phone); // 添加手机号
				},
				error: function(data,errorMsg) {
                   console.log('查询失败');
				}
			});
		});
		// 联系人删除 动态节点添加操作
		$('.manage').on('click', el.J_delete, function(ev) {
			ev.preventDefault();
			var target = ev.target;
			var id = $(target).siblings('input').val(),
				path_url = _ajax.url.company_info.company_member.del;
			$.ajax({
				url: path_url,
				type: 'get',
				dataType: 'json',
				data: {
					id: id,
				},
				success: function(data) {
					console.log(data.code);
					if (data.code == 1) {
                        $(target).parents('tr').remove(); // 删除整行
					}else{
						console.log('删除失败');
					}
				},
				error: function(data,errorMsg) {
                   console.log('删除失败');
				}
			});
		});
	}

	// 图片验证码
	function _imgCode() {
		// 点击刷新验证码
		$(el.J_img_code).click(function() {
			var path_url = _ajax.url.imgCode;
			$(el.J_img_code).attr('src', path_url); // 点击刷新
		});

		// 失焦验证
		$(el.J_img_code_input).blur(function() {
			var code = $(el.J_img_code_input).val(),
				path_url = _ajax.url.checkImgCode;
			$.ajax({
				url: path_url,
				type: 'get',
				dataType: 'json',
				data: {
					code: code
				},
				success: function (data) {
					if (data.code == 0) { // 返回0成功
						$(el.J_img_code_input).siblings('p').removeClass('none').css('color', 'green');
						$(el.J_img_code_input).siblings('p').text(data.ext);
					}
					else if(data.code == 1){ // 返回1失败
						$(el.J_img_code_input).siblings('p').removeClass('none').css('color', '#E72F2F');
						$(el.J_img_code_input).siblings('p').text(data.ext);
					}
					else{ // 返回2需要刷新
						$(el.J_img_code_input).siblings('p').removeClass('none').css('color', '#E72F2F');
						$(el.J_img_code_input).siblings('p').text(data.ext);
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
		var regex = /^(?:13\d|15\d|17\d|18[123456789])-?\d{5}(\d{3}|\*{3})$/;

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

			var url = _ajax.url.code;
			url = url + "?action=chk&tel=" + tel;
			url = url + "&sid=" + Math.random();
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
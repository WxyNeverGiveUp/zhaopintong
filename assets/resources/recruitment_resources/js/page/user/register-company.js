/*已入驻单位的js
吴晓阳*/
// 入口函数
$(document).ready(function() {
	// 变量声明
	// 对象盒子
	var el = {
		J_require_text: '.require-text', // 必填文本
		J_require_option: '.require-option', // 必填下拉框
		J_submit: '.sub-btn', // 提交按钮 
		J_search_input: '.search-input', // 搜索消息
		J_tip: '.tip', // 消息提示
		J_search_tip: '.search-tip', // 搜索消息的提示
		J_search_btn: '.search-btn', // 搜索按钮
		J_company_name: '.company-name', // 注册单位显示
		// 联系人基本信息
		J_name: '.name', // 姓名
		J_phone: '.phone', // 手机号
		J_psw: '.psw', // 密码
		J_psw2: '.psw2', // 确认密码
		J_email: '.email', // email
		J_alumni_switch: '.alumni-switch', // 是否是校友开关
		J_alumni: '.alumni', // 校友信息
		J_select: '.info-select', // 下拉框
		J_address: '.address', // 校友填写项中的通讯地址(唯一必填文本框)
		J_img_code_input: '#img-code-input', // 图片验证码输入框
		J_img_code: '#img-code', // 图片验证码
		// 企业认证资料 上传图片	 
		J_img_box: '.img-box', // 照片盒子
		J_upload_box: '#upload-box', // 预览照片的容器
		J_choose: '.file-pick', // 选择按钮
		J_upload: '.upload', // 上传按钮
		J_delete: '.img-delete', // 删除按钮
		J_uncommitted: '.uncommitted', // 未提交的图片
		J_select_img: '.select-img', // 选择的图片
		J_editor: '#editor', // 富文本编辑企业介绍
	    J_close: '.close-img', // 查看大图的隐藏
		J_pop: '.pop', // 弹出层
		
	};
	// 消息盒子 暂时没什么用
	var tip = {
		J_search_tip: '请输入18位的同意社会信用代码',
		J_name_tip: '必须填写真实姓名',
	}
	
	// 加载函数
	_verify();
	_upload();
	_imgCode();
	_getCompanyName();

	// 验证函数 (私有函数_)
	function _verify() {
		var flag1, // 文本框
			flag2, // 下拉框
			flag3; // 认证资料
		// 获取到body元素
		var body = document.body;
		
		// 消息搜索
		$(el.J_search_input).focus(function(ev){
			ev.stopPropagation();
			$(el.J_search_tip).removeClass('none');
		});
		$(el.J_search_input).blur(function(ev){
			ev.stopPropagation();
			$(el.J_search_tip).addClass('none');
		});


		$(el.J_submit).click(function(event) {
			// 必填文本框
			$(el.J_require_text).each(function() {
				var textVal = $(this).val();
				if (textVal.length == 0) {
					flag1 = true;
					$(this).css('border','1px solid #F61E1E');
					$(this).parent().next().removeClass('none');
					console.log('error:文本框未填写'); // 调试提醒
				} else{
					flag1 = false;
					$(this).css('border','1px solid #ccc');
				}
			});
			$(el.J_require_text).change(function() {
				var textVal = $(this).val();
				$(this).parent().next().addClass('none');
				if (textVal.length == 0) {
					flag1 = true;
					$(this).css('border','1px solid #F61E1E');
					$(this).parent().next().removeClass('none');
					console.log('error:文本框未填写'); // 调试提醒
				} else{
					$(this).css('border','1px solid #ccc');
				}
			});
			// 必填下拉框
			$(el.J_require_option).each(function() {
				var optionValue = $(this).val();
				if (optionValue == -1) {
					flag2 = true;
					$(this).css('border','1px solid #F61E1E');
					console.log('error:下拉框未填写'); // 调试提醒
				} else{
					flag2 = false;
					$(this).css('border','1px solid #ccc');
				}
			});
			$(el.J_require_option).change(function() {
				var optionValue = $(this).children('option:selected').val();
				if (optionValue == -1) {
					flag2 = true;
					$(this).css('border','1px solid #F61E1E');
					console.log('error:下拉框未填写'); // 调试提醒
				} else{
					$(this).css('border','1px solid #ccc');
				}
			});

			

			// 阻止表单提交
			if (flag1 || flag2) {
				console.log('error:信息未完成');
				$("body").animate({scrollTop:0}, 500);
				return false;
			}else{
				return true;
			}
		});
		// 姓名
		$(el.J_name).blur(function() {
			var reg = /^([\u4e00-\u9fa5]){2,7}$/;
			if (!(reg.test($(el.J_name).val())) && ($(el.J_name).val().length!=0)){
				$(el.J_name).css('border','1px solid #F61E1E');
				$(el.J_name).parent().next().removeClass('none');
			}else{
				$(el.J_name).css('border','1px solid #ccc');
				$(el.J_name).parent().next().addClass('none');
			}
		});

		// 手机号
		$(el.J_phone).blur(function() {
			var reg = /^1[3|4|5|7|8][0-9]{9}$/;
			if(!(reg.test($(el.J_phone).val())) && ($(el.J_phone).val().length!=0)){
				$(el.J_phone).css('border','1px solid #F61E1E');
				$(el.J_phone).parent().next().removeClass('none');
			}else{
				$(el.J_phone).css('border','1px solid #ccc');
				$(el.J_phone).parent().next().addClass('none');
				// 增加是否存在重复号码
				var phone = $(el.J_phone).val(),
					path_url = _ajax.url.user.registered.phone;
				$.ajax({
					url: path_url,
					type: 'get',
					dataType: 'json',
					data: {
						phone: phone,
					},
					success: function(data){
						var code = data.code;
						if (code) {
							$(el.J_phone).parent().next().next().addClass('none');
							console.log('可以注册');
						}
						else{
							$(el.J_phone).parent().next().next().removeClass('none');
						}
					},
					error: function(data,errorMsg) {
						console.log('error:异常,JSON数据出错');
					}
				})
			}
		});

		// 登录密码
		$(el.J_psw).blur(function() {
			var reg = /^[a-zA-Z0-9]{6,16}$/;
			if(!(reg.test($(el.J_psw).val())) && ($(el.J_psw).val().length != 0)){
				$(el.J_psw).css('border','1px solid #F61E1E');
				$(el.J_psw).parent().next().removeClass('none');
			}else{
				$(el.J_psw).css('border','1px solid #ccc');
				$(el.J_psw).parent().next().addClass('none');
			}
		});

		// 重复密码
		$(el.J_psw2).blur(function() {
			if(!($(el.J_psw2).val() == $(el.J_psw).val()) && ($(el.J_psw2).val().length != 0)){
				$(el.J_psw2).css('border','1px solid #F61E1E');
				$(el.J_psw2).parent().next().removeClass('none');
			}else{
				$(el.J_psw2).css('border','1px solid #ccc');
				$(el.J_psw2).parent().next().addClass('none');
			}
		});

		// 邮箱验证
		$(el.J_email).blur(function() {
			var reg = /^(\w)+(\.\w+)*@(\w)+((\.\w{2,3}){1,3})$/;
			if(!(reg.test($(el.J_email).val())) && ($(el.J_email).val().length != 0)){
				$(el.J_email).css('border','1px solid #F61E1E');
				$(el.J_email).parent().next().removeClass('none');
			}else{
				$(el.J_email).css('border','1px solid #ccc');
				$(el.J_email).parent().next().addClass('none');
			}
		});
	};

	// 是否是校友 (选择显示)
	$(el.J_alumni_switch).click(function() {
		if ($(el.J_alumni_switch).is(':checked')) {
            $(el.J_alumni).removeClass('none');
            $('.alumni .info-select').addClass('require-option');
            $(el.J_address).addClass('require-text');
		}else{
            $(el.J_address).removeClass('require-text');
            $('.alumni .info-select').removeClass('require-option');
            $(el.J_alumni).addClass('none');
		}
	});

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
		    var file = document.getElementsByName("zhizhaoUrl[]")[0].files;
		    // 判断是否上传的是图片
		    var reg = /(\.*.jpg$)|(\.*.png$)|(\.*.jpeg$)|(\.*.gif$)|(\.*.bmp$)/;      
		    if(!reg.test(this.value)) { 
		        alert("系统仅支持jpg/jpeg/png/gif/bmp格式的照片！");  
		        this.focus(); 
		    }else{
		    	// 实现多张上传预览
		    	for(var i=0;i<file.length;i++){	
		    		var imgObjPreview = document.getElementById("img"+i);
		    		box.innerHTML += "<li class='uncommitted' id='" + id + "'><img class='select-img' id='img" + id + '-' + i + "' data-num='"+ id + "'/><span>认证资料</span><a href='javascript:;' class='img-delete'>删除</a></li>";
		    		$('#img'+id + '-'+i).attr('src', getObjectURL(this.files[i])); // 或 this.files[0] this->input
		    	}
		    }
			$('.operate .file-pick').eq(0).hide();
			$(el.J_upload).removeClass('none');
		});

		// 继续选择
		$(el.J_upload).click(function() {
			id += 1;
			$('.operate').prepend('<input type="file" class="file-pick" name="zhizhaoUrl[]" value="请选择文件..." data-num="'+ id +'">');
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

	// 获取公司名称
	function _getCompanyName() {
		$(el.J_search_btn).click(function() {
			var value = $(el.J_search_input).val(),
				path_url = _ajax.url.user.registered.queryName;
			$.ajax({
				url: path_url,
				type: 'get',
				dataType: 'json',
				data: {
					barCode: value,
				},
				success: function(data){
					var code = data.code; // 如果为0表示已经存在
					console.log(code);
					if (code) {
						$('.company-name').val(data.name);
						$('.company-name').next().addClass('none');
					}
					else{
						$('.company-name').val("");
						$('.company-name').next().removeClass('none');
					}
				},
				error: function(data,errorMsg){
					console.log('error:异常,JSON数据出错');
				}
			})
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

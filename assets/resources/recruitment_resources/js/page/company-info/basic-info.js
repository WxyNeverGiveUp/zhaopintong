/*公司信息-认证资料的js
吴晓阳*/
// 入口函数
$(document).ready(function() {
	//变量声明
	var el = {
		J_require_text: '.require-text', // 必填文本
		J_require_option: '.require-option', // 必填下拉框
		J_submit: '.submit-btn', // 提交按钮
		// 企业注册认证信息
		J_company_name: '.company-name', // 单位名称
		J_province: ".province", // 省份
		J_city: ".city", // 省份
		J_telephone: '.telephone', // 固定电话
		J_work_email: '.work-email', // 招聘邮箱
		J_postcode: '.postcode', // 邮编 
	}

	// 加载函数
	_verify();
	_upload();

	
	// 验证函数
	function _verify() {
		var flag1, // 文本框
			flag2; // 认证资料
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
					flag1 = false;
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
				} else{
					$(this).css('border','1px solid #ccc');
				}
			});

			// 阻止表单提交
			if (flag1 || flag2) {
				console.log('error:信息未完成');
				$("body").animate({scrollTop:0}, 500);
				return false;
			}
		});

	};

	// 上传
	function _upload() {
		$('.file-pick').change(function(){
			var file = this.files[0];  
			if(window.FileReader) {  
			    var fr = new FileReader();  
			    fr.onloadend = function(e) {  
			        document.getElementById("portrait").src = e.target.result;
			    };  
			    fr.readAsDataURL(file);  
			}
		});
		// 上传视频
		$('#video-choose').change(function() {
			var reg = /\w+(.flv|.rvmb|.mp4|.avi|.wmv)$/
			var video = $('#video-choose');
			var file = this.files[0];
			if (!reg.test(video.val())) {
				alert('视频类型必须是mp4,avi,wmv,rvmb,flv的一种');
				return false;
			}
		});
	}
});
/*申请职位的js
吴晓阳*/
// 入口函数
$(document).ready(function() {
	//变量声明
	var el = {
		J_require_text: '.require-text', // 必填文本
		J_require_option: '.require-option', // 必填下拉框
		J_submit: '.submit-btn', // 提交按钮
		J_position_name: '.position-name', // 职位名称
		J_people_num: '.people-num', // 招聘人数
		J_select_result: '.select-result', // query(relativeSelectors: DOMString)专业选择的结果
		J_major_result: '.major', // 专业选择的结果的隐藏的input
		J_province: '.province', // 省
		J_city: '.city', // 市
		J_position_type: 'input[name=isTeacher]', // 教师类与非教师类
		J_nlimited_professional: '.nlimited-professional', // 不限制专业复选框
	}

	// 加载函数
	_majorChoose();
	_getArea();
	_verify();

	// 专业选择函数以及一些传值操作
	function _majorChoose() {
		$('.major-list li').click(function() {
			$(this).addClass('major-active').siblings().removeClass('major-active');
			// 获取元素属性
			var code = $(this).attr('majorCode');
			// 先隐藏已有元素
			$('.major-detail label').hide();
			$('.major-detail label').each(function(index, el) {
				$("label[majorCode="+ code +"]").show();
			});
			var selectLength = $("label[majorCode="+ code +"]").length,
				path_url = _ajax.url.recruitment_info.apply.major;
			// 不加载已经加载专业名称
			if (selectLength == 0) {
				$.ajax({
					url: path_url,
					type: 'get',
					dataType: 'json',
					data: {
						majorId: code
					},
					success: function (data){
						var majorOption = "<label class='selectAll-box' majorCode=" + code +"><input type='checkbox' class='selectAll'>全选</label>";
						$.each(data, function(i,o) {
							majorOption += "<label majorCode=" + o.parent_id 
							+"><input type='checkbox' majorName="+ o.name 
							+" class='major-check'><span majorcode="+ o.parent_id 
							+" class="+ o.name + " majorId="+ o.id +">"+ o.name 
							+"</span></label>"
						});
                        $('.major-detail').append(majorOption);
                        // 若已经存在则自动勾选  未修改页提供的方法
                        var arr = new Array;
                        var majorId = "";
                        $('.select-result span').each(function (index, object) {
                        	majorId = $(object).attr('majorid');
                        	arr.push(majorId);
                        });
                        if(arr.length > 0){
                        	for (var i = 0; i < arr.length; i++) {
								$('.major-check + span[majorid='+ arr[i] +']').prev().prop('checked','checked');
							}
                        }
					},
					error: function (data){
						console.log('error:JSON对象异常');
					}
				})
			};
		});

		// 显示已经选择
		$('.demand-specialty').on('click','.major-check', function(){
			var that = $(this),
				majorName = that.attr('majorName'), // 获取input的majorCode 
				code = that.parent().attr('majorCode'), // 获取父元素的majorCode
				chooseDom = that.next(), // 获取到该元素
			    checkResult = that.is(':checked'); // 选择状态
			var chooseText = that.next().prop('outerHTML'); // 获取该元素节点

			if (checkResult) {
			    $(el.J_select_result).append(chooseText); 
			}else{
				$('.select-result .' + majorName).remove();
				$("label[majorCode=" + code +"] .selectAll").prop('checked',false); // 取消任意一个全选框为false
			}
		});

		// 全选功能
		$('.demand-specialty').on('click','.selectAll', function(){
			var that = $(this),
				code = that.parent().attr('majorCode'), // 父元素的majorCode
			    checkResult = that.is(':checked'), // 选择状态
			    chooseText = "";
			if (checkResult) {
				$("label[majorCode=" + code +"] input").prop('checked',true); // 全选框为true
				$(".select-result span[majorCode=" + code +"]").remove(); // 先清除以前可能选择了的选项
				$(".major-detail span[majorCode=" + code +"]").each(function(index,object) {
					chooseText += $(object).prop('outerHTML'); // 循环出来需要的值
				});
				$(el.J_select_result).append(chooseText);
			}else{
				$("label[majorCode=" + code +"] input").prop('checked',false);
				$(".select-result span[majorCode=" + code +"]").remove();
			}
		});

		// 任意点击都会把值传到需要提交的input内
		$('body').click(function() {
			var major_val = $(el.J_major_result).val();
			var result = "";
			$('.select-result span').each(function(index,object) {
				result += $(object).attr('majorId') + ',';
			})
			$(el.J_major_result).val(result);
		});

		// 教师与非教师职位类别
		// 初始赋值 鉴定哪个被选中
		var nowResult = $(el.J_position_type).eq(0).prop('checked');
		if (nowResult) {
			$('.teacher-position').show();
			$('.teacher-position').children('select').prop('name', 'positionType');
			$('.teacher-position').children('select').addClass('require-option');
			$('.not-t-position').hide();
			$('.not-t-position').children('select').prop('name','');
			$('.not-t-position').children('select').removeClass('require-option');
		}else{
			$('.teacher-position').hide();
			$('.teacher-position').children('select').prop('name', '');
			$('.teacher-position').children('select').removeClass('require-option');
			$('.not-t-position').show();
			$('.not-t-position').children('select').prop('name','positionType');
			$('.not-t-position').children('select').addClass('require-option');

		}
		$(el.J_position_type).change(function() {
			var result = $(el.J_position_type).eq(0).prop('checked');
			if (result) {
				$('.teacher-position').show();
				$('.teacher-position').children('select').prop('name', 'positionType');
				$('.teacher-position').children('select').addClass('require-option');
				$('.not-t-position').hide()
				$('.not-t-position').children('select').prop('name','');
				$('.not-t-position').children('select').removeClass('require-option');
			}else{
				$('.teacher-position').hide();
				$('.teacher-position').children('select').prop('name', '');
				$('.teacher-position').children('select').removeClass('require-option');
				$('.not-t-position').show()
				$('.not-t-position').children('select').prop('name','positionType');
				$('.not-t-position').children('select').addClass('require-option');

			}
		});

		// 鉴定是否选择不限专业
		$(el.J_nlimited_professional).change(function() {
			var status = $(el.J_nlimited_professional).prop('checked');
			if (status) {
				$('.demand-specialty').addClass('none');
				$('.select-result').text('');
			}else{
				$('.demand-specialty').removeClass('none');
				$('.select-result').text('请您选择:');
			}
		});

	};

	// 省份-城市联动
	function _getArea() {
		// 如果省份改变
		$(el.J_province).change(function() {
			$(el.J_city).children('option').remove();
			$(el.J_city).append("<option value='-1' selected='selected'>请选择</option>");
			var provinceId = $(this).find('option:selected').val();
			$.ajax({
				url: 'http://www.dsjyw.net/recruitEntrance/recruitmentInfo/recruitment/CityJson',
				type: 'get',
				dataType: 'json',
				data: {
					provinceId: provinceId
				},
				success: function (data,msg) {
					var option = "";
					$.each(data,function(index, object) {
						var code = object.code; // 如果返回code=0 说明是直辖市 不需要添加城市option
						if (!code) {
							$('.city').removeClass('require-option');
							$('.city').css('border','1px solid #ccc');
						}else{
							$('.city').addClass('require-option');
							$.each(object.items,function(i, o) {
								option += "<option value=" + o.id + ">" + o.name + "</option>";
							});
							$(el.J_city).append(option);
						}
					});
				},
				error: function (data,msg) {
					console.log('异常,获取JSON数据失败');
					console.log(msg);
				}
			})
		});
	};
	
	// 验证函数
	function _verify() {
		var flag1, // 文本框
			flag2, // 下拉框
			flag3; // 认证资料
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
					$(this).css('border','1px solid #F61E1E');
					$(this).parent().next().removeClass('none');
					// console.log('error:文本框未填写'); // 调试提醒
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
					// console.log('error:下拉框未填写'); // 调试提醒
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
					// console.log('error:下拉框未填写') // 调试提醒
				} else{
					$(this).css('border','1px solid #ccc');
				}
			});


			// 阻止表单提交
			if (flag1 || flag2 || flag3) {
				console.log('error:信息未完成');
				console.log(flag1);
				console.log(flag2);
				$("body").animate({scrollTop:0}, 500);
				return false;
			}else{
				return true;
			}
		});
		// 20字以内的职位名称
		$(el.J_position_name).blur(function() {
			var reg = /^.{1,20}$/; // 任意20个字符
			if (!(reg.test($(el.J_position_name).val())) && ($(el.J_position_name).val().length!=0)){
				$(el.J_position_name).css('border','1px solid #F61E1E');
				$(el.J_position_name).next().removeClass('none');
			}else{
				$(el.J_position_name).css('border','1px solid #ccc');
				$(el.J_position_name).next().addClass('none');
			}
		});
		// 人数正则
		$(el.J_people_num).blur(function() {
			var reg = /^\+?[1-9][0-9]*|[\u4e00-\u9fa5]$/; // 非零正整数
			if (!(reg.test($(el.J_people_num).val())) && ($(el.J_people_num).val().length!=0)){
				$(el.J_people_num).css('border','1px solid #F61E1E');
				$(el.J_people_num).next().removeClass('none');
			}else{
				var value = $(el.J_people_num).val();
				var trim = $(value).replace(/\s$/,'');
				value = trim;
				$(el.J_people_num).css('border','1px solid #ccc');
				$(el.J_people_num).next().addClass('none');
			}
		});
	};
});
/*-----------------------------------------------------------------------------
* @DescriSion: user相关js
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.07.17
* ==NOTES:=============================================
* v1.0.0(2015.07.17):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/user/user',function(S,login,register,modify,search,nav){
	PW.namespace('page.user.user');
	PW.page.user.user = function(){
		new login();
		new register();
		new modify();
		new search();
		new nav();
	}
},{
	requires:['user/login','user/register','user/modify','user/search','user/nav']
})

/*---------------------------------登录----------------------------------------*/
KISSY.add('user/login',function(S){
	var 
		$ = S.all,
		on = S.Event.on,
		userIO = PW.io.user.user,
		el = {
			J_login : '.login',
			J_register : '.register',
			J_stu_login : '.stu-login',
			J_pop : '.pop',
			J_close : '.close',
			J_submit : '.submit',
			J_account : '.account',
			J_account_tip : '.account-tip',
			J_password : '.password',
			J_password_tip : '.password-tip',
			J_login_part : '.login-part',
			J_nenu_user : '.nenu-user',
			J_other_user : '.other-user',
			J_to_register : '.to-register',
			J_nick_name : '.nick-name',
			J_forget_password : '.forget-password',
			J_get_password : '.get-password',
			J_sure_email : '.sure-email',
			J_cancel_email : '.cancel-email'
		},
		myvar = {
			nullTip:'输入不能为空',
			passwordLenTip:'密码长度不得少于8位',
			accountLenTip:'学号长度为10位',
			accountFormatTip:'账号格式错误',
			studentAccount:'学号',
			otherAccount:'账号',
			loginErrorTip:"密码或账号不符,请重新输入",
			emailTip : 'email格式不正确',
			emailSucTip : 'email输入成功,请稍后登录邮箱,获取密码',
			emailNotExistedTip : 'email不存在,请重新输入'
		};

	function login(){
		this.init();
	}

	S.augment(login , {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var
				isFromat = false,
				emailReg = /^[0-9a-zA-Z_\-\.]+@[0-9a-zA-Z_\-]+(\.[0-9a-zA-Z_\-]+)+$/,
				that = this;

			$(el.J_stu_login).on('click',function(ev){
				$(el.J_pop).show();
				$(el.J_login).show();
				$(el.J_register).hide();
			});

			$(el.J_close).on('click',function(ev){
				$(el.J_pop).hide();
			});

			$('input[type="radio"]',el.J_login_part).on('click',function(ev){
				if($(ev.currentTarget).hasClass(el.J_nenu_user)){
					$('em',el.J_account).text(myvar.studentAccount);
				}
				if($(ev.currentTarget).hasClass(el.J_other_user)){
					$('em',el.J_account).text(myvar.otherAccount);
				}
			})

			$(el.J_submit).on('click',function(ev){
				ev.preventDefault();
				var
					isSubmit = true,
					accountVal = $('input',el.J_account).val(),
					password = $('input',el.J_password).val(),
					userType = $('input[type="radio"]:checked',el.J_login_part),
					numReg = /^\d+$/;
				if(accountVal.length == 0){
					isSubmit = false;
					$(el.J_account_tip).show();
					$(el.J_account_tip).text(myvar.nullTip);
				}
						
				else if(!emailReg.test(accountVal)){
					if(numReg.test(accountVal) && userType.hasClass(el.J_nenu_user)){
						if(!that._isStuID(accountVal)){
							isSubmit = false;
							$(el.J_account_tip).show();
							$(el.J_account_tip).text(myvar.accountLenTip);
						}
					}
					else{
						isSubmit = false;
						$(el.J_account_tip).show();
						$(el.J_account_tip).text(myvar.accountFormatTip);
					}
				};
				
				if(password.length == 0){
				 	isSubmit = false;
				 	$(el.J_password_tip).show();
					$(el.J_password_tip).text(myvar.nullTip);
				}

				else if(password.length < 8 && $(el.J_other_user).attr('checked') == 'checked'){
				 	isSubmit = false;
				 	$(el.J_password_tip).show();
					$(el.J_password_tip).text(myvar.passwordLenTip);
				};

				if(isSubmit){
					var 
						para = S.io.serialize("#L-form");
					userIO.isLogin(para,function(code,data,msg){
						if(code == 0){
							$(el.J_nick_name).parent().parent().show();
							$(el.J_nick_name).text(data);
							$(el.J_pop).hide();
							$(el.J_stu_login).hide();
							window.location.reload(true);
						}
						if(code == 1){
							$(el.J_account_tip).hide();
							$(el.J_password_tip).show();
							$(el.J_password_tip).text(myvar.loginErrorTip);
							$(el.J_account_tip).text(myvar.loginErrorTip);
						}
						if(code == 2){
							var 
								currentWwwHref = window.location.href,
								pathName = window.location.pathname,
								pos = currentWwwHref.indexOf(pathName),
								host = currentWwwHref.substring(0,pos);
							window.location.href = host + '/user/message/seekmessage';
						}
					})	
				}
			});

			$('input',el.J_account).on('click',function(ev){
				$(el.J_account_tip).hide();
			});

			$('input',el.J_password).on('click',function(ev){
				$(el.J_password_tip).hide();
			});

			$(el.J_to_register).on('click',function(ev){
				$(el.J_login).hide();
				$(el.J_register).show();
			});

			$(el.J_forget_password).on('click',function(ev){
				$(el.J_login).hide();
				$(el.J_get_password).show();
			});

			$('input' , el.J_get_password).on('change',function(ev){
				var
					email = $('input' , el.J_get_password).val();

				if(emailReg.test(email))
					isFromat = true;
				else{
					isFromat = false;
					$('span',el.J_get_password).text(myvar.emailTip);
				}
			});

			$('input' , el.J_get_password).on('click',function(ev){
				$('span',el.J_get_password).text('');
			});

			$(el.J_sure_email).on('click',function(ev){
				if(isFromat){
					var
						para = 'email='+$('input' , el.J_get_password).val();
					userIO.putEmail(para,function(code,data,msg){
						if(code == 0){
							$('input' , el.J_get_password).hide();
							$(el.J_sure_email).hide();
							$('em' , el.J_get_password).text(myvar.emailSucTip);
						}
						if(code == 1){
							$('span',el.J_get_password).text(myvar.emailNotExistedTip);
						}
					})	
				}
			});

			$(el.J_cancel_email).on('click',function(ev){
				$(el.J_get_password).hide();
				$(el.J_login).show();
			})
		},

		_isStuID:function(account){
			var 
				length = account.length;
			if(length == 10)
				return true;
			else 
				return false;
		}
	});

	return login;

},{
	requires:['core','sizzle','io/user/user']
})

/*---------------------------------注册----------------------------------------*/
KISSY.add('user/register',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		userIO = PW.io.user.user,
		el = {
			J_register : '.register',
			J_login : '.login',
			J_login_part : '.login-part',
			J_register_part : '.register-part',
			J_register_submit : '.register-submit',
			J_user_name : '.user-name',
			J_email : '.email',
			J_first_password : '.first-password',
			J_check_password : '.check-password',
			J_name_tip : '.name-tip',
			J_email_tip : '.email-tip',
			J_first_password : '.first-password',
			J_check_password : '.check-password',
			J_fir_pass_tip : '.fir-pass-tip',
			J_check_pass_tip : '.check-pass-tip'
		},
		myvar = {
			nameNullTip : '姓名不能为空',
			nameRegisteredTip : "该名称已被注册",
			nameCanUsedTip:'该名称可被注册',
			emailNullTip:'邮箱不能为空',
			emailTip : 'email格式不正确',
			emailRegisteredTip : "该邮箱已被注册",
			emailCanUsedTip:'该邮箱可被注册',
			passwordLenTip:'密码长度不得少于8位',
			firstPasswordNullTip:'密码不能为空',
			checkPasswordNullTip:'确认密码不能为空',
			notSameTip : '确认密码与密码不一致',
			registerSucTip : '注册成功 ,请前往注册邮箱激活后登录',
			registerFailTip : '注册失败 ，请重试'
		}; 

	function register() {
		this.init();
	}

	S.augment(register , {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var
				that = this,
				isPasswordFit = true,
				isNameFit = true,
				isAccountFit = true,
				isCheckFit = true
				emailReg = /^[0-9a-zA-Z_\-\.]+@[0-9a-zA-Z_\-]+(\.[0-9a-zA-Z_\-]+)+$/;	

			// $('input' , el.J_user_name).on('change',function(ev){
			// 	var 
			// 		para = "userName="+$('input' , el.J_user_name).val();

			// 	userIO.isNameRegistered(para,function(code,data,msg){
			// 		if(code == 0){
			// 			isNameFit = false;
			// 			$(el.J_name_tip).text(myvar.nameRegisteredTip);
			// 		}
			// 		if(code == 1){
			// 			isNameFit = true;
			// 			$(el.J_name_tip).text(myvar.nameCanUsedTip);
			// 		}
			// 	})
			// });

			$('input' , el.J_email).on('change',function(ev){
				var
					email = $('input' , el.J_email).val();
					para = "email="+email;

				if(!emailReg.test(email)){
					isAccountFit = false;
					$(el.J_email_tip).text(myvar.emailTip);
				}

				else{
					userIO.isEmailRegistered(para,function(code,data,msg){
						if(code == 0){
							isAccountFit = false;
							$(el.J_email_tip).text(myvar.emailRegisteredTip);
						}
						if(code == 1){
							isAccountFit = true;
							$(el.J_email_tip).text(myvar.emailCanUsedTip);
						}
					})
				}
			});

			$('input',el.J_first_password).on('change',function(ev){
				var
					firstPassword = $('input' , el.J_first_password).val(),
					checkPassword = $('input',el.J_check_password).val();
				if(firstPassword.length < 8){
					isPasswordFit = false;
					$(el.J_fir_pass_tip).text(myvar.passwordLenTip)
				}
				else{

					if(checkPassword != firstPassword){
						isCheckFit = false;
						$(el.J_check_pass_tip).text(myvar.notSameTip)
					}
					else{
						isCheckFit = true;
						isPasswordFit = true;
						$(el.J_check_pass_tip).text('');
					}
				}
			});

			$('input',el.J_check_password).on('change',function(ev){
				var
					firstPassword = $('input' , el.J_first_password).val(),
					checkPassword = $('input',el.J_check_password).val();

				if(checkPassword.length < 8){
					isCheckFit = false;
					$(el.J_check_pass_tip).text(myvar.passwordLenTip)
				}
				else{
					if(checkPassword != firstPassword){
						isCheckFit = false;
						$(el.J_check_pass_tip).text(myvar.notSameTip)
					}
					else{
						isCheckFit = true;
						isPasswordFit = true;
						$(el.J_check_pass_tip).text('');
					}
				}
			});

			$('input' , el.J_user_name).on('click',function(ev){
				$(el.J_name_tip).text('');
			});

			$('input' , el.J_email).on('click',function(ev){
				$(el.J_email_tip).text('');
			});

			$('input',el.J_first_password).on('click',function(ev){
				$(el.J_fir_pass_tip).text('');
			});

			$('input',el.J_check_password).on('click',function(ev){
				$(el.J_check_pass_tip).text('');
			});

			$(el.J_register_submit).on('click',function(ev){
				ev.preventDefault();
				var
					userName = $('input',el.J_user_name).val(),
					email = $('input',el.J_email).val(),
					firstPassword = $('input' , el.J_first_password),
					checkPassword = $('input',el.J_check_password);

				if(userName.length == 0){
					$(el.J_name_tip).text(myvar.nameNullTip);
					isNameFit = false;
				}

				if(email.length == 0){
					isAccountFit = false;
					$(el.J_email_tip).text(myvar.emailNullTip);
				}

				if(firstPassword.length == 0){
					isPasswordFit = false;
					$(el.J_fir_pass_tip).text(myvar.firstPasswordNullTip);
				}

				if(checkPassword.length == 0){
					isCheckFit = false;
					$(el.J_check_pass_tip).text(myvar.checkPasswordNullTip);
				}

				if(isCheckFit && isNameFit && isPasswordFit && isAccountFit){
					var
						para = S.io.serialize("#R-form");

					userIO.isRegister(para,function(code,data,msg){
						if(code == 0){
							$(el.J_register).hide();
							$('h1',el.J_login_part).text(myvar.registerSucTip);
							$(el.J_login).show();
						}
						if(code == 1){
							$('h1' , el.J_register).text(myvar.registerFailTip);
						}
					})
				}
			});
		}
	});
	return register;
},{
	requires:['core','io/user/user']
})

/*-------------------------------修改用户信息----------------------------------*/
KISSY.add('user/modify',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		userIO = PW.io.user.user,
		el = {
			J_pop : '.pop',
			J_login : '.login',
			J_nick_name : '.nick-name',
			J_to_homepage : '.to-homepage',
			J_to_modify : '.to-modify',
			J_user_info : '.user-info',
			J_modify_info : '.modify-info',
			J_modify_name : '.modify-name',
			J_modify_password : '.modify-password',
			J_new_check_password : '.new-check-password',
			J_modify_account : '.modify-account',
			J_to_modify_name : '.to-modify-name',
			J_cancel_modify_name : '.cancel-modify-name',
			J_to_modify_password : '.to-modify-password',
			J_cancel_modify_password : '.cancel-modify-password',
			J_to_modify_account : '.to-modify-account',
			J_cancel_modify_account : '.cancel-modify-account',
			J_editing : 'editing',
			J_new_password : '.new-password',
			J_to_submit : '.to-submit',
			J_to_close : '.to-close'
		},
		myvar = {
			nameNullTip : '姓名不能为空',
			nameRegisteredTip : "该名称已被注册",
			nameCanUsedTip:'该名称可被注册',
			emailNullTip:'邮箱不能为空',
			emailFormatTip : 'email格式不正确',
			emailRegisteredTip : "该邮箱已被注册",
			emailCanUsedTip:'该邮箱可被注册',
			passwordLenTip:'密码长度不得少于8位',
			passwordNullTip:'密码不能为空',
			newPasswordNullTip:'新密码不能为空',
			checkPasswordNullTip:'确认密码不能为空',
			notSameTip : '确认密码与新密码不一致',
			oldPassword :'原密码',
			modifySuc : '修改成功',
			modifyFail : '原密码错误，请稍后重试',
			modifyTip: '修改用户信息',
			nenuUser : 1,
			otherUser : 2
		}
		emailReg = /^[0-9a-zA-Z_\-\.]+@[0-9a-zA-Z_\-]+(\.[0-9a-zA-Z_\-]+)+$/;

	function modify(){
		this.init();
	}

	S.augment(modify,{
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			var
				newName ,
				password ,
				name,
				account,
				userType,
				isPasswordModify = false,
				isPasswordFit = true,
				isNameFit = true,
				isAccountFit = true,
				isNewPasswordFit = true,
				isNewCheckFit = true;
			$(el.J_nick_name).on('mouseenter',function(ev){
				$(el.J_user_info).show();
			});

			$(el.J_nick_name).on('mouseleave',function(ev){
				$(el.J_user_info).hide();
			});

			$(el.J_user_info).on('mouseenter',function(ev){
				$(el.J_user_info).show();
			});

			$(el.J_user_info).on('mouseleave',function(ev){
				$(el.J_user_info).hide();
			});

			// $(el.J_to_modify).on('click',function(ev){
			// 	var 
			// 		para = 'userId='+$(el.J_nick_name).attr('id');
			// 	userIO.getUserInfo(para , function(rs,data,msg){

			// 		name = data.name;
			// 		account = data.account;
			// 		password = data.password;
			// 		userType = data.userType;

			// 		$('input',el.J_modify_name).val(data.name);
			// 		$('input',el.J_modify_account).val(data.account);
			// 		$('input',el.J_modify_password).val(data.password);
					
			// 		$(el.J_pop).show();
			// 		$(el.J_login).hide();
			// 		$(el.J_modify_info).show();
			// 		$('div',el.J_modify_info).show();
			// 		$(el.J_to_submit).show();
			// 		$(el.J_new_password).hide();
			// 		$(el.J_new_check_password).hide();
			// 		$(el.J_to_modify_name).show();
			// 		$(el.J_to_modify_password).show();
			// 		$(el.J_cancel_modify_name).hide();
			// 		$(el.J_cancel_modify_password).hide();
			// 		$(el.J_cancel_modify_account).hide();
			// 		$('input',el.J_modify_info).removeClass(el.J_editing);
			// 		$('h1',el.J_modify_info).text(myvar.modifyTip);

			// 		if(userType == myvar.otherUser){
			// 			$(el.J_to_modify_account).show();
			// 		}

			// 		if(userType == myvar.nenuUser){
			// 			$(el.J_modify_account).remove();
			// 		}
			// 	})
			// });

			// $(el.J_to_modify_name).on('click',function(ev){
			// 	$('input',el.J_modify_name).removeAttr('readonly');
			// 	$('input',el.J_modify_name).addClass(el.J_editing);
			// 	$(el.J_to_modify_name).hide();
			// 	$(el.J_cancel_modify_name).show();
			// });

			// $(el.J_to_modify_password).on('click',function(ev){
			// 	isPasswordModify = true;
			// 	$(el.J_new_check_password).show();
			// 	$(el.J_new_password).show();
			// 	$('input',el.J_modify_password).removeAttr('readonly');
			// 	$('input',el.J_modify_password).val('');
			// 	$('span',el.J_modify_password).text(myvar.oldPassword);
			// 	$('input',el.J_modify_password).addClass(el.J_editing);
			// 	$('input',el.J_new_check_password).addClass(el.J_editing);
			// 	$('input',el.J_new_password).addClass(el.J_editing);
			// 	$(el.J_cancel_modify_password).show();
			// 	$(el.J_to_modify_password).hide();
			// });

			// $(el.J_to_modify_account).on('click',function(ev){
			// 	$('input' , el.J_to_modify_account).val(account);
			// 	$('input',el.J_modify_account).removeAttr('readonly');
			// 	$('input',el.J_modify_account).addClass(el.J_editing);
			// 	$(el.J_cancel_modify_account).show();
			// 	$(el.J_to_modify_account).hide();
			// 	$('input',el.J_modify_password).removeClass(el.J_editing);
			// });

			// $(el.J_cancel_modify_name).on('click',function(ev){
			// 	$('input',el.J_modify_name).val(name);
			// 	$('input',el.J_modify_name).attr('readonly','readonly');
			// 	$(el.J_cancel_modify_name).hide();
			// 	$(el.J_to_modify_name).show();
			// 	$('input',el.J_modify_name).removeClass(el.J_editing);
			// 	$('em',el.J_modify_name).text('');
			// });

			// $(el.J_cancel_modify_password).on('click',function(ev){
			// 	isPasswordModify = false;
			// 	$('input' , el.J_modify_password).val(password);
			// 	$('input' , el.J_modify_password).attr('readonly','readonly');
			// 	$(el.J_cancel_modify_password).hide();
			// 	$(el.J_to_modify_password).show();
			// 	$(el.J_new_check_password).hide();
			// 	$(el.J_new_password).hide();
			// 	$('input',el.J_modify_password).removeClass(el.J_editing);
			// 	$('input',el.J_new_check_password).removeClass(el.J_editing);
			// 	$('input',el.J_new_password).removeClass(el.J_editing);
			// 	$('em',el.J_modify_password).text('');
			// });

			// $(el.J_cancel_modify_account).on('click',function(ev){
			// 	$(el.J_cancel_modify_account).hide();
			// 	$(el.J_to_modify_account).show();
			// 	$('input' , el.J_modify_account).removeClass(el.J_editing);
			// 	$('input' , el.J_modify_account).attr('readonly','readonly');
			// 	$('input' , el.J_modify_account).val(account);
			// 	$('em' , el.J_modify_account).text('');
			// })
			// $('input' , el.J_modify_name).on('change',function(ev){
			// 	var 
			// 		para = "userName="+$('input' , el.J_modify_name).val();

			// 	userIO.isNameRegistered(para,function(code,data,msg){
			// 		if(code == 0){
			// 			isNameFit = false;
			// 			$('em',el.J_modify_name).text(myvar.nameRegisteredTip);
			// 		}
			// 		if(code == 1){
			// 			isNameFit = true;
			// 			$('em',el.J_modify_name).text(myvar.nameCanUsedTip);
			// 			newName = $('input' , el.J_modify_name).val();
			// 		}
			// 	})
			// });

			// $('input' , el.J_modify_account).on('change',function(ev){
			// 	var
			// 		email = $('input' , el.J_modify_account).val(),
			// 		para = "email="+email;

			// 	if(!emailReg.test(email)){
			// 		$('em',el.J_modify_account).text(myvar.emailFormatTip);
			// 		isAccountFit = false;
			// 	}
			// 	else{
			// 		userIO.isEmailRegistered(para,function(code,data,msg){
			// 			if(code == 0){
			// 				isAccountFit = false;
			// 				$('em',el.J_modify_account).text(myvar.emailRegisteredTip);
			// 			}
			// 			if(code == 1){
			// 				isAccountFit = true;
			// 				$('em',el.J_modify_account).text(myvar.emailCanUsedTip);
			// 			}
			// 		})
			// 	}
			// });

			// $('input' , el.J_modify_password).on('change',function(ev){
			// 	var
			// 		password = $('input' , el.J_modify_password).val();
			// 	if(password.length < 8){
			// 		isPasswordFit = false;
			// 		$('em',el.J_modify_password).text(myvar.passwordLenTip);
			// 	}
			// 	else{
			// 		isPasswordFit = true;
			// 	}
			// })

			// $('input',el.J_new_password).on('change',function(ev){
			// 	var
			// 		newPassword = $('input' , el.J_new_password).val(),
			// 		newCheckPassword = $('input',el.J_new_check_password).val();
			// 	if(newPassword.length < 8){
			// 		isNewPasswordFit = false;
			// 		$('em',el.J_new_password).text(myvar.passwordLenTip);
			// 	}
			// 	else{
			// 		if(newCheckPassword != newPassword){
			// 			isNewPasswordFit = false;
			// 			$('em',el.J_new_check_password).text(myvar.notSameTip)
			// 		}
			// 		else{
			// 			isNewPasswordFit = true;
			// 			isNewCheckFit = true;
			// 			$('em',el.J_new_check_password).text('');
			// 		}
			// 	}
			// });

			// $('input',el.J_new_check_password).on('change',function(ev){
			// 	var
			// 		newPassword = $('input' , el.J_new_password).val(),
			// 		newCheckPassword = $('input',el.J_new_check_password).val();

			// 	if(newCheckPassword != newPassword){
			// 		isNewCheckFit = false;
			// 		$('em',el.J_new_check_password).text(myvar.notSameTip)
			// 	}
			// 	else{
			// 		isNewCheckFit = true;
			// 		isNewPasswordFit = true;
			// 		$('em',el.J_new_check_password).text('');
			// 	}
			// });

			// $('input' , el.J_modify_name).on('click',function(ev){
			// 	$('em',el.J_modify_name).text('');
			// });

			// $('input',el.J_modify_password).on('click',function(ev){
			// 	$('em',el.J_modify_password).text('');
			// });

			// $('input',el.J_modify_account).on('click',function(ev){
			// 	$('em',el.J_modify_account).text('');
			// });

			// $('input',el.J_new_password).on('click',function(ev){
			// 	$('em',el.J_new_password).text('');
			// });

			// $('input',el.J_new_check_password).on('click',function(ev){
			// 	$('em',el.J_new_check_password).text('');
			// });

			// $(el.J_to_submit).on('click',function(ev){
			// 	var
			// 		name = $('input' , el.J_modify_name).val(),
			// 		password = $('input' , el.J_modify_password).val(),
			// 		newPassword = $('input' , el.J_new_password).val(),
			// 		newCheckPassword = $('input' , el.J_new_check_password).val();

			// 	if(name.length == 0){
			// 		isNameFit = false;
			// 		$('em',el.J_modify_name).text(myvar.nameNullTip);
			// 	}

			// 	if(password.length == 0){
			// 		isPasswordFit = false;
			// 		$('em',el.J_modify_password).text(myvar.passwordNullTip);
			// 	}

			// 	if(newPassword.length == 0){
			// 		if(isPasswordModify){
			// 			isNewPasswordFit = false;
			// 			$('em' , el.J_new_password).text(myvar.newPasswordNullTip);
			// 		}
			// 	}

			// 	if(newCheckPassword.length == 0){
			// 		if(isPasswordModify){
			// 			isNewCheckFit = false;
			// 			$('em' , el.J_new_check_password).text(myvar.checkPasswordNullTip);
			// 		}
			// 	}

			// 	if(isNameFit && isPasswordFit && isAccountFit && isNewCheckFit && isNewPasswordFit){
			// 		var
			// 			para = S.io.serialize("#M-form");
			// 		userIO.isModifySuc(para,function(code,data,msg){
			// 			if(code == 0){
			// 				$('h1',el.J_modify_info).text(myvar.modifySuc);
			// 				$('div',el.J_modify_info).hide();
			// 				$(el.J_to_submit).hide();
			// 				$('em' , el.J_modify_info).text('');

			// 				if(data.isNameModified == 0){
			// 					$(el.J_nick_name).text(newName);
			// 				}
			// 			}
			// 			if(code == 1){
			// 				$('h1' , el.J_modify_info).text(myvar.modifyFail);
			// 				$('em' , el.J_modify_info).text('');
			// 			}
			// 		})
			// 	}
			// });


			// $(el.J_to_close).on('click' , function(ev){
			// 	$(el.J_pop).hide();
			// 	$('em' , el.J_modify_info).text('');
			// })
		}
	});

	return modify;
},{
	requires:['core','io/user/user']
})

/*-------------------------------------搜索-------------------------------------*/
KISSY.add('user/search',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		el = {
			J_search_options : '.search-options',
			J_show_options : '.show-options',
			J_preach_search : '.preach-search',
			J_recruit_search : '.recruit-search',
			J_company_search : '.company-search',
			J_graduate_search : '.graduate-search',
			J_search_input : '.search-input',
			J_search_form : '.search-form'
		};

	function search(){
		this.init();
	}

	S.augment(search , {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			$(el.J_show_options).on('click',function(ev){
				$(el.J_search_options).show();
			});

			$(el.J_search_form).on('mouseleave',function(ev){
				$(el.J_search_options).hide();
			})

			$(el.J_preach_search).on('click',function(ev){
				$(el.J_search_input).attr('placeholder','搜索宣讲会');
				$('input[name="preach"]').val(1);
				$('input[name="recruit"]').val(0);
				$('input[name="company"]').val(0);
				$('input[name="graduate"]').val(0);
			});

			$(el.J_recruit_search).on('click',function(ev){
				$(el.J_search_input).attr('placeholder','搜索招聘信息');
				$('input[name="preach"]').val(0);
				$('input[name="recruit"]').val(1);
				$('input[name="company"]').val(0);
				$('input[name="graduate"]').val(0);
			});

			$(el.J_company_search).on('click',function(ev){
				$(el.J_search_input).attr('placeholder','搜索用人单位');
				$('input[name="preach"]').val(0);
				$('input[name="recruit"]').val(0);
				$('input[name="company"]').val(1);
				$('input[name="graduate"]').val(0);
			});

			$(el.J_graduate_search).on('click',function(ev){
				$(el.J_search_input).attr('placeholder','搜索毕业生');
				$('input[name="preach"]').val(0);
				$('input[name="recruit"]').val(0);
				$('input[name="company"]').val(0);
				$('input[name="graduate"]').val(1);
			});

			$(el.J_search_options).on('mouseleave',function(ev){
				$(el.J_search_options).hide();
			})
		}
	})
	return search;
},{
	requires:['core']
})

/*-----------------------------------头部导航栏与求职通头部短信订制-------------------------------*/
KISSY.add('user/nav',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		userIO = PW.io.user.user,
		el = {
			J_recruit_nav : '.recruit-nav',
			J_recruit_type : '.recruit-type',
			J_pop : '.pop',
			J_login : '.login',
			J_register : '.register',
			J_enter_job : '.enter-job',
			J_message_order : '.message-order',
			J_message_pop_layer : '.message-pop-layer',
			J_message_pop : '.message-pop',
			J_sure_message : '.sure-message',
			J_vip_user_tip : '.vip-user-tip',
			J_activate_user_tip : '.activate-user-tip',
			J_position_order : '.position-order'
		};

	function nav(){
		this.init();
	}

	S.augment(nav , {
		init:function(){
			this._addEventListener();
		},

		_addEventListener:function(){
			$(el.J_recruit_nav).on('mouseenter',function(ev){
				$(el.J_recruit_type).show();
			});

			$(el.J_recruit_nav).on('mouseover',function(ev){
				$(el.J_recruit_type).show();
			});

			$(el.J_recruit_type).on('mouseenter',function(ev){
				$(el.J_recruit_type).show();
			});

			$(el.J_recruit_type).on('mouseleave',function(ev){
				$(el.J_recruit_type).hide();
			});

			$(el.J_recruit_nav).on('mouseleave',function(ev){
				$(el.J_recruit_type).hide();
			});

			$(el.J_enter_job).on('click',function(ev){
				userIO.enterJobApply({},function(code,data,msg){
					if(code == 1){
						$(el.J_pop).show();
						$(el.J_login).show();
						$(el.J_register).hide();
					}
					// if(code == 0){
					// 	window.location.her
					// }
				})
			});

			$(el.J_message_order).on('click',function(ev){
				$(el.J_message_pop_layer).show();
				$(el.J_message_pop).show();
				$(el.J_vip_user_tip).show();
			});

			$(el.J_sure_message).on('click',function(ev) {
				$(el.J_message_pop_layer).hide();
				$(el.J_message_pop).hide();
				$(el.J_vip_user_tip).hide();
				$(el.J_activate_user_tip).hide();
			});

			$(el.J_position_order).on('click',function(ev){
				$(el.J_message_pop_layer).show();
				$(el.J_message_pop).show();
				$(el.J_activate_user_tip).show();
			});

			
		}
	})
	return nav;
},{
	requires:['core','io/user/user']
})
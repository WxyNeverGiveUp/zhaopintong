/*-----------------------------------------------------------------------------
* @DescriSion: 修改信息相关js
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.08.02
* ==NOTES:=============================================
* v1.0.0(2015.08.02):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/user_info/modifyInfo' , function(S , modifyInfoShow){
	PW.namespace('page.user_info.modifyInfo');
	PW.page.user_info.modifyInfo = function(){
		new modifyInfoShow();
	}
},{
	requires:['modifyInfo/modifyInfoShow']
})

/*------------------------------------------------------------------------------*/
KISSY.add('modifyInfo/modifyInfoShow' , function(S){
	var
		$ = S.all,
		on = S.Event.on,
		el = {
			J_selected : 'selected',
			J_modify_nav : '.modify-nav',
			J_upload_file : '.upload-file',
			J_upload_tip : '.upload-tip',
			J_submit_img : '.submit-img',
			J_cancel_img : '.cancel-img',
			J_right_detail : '.right-detail',
			J_submit_pass : '.submit-pass',
			J_cancel_pass : '.cancel-pass',
			J_old_password_input : '.old-password-input',
			J_new_password_input : '.new-password-input',
			J_check_password_input : '.check-password-input',
			J_password_tip : '.modi-password-tip'
		};

	function modifyInfoShow(){
		this.init();
	}

	S.augment(modifyInfoShow , {
		init:function(){
			this._addEventLisenter();
			this._currentPosi();
		},

		_addEventLisenter:function(){
			var
				file,
				fileSize,
				filePath,
				suffix;

			$('li',el.J_modify_nav).item(0).addClass(el.J_selected);

			$(el.J_upload_file).on('change',function(){

				file = document.getElementById('img-portrait').files;
				fileSize = file[0].size/1024;
				filePath = $(el.J_upload_file).val();
				suffix = filePath.substring(filePath.length-3 , filePath.length);

				if(suffix != 'jpg' && suffix != 'png' && suffix != 'gif' && suffix != 'jpeg' && suffix != 'PNG' && suffix != 'GIF' && suffix != 'JPEG' && suffix != 'JPG'){
					$(el.J_upload_tip).text('请上传gif,png,jpg格式的照片');
					$(el.J_submit_img).attr('disabled','disabled');
				}
				else if(fileSize > 2560){
					$(el.J_upload_tip).text('文件不得大于2.5M');
					$(el.J_submit_img).attr('disabled','disabled');
				}
				else{
					$(el.J_upload_tip).text('已选择'+filePath);
					$(el.J_submit_img).removeAttr('disabled','disabled');
				}
			});

			$(el.J_submit_img).on('click',function(ev){
				if(filePath == undefined){
					ev.preventDefault();
					$(el.J_upload_tip).text('请选择上传文件');
				}
			});

			$(el.J_cancel_img).on('click',function(ev){
				filePath = undefined;
				$(el.J_upload_file).val('');
				$(el.J_upload_tip).text('');
			});

			$(el.J_submit_pass).on('click',function(ev){
				var
					oldPassword = $(el.J_old_password_input).val(),
					newPassword = $(el.J_new_password_input).val(),
					checkPassword = $(el.J_check_password_input).val();
				if(oldPassword.length < 8 || newPassword.length < 8 || checkPassword.length < 8){
					ev.preventDefault();
					$(el.J_password_tip).text('密码长度不得少于8位');
				}

				else if(newPassword != checkPassword){
					ev.preventDefault();
					$(el.J_password_tip).text('新密码与确认密码不一致');
				}
			})
		},

		_currentPosi:function(){
			$('li',el.J_modify_nav).on('click',function(ev){
				var
					index = $(ev.currentTarget).attr('index');
				$(ev.currentTarget).addClass(el.J_selected);
				$(ev.currentTarget).siblings().removeClass(el.J_selected);
				$(el.J_right_detail).children().item(index-1).show();
				$(el.J_right_detail).children().item(index-1).siblings().hide();
			})
		}
	});

	return modifyInfoShow;
},{
	requires:['event']
})

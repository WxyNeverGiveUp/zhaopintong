/*-----------------------------------------------------------------------------
* @Description:     我的主页中ajax相关js
* @Version:         1.0.0
* @author:          daiqiaoling
* @date             2015.6.23
* ==NOTES:=============================================
* v1.0.0(2015.6.23):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/job_apply/my_home' , function(S){
	var urls;
	try{
		urls = PW.Env.url.job_apply.myHome;
	}catch(e){
		S.log('地址信息错误');
		return;
	}

	PW.namespace('io.job_apply.my_home');
	
	S.mix(PW.io.job_apply.my_home, {
		conn: urls ,
		// 发出生成我的特长的ajax请求
		addSpecialty:function(data,callback){
			S.IO({
				url:urls.addSpecialty,
				type:'get',
				data:data,
				dataType:'json',
				success:function(rs){
					callback(
						rs.code,
						rs.data,
						rs.errMsg
						);
				},
				error:function(rs){
					callback(
						false,
						null,
						PW.Eng.msg[0]
					);
				}
			});
		},
		delSpecialty:function(data,callback){
			S.IO({
				url:urls.delSpecialty,
				type:'get',
				data:data,
				dataType:'json',
				success:function(rs){
					callback(
						rs.code == 0,
						rs.data,
						rs.errMsg
						);
				},
				error:function(rs){
					callback(
						false,
						null,
						PW.Eng.msg[0]
					);
				}
			});
		},
		getCity:function(data,callback){
			S.IO({
				url:urls.getCity,
				type:'get',
				data:data,
				dataType:'json',
				success:function(rs){
					callback(
						rs.code == 0,
						rs.data,
						rs.errMsg
						);
				},
				error:function(rs){
					callback(
						false,
						null,
						PW.Eng.msg[0]
					);
				}
			});
		},
		isEdited:function(data,callback){
			S.IO({
				url:urls.isEdited,
				type:'get',
				data:data,
				dataType:'json',
				success:function(rs){
					callback(
						rs.code == 0,
						rs.data,
						rs.errMsg
						);
				},
				error:function(rs){
					callback(
						false,
						null,
						PW.Eng.msg[0]
					);
				}
			});
		},
		isEduEdited:function(data,callback){
			S.IO({
				url:urls.isEduEdited,
				type:'get',
				data:data,
				dataType:'json',
				success:function(rs){
					callback(
						rs.code == 0,
						rs.data,
						rs.errMsg
						);
				},
				error:function(rs){
					callback(
						false,
						null,
						PW.Eng.msg[0]
					);
				}
			});
		},
		getMajor:function(data,callback){
			S.IO({
				url:urls.getMajor,
				type:'get',
				data:data,
				dataType:'json',
				success:function(rs){
					callback(
						rs.code == 0,
						rs.data,
						rs.errMsg
						);
				},
				error:function(rs){
					callback(
						false,
						null,
						PW.Eng.msg[0]
					);
				}
			});
		},
		putValidCode:function(data,callback){
			S.IO({
				url:urls.putValidCode,
				type:'get',
				data:data,
				dataType:'json',
				success:function(rs){
					callback(
						rs.code,
						rs.data,
						rs.errMsg
						);
				},
				error:function(rs){
					callback(
						false,
						null,
						PW.Eng.msg[0]
					);
				}
			});
		}
	})
})
/*-----------------------------------------------------------------------------
* @Description:     preach页面ajax相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.6.30
* ==NOTES:=============================================
* v1.0.0(2015.6.30):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/job_apply/signup', function(S){
	var urls;
	try{
		urls = PW.Env.url.job_apply.signup;
	}catch(e){
		S.log('地址信息错误');
		return;
	}

	PW.namespace('io.job_apply.signup');
	S.mix(PW.io.job_apply.signup , {
		conn: urls,
		signupIO:function(data,callback){
			S.IO({
				url:urls.signup,
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
		}
	})
})
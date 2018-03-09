/*-----------------------------------------------------------------------------
* @Description:     change-phone页面ajax
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.08.01
* ==NOTES:=============================================
* v1.0.0(2015.08.01):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/job_apply/phone' , function(S){
	var urls;
	try{
		urls = PW.Env.url.job_apply.phone;
	}catch(e){
		S.log('地址信息错误');
		return;
	}

	PW.namespace('io.job_apply.phone');
	
	S.mix(PW.io.job_apply.phone, {
		conn: urls ,
		// 发出生成我的特长的ajax请求
		delPhone:function(data,callback){
			S.IO({
				url:urls.delPhone,
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
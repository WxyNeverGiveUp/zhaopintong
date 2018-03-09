/*-----------------------------------------------------------------------------
* @Description:     company页面ajax相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.6.17
* ==NOTES:=============================================
* v1.0.0(2015.6.17):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/job_apply/job_subscription' , function(S){
	var urls;
	try{
		urls = PW.Env.url.job_apply.jobSubscription;
	}catch(e){
		S.log('地址信息错误');
		return;
	}

	PW.namespace('io.job_apply.jobSubscription');
	S.mix(PW.io.job_apply.jobSubscription , {
		conn: urls ,
		createLiIO:function(data,callback){
			S.IO({
				url:urls.createLi,
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
		createSelectIO:function(data,callback){
			S.IO({
				url:urls.createSelect,
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
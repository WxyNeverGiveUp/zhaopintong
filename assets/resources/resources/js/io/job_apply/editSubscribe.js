/*-----------------------------------------------------------------------------
* @Description:     编辑订阅ajax
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.7.31
* ==NOTES:=============================================
* v1.0.0(2015.7.31):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/job_apply/editSubscribe' , function(S){
	var urls;
	try{
		urls = PW.Env.url.job_apply.editSubscribe;
	}catch(e){
		S.log('地址信息错误');
		return;
	}

	PW.namespace('io.job_apply.editSubscribe');
	
	S.mix(PW.io.job_apply.editSubscribe, {
		conn: urls ,
		// 发出生成我的特长的ajax请求
		delSub:function(data,callback){
			S.IO({
				url:urls.delSub,
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
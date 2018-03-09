/*-----------------------------------------------------------------------------
* @Description:     comment页面相关ajax
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.07
* ==NOTES:=============================================
* v1.0.0(2015.07.07):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/company/comment',function(S){
	var urls;
	try{
		urls=PW.Env.url.company.comment;
	}catch(e){
		S.log('地址信息错误');
		return;
	}

	PW.namespace('io.company.comment');
	S.mix(PW.io.company.comment ,{
		conn: urls ,
		getPraiseNum:function(data,callback){
			S.IO({
				url:urls.getPraiseNum,
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
			})
		}
	})
})
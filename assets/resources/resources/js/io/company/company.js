/*-----------------------------------------------------------------------------
* @Description:     company页面ajax相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.6.17
* ==NOTES:=============================================
* v1.0.0(2015.6.17):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/company/company' , function(S){
	var urls;
	try{
		urls = PW.Env.url.company.company;
	}catch(e){
		S.log('地址信息错误');
		return;
	}

	PW.namespace('io.company.company');
	S.mix(PW.io.company.company , {
		conn: urls ,
		getFollowNumber:function(data,callback){
			S.IO({
				url:urls.getFollowNumber,
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
		createLocationIO:function(data,callback){
			S.IO({
				url:urls.createLocationLi,
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
		createIndustryIO:function(data,callback){
			S.IO({
				url:urls.createIndustryLi,
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
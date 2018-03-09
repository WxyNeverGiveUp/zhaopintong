/*-----------------------------------------------------------------------------
* @Description:     preach页面ajax相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.6.30
* ==NOTES:=============================================
* v1.0.0(2015.6.30):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/preach/preach' , function(S){
	var urls;
	try{
		urls = PW.Env.url.preach.preach;
	}catch(e){
		S.log('地址信息错误');
		return;
	}

	PW.namespace('io.preach.preach');
	S.mix(PW.io.preach.preach , {
		conn: urls ,
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
		},
		enroll:function(data,callback){
			S.IO({
				url:urls.enroll,
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
		isRightTime:function(data,callback){
			S.IO({
				url:urls.isRightTime,
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
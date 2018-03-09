/*-----------------------------------------------------------------------------
* @Description:     收藏职位页面ajax相关js
* @Version:         1.0.0
* @author:          daiqiaoling
* @date             2015.7.13
* ==NOTES:=============================================
* v1.0.0(2015.6.23):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/job_apply/collect' , function(S){
	var urls;
	try{
		urls = PW.Env.url.job_apply.collect;
	}catch(e){
		S.log('地址信息错误');
		return;
	}

	PW.namespace('io.job_apply.collect');
	
	S.mix(PW.io.job_apply.collect, {
		conn: urls ,
		// 发出生成我的特长的ajax请求
		collectIO:function(data,callback){
			S.IO({
				url:urls.collect,
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

		//发出生成公司信息及关注
		getCompanylist:function(data,callback){
			S.IO({
				url:urls.getCompanylist,
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

		isCollect:function(data,callback){
			S.IO({
				url:urls.isCollect,
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
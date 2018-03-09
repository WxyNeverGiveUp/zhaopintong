/*-----------------------------------------------------------------------------
* @Description:     job-detail页面ajax请求
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.03
* ==NOTES:=============================================
* v1.0.0(2015.07.03):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/company/jobDetail',function(S){
	var urls;
	try {
		urls = PW.Env.url.company.jobDetail;
	}catch(e){
		S.log('地址信息错误');
		return;
	}

	PW.namespace('io.company.jobDetail');

	S.mix(PW.io.company.jobDetail,{
		conn:urls,
		getFollowNumberIO:function(data,callback){
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
			})
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
			})
		},
        getDay:function(data,callback){
            S.IO({
                url:urls.getDay,
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
            })
        }
	})
})
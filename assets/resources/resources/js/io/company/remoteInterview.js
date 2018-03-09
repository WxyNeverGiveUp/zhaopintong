/*-----------------------------------------------------------------------------
* @Description:     remote-interview页面ajax
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.04
* ==NOTES:=============================================
* v1.0.0(2015.07.04):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/company/remoteInterview',function(S){
	var urls;
	try {
		urls = PW.Env.url.company.remoteInterview;
	}catch(e){
		S.log('地址信息错误');
		return;
	}

	PW.namespace('io.company.remoteInterview');

	S.mix(PW.io.company.remoteInterview,{
		conn:urls,
		isEnroll:function(data,callback){
			S.IO({
				url:urls.isEnroll,
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
            })
        }
	})
})
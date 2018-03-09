/*-----------------------------------------------------------------------------
* @Description:     招聘信息部分的ajax相关的js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.22
* ==NOTES:=============================================
* v1.0.0(2014.9.16):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/recruit/recruit',function(S){
	var urls;
    try{
        urls = PW.Env.url.recruit.recruit;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.recruit.recruit');
    S.mix(PW.io.recruit.recruit,{
        conn: urls,
        valid: function(data, callback){
            S.IO({ 
                url: urls.valid,
                type: 'GET',
                data: data,
                dataType: 'json',
                success: function(rs){
                    callback(
                        rs.code == 0,
                        rs.data,
                        rs.errMsg
                    );
                },
                error: function(err){
                    callback(
                        false,
                        null,
                        PW.Env.msg[0]
                    );
                }
            });
        },
        delBatch: function(data, callback){
            S.IO({ 
                url: urls.delBatch,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(rs){
                    callback(
                        rs.code == 0,
                        rs.data,
                        rs.errMsg
                    );
                },
                error: function(err){
                    callback(
                        false,
                        null,
                        PW.Env.msg[0]
                    );
                }
            });
        },
        enter: function(data, callback){
            S.IO({ 
                url: urls.enter,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(rs){
                    callback(
                        rs.code == 0,
                        rs.data,
                        rs.errMsg
                    );
                },
                error: function(err){
                    callback(
                        false,
                        null,
                        PW.Env.msg[0]
                    );
                }
            });
        },
        getCity: function(data, callback){
            S.IO({ 
                url: urls.getCity,
                type: 'get',
                data: data,
                dataType: 'json',
                success: function(rs){
                    callback(
                        rs.code == 0,
                        rs.data,
                        rs.errMsg
                    );
                },
                error: function(err){
                    callback(
                        false,
                        null,
                        PW.Env.msg[0]
                    );
                }
            });
        }
    });
},{
	requires:['mod/ext']
});
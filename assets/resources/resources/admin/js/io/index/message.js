/*-----------------------------------------------------------------------------
* @Description:     index部分-消息列表页相关ajax(message.js)
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.10.31
* ==NOTES:=============================================
* v1.0.0(2014.10.31):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/index/message',function(S){
	var urls;
    try{
        urls = PW.Env.url.Index.message;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.indexer.message');
    S.mix(PW.io.indexer.message,{
        conn: urls,
        del: function(data, callback){
            S.IO({ 
                url: urls.del,
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
        handle: function(data, callback){
            S.IO({ 
                url: urls.handle,
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
        get: function(data, callback){
            S.IO({
                url: urls.get,
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
        send: function(data, callback){
            S.IO({
                url: urls.send,
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
        }
    });
},{
	requires:['mod/ext']
});
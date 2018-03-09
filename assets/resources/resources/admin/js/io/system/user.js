/*-----------------------------------------------------------------------------
* @Description:     宣讲会部分的ajax相关的js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.28
* ==NOTES:=============================================
* v1.0.0(2014.9.28):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/system/user',function(S){
	var urls;
    try{
        urls = PW.Env.url.system.user;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.system.user');
    S.mix(PW.io.system.user,{
        conn: urls,
        lock: function(data, callback){
            S.IO({ 
                url: urls.lock,
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
        unlock: function(data, callback){
            S.IO({ 
                url: urls.unlock,
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
        delBatch:function(data, callback){
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
        //添加用户时，用户名验重ajax
        addValid:function(data, callback){
            S.IO({ 
                url: urls.addValid,
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
        //修改用户时，用户名验重ajax
        modValid:function(data, callback){
            S.IO({ 
                url: urls.modValid,
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
        }
    });
},{
	requires:['mod/ext']
});
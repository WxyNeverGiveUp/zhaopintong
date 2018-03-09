/*-----------------------------------------------------------------------------
* @Description:     地点部分的ajax相关的js
* @Version:         1.0.0
* @author:          chenke(396985267@qq.com)
* @date             2014.10.01
* ==NOTES:=============================================
* v1.0.0(2014.10.01):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/system/place',function(S){
	var urls;
    try{
        urls = PW.Env.url.system.place;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.system.place');
    S.mix(PW.io.system.place,{
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
        nameValid: function(data, callback){
            S.IO({ 
                url: urls.nameValid,
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
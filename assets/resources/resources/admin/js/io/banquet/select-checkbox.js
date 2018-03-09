/*-----------------------------------------------------------------------------
* @Description:     招聘信息中需求信息部分的ajax相关的js
* @Version:         1.0.0
* @author:          daiql(1649500603@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/banquet/select-checkbox',function(S){
	var urls;
    try{
        urls = PW.Env.url.banquet.select;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.banquet.select');
    S.mix(PW.io.banquet.select,{
        conn: urls,
        attendee: function(data, callback){
            S.IO({ 
                url: urls.attendee,
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
        contact: function(data, callback){
            S.IO({ 
                url: urls.contact,
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
        getUser: function(data, callback){
            S.IO({ 
                url: urls.getUser,
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
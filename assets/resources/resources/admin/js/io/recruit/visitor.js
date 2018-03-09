/*-----------------------------------------------------------------------------
* @Description:     招聘信息中来访人信息部分的ajax相关的js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.16
* ==NOTES:=============================================
* v1.0.0(2014.9.16):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/recruit/visitor',function(S){
	var urls;
    try{
        urls = PW.Env.url.recruit.visitor;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.recruit.visitor');
    S.mix(PW.io.recruit.visitor,{
        conn: urls,
        add: function(data, callback){
            S.IO({ 
                url: urls.add,
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
        del: function(data, callback){
            S.IO({ 
                url: urls.del+'/'+data.id+'/del',
                type: 'DELETE',
                data: {},
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
        getContact: function(data, callback){
            S.IO({ 
                url: urls.getContact,
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
        getLeader:function(data,callback){
            S.IO({ 
                url: urls.getLeader,
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
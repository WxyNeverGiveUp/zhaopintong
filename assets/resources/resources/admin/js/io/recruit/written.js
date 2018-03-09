/*-----------------------------------------------------------------------------
* @Description:     招聘信息中笔试信息部分的ajax相关的js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.23
* ==NOTES:=============================================
* v1.0.0(2014.9.16):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/recruit/written',function(S){
	var urls;
    try{
        urls = PW.Env.url.recruit.written;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.recruit.written');
    S.mix(PW.io.recruit.written,{
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
        del: function(data, callback){
            S.IO({ 
                url: urls.del+'/'+data.id+'/written/del',
                type: 'DELET',
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
        }
    });
},{
	requires:['mod/ext']
});
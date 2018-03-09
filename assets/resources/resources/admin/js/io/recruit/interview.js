/*-----------------------------------------------------------------------------
* @Description:     招聘信息中面试信息部分的ajax相关的js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.23
* ==NOTES:=============================================
* v1.0.0(2014.9.23):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/recruit/interview',function(S){
	var urls;
    try{
        urls = PW.Env.url.recruit.interview;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.recruit.interview');
    S.mix(PW.io.recruit.interview,{
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
                url: urls.del+'/'+data.id+'/interview/del',
                type: 'DELET',
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
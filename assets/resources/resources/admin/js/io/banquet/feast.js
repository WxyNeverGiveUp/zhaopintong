/*-----------------------------------------------------------------------------
* @Description:     招聘信息中需求信息部分的ajax相关的js
* @Version:         1.0.0
* @author:          daiql(1649500603@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/banquet/feast',function(S){
	var urls;
    try{
        urls = PW.Env.url.banquet.feast;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.banquet.feast');
    S.mix(PW.io.banquet.feast,{
        conn: urls,
        communication: function(data, callback){
            S.IO({ 
                url: urls.communication,
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
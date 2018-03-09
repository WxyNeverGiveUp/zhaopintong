/*-----------------------------------------------------------------------------
* @Description:     场地部分的ajax相关的js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/recruit/space',function(S){
	var urls;
    try{
        urls = PW.Env.url.recruit.space;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.recruit.space');
    S.mix(PW.io.recruit.space,{
        conn: urls,
        load: function(data, callback){
            S.IO({ 
                url: urls.load,
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
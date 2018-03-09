/*-----------------------------------------------------------------------------
* @Description:     首页部分的ajax相关的js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/index/index',function(S){
	var urls;
    try{
        urls = PW.Env.url.Index;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.Index.Index');
    S.mix(PW.io.Index.Index,{
        conn: urls,
        getInfo: function(data, callback){
            S.IO({ 
                url: urls.getInfo,
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
        getLectureDate: function(data, callback){
            S.IO({ 
                url: urls.getLectureDate,
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
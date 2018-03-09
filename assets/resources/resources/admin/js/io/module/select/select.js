/*-----------------------------------------------------------------------------
* @Description:     弹出层下来列表部分相关ajax
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.18
* ==NOTES:=============================================
* v1.0.0(2014.9.18):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/module/select/select',function(S){
	var urls;
    try{
        urls = PW.Env.url.module.select;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.module.select');
    S.mix(PW.io.module.select,{
        conn: urls,
        getOption: function(data, callback){
            S.IO({ 
                url: urls.getOption,
                type: 'get',
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
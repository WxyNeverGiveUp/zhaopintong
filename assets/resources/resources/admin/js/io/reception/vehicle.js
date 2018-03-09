/*-----------------------------------------------------------------------------
* @Description:     车辆管理部分的ajax相关的js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/reception/vehicle',function(S){
	var urls;
    try{
        urls = PW.Env.url.reception.vehicle;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.reception.vehicle');
    S.mix(PW.io.reception.vehicle,{
        conn: urls,
        delBatch: function(data, callback){
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
        }
    });
},{
	requires:['mod/ext']
});
/*-----------------------------------------------------------------------------
* @Description:     companyPreach页面ajax请求
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.07.03
* ==NOTES:=============================================
* v1.0.0(2015.07.03):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/company/companyPreach',function(S){
    var urls;
    try {
        urls = PW.Env.url.company.companyPreach;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.company.companyPreach');

    S.mix(PW.io.company.companyPreach,{
        isEnroll:function(data,callback){
            S.IO({
                url:urls.isEnroll,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                        rs.code,
                        rs.data,
                        rs.errMsg
                        );
                },
                error:function(rs){
                    callback(
                        false,
                        null,
                        PW.Eng.msg[0]
                    );
                }
            })
        }
    })
})
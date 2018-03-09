/*-----------------------------------------------------------------------------
* @Description:     省市联动部分相关ajax
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.16
* ==NOTES:=============================================
* v1.0.0(2014.9.16):
     初始生成
* ---------------------------------------------------------------------------
* v2.0.0(2015.5.28):
* @author:          xuyihong(597262617@qq.com)
* 添加学科-专业相关联动ajax
* ---------------------------------------------------------------------------*/
KISSY.add('io/module/linkage/linkage',function(S){
    var urls;
    try{
        urls = PW.Env.url.module.linkage;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.module.linkage');
    S.mix(PW.io.module.linkage,{
        conn: urls,
        getProvice: function(data, callback){
            S.IO({ 
                url: urls.getProvice,
                type: 'GET',
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
        getCity: function(data, callback){
            S.IO({ 
                // url: urls.getCity+'/'+data.provinceId,
                url: urls.getCity,
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
        getCityEmp: function(data, callback){
            S.IO({ 
                url: urls.getCityEmp+'/'+data.provinceId,
                // url: urls.getCityEmp,
                type: 'GET',
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
        getMajor: function(dataId,dataName, callback){
            S.IO({ 
                url: urls.getMajor+'/'+dataId.id+'&'+dataName.name,
                // url: urls.getMajor,
                type: 'GET',
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
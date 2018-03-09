/*-----------------------------------------------------------------------------
* @Description: 单位联系人ajax操作 (companyInfo.js)
* @Version: 	V1.0.0
* @author: 		chenke(396985267@qq.com)
* @date			2014.09.16
* ==NOTES:=============================================
* v1.0.0(2014.09.16):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('io/company/companyInfo', function(S){
	var urls;
    try{
        urls = PW.Env.url.company.companyInfo;
    }catch(e){
        S.log('地址信息错误');
        return;
    } 

    PW.namespace('io.company.companyInfo');
    S.mix(PW.io.company.companyInfo,{
        conn: urls,
        //添加联系人
        addConnecter: function(data, callback){
            S.IO({ 
                url: urls.addConnecter,
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
        //删除联系人
        delConnecter: function(data, callback){
            S.IO({ 
                url: urls.delConnecter+'/'+data.id+'/del',
                type: 'DELETE',
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
        delBatch:function(data,callback){
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
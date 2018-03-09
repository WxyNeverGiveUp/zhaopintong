/*-----------------------------------------------------------------------------
* @Description: 联系人信息ajax操作 (contact.js)
* @Version: 	V1.0.0
* @author: 		chenke(396985267@qq.com)
* @date			2014.09.24
* ==NOTES:=============================================
* v1.0.0(2014.09.24):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('io/company/contact', function(S){
	var urls;
    try{
        urls = PW.Env.url.company.contact;
    }catch(e){
        S.log('地址信息错误');
        return;
    } 

    PW.namespace('io.company.contact');
    S.mix(PW.io.company.contact,{
        conn: urls,
        //添加联系人
        add: function(data, callback){
            S.IO({ 
                url: urls.add,
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
        del: function(data, callback){
            S.IO({ 
                url: urls.del,
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
        //修改联系人
        mod: function(data, callback){
            S.IO({ 
                url: urls.mod,
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
        //获取联系人
        getContact: function(data, callback){
            S.IO({ 
                url: urls.getContact,
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
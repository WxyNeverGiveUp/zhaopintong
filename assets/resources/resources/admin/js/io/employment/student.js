/*-----------------------------------------------------------------------------
* @Description: 联系人信息ajax操作 (contact.js)
* @Version: 	V1.0.0
* @author: 		chenke(396985267@qq.com)
* @date			2014.09.24
* ==NOTES:=============================================
* v1.0.0(2014.09.24):
* 	初始生成 
* ---------------------------------------------------------------------------
* v2.0.0(2014.05.20)
* @author:        xuyihong(597262617@qq.com)
* 添加 del searchStruct searchOrderbyStruct
*---------------------------------------------------------------------------------*/
KISSY.add('io/employment/student', function(S){
	var urls;
    try{
        urls = PW.Env.url.employment.student;
    }catch(e){
        S.log('地址信息错误');
        return;
    } 

    PW.namespace('io.employment.student');
    S.mix(PW.io.employment.student,{
        conn: urls,
        //添加学生
        add: function(data, callback){
            S.IO({ 
                url: urls.add,
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
        },
        del:function(data,callback){
            S.IO({
                url:urls.del,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                       rs.code == 0,
                       rs.data,
                       rs.errMsg );//错误提示
                },
                error:function(err){
                    callback(
                        false,
                        null,
                        PW.Env.msg[0]
                        );
                }
            });
        },
        searchStruct: function(data, callback){
            S.IO({
                url:urls.searchStruct,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                       rs.code == 0,
                       rs.data,
                       rs.errMsg );
                },
                error:function(err){
                    callback(
                        false,
                        null,
                        PW.Env.msg[0]
                        );
                }
            });
        },
        searchOrderbyStruct: function(data, callback){
            S.IO({
                url:urls.searchOrderbyStruct,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                       rs.code == 0,
                       rs.data,
                       rs.errMsg );
                },
                error:function(err){
                    callback(
                        false,
                        null,
                        PW.Env.msg[0]
                        );
                }
            });
        },
        searchRate: function(data, callback){
            S.IO({
                url:urls.searchRate,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                       rs.code == 0,
                       rs.data,
                       rs.errMsg );
                },
                error:function(err){
                    callback(
                        false,
                        null,
                        PW.Env.msg[0]
                        );
                }
            });
        },
        searchOrderbyRate: function(data, callback){
            S.IO({
                url:urls.searchOrderbyRate,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                       rs.code == 0,
                       rs.data,
                       rs.errMsg );
                },
                error:function(err){
                    callback(
                        false,
                        null,
                        PW.Env.msg[0]
                        );
                }
            });
        },
        searchRateNi: function(data, callback){
            S.IO({
                url:urls.searchRateNi,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                       rs.code == 0,
                       rs.data,
                       rs.errMsg );
                },
                error:function(err){
                    callback(
                        false,
                        null,
                        PW.Env.msg[0]
                        );
                }
            });
        },
        searchOrderbyRateNi: function(data, callback){
            S.IO({
                url:urls.searchOrderbyRateNi,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                       rs.code == 0,
                       rs.data,
                       rs.errMsg );
                },
                error:function(err){
                    callback(
                        false,
                        null,
                        PW.Env.msg[0]
                        );
                }
            });
        },
        searchStructEmp: function(data, callback){
            S.IO({
                url:urls.searchStructEmp,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                       rs.code == 0,
                       rs.data,
                       rs.errMsg );
                },
                error:function(err){
                    callback(
                        false,
                        null,
                        PW.Env.msg[0]
                        );
                }
            });
        },
        searchOrderbyStructEmp: function(data, callback){
            S.IO({
                url:urls.searchOrderbyStructEmp,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                       rs.code == 0,
                       rs.data,
                       rs.errMsg );
                },
                error:function(err){
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
/*-----------------------------------------------------------------------------
* @Description:     宣讲会部分的ajax相关的js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/recruit/lecture',function(S){
	var urls;
    try{
        urls = PW.Env.url.recruit.lecture;
    }catch(e){
        S.log('地址信息错误');
        return;
    }

    PW.namespace('io.recruit.lecture');
    S.mix(PW.io.recruit.lecture,{
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
        },
        //添加形象大使
        add:function(data,callback){
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
        /*添加来访人*/
        addVisitor:function(data,callback){
            S.IO({ 
                url: urls.addVisitor,
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
        /*修改来访人*/
        editVisitor:function(data,callback){
            S.IO({ 
                url: urls.editVisitor,
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
        /*删除来访人*/
        delVisitor:function(data,callback){
            S.IO({ 
                // url: urls.delVisitor,
                url: urls.delVisitor+'/'+data.id,
                type: 'DELETE',
                // data:data,
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
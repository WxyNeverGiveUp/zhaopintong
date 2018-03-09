/*-----------------------------------------------------------------------------
* @Description: graduate页面ajax操作 (graduate.js)
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.07.02
* ==NOTES:=============================================
* v1.0.0(2015.07.02):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('io/graduate/graduate',function(S){
	var urls;
	try{
		urls = PW.Env.url.graduate.graduate;
	}catch(e){
		S.log('地址信息错误');
        return;
	}

	PW.namespace('io.graduate.graduate');
	S.mix(PW.io.graduate.graduate,{
		conn:urls,
		createSchoolIO:function(data,callback){
            S.IO({
                url:urls.createSchoolLi,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                        rs.code == 0,
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
            });
        },
        createMajorIO:function(data,callback){
            S.IO({
                url:urls.createMajorLi,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                        rs.code == 0,
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
            });
        },
        createIndustryIO:function(data,callback){
            S.IO({
                url:urls.createIndustryLi,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                        rs.code == 0,
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
            });
        },
        createLocationIO:function(data,callback){
            S.IO({
                url:urls.createLocationLi,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                        rs.code == 0,
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
            });
        },
        getSearchNumberIO:function(data,callback){
            S.IO({
                url:urls.getSearchNumber,
                type:'get',
                data:data,
                dataType:'json',
                success:function(rs){
                    callback(
                        rs.code == 0,
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
            });
        }
	});
},{
	requires:['mod/ext']
});
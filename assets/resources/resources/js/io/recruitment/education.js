/*-----------------------------------------------------------------------------
* @Description: education页面ajax操作 (education.js)
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.06.17
* ==NOTES:=============================================
* v1.0.0(2015.06.17):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('io/recruitment/education',function(S){
	var urls;
	try{
		urls = PW.Env.url.recruitment.education;
	}catch(e){
		S.log('地址信息错误');
        return;
	}

	PW.namespace('io.recruitment.education');
	S.mix(PW.io.recruitment.education,{
		conn:urls,
		createPositionTypeIO:function(data,callback){
            S.IO({
                url:urls.createPositionTypeLi,
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
        createPropertyIO:function(data,callback){
            S.IO({
                url:urls.createPropertyLi,
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
        isCollectIO:function(data,callback){
            S.IO({
                url:urls.isCollect,
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
            });
        }
	});
},{
	requires:['mod/ext']
});
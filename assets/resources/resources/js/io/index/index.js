/*-----------------------------------------------------------------------------
* @DescriSion: index页面相关ajax
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.07.11
* ==NOTES:=============================================
* v1.0.0(2015.07.11):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('io/index/index',function(S){
	var urls;
	try {
		urls = PW.Env.url.index.index;
	}catch(e){
		S.log("地址信息错误");
		return;
	}

	PW.namespace('io.index.index');
	S.mix(PW.io.index.index,{
		conn:urls,
		getPreachDayIO:function(data,callback){
			S.IO({
                url:urls.getPreachDay,
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
        getCity:function(data,callback){
            S.IO({
                url:urls.getCity,
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
	})
})
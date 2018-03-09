/*-----------------------------------------------------------------------------
* @Description:     preachCalendar页面ajax相关js
* @Version:         1.0.0
* @author:          zhaokaikang
* @date             2015.8.6
* ==NOTES:=============================================
* v1.0.0(2015.8.6):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('io/preach/preachCalendar' , function(S){
	var urls;
	try{
		urls = PW.Env.url.preach.preachCalendar;
	}catch(e){
		S.log('地址信息错误');
		return;
	}

	PW.namespace('io.preach.preachCalendar');
	S.mix(PW.io.preach.preachCalendar , {
		conn: urls ,
		getPreachDay:function(data,callback){
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
		}
	})
})
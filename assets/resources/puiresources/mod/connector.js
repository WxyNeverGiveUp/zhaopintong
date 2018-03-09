 /*-----------------------------------------------------------------------------
* @Description: 接口同意管理插件 (connector.js)
* @Version: 	V1.0.0
* @author: 		simon(406400939@qq.com)
* @date			2013.06.20
* ==NOTES:=============================================
* v1.0.0(2013.06.20):
* 	目前设计的格式为：
* 		conn
* 		  |---module1
*         |	    |--interface11 : [url, type, comment] or {url: u, type: t, comment: '-'}
*     	  |		|--interface12 : [url, type, comment] or {url: u, type: t, comment: '-'}
* 		  |---module2
*         |	    |--interface21 : [url, type, comment] or {url: u, type: t, comment: '-'}
*     	  |		|--interface22 : [url, type, comment] or {url: u, type: t, comment: '-'}
*     	  |---module3
*         |	    |--interface31 : [url, type, comment] or {url: u, type: t, comment: '-'}
*     	  |		|--interface32 : [url, type, comment] or {url: u, type: t, comment: '-'}
*    需要注意的是所有接口都是一次性生成的，没有后期增添的说法,
*    示例：
*	conn = PW.mod.Connector({
*		login: [
*			['isLogin', 'http://xx/isLogin', 'get', ''],
*			['getValidCode', 'http://xx/getValidCode', 'post', ''],
*			['match2', 'http://xx/match', 'post', ''],
*		],
*		pay: [
*			['getAddress','http://xx/getAddress','get','']
*		]
*	});
*	v1.0.1(2013-09-25):
*		添加配置属性，在最后添加一个提示信息，如果提示存在，那么认为是
*		需要遮罩的提示信息，用S.once处理
* ---------------------------------------------------------------------------*/

KISSY.add('mod/connector', function(S,Connector){
	PW.namespace('mod.Connector');
	PW.mod.Connector = function(param){
		return new Connector(param);
	}
},{
	requires:[
		'connector/core'
	]
});

KISSY.add('connector/core', function(S,Module){
	var
		CONN_CONFIG = {
			url: '',
			type: 'post',
			comment: ''
		}
	function Connector(table){
		this._init(table);
	}

	S.augment(Connector, {
		_init: function(table){
			var
				that = this;
			for(var p in table){
				 if(!S.isArray(table[p])) continue;
				 var o = {}; 
				 o[p] = new Module(p, table[p]);
				 S.mix(this, o);
			}
		}
	});

	return Connector;
},{
	requires:[
		'connector/module',
		'core'
	]
})



KISSY.add('connector/module', function(S, Connection){

	var
		IO = S.IO;

	function Module(mn, list){
		this.name = mn;
		this._init(list);
	}

	S.augment(Module, {
		_init: function(list){
			var that = this;
			S.each(list, function(conn){
				if(S.isString(conn[0]) && conn[0] != '' && S.isString(conn[1]) && conn[1] != ''){
					switch(conn[2].toLowerCase()){
						case 'post':
						case 'delete':
						case 'put':
							conn[2] = conn[2].toLowerCase();
							break;
						default:
							conn[2] = 'get';
					}
					var  o = {};
					o[conn[0]] = new Connection(conn); 
					S.mix(that, o);
				}
			})
		}
	})
	return Module;
},{
	requires:[
		'connector/connection'
	]
})


KISSY.add('connector/connection', function(S){

	var 
		IO  = S.IO, JSON = S.JSON, Juicer = S.juicer,
		CONNECTOR_SETTING = PW.Env.modSettings.connector;
	var Connection = function(connData){
		this.name = connData[0];
		this.url = connData[1];
		this.type = connData[2];
		this.comment = connData[3];
		this.loadingTip = connData[4] || '';
	}

	S.augment(Connection, {
		send: function(data, callback){
			var that = this,
				fullPath,
				ajaxSettings = {};

			//获取完整路径
			fullPath = that._getCompleteUrl(data);
			//配置ajax参数
			ajaxSettings = {
				url : fullPath,
				type: this.type,
				data: data,
				success: function(rs){
					if(S.isString(rs)) rs = JSON.parse(rs);
					callback(rs);
				},
				error: function(err){
					callback({
						code: -1,
						errorMsg: err
					})
				}
			}
			//notice: 
			//此处是针对重复验证做的处理，如果在配置中设置了提示信息，
			//那么认为是要阻止重复点击提交的，会生成遮罩
			if(!that.loadingTip){
				IO(ajaxSettings)	
			}else{
				S.once(S.mix({
					hint: that.loadingTip
				}, ajaxSettings));					
			}
		},
		/**
		 * 获取完整的ajax路径
		 * 因为有测试模式和布署模式，所以url会有不同
		 * @param {obj} data 用户传入参数
		 * @return {String} fullpath
		 */
		_getCompleteUrl: function(data){
			var
				that = this,
				path,
				prefix;
			path = Juicer(that.url, data);
			prefix = that.getConnPrefix();
			return (prefix + path);
		},
		/**
		 * 获取当连接的地址前缀
		 * @return {String} 当前配置的数据地址前缀
		 */
		getConnPrefix: function(){
			var
				that = this;
			return (!CONNECTOR_SETTING.debug)?
						CONNECTOR_SETTING.deployUrlPrefix:
						CONNECTOR_SETTING.debugUrlPrefix;
		}
	});
	return Connection;
},{
	requires: ['core','mod/juicer', 'mod/once']
})
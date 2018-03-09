/*-----------------------------------------------------------------------------
* @Description: 分页组件 (pagination.js)
* @Version: 	V1.0.0
* @author: 		huanhuan(965788210@qq.com)
* @date			2013.08.10
* ==NOTES:=============================================
* v1.0.0(2013.08.10):
* 	进行整合：
* v1.0.1(2013-10-08):
*     modified by Simon:
*     在beforeDataLoad函数执行的时候，获取其返回值， 如果为false，则丢弃本次分页数据
* ---------------------------------------------------------------------------*/
	
KISSY.add('mod/pagination',function(S,pagination){
    
    PW.pagination = function(param){
        return new pagination(param);
    }
    
},{requires:['mod/pagination/core']});
/*-----------------------------------------------------------------------------
* @Description: 分页 (core.js)
* @Version:     V1.0.0
* @author:         huanhuan(965788210@qq.com)
* @date            2012.9.26
* ==NOTES:=============================================
* v1.0.0(2012.9.26):
*     初始生成 
* v1.0.1(2012.12.10)
* ---------------------------------------------------------------------------*/
    
KISSY.add('mod/pagination/core', function(S,a,juicer,j,Pctrl) {
    var 
        DOM = S.DOM, get = DOM.get, query = DOM.query, JSON = S.JSON, on = S.Event.on, IO = S.IO,
        EMPTY_FUNCTION = function(){},
        DEFAULT_THEME_PKG = {
            showCounter: true,//定义是否显示分页中的计数部分
            themeCounter: '{page}/{pageCount}',//计数部分样式
            showJump: true,//定义是否显示分页中的点击按钮跳转部分
            themeJump: '跳到第{input}页{button}',
            showPageNum: true,//定义是否只需要展示上一页，下一页部分
            showFirst: false,
            showLast: false,
            themeCss: 'J_pagination_theme1',
            firstPageTip: '<<',
            lastPageTip: '>>',
            prevPageTip:'上一页',
            nextPageTip:'下一页'
        },
        config = {
	        renderTo:'',
	        juicerRender:'',
	        dataRender:'#J_template',
	        page: 1,//定义当前页
	        pageSize: 10000,//定义每页显示的记录数
	        dataCount: 0,//定义总记录数
	        pageCount: 0,
            themePackageURL: '',//主题包访问地址
            themeName: 'default',
	        type: 'get',
	        url:'',
	        extraParam: null,
	        sendData: function(){},
	        configUrl: function(url,page,extraParam){},
	        beforeDataLoad: EMPTY_FUNCTION,
	        afterDataLoad: EMPTY_FUNCTION,
            initCompleted: EMPTY_FUNCTION,//初始化对象完成后的回调函数
            beforeSendAJAX: EMPTY_FUNCTION,//获取数据ajax发送之前
            errorFun: EMPTY_FUNCTION//ajax发送失败后的回调函数
    	};
    THEME_URL = PW.libUrl + 'js/base/assets/pagination/css/default.css';
    S.getScript(THEME_URL,{charset:'utf-8'});
    var pagination = function(param){
        var cfg = S.clone(config);
        this.opts = S.mix(cfg,param,true,[],true);
        this.lock = false;      //定义点击分页按钮的一个锁，防止多次点击。默认关锁
        this.init();
    };
    
    pagination.prototype = {
        /**
         * 初始化函数
         */
        init: function(){
            var
                that = this,
                opts = that.opts;
            that.prevPaginationData = null;that._getThemePackage();
            
            if(opts.initCompleted){
                opts.initCompleted();//初始化完成后的函数
            }
        },
        /**
         * 重新加载页面，是一个外部方法
         * @param  参数param是需要重新加载页面的分页新配置，没有可不写
         */
        reload: function(param){
            this.opts = S.mix(this.opts,param);
            this.tDOM.html('');
            this.init();
        },
        /**
         * 更新分页显示，是一个外部方法，主要用于在添加或者删除记录的情况下，判断分页的页数是否需要改变
         * @param  参数param是需要改变的分页的新配置
         */
        refresh: function(param){
            //更新dataCount
            //计算分页
            //执行toPage操作
            this.opts = S.mix(this.opts,param);
            var pageC = Math.ceil(this.opts.dataCount/this.opts.pageSize);
            var that = this;
            if(pageC != this.opts.pageCount){
                that._sendAjax(this.opts.page);
            }
        },
        _getThemePackage:function(){
            var
                that  = this,
                opts  = that.opts,
                tPURL = opts.themePackageURL,
                xhr;
            if(tPURL && tPURL !== '' && tPURL !== null && tPURL !== undefined){
                xhr = $.getJSON(tPURL,function(tp){
                    tp = (S.isString(tp))? S.JSON.parse(tp): tp;
                    var themeName;
                    if(opts.themeName){
                        themeName = opts.themeName;
                    }else{
                        themeName = 'default';
                    }
                    that.opts.themePackage = tp.pagination[themeName];
                });
                window.setTimeout(function(){
                    if(xhr.status!==200){
                        S.log('主题包获取失败,直接使用默认配置！');
                        if(opts.themePackage){
                            var themeName;
                            if(opts.themeName){
                                themeName = opts.themeName;
                            }else{
                                themeName = 'default';
                            }
							if(PW.modSettings.pagination){
								that.opts.themePackage = PW.modSettings.pagination.themePackage[themeName];
							}
                        }else{
                            opts.themePackage = DEFAULT_THEME_PKG;
                        }
                    }
                },2000);
            that.prevPaginationData;
			that.pctrl = new Pctrl(opts);
			that._sendAjax(opts.page);    
            }else{
                if(PW.modSettings){
                    if(PW.modSettings.pagination){
                        var themeName;
                        if(opts.themeName){
                            themeName = opts.themeName;
                        }else{
                            themeName = 'default';
                        }
                        that.opts.themePackage = PW.modSettings.pagination.themePackage[themeName];
                    }else{
                        opts.themePackage = DEFAULT_THEME_PKG;
                    }
                }
                that.prevPaginationData;
                that.pctrl = new Pctrl(opts);
                that._sendAjax(opts.page);
            }
        },
        _sendAjax: function(page){
            var
                that = this,
                opts = that.opts,
                URL;
            if(!opts.timeout || opts.timeout == undefined || opts.timeout == '' || opts.timeout == null){
                opts.timeout = 10;
            }
            that.lock = true;      //加锁
            IO({
                type: opts.type,
                url: opts.configUrl(that.opts.url,that.opts.page,that,that.prevPaginationData),
                data: S.mix(opts.extraParam,{_:S.now()}),
                timeout: opts.timeout,
                success: function(data){
                    data = (S.isString(data)) ? S.JSON.parse(data): data;
                    if(opts.beforeDataLoad){
                        //modified by Simon: 
                        //  执行beforeDataLoad函数，并且判断
                        //  如果返回了false，则后续内容暂不执行，所获数据抛弃
                        if(opts.beforeDataLoad(that,data) == false){
                            //直接解锁
                            that.lock = false;   
                            return;
                        }
                    }
                    //如果后台传dataCount过来就将传来的值指定给当前分页的dataCount
                    //如果没有传值，则使用默认的配置值
                    if(data.dataCount !== null && data.dataCount !== '' &&data.dataCount !== undefined){
                        opts.dataCount = data.dataCount;
                    }
                    opts.pageCount = Math.ceil(opts.dataCount/opts.pageSize);
                    that.opts.sendData(that,data);
                    for(i in data){
                        if(S.isArray(data[i])){
                            var list = data[i];
                        }
                    }
                    if(list){
						if(list.length == 0){
							var name = DOM.get(opts.dataRender).nodeName.toLowerCase();
							if(name == 'tbody'){
								$(opts.dataRender).parent().attr("id","J_dataRenderTab")
								var
									tab = document.getElementById("J_dataRenderTab"),
									cells = tab.rows[0].cells.length,
									html = '<tr><td colspan="'+cells+'" align="center">暂无数据！</td></tr>';
								
								$(opts.dataRender).html(html);
							}else{
								DOM.html(opts.dataRender,'暂无数据！');
							}
						}else{
							
							var tpl = document.getElementById(opts.juicerRender).innerHTML;
							that.prevPaginationData = data;
							var html = juicer(tpl, data);
							DOM.html(opts.dataRender,html);
							DOM.html(opts.renderTo,'');
							that.pctrl.refresh(opts.dataCount,page);
						}
					}
                    
                    that._pageClick();
                    if(opts.afterDataLoad){
                        opts.afterDataLoad(that,data,page);
                    }

                    that.lock = false;   //ajax成功之后关闭锁
                },
                error: function(){
                    S.log('ajax发送失败，没有获取到数据！');
                    if(opts.errorFun){
                        opts.errorFun(that);
                    }
                }
            });
            
        },
        _pageClick:function(){
            
            //绑定click事件
            var 
                that = this,
                opts = that.opts;
            that.tDOM = $(opts.renderTo);
            //点击首页
            that.tDOM.find('#J_firstPage').click(function(){
                if(!($(this).hasClass('disabled'))){
                    if(!that.lock){
                        opts.page = 1;
                        that._sendAjax(opts.page);
                    }
                }
            });
            //点击上一页
            that.tDOM.find('#J_prevPage').click(function(){
                if(!($(this).hasClass('disabled'))){
                    if(!that.lock){
                        opts.page = opts.page - 1;
                        that._sendAjax(opts.page);
                    }
                    
                }
            });
            //点击下一页
            that.tDOM.find('#J_nextPage').click(function(){
                if(!($(this).hasClass('disabled'))){
                    if(!that.lock){
                        opts.page = opts.page + 1;
                        that._sendAjax(opts.page);
                    }
                    
                }
            });
            //点击尾页
            that.tDOM.find('#J_lastPage').click(function(){
                if(!($(this).hasClass('disabled'))){
                    if(!that.lock){
                        opts.page = opts.pageCount;
                        that._sendAjax(opts.page);
                    }
                }
            });
            //点击指定的页码
            that.tDOM.find(".J_page").each(function(){
                if(!($(this).hasClass('check'))){
                    $(this).click(function(){
                        if(!that.lock){
                            var val = $(this).children('span').text();
                            opts.page = parseInt(val);
                            that._sendAjax(opts.page);
                        }
                    });
                }
                
            });
            //点击按钮进行跳转
            that.tDOM.find('.J_btnGo').click(function(){
                if($(this).prev('#pageNum').val() && $(this).prev('#pageNum').val() !== '' && $(this).prev('#pageNum').val() !== null){
                    if(!that.lock){
                        var p = $(this).prev('#pageNum').val();
                        p = parseInt(p);
                        if(p > that.opts.pageCount){
                            opts.page = opts.pageCount;
                        }else if(p < 1){
                            opts.page = 1;
                        }else if(isNaN(p)){
                            opts.page = 1;
                        }else{
                            opts.page = p;
                        }
                        that._sendAjax(opts.page);
                    }
                }
            });
        },
        _toPage: function(page){
            page           = parseInt(page);
            this.opts.page = page;
            this.tDOM.html("");
            this.pctrl.refresh(this.opts.dataCount,this.opts.page);
        }
    }

    return pagination; 
}, {
    requires : ['thirdparty/jquery','mod/juicer','core','pagination/Pctrl']
});

KISSY.add('pagination/Pctrl',function(S){
    var
        DOM = S.DOM, get = DOM.get, query = DOM.query, JSON = S.JSON, on = S.Event.on, io = S.io,
        config = {
            renderTo:'',
            pageSize: 10,
            showCounter: true,
            showJump: true,
            showPageNum: true
        };
    function Pctrl(param){
        this.opts = S.mix(config,param,true,[],true);
        this.page = 1;
    }
    S.augment(Pctrl,S.EventTarget);
    S.augment(Pctrl,{
        _init:function(){},
        refresh: function(dataCount,page){
            //计算页数，然后根据页数获取html
            var
                that = this,
                opts = that.opts;
            opts.dataCount = dataCount;
            opts.pageCount = Math.ceil(opts.dataCount/opts.pageSize);
            that._checkPage(page);
            var html = that._generateHTML(page);
            var d = $(this.opts.renderTo).append(html);
            $(this.opts.renderTo).addClass(opts.themePackage.themeCss);
            that._addEvt(d);
        },
        
        _addEvt: function(html){
            this._checkPage();
            d = DOM.create(html);
            return d;
        },
        /**
         * 配置处理函数，对配置项里面的主题配置进行处理，提取出需要的内容
         */
        _configHandel:function(){
            var
                that = this,
                opts = that.opts;
			if(opts.themePackage){
				that.themeCounter = opts.themePackage.themeCounter;
				that.themeCounter = S.substitute(that.themeCounter,{page:opts.page,pageCount:opts.pageCount});
				that.themeJump    = opts.themePackage.themeJump;
				that.themeJump    = S.substitute(that.themeJump,{input:'<input type="text" id="pageNum" name="pageNum" class="goTo" value="" />',button:'<button class="J_btnGo" type="button">&nbsp;</button>'});
			}
        },
        /**
         * 拼装分页html
         */
        _generateHTML: function(page){ //
            var
				that = this,
                strHtml = '',
                opts = this.opts,
                endPage = 0;
            opts.page = page;
            var
                prevPage = parseInt(opts.page) - 1,
                nextPage = parseInt(opts.page) + 1;
            that._configHandel();

            /*
			if(opts.themePackage.showCounter == true){
                strHtml += '<span class="count">'+this.themeCounter+'</span>';
            }*/
            if (prevPage < 1) {
                if(opts.themePackage.showFirst == true){
                    strHtml += 
                        '<span id="J_firstPage" class="firstPage disabled" title="First Page">'+opts.themePackage.firstPageTip+'</span>';
                }
                strHtml += 
                    '<span id="J_prevPage" class="prevPage disabled" title="Prev Page">'+opts.themePackage.prevPageTip+'&nbsp;</span>';
            } else {
                if(opts.themePackage.showFirst == true){
                    strHtml += 
                        '<a><span id="J_firstPage" title="First Page">'+opts.themePackage.firstPageTip+'</span></a>';
                }
                strHtml += 
                    '<a><span id="J_prevPage" title="Prev Page">'+opts.themePackage.prevPageTip+'&nbsp;</span></a>';
            }
            if (opts.page != 1 && opts.themePackage.showPageNum == true) strHtml += '<a class="J_page"><span title="Page 1">1</span></a>';
            if (opts.page >= 5 && opts.themePackage.showPageNum == true) strHtml += '<span class="until">...</span>';
            if (opts.pageCount > opts.page + 2) {
                endPage = opts.page + 2;
            } else {
                endPage = opts.pageCount;
            }
            for (var i = opts.page - 2; i <= endPage; i++) {
                if (i > 0) {
                    if (i == opts.page && opts.themePackage.showPageNum == true) {
                        strHtml += '<span title="Page ' + i + '" class="check">' + i + '</span>';
                    }else {
                        if (i != 1 && i != opts.pageCount && opts.themePackage.showPageNum == true) {
                            strHtml += '<a class="J_page"><span title="Page ' + i + '">' + i + '</span></a>';
                        }
                    }
                }
            }
            if (opts.page + 3 < opts.pageCount && opts.themePackage.showPageNum == true) strHtml += '<span class="until">...</span>';
            if (opts.page != opts.pageCount && opts.themePackage.showPageNum == true) strHtml += '<a class="J_page"><span title="Page ' + this.opts.pageCount + '">' + this.opts.pageCount + '</span></a>';
            if (nextPage > opts.pageCount) {
                strHtml += '<span id="J_nextPage" class="nextPage disabled" title="Next Page">&nbsp;'+opts.themePackage.nextPageTip+'</span>';
                if(opts.themePackage.showLast == true){
                    strHtml += '<span id="J_lastPage" class="lastPage disabled" title="Last Page">'+opts.themePackage.lastPageTip+'</span>'; 
                }
            } else {
                strHtml += '<a><span id="J_nextPage" title="Next Page">&nbsp;'+opts.themePackage.nextPageTip+'</span></a>';
                if(opts.themePackage.showLast == true){
                    strHtml += '<a><span id="J_lastPage" title="Last Page">'+opts.themePackage.lastPageTip+'</span></a>'; 
                }
            }
			if(opts.themePackage.showCounter == true && opts.pageCount >= 5){
                strHtml += '<span class="count">'+this.themeCounter+'</span>';
            }
            if(opts.themePackage.showJump == true && opts.pageCount >= 5){
                strHtml += this.themeJump;
            }
            strHtml += '<br/>';
            if(opts.pageCount <= 1){
                strHtml = '';
            }
            return strHtml;
        },
        /**
         * 对当前页数和总页数进行验证
         */
        _checkPage: function(){
            var
                opts = this.opts;
            //当前页数为非数字，则当前页数置为1
            if (isNaN(parseInt(opts.page))) opts.page = 1;
            //总页数为非数字，则将总页数置为1
            if (isNaN(parseInt(opts.pageCount))) opts.pageCount = 1;
            if (opts.page < 1) opts.page = 1;
            if (opts.pageCount < 1) opts.pageCount = 1;
            if (opts.page > opts.pageCount) opts.page = opts.pageCount;
            opts.page = parseInt(opts.page);
            opts.pageCount = parseInt(opts.pageCount);
        }       
    }); 
    
    return  Pctrl;  
},{
    requires:['core','thirdparty/jquery']
});
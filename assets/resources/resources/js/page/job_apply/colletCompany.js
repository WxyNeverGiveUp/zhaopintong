KISSY.add('page/job_apply/colletCompany',function(S,colletCompanyShow){
    PW.namespace('page.job_apply.colletCompany'); 
    PW.page.job_apply.colletCompany = function(param){
    	new colletCompanyShow(param);
    }
},{
   requires:['colletCompany/colletCompanyShow']
})
/********************************************/
KISSY.add('colletCompany/colletCompanyShow',function(S){
    var 
        $ = S.all,
        on = S.Event.on,
		Pagination = PW.mod.Pagination,
		focusIO = PW.io.job_apply.collect,
        el = {
              J_isFocus:'.isFocus',
              J_unFocus:'.unFocus',
              J_companyInfo_template:'#companyInfo_template',
              J_noFocus:'.no-focus',
              J_yesFocus:'.yes-focus',
              J_focusNum:'.focus-num',
              J_companyRecommend:'.company-recommend'
        },
        myvar = {
        	isFocus : 0
        };
    function colletCompanyShow(param){
    	this.opts = param;
    	this.pagination;
        this.init();
    }
    S.augment(colletCompanyShow,{
        init:function(){
        	this._addEventListener();
        	this._pagination();
        },
        //分页实现
        _pagination:function(Param){  
			var 
				that = this,
				opts = that.opts;
			that.pagination = Pagination.client(opts);
		},
        //关注,取消,关注单位数量的变化
        _addEventListener:function(){
        	var that = this;
        	$(el.J_companyInfo_template).delegate('click','a',function(ev){
        		if($(ev.currentTarget).hasClass(el.J_isFocus)){
        			myvar.isFocus = 1;
                    var Focus = $(ev.currentTarget).parent();
                    var isFocus = $(ev.currentTarget);
					var unFocus = $(ev.currentTarget).next();
					var companyId = Focus.parent().attr('id');
					var urlParam = 'companyId='+companyId+'&isFocus='+myvar.isFocus;
					focusIO.isCollect(urlParam , function(rs,data,msg){
						if(rs){
							$(el.J_focusNum).text(data);
							//全部都没有关注时关注提醒
        			         $(el.J_noFocus).hide();
        			         $(el.J_yesFocus).show();
                            unFocus.show();
                            isFocus.hide(); 
						}
					});
        		}
        		if($(ev.currentTarget).hasClass(el.J_unFocus)){
        			myvar.isFocus = 0;
                    var Focus = $(ev.currentTarget).parent();
                    var unFocus = $(ev.currentTarget);
					var isFocus = $(ev.currentTarget).prev();
					var companyId = Focus.parent().attr('id');
					var urlParam = 'companyId='+companyId+'&isFocus='+myvar.isFocus;
					focusIO.isCollect(urlParam , function(rs,data,msg){
						if(rs){
							$(el.J_focusNum).text(data);
							//全部都没有关注时关注提醒
							if(data == 0){
        			            $(el.J_noFocus).show();
        			            $(el.J_yesFocus).hide();
                                window.location.reload();
        		            }
                            isFocus.show();
                            unFocus.hide();
						}

					});
        		}
        		
        		
        		
        	})
        }        
    });
    return colletCompanyShow;
},{
	requires:['mod/pagination','event','mod/defender','io/job_apply/collect']
})
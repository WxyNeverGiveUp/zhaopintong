/*-----------------------------------------------------------------------------
* @Description:     suggest相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.15
* ==NOTES:=============================================
* v1.0.0(2014.9.15):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('module/suggest',function(S,core){
	PW.namespace('module.suggest');
	PW.module.suggest = function(param){
		new core(param);
	};
},{
	requires:['suggest/core']
});

/*----------------------------------suggest-----------------------------------*/
KISSY.add('suggest/core',function(S){
	var
		$ = S.all, on = S.Event.on, delegate = S.Event.delegate,
		CompanyIO = PW.io.module.company,
	el = {
		companyHolder:'#J_companyHolder',//指向单位名称
		companyFidle:'#J_companyFidle'//指向sugget的列表
	};

	function core(param){
		this.opts = param;
		this.init();
	}
	S.augment(core,{
		init:function(){
			this._addEventListener();
			this._getCompany();
		},
		_addEventListener:function(){
			var
				that = this;
			/*当输入单位名称时*/
			on(el.companyHolder,'keyup',function(evt){
				var
					target = evt.target,
					val = $(target).val(),
					holder = $(target).next(el.companyFidle);
				if(val == ''){
					that._hideCompanyFidle(holder);
				}else{
					that._showCompanyFidle(holder,val);
				}
			});
			/*点击其他地方*/
			on('body','click',function(){
				that._hideCompanyFidle(el.companyFidle);
			});
		},
		/*隐藏suggest的列表*/
		_hideCompanyFidle:function(target){
			$(target).hide();
		},
		/*显示suggest的列表*/
		_showCompanyFidle:function(target,val){
			var
				that = this;
			that._addCompany(target,val);
			$(target).show();
		},
		/*把符合输入的单位名称显示在suggest的列表中*/
		_addCompany:function(holder,val){
			var
				that = this,
				companyInfo = that.companyInfo,
				liHtml = '',
				companys;
			S.each(companyInfo,function(i,o){
				if(that._suggest(val,i)){
					liHtml = liHtml+'<li data-id="'+i.id+'">'+i.cnName+'</li>';
				}
			});
			$(holder).html(liHtml);
			companys = $('li',holder);
			/*点击单位名称*/
			on(companys,'click',function(evt){
				that._showSelectCompany(evt.target);
			});
		},
		/*正则匹配*/
		_suggest:function(val,data){
			var
				regexp = RegExp(val,"i");
			if(regexp.test(data.cnName) || regexp.test(data.enName)){
				return true;
			}else{
				return false;
			}
		},
		/*把用户选择的单位显示到输入框中*/
		_showSelectCompany:function(target){
			var
				that = this,
				company = $(target).html(),
				parent = $(target).parent();
			$(parent).prev(el.companyHolder).val(company);
			that._hideCompanyFidle(parent);
		},
		/*获取系统中的单位*/
		_getCompany:function(){
			var
				that = this;
			CompanyIO.getCompany({},function(rs,data,errorMes){
				if(rs){
					that.companyInfo = data;
				}else{
					S.log(errorMes);
				}
			});
		}
	});
	return core;
},{
	requires:['core','io/module/company/getCompany']
});
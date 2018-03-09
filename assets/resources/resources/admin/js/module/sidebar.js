/*-----------------------------------------------------------------------------
* @Description: 侧边栏js (sidebar.js)
* @Version: 	V1.0.0
* @author: 		shenj(1073805310@qq.com)
* @date			2014.08.11
* ==NOTES:=============================================
* v1.0.0(2014.08.11):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('module/sidebar',function(S,core){
	PW.namespace('module.Sidebar');
	PW.module.Sidebar = function(param){
		new core(param);
	}
},{
	requires:['sidebar/core']
});
/*----------------------------侧边栏js--------------------------------*/
KISSY.add('sidebar/core',function(S){
	var
		$ = S.all, on = S.Event.on,
		el = {
			sidebarHolder:'.sidebar',//指向侧边栏导航
			submenuHolder:'.submenu',//指向二级菜单
			J_province_select:'.province-select',
			J_city_select:'.city-select'
		},
		recruitIO = PW.io.recruit.recruit,
		NONE_CLASS = 'none',
		OPEN_CLASS = 'open';

	function core(param){
		this.opts = param;
		this.init();
	}

	S.augment(core,{
		init:function(){
			this._addEventLintener();
			this._getCity();
		},
		_addEventLintener:function(){
			var
				that = this,
				clickHolder = $('a',el.sidebarHolder);
			on(clickHolder,'click',function(evt){
				that._showCurrentMenu(evt.currentTarget);
				that._hideOtherMenu(evt.currentTarget);
			});
		},
		_showCurrentMenu:function(holder){
			var
				that = this,
				$submenuHolder = $(holder).next(),
				$parent = $(holder).parent();
			if($submenuHolder && $submenuHolder.hasClass(NONE_CLASS)){
				$submenuHolder.show();
				$submenuHolder.removeClass(NONE_CLASS);
				$parent.addClass(OPEN_CLASS);
			}
		},
		_hideOtherMenu:function(holder){
			var
				that = this,
				$parent = $(holder).parent(),
				$dom = $parent.siblings(),
				$otherSubmenu,
				$i;
			S.each($dom,function(i,o){
				$i = $(i),
				$otherSubmenu = $(el.submenuHolder,$i);
				if(!$otherSubmenu.hasClass(NONE_CLASS)){
					$otherSubmenu.hide();
					$otherSubmenu.addClass(NONE_CLASS);
					$i.removeClass(OPEN_CLASS);
				}
			});
		},
		_getCity:function(){
			$(el.J_province_select).on('change',function(ev){
				var provinceId = $(ev.currentTarget).children('option:selected').val();
				recruitIO.getCity({provinceId:provinceId},function(rs,data,msg){
					if(rs){
	            			$(el.J_city_select).html('');
	            			var optionHtml;
	            			S.each(data,function(item){
	            				optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
	            				$(el.J_city_select).append(optionHtml);
	            			})
	            			optionHtml = '<option value=0>全部</option>';
	            			$(el.J_city_select).prepend(optionHtml);
	            		}
				})
			})
		}
	});

	return core;
},{
	requires:['core','io/recruit/recruit']
});
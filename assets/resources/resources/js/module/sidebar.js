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
			submenuHolder:'.submenu'//指向二级菜单
		},
		NONE_CLASS = 'none',
		OPEN_CLASS = 'open';

	function core(param){
		this.opts = param;
		this.init();
	}

	S.augment(core,{
		init:function(){
			this._addEventLintener();
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
		}
	});

	return core;
},{
	requires:['core']
});
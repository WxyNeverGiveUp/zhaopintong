/*-----------------------------------------------------------------------------
* @Description:    	关于我们主页页面相关js
* @Version:         1.0.0
* @author:          hujun
* @date             2015.7.13
* ==NOTES:=============================================
* v1.0.0(2015.7.13):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/about_us/about_index' , function(S,nav){
	PW.namespace('page.about_us.about_index');
	PW.page.about_us.about_index = function(){
		new nav();
	}
},{
	requires:['about_index/nav']
});
/*----------------------左侧导航-------------------------------*/
KISSY.add('about_index/nav',function(S){
	var $ = S.all, 
		on = S.Event.on,
		DOM = S.DOM,

		el = {
				J_Nav:'.left-nav-content',
				J_Content:'.content',
				J_ContentBox:'.content-box',
				J_current_position:'.current-position'
		};
		
	function nav(){
		this.init();
	}
	
	S.augment(nav,{
			init:function(){
				this._selectContent();
			},
			_selectContent:function(){
			/*********************写法1************************/
				/*var that = this,
					$nav_li=$('li',el.J_Nav),
					$content_div=$('div',el.J_Content);
				$nav_li.each(function(ele,index){
					$(ele).on('click',function(ev){
						var $currentTarget=$(ev.currentTarget),
							$li_index=$(this).attr('index'),
							index = parseInt($li_index , 10);	
						$currentTarget.addClass('selected').siblings('li').removeClass("selected");
						$content_div.item(index).show().siblings().hide();
					});
				});*/

			/*********************写法2************************/
				var 
					that=this,
					$nav_li=$('li',el.J_Nav),
					$content_div=$('div',el.J_Content),
					$content_height = Number($(el.J_ContentBox).css('height').slice(0,-2)) - 113;
				$(el.J_Nav).css('height',$content_height+"px");
				S.each($nav_li , function(ele,index){
					var
						li_index = index,
						li_ele = ele;
					$(li_ele).on('click',function(ev){
						var 
							currentPostion = $(ev.currentTarget).children().text();
						$(el.J_current_position).text(currentPostion);									
						$(this).addClass('selected');
						$(this).siblings('li').removeClass("selected");
						$content_div.each(function(ele,index){							
							if(index == li_index){
								$(ele).show();
								$(ele).siblings().hide();
								$content_height = Number($(ele).css('height').slice(0,-2)) - 113;
								$(el.J_Nav).css('height','300px');
								$(el.J_Nav).css('height',$content_height+"px");
							}
						});
					});
				});
		}
	});		
	return nav;
	},{
		requires:['event','sizzle']
	});
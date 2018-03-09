/*-----------------------------------------------------------------------------
* @Description: 拓片轮播组件核心js代码 (imgplayer.js)
* @Version: 	V1.0.0
* @author: 		huanhuan(965788210@qq.com)
* @date			2013.08.10
* ==NOTES:=============================================
* v1.0.0(2013.08.10):
* 	进行整合：将plugin中imgPlayer下的core.js和原imgplayer.js整合到一起 
* ---------------------------------------------------------------------------*/
	
KISSY.add('mod/imgplayer',function(S,imgplayer){

    PW.imgplayer = function(param){
        return new imgplayer(param);
    }
},{
	requires:['mod/imgplayer/core']
});

KISSY.add('mod/imgplayer/core', function(S, Juicer){
	var
		$ = S.all, DOM = S.DOM, query = DOM.query, on = S.Event.on, Anim = S.Anim,
		config = {
			renderTo : '',
			imgInfor : [],
			effect : 'fade',/*fade或者是slide*/
			direction : '',/*left或者是top，只针对slide，如果effect选择的是fade，这个参数就要删掉*/
			areaWidth : '530',
			areaHeight : '220',
			indexTheme : 'a',
			titleTheme : 'a',
			showTitle : true,
			linkTarget : '_blank'
		},
		IMG_PLAYER_TEMP = 
			'<div class="img-holder" style="width:&{areaWidth}px;height:&{areaHeight}px">'
			+'<div class="img-wrapper clearfix" style="width:&{parseInt(areaWidth)*3}px; height:&{areaHeight}px;">'
			+'{@each imgInfor as it,index}'
			+'<a target="&{linkTarget}" id="img&{parseInt(index)+1}" class="imgList" href="&{it.imgLink}">'
			+'<img src="&{it.imgSrc}" width="&{areaWidth}" height="&{areaHeight}" alt="轮播图片显示" /></a>'
			+'{@/each}'
			+'</div></div>'
			+'<div class="index-holder index-holder-&{indexTheme}">'
			+'{@each imgInfor as it,index}'
			+'{@if index==0}'
			+'<a id="index&{parseInt(index)+1}" href="javascript:;" class="active">&{parseInt(index)+1}</a>'
			+'{@else}'
			+'<a id="index&{parseInt(index)+1}" href="javascript:;" class="">&{parseInt(index)+1}</a>'
			+'{@/if}'
			+'{@/each}'
			+'</div>'
			+'<div class="title-holder title-holder-&{titleTheme}">'
			+'{@each imgInfor as it,index}'
			+'<p id="title&{parseInt(index)+1}">&{it.imgTitle}</p>'
			+'{@/each}'
			+'</div>',
		IMG_PLAYER_TEMP_FADE =
			'<div class="img-holder" style="width:&{areaWidth}px;height:&{areaHeight}px">'
			+'<div class="img-wrapper clearfix" style="width:&{areaWidth}px; height:&{areaHeight}px;">'
			+'{@each imgInfor as it,index}'
			+'<a target="&{linkTarget}" id="img&{parseInt(index)+1}" class="imgList" href="&{it.imgLink}">'
			+'<img src="&{it.imgSrc}" width="&{areaWidth}" height="&{areaHeight}" /></a>'
			+'{@/each}'
			+'</div></div>'
			+'<div class="index-holder index-holder-&{indexTheme}">'
			+'{@each imgInfor as it,index}'
			+'{@if index==0}'
			+'<a id="index&{parseInt(index)+1}" href="javascript:;" class="active">&{parseInt(index)+1}</a>'
			+'{@else}'
			+'<a id="index&{parseInt(index)+1}" href="javascript:;" class="">&{parseInt(index)+1}</a>'
			+'{@/if}'
			+'{@/each}'
			+'</div>'
			+'<div class="title-holder title-holder-&{titleTheme}">'
			+'{@each imgInfor as it,index}'
			+'<p id="title&{parseInt(index)+1}">&{it.imgTitle}</p>'
			+'{@/each}'
			+'</div>',
		PREV_NEXT_BTN = '<a class="prev"></a><a class="next"></a>';

	function imgplayer(arg){
		this.opts = S.merge(config, arg);
		var
			opts = this.opts;
		this.tDOM = $(opts.renderTo);
		/*图片的张数*/
		this.len = opts.imgInfor.length;
		/*图片轮播动画最小left值*/
		this.minLeft = -opts.areaWidth;
		/*图片轮播动画最大left值*/
		this.maxLeft = opts.areaWidth;
		/*图片轮播动画最小top值*/
		this.minTop = -opts.areaHeight;
		/*图片轮播动画最大top值*/
		this.maxTop = opts.areaHeight;
		this.init();
	}
	THEME_URL = PW.libUrl + 'js/base/assets/imgPlayer/css/default.css';
    S.getScript(THEME_URL,{charset:'utf-8'});
	S.augment(imgplayer, S.EventTarget, {
		init:function(){
			/*当前图片页码*/
			this.page = 1;
			this._generateCode();
			this._initStyle(1, 2);
			this._addEvt();
			this._anim();
		},
		/**
		 * 根据配置生成图片轮播代码
		 */
		_generateCode:function(){
			var
				that = this,
				opts = that.opts,
				tempHTML,/*代码模版*/
				imgplayerHTML;/*生成的轮播代码*/

			tpl = (opts.effect == 'slide')? IMG_PLAYER_TEMP: IMG_PLAYER_TEMP_FADE;
			imgplayerHTML = Juicer(tpl, opts);
			that.tDOM.append(imgplayerHTML);
			that.tDOM.css({
				width: opts.areaWidth,
				height: opts.areaHeight
			});
			/*指向所有的图片*/
			this.imgHolder = query('.img-wrapper a', opts.renderTo);
			/*指向所有的index按钮*/
			this.indexHolder  = query('.index-holder a', opts.renderTo);
			/*指向所有的图片title*/
			this.titleHolder = query('.title-holder p', opts.renderTo);
		},
		/**
		 * 初始化样式
		 * @param  {[type]} from [当前图片页码 this.page]
		 * @param  {[type]} to [执行动画到达的图片页码]
		 */
		_initStyle:function(from,to){
			var
				that = this,
				opts = that.opts,
				effect = opts.effect;/*轮播效果*/
			
			/*是否显示图片title部分*/
			that._showTitle();
			/*index按钮样式初始化*/
			that._indexStyle(from);
			/*title显示内容初始化*/
			that._titleContent(from);
			(effect == 'slide')? that._slideStyle(from, to): that._fadeStyle(from, to);

		},
		/**
		 * 轮播效果为slide时的初始化样式
		 * @param  {[type]} from [当前图片页码 this.page]
		 * @param  {[type]} to [执行动画到达的图片页码]
		 */
		_slideStyle:function(from, to){
			var
				that = this,
				opts = that.opts,
				direction = opts.direction;

			(direction == 'left')? that._slideHorizontalStyle(from, to): that._slideVerticalStyle(from, to);
		},
		/**
		 * 轮播效果为fade时的初始化样式
		 * 初始化设置
		 * 当前图片的index值为6
		 * 其余图片的index值为4
		 * @param  {[type]} from [当前图片页码 this.page]
		 * @param  {[type]} to [执行动画到达的图片页码]
		 */
		_fadeStyle:function(from, to){
			var
				that = this,
				opts = that.opts,
				imgHolder = that.imgHolder;
			
			DOM.css(imgHolder,{
				display : 'block',
				zIndex : '4',
				opacity : '1'
			});
			DOM.css(imgHolder[to - 1],{zIndex:'5'});
			DOM.css(imgHolder[from - 1],{zIndex:'6'});
		},
		/**
		 * 轮播效果水平方向滑动初始化样式
		 * 当前显示的图片的left值为0，z-index值为5
		 * 比当前图片页码小的图片的left值为图片宽度的负值，z-indx值为4
		 * 比当前图片页码大的图片的left值为图片的宽度，z-index值为4
		 * @param  {[type]} from [当前图片页码 this.page]
		 * @param  {[type]} to [执行动画到达的图片页码]
		 */
		_slideHorizontalStyle:function(from, to){
			var
				that = this,
				opts = that.opts,
				imgHolder = that.imgHolder,
				len = that.len,
				i, j;
			
			DOM.css(imgHolder,{
				zIndex:'4'
			});
			DOM.css(imgHolder[from - 1],{
				left : 0,
				zIndex : '5'
			});
			if(from == 1){
				for(j = from; j < len - 1; j++){
					DOM.css(imgHolder[j],{
						left:that.maxLeft
					});
				}
				DOM.css(imgHolder[len - 1],{
					left:that.minLeft
				});
			}else if(from == len){
				for(i = 0; i < from - 1; i++){
					DOM.css(imgHolder[i],{
						left:that.minLeft
					});
				}
				DOM.css(imgHolder[0],{
					left:that.maxLeft
				});
			}else{
				for(i = 0; i < from - 1; i++){
					DOM.css(imgHolder[i],{
						left:that.minLeft
					});
				}
				for(j = from; j < len; j++){
					DOM.css(imgHolder[j],{
						left:that.maxLeft
					});
				}
			}
			
		},
		/**
		 * 轮播效果竖直方向滑动初始化样式
		 * @param  {[type]} from [当前图片页码 this.page]
		 * @param  {[type]} to [执行动画到达的图片页码]
		 */
		_slideVerticalStyle:function(from, to){
			var
				that = this,
				opts = that.opts,
				imgHolder = that.imgHolder,
				len = that.len,
				i, j;
			
			DOM.css(imgHolder,{
				zIndex:'4'
			});
			DOM.css(imgHolder[from - 1],{
				top : 0,
				zIndex : '5'
			});
			if(from == 1){
				for(j = from; j < len - 1; j++){
					DOM.css(imgHolder[j],{
						top:that.maxTop
					});
				}
				DOM.css(imgHolder[len - 1],{
					top:that.minTop
				});
			}else if(from == len){
				for(i = 0; i < from - 1; i++){
					DOM.css(imgHolder[i],{
						top:that.minTop
					});
				}
				DOM.css(imgHolder[0],{
					top:that.maxTop
				});
			}else{
				for(i = 0; i < from - 1; i++){
					DOM.css(imgHolder[i],{
						top:that.minTop
					});
				}
				for(j = from; j < len; j++){
					DOM.css(imgHolder[j],{
						top:that.maxTop
					});
				}
			}
		},
		/**
		 * [_indexStyle index按钮样式]
		 * @param  {[type]} p [当前图片页码]
		 */
		_indexStyle:function(p){
			var
				that = this,
				opts = that.opts,
				indexHolder = that.indexHolder;

			$(indexHolder).removeClass('active');
			$(indexHolder[p-1]).addClass('active');
		},
		/**
		 * [_showTitle 是否显示图片title部分]
		 */
		_showTitle:function(){
			var
				that = this,
				opts = that.opts;
			if(opts.indexTheme == 'c'){
				$(opts.renderTo).one('.index-holder').html(PREV_NEXT_BTN);
				that.prevBtn = $(opts.renderTo).one('.prev');
				that.nextBtn = $(opts.renderTo).one('.next');
			}
			if(opts.showTitle == 'false' || opts.showTitle == false){
				$(opts.renderTo).one('.title-holder').remove();
			}
		},
		/**
		 * [_titleStyle 图片title内容，对应的显示图片对应的title]
		 * @param  {[type]} p [当前图片页码]
		 */
		_titleContent:function(p){
			var
				that = this,
				opts = that.opts,
				titleHolder = that.titleHolder;
			$(titleHolder).css('display','none');
			$(titleHolder[p-1]).css('display','block');
		},
		/**
		 * 检查图片页码
		 * @param  {[type]} p [要检查的页码]
		 * @return {[type]}   [检查后的页码]
		 */
		_checkPage:function(p){
			var
				len = this.len;
			p = (p % len);
			if(p == 0) p = len;
			return p;
		},
		/**
		 * [_addEvt 添加监听事件]
		 */
		_addEvt:function(){
			var
				that = this,
				opts = that.opts,
				indexHolder = that.indexHolder,
				from, to;
			for(var i in indexHolder){
				/*添加鼠标移入事件*/
				on(indexHolder[i], 'mouseover', function(evt){
					to = $(evt.target).text();
					/*修改index按钮样式*/
					that._indexStyle(to);
					/*修改图片title的内容*/
					that._titleContent(to);
					from = that.page;
					window.clearInterval(window.timer);
					if(that.anim) that.anim.stop();
					if(that.nextAnim) that.nextAnim.stop();
					that._toPage(from, to);
				});
				/*添加鼠标移出事件*/
				on(indexHolder[i], 'mouseout', function(evt){
					from = that.page;
					to = from + 1;
					that._anim(from, to);
				});
			}
			/*添加prev点击事件*/
			on(that.prevBtn, 'click', function(){
				from = that.page;
				to = that._checkPage(from - 1);
				/*修改index按钮样式*/
				that._indexStyle(to);
				/*修改图片title的内容*/
				that._titleContent(to);
				
				window.clearInterval(window.timer);
				if(that.anim) that.anim.stop();
				if(that.nextAnim) that.nextAnim.stop();
				that._toPage(from, to);
			});
			/*添加next点击事件*/
			on(that.nextBtn, 'click', function(){
				from = that.page;
				to = that._checkPage(from + 1);
				/*修改index按钮样式*/
				that._indexStyle(to);
				/*修改图片title的内容*/
				that._titleContent(to);
				
				window.clearInterval(window.timer);
				if(that.anim) that.anim.stop();
				if(that.nextAnim) that.nextAnim.stop();
				that._toPage(from, to);
			});
		},
		/**
		 * [_anim 自动执行轮播动画]
		 */
		_anim:function(){
			var
				that = this,
				opts = that.opts;

			window.timer = window.setInterval(function(){
				that._toPage(that.page, that.page+1);
			},3000);
		},
		/**
		 * [_toPage 两张图片之间的轮播]
		 * @param  {[type]} from [轮播动画开始图片页码]
		 * @param  {[type]} to   [轮播动画结束图片页码]
		 */
		_toPage:function(from, to){
			var
				that = this,
				opts = that.opts,
				effect = opts.effect;
			from = that._checkPage(from);
			to = that._checkPage(to);

			(effect == 'slide')? that._slidePage(from, to): that._fadePage(from, to);
		},
		/**
		 * [_slidePage 两张图片实现滑动效果]
		 * @param  {[type]} from [轮播动画开始图片页码]
		 * @param  {[type]} to   [轮播动画结束图片页码]
		 */
		_slidePage:function(from, to){
			var
				that = this,
				opts = that.opts,
				direction = opts.direction;
			(direction == 'left')? that._slidePageHorizontal(from, to): that._slidePageVertical(from, to);
		},
		/**
		 * [_fadePage 两张图片实现淡出淡入效果]
		 * @param  {[type]} from [实现淡出效果的图片页码]
		 * @param  {[type]} to   [实现淡入效果的图片页码]
		 */
		_fadePage:function(from, to){
			var
				that = this,
				opts = that.opts,
				imgHolder = that.imgHolder;
			

			/*执行淡出淡入效果前先调整样式*/
			that._fadeStyle(from, to);
			/*修改index按钮样式*/
			that._indexStyle(to);
			/*修改图片title的内容*/
			that._titleContent(to);

			that.anim = Anim(imgHolder[from-1],{
				opacity : '0',
				zIndex : '4'
			}, .8, 'fadeOut').run();

			that.nextAnim = Anim(imgHolder[to-1],{
				opacity : '1',
				zIndex : '6'
			}, .2, 'fadeIn', function(){//动画执行后的回调
				/*动画执行后修改当前图片页码*/
				that.page = to;
			}).run();
		},
		/**
		 * [_slidePageHorizontal 两张图片实现水平方向滑动效果]
		 * @param  {[type]} from [当前图片页面]
		 * @param  {[type]} to   [滑动后展示的图片页码]
		 */
		_slidePageHorizontal:function(from, to){
			var
				that = this,
				opts = that.opts,
				len = that.len;
			/*执行滑动动画前先调整样式*/
			that._slideHorizontalStyle(from, to);
			/*特殊情况1：当前页码是1*/
			if(from == 1){
				if(to > 1 && to < len){
					that._slideLeft(from, to);
				}else if(to == len){
					that._slideRight(from, to);
				}
			}
			/*特殊情况2：当前页码是that.len*/
			else if(from == len){
				if(to > 1 && to < len){
					that._slideRight(from, to);
				}else if(to == 1){
					that._slideLeft(from, to);
				}
			}
			/*其他情况*/
			else if(from > to){
				that._slideRight(from, to);
			}else if(from < to){
				that._slideLeft(from, to);
			}
		},
		/**
		 * [_slideRight 两张图片实现向右滑动效果]
		 * @param  {[type]} from [当前图片页面]
		 * @param  {[type]} to   [滑动后展示的图片页码]
		 */
		_slideRight:function(from, to){
			var
				that = this,
				imgHolder = that.imgHolder;

			/*修改index按钮样式*/
			that._indexStyle(to);
			/*修改图片title的内容*/
			that._titleContent(to);
			that.anim = jQuery(imgHolder[from-1]).animate({
				left:that.maxLeft+'px',
				zIndex:'4'
			}, 500);

			that.nextAnim = jQuery(imgHolder[to-1]).animate({
				left:'0',
				zIndex:'5'
			}, 500, function(){
				that.page = to;
			});
		},
		/**
		 * [_slideLeft 两张图片实现向左滑动效果]
		 * @param  {[type]} from [当前图片页面]
		 * @param  {[type]} to   [滑动后展示的图片页码]
		 */
		_slideLeft:function(from, to){
			var
				that = this,
				imgHolder = that.imgHolder;

			/*修改index按钮样式*/
			that._indexStyle(to);
			/*修改图片title的内容*/
			that._titleContent(to);
			that.anim = jQuery(imgHolder[from-1]).animate({
				left:that.minLeft+'px',
				zIndex:'4'
			}, 500);

			that.nextAnim = jQuery(imgHolder[to-1]).animate({
				left:'0',
				zIndex:'5'
			}, 500, function(){
				that.page = to;
			});
		},
		/**
		 * [_slidePageVertical 两张图片实现竖直方向滑动效果]
		 * @param  {[type]} from [当前图片页面]
		 * @param  {[type]} to   [滑动后展示的图片页码]
		 */
		_slidePageVertical:function(from, to){
			var
				that = this,
				opts = that.opts,
				len = that.len;
			/*执行滑动动画前先调整样式*/
			that._slideVerticalStyle(from, to);
			/*特殊情况1：当前页码是1*/
			if(from == 1){
				if(to > 1 && to < len){
					that._slideUp(from, to);
				}else if(to == len){
					that._slideDown(from, to);
				}
			}
			/*特殊情况2：当前页码是that.len*/
			else if(from == len){
				if(to > 1 && to < len){
					that._slideDown(from, to);
				}else if(to == 1){
					that._slideUp(from, to);
				}
			}
			/*其他情况*/
			else if(from > to){
				that._slideDown(from, to);
			}else if(from < to){
				that._slideUp(from, to);
			}
		},
		/**
		 * [_slideUp 两张图片实现向上滑动效果]
		 * @param  {[type]} from [当前图片页面]
		 * @param  {[type]} to   [滑动后展示的图片页码]
		 */
		_slideUp:function(from, to){
			var
				that = this,
				imgHolder = that.imgHolder;

			/*修改index按钮样式*/
			that._indexStyle(to);
			/*修改图片title的内容*/
			that._titleContent(to);
			that.anim = jQuery(imgHolder[from-1]).animate({
				top:that.minTop+'px',
				zIndex:'4'
			}, 500);

			that.nextAnim = jQuery(imgHolder[to-1]).animate({
				top:'0',
				zIndex:'5'
			}, 500, function(){
				that.page = to;
			});
		},
		/**
		 * [_slideDown 两张图片实现向下滑动效果]
		 * @param  {[type]} from [当前图片页面]
		 * @param  {[type]} to   [滑动后展示的图片页码]
		 */
		_slideDown:function(from, to){
			var
				that = this,
				imgHolder = that.imgHolder;

			/*修改index按钮样式*/
			that._indexStyle(to);
			/*修改图片title的内容*/
			that._titleContent(to);
			that.anim = jQuery(imgHolder[from-1]).animate({
				top:that.maxTop+'px',
				zIndex:'4'
			}, 500);

			that.nextAnim = jQuery(imgHolder[to-1]).animate({
				top:'0',
				zIndex:'5'
			}, 500, function(){
				that.page = to;
			});
		}
	});

	return imgplayer;
},{
	requires:['mod/juicer','core','sizzle','thirdparty/jquery']
});
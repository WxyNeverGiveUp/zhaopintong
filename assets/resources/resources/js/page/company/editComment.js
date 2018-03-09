/*-----------------------------------------------------------------------------
* @Description:     edit-comment页面相关js
* @Version:         1.0.0
* @author:          yihuan
* @date             2015.07.26
* ==NOTES:=============================================
* v1.0.0(2015.07.26):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/company/editComment',function(S,editCommentShow){
	PW.namespace('page.company.editComment');
	PW.page.company.editComment = function(){
		new editCommentShow();
	}
},{
      requires:['editComment/editCommentShow']
})
/*--------------------------------------------------------------------*/
KISSY.add('editComment/editCommentShow',function(S){
	var
	   $ = S.all,
	   on = S.Event.on,
       DOM = S.DOM,
	   Defender = PW.mod.Defender,
	   jobDetailIO = PW.io.company.jobDetail,
	   el = {
             J_starLevel:'.starLevel',
             J_barLevel:'.barLevel',
             J_submit:'.submit',
             J_starDescribe:'.starDescribe',
             J_barDescribe:'.barDescribe',
             J_starTip:'.starTip',
             J_form:'#form',
             J_context:'.context',
             J_contextTip:'.contextTip',
             J_radioTip:'.radioTip',
             J_barTip1:'.barTip1',
             J_barTip2:'.barTip2',
             J_barTip3:'.barTip3',
             J_barTip4:'.barTip4',
             J_barTip5:'.barTip5',
             J_star_level_1_hover:'star-level-1-hover',
             J_star_level_2_hover:'star-level-2-hover',
             J_star_level_3_hover:'star-level-3-hover',
             J_star_level_4_hover:'star-level-4-hover',
             J_star_level_5_hover:'star-level-5-hover',
             J_star_level_1:'star-level-1',
             J_star_level_2:'star-level-2',
             J_star_level_3:'star-level-3',
             J_star_level_4:'star-level-4',
             J_star_level_5:'star-level-5',
             J_bar_level_1_hover:'bar-level-1-hover',
             J_bar_level_2_hover:'bar-level-2-hover',
             J_bar_level_3_hover:'bar-level-3-hover',
             J_bar_level_4_hover:'bar-level-4-hover',
             J_bar_level_5_hover:'bar-level-5-hover',
             J_bar_level_1:'bar-level-1',
             J_bar_level_2:'bar-level-2',
             J_bar_level_3:'bar-level-3',
             J_bar_level_4:'bar-level-4',
             J_bar_level_5:'bar-level-5',
             J_barLevel1:'.barLevel1',
             J_barDescribe1:'.barDescribe1',
             J_barLevel2:'.barLevel2',
             J_barDescribe2:'.barDescribe2',
             J_barLevel3:'.barLevel3',
             J_barDescribe3:'.barDescribe3',
             J_barLevel4:'.barLevel4',
             J_barDescribe4:'.barDescribe4',
             J_barLevel5:'.barLevel5',
             J_barDescribe5:'.barDescribe5',
             J_starRate :'.star-rate',
             J_barRate1 :'.bar-rate1',
             J_barRate2 :'.bar-rate2',
             J_barRate3 :'.bar-rate3',
             J_barRate4 :'.bar-rate4',
             J_barRate5 :'.bar-rate5',
             J_tijiao :'.tijiao',
             J_eidt_page : '.eidt-page',
             J_follow :'.follow',
             J_unfollow : '.unfollow',
             J_follow_number : ".follow-number",
             J_company_name:".company-name",
             J_company_id:"company-id"

	    },
        myvar = {
            yes:1,
            no : 0
        };
    function editCommentShow(){
        this.init();
    }
	S.augment(editCommentShow,{
		init:function(){
		    this._addEventListener();
            this._initContent();
	    },
         //页面内容的初始化
        _initContent:function(){
            var that = this;
          //starLevel的初始化
          var starValue = $(el.J_starRate).attr('value');
          if(starValue == 1){
            $(el.J_starLevel).addClass(el.J_star_level_1);
          }
          if(starValue == 2){
            $(el.J_starLevel).addClass(el.J_star_level_2);
          }
          if(starValue == 3){
            $(el.J_starLevel).addClass(el.J_star_level_3);
          }
          if(starValue == 4){
            $(el.J_starLevel).addClass(el.J_star_level_4);
          }
          if(starValue == 5){
            $(el.J_starLevel).addClass(el.J_star_level_5);
          }
          //barLevel的初始化
          that._initBar(el.J_barRate1,el.J_barLevel1);
          that._initBar(el.J_barRate2,el.J_barLevel2);
          that._initBar(el.J_barRate3,el.J_barLevel3);
          that._initBar(el.J_barRate4,el.J_barLevel4);
          that._initBar(el.J_barRate5,el.J_barLevel5);
        },
        //barLevel的初始化
        _initBar:function(c,e){
         var Value = $(c).attr('value');
          if(Value == 1){
            $(e).addClass(el.J_bar_level_1);
          }
          if(Value == 2){
            $(e).addClass(el.J_bar_level_2);
          }
          if(Value == 3){
            $(e).addClass(el.J_bar_level_3);
          }
          if(Value == 4){
            $(e).addClass(el.J_bar_level_4);
          }
          if(Value == 5){
            $(e).addClass(el.J_bar_level_5);
          }
        },
        //事件监听
        _addEventListener:function(){
            var 
                isCollect,isFollow,
                that = this;
            //鼠标移入star时背景图片的改变,addclass()在后面
            $('a',el.J_starLevel).on('mouseenter',function(ev){
            	var value = $(ev.currentTarget).attr('value');
            	$(el.J_starTip).hide();//星级评价提醒
            	if(value == 1){
                   $(el.J_starLevel).removeClass('star-level-2-hover star-level-3-hover star-level-4-hover star-level-5-hover').addClass(el.J_star_level_1_hover);
                   $(el.J_starDescribe).text($(ev.currentTarget).text());
                }
            	if(value == 2){
            	   $(el.J_starLevel).removeClass('star-level-1-hover star-level-3-hover star-level-4-hover star-level-5-hover').addClass(el.J_star_level_2_hover);
                   $(el.J_starDescribe).text($(ev.currentTarget).text());
            	}
            	if(value == 3){
            	   $(el.J_starLevel).removeClass('star-level-1-hover star-level-2-hover star-level-4-hover star-level-5-hover').addClass(el.J_star_level_3_hover);
                   $(el.J_starDescribe).text($(ev.currentTarget).text());
                }
            	if(value == 4){
            	   $(el.J_starLevel).removeClass('star-level-1-hover star-level-3-hover star-level-2-hover star-level-5-hover').addClass(el.J_star_level_4_hover);
                   $(el.J_starDescribe).text($(ev.currentTarget).text());
            	}
            	if(value == 5){
            	   $(el.J_starLevel).removeClass('star-level-1-hover star-level-3-hover star-level-4-hover star-level-2-hover').addClass(el.J_star_level_5_hover);
                   $(el.J_starDescribe).text($(ev.currentTarget).text());
            	}
            });
            // 鼠标点击star时背景图片的改变
            $('a',el.J_starLevel).on('click',function(ev){
                var value = $(ev.currentTarget).attr('value');
                $(el.J_starLevel).removeClass('star-level-1-hover star-level-2-hover star-level-3-hover star-level-4-hover star-level-5-hover');
             	if(value == 1){
                   $(el.J_starLevel).removeClass('star-level-2 star-level-3 star-level-4 star-level-5 ').addClass(el.J_star_level_1);
                   DOM.attr(el.J_starRate,{value:1});
                }
            	if(value == 2){
            	   $(el.J_starLevel).removeClass('star-level-1 star-level-3 star-level-4 star-level-5 ').addClass(el.J_star_level_2);               
            	   DOM.attr(el.J_starRate,{value:2});
                }
            	if(value == 3){
            	   $(el.J_starLevel).removeClass('star-level-2 star-level-1 star-level-4 star-level-5 ').addClass(el.J_star_level_3);
                   DOM.attr(el.J_starRate,{value:3});
                }
            	if(value == 4){
            	   $(el.J_starLevel).removeClass('star-level-2 star-level-1 star-level-3 star-level-5 ').addClass(el.J_star_level_4);
            	   DOM.attr(el.J_starRate,{value:4});
                }
            	if(value == 5){
            	   $(el.J_starLevel).removeClass('star-level-2 star-level-1 star-level-4 star-level-3 ').addClass(el.J_star_level_5);
            	   DOM.attr(el.J_starRate,{value:5});
                }
            	$(el.J_starDescribe).text(value);
            	$(el.J_starTip).hide();
            });
            //鼠标移出star时背景图片的改变
            $(el.J_starLevel).on('mouseleave',function(ev){
                $(el.J_starLevel).removeClass('star-level-1-hover star-level-2-hover star-level-3-hover star-level-4-hover star-level-5-hover');
            });
            //鼠标移入bar时背景图片的改变,addclass()在后面
            $('a',el.J_barLevel1).on('mouseenter',function(ev){
                 that._barMouseenter(el.J_barLevel1,el.J_barDescribe1,ev);
            });
            $('a',el.J_barLevel2).on('mouseenter',function(ev){
                that._barMouseenter(el.J_barLevel2,el.J_barDescribe2,ev);
            });
            $('a',el.J_barLevel3).on('mouseenter',function(ev){
                that._barMouseenter(el.J_barLevel3,el.J_barDescribe3,ev);
            });

            $('a',el.J_barLevel4).on('mouseenter',function(ev){
                that._barMouseenter(el.J_barLevel4,el.J_barDescribe4,ev);
            });

            $('a',el.J_barLevel5).on('mouseenter',function(ev){
                 that._barMouseenter(el.J_barLevel5,el.J_barDescribe5,ev);
            });
            //鼠标移出bar时背景图片的改变
            $(el.J_barLevel1).on('mouseleave',function(ev){
              $(el.J_barLevel1).removeClass('bar-level-1-hover bar-level-2-hover bar-level-3-hover bar-level-4-hover bar-level-5-hover');
            });
            $(el.J_barLevel2).on('mouseleave',function(ev){
              $(el.J_barLevel2).removeClass('bar-level-1-hover bar-level-2-hover bar-level-3-hover bar-level-4-hover bar-level-5-hover');
            });
            $(el.J_barLevel3).on('mouseleave',function(ev){
              $(el.J_barLevel3).removeClass('bar-level-1-hover bar-level-2-hover bar-level-3-hover bar-level-4-hover bar-level-5-hover');
            });
            $(el.J_barLevel4).on('mouseleave',function(ev){
              $(el.J_barLevel4).removeClass('bar-level-1-hover bar-level-2-hover bar-level-3-hover bar-level-4-hover bar-level-5-hover');
            });
            $(el.J_barLevel5).on('mouseleave',function(ev){
              $(el.J_barLevel5).removeClass('bar-level-1-hover bar-level-2-hover bar-level-3-hover bar-level-4-hover bar-level-5-hover');
            });
            //点击bar时背景图片的改变
            $('a',el.J_barLevel1).on('click',function(ev){
                that._barClick(el.J_barLevel1,el.J_barDescribe1,ev,el.J_barRate1);
            });
            $('a',el.J_barLevel2).on('click',function(ev){
                that._barClick(el.J_barLevel2,el.J_barDescribe2,ev,el.J_barRate2);
            });
            $('a',el.J_barLevel3).on('click',function(ev){
                that._barClick(el.J_barLevel3,el.J_barDescribe3,ev,el.J_barRate3);
            });
            $('a',el.J_barLevel4).on('click',function(ev){
                that._barClick(el.J_barLevel4,el.J_barDescribe4,ev,el.J_barRate4);
            });
            $('a',el.J_barLevel5).on('click',function(ev){
                that._barClick(el.J_barLevel5,el.J_barDescribe5,ev,el.J_barRate5);
            });
          
            //表单注册的验证
           
            on(el.J_tijiao,'click',function(ev){
                that._formSubmit(ev);

            });

            $(el.J_unfollow).on('click',function(ev){
                isFollow = myvar.no;
                $(el.J_unfollow).hide();
                $(el.J_follow).show();
                var para = 'companyId='+$(el.J_company_name).attr(el.J_company_id)+'&isFollow='+isFollow;
                jobDetailIO.getFollowNumberIO(para,function(code,data,msg){
                    if(code == 0){
                        $(el.J_follow_number).text(data.followNumber);
                    }
                })
            });

            $(el.J_follow).on('click',function(ev){
                isFollow = myvar.yes;
                $(el.J_follow).hide();
                $(el.J_unfollow).show();
                var para = 'companyId='+$(el.J_company_name).attr(el.J_company_id)+'&isFollow='+isFollow;
                jobDetailIO.getFollowNumberIO(para,function(code,data,msg){
                    if(code == 1){
                        $(el.J_pop).show();
                        $(el.J_login).show();
                        $(el.J_register).hide();
                    }
                    if(code == 0){
                        $(el.J_follow_number).text(data.followNumber);
                    }
                })
            });
        },
        //bar鼠标点击时背景图片的具体实现
        _barClick:function(a,b,ev,h){
            var value = $(ev.currentTarget).attr('value');
            
            $(a).removeClass('bar-level-1-hover bar-level-2-hover bar-level-3-hover bar-level-4-hover bar-level-5-hover');
            if(value == 1){
               $(a).removeClass('bar-level-2 bar-level-3 bar-level-4 bar-level-5').addClass('bar-level-1');
               DOM.attr(h,{value:1});
             }
            if(value == 2){
                $(a).removeClass('bar-level-1 bar-level-3 bar-level-4 bar-level-5').addClass('bar-level-2');
                DOM.attr(h,{value:2});
             }
             if(value == 3){
                $(a).removeClass('bar-level-2 bar-level-1 bar-level-4 bar-level-5').addClass('bar-level-3');
                DOM.attr(h,{value:3});
             }
             if(value == 4){
                $(a).removeClass('bar-level-2 bar-level-3 bar-level-1 bar-level-5').addClass('bar-level-4');
                DOM.attr(h,{value:4});
             }
             if(value == 5){
              $(a).removeClass('bar-level-2 bar-level-3 bar-level-4 bar-level-1').addClass('bar-level-5');
                DOM.attr(h,{value:5});
             }
             $(b).text(value);
             $(el.J_barTip).hide();
        },
        //bar鼠标移动时背景图片的具体实现
        _barMouseenter:function(n,m,ev){
                var value =$(ev.currentTarget).attr('value'); 
                if(value == 1){
                    $(n).removeClass('bar-level-2-hover bar-level-3-hover bar-level-4-hover bar-level-5-hover').addClass('bar-level-1-hover');
                    $(m).text($(ev.currentTarget).text());
                }
                if(value == 2){
                    $(n).removeClass('bar-level-1-hover bar-level-3-hover bar-level-4-hover bar-level-5-hover').addClass('bar-level-2-hover');  
                    $(m).text($(ev.currentTarget).text());
                 }
                 if(value == 3){
                    $(n).removeClass('bar-level-2-hover bar-level-1-hover bar-level-4-hover bar-level-5-hover').addClass('bar-level-3-hover');                  
                    $(m).text($(ev.currentTarget).text());
                }
                if(value == 4){
                    $(n).removeClass('bar-level-2-hover bar-level-3-hover bar-level-1-hover bar-level-5-hover').addClass('bar-level-4-hover');               
                    $(m).text($(ev.currentTarget).text());
                }
                if(value == 5){
                    $(n).removeClass('bar-level-2-hover bar-level-3-hover bar-level-4-hover bar-level-1-hover').addClass('bar-level-5-hover');                  
                    $(m).text($(ev.currentTarget).text());
                }
        },
        //表单注册的验证具体实现
        _formSubmit:function(ev){
        	ev.preventDefault();
        	var 
        	   length = 0,
        	   radio = $('input:[type="radio"]',el.J_eidt_page),
        	   isContextNull = true,
               isStarLevel = true,
               isBarLevel = true;
        	//点赞不能选多个且不能不选
            S.each(radio,function(i,r){
				if($(i).attr('checked')=='checked'){
				   length ++;
				}	
			});

            if(length == 0){
                $(el.J_radioTip).show();
            };

            //starLevel和barLevel的值不能为空
            var starvalue = $(el.J_starRate).attr('value');
            if(starvalue < 0){
                isStarLevel = false;
                $(el.J_starTip).show();
            }
			//评论的字数不少于10个字
            if($(el.J_context).val().length == 0){
                isContextNull=false;
                $(el.J_contextTip).text('请填写10字以上').show();
            }  
            var that = this;
            that._tipBar(el.J_barRate1,el.J_barTip1); 
            that._tipBar(el.J_barRate2,el.J_barTip2);  
            that._tipBar(el.J_barRate3,el.J_barTip3);  
            that._tipBar(el.J_barRate4,el.J_barTip4);  
            that._tipBar(el.J_barRate5,el.J_barTip5);         
            //当四者条件都成立时才允许提交
			if(length == 1 && isContextNull && isStarLevel && isBarLevel){
				document.getElementById('J_form').submit();
			}
        },
        //barTip的实现
        _tipBar:function(f,g){
            var barvalue = $(f).attr('value');
            if(barvalue < 0){
                isBarLevel=false;
                $(g).show();
            } 
        }
    });
    return editCommentShow;
},{
	requires:['mod/defender','io/company/jobDetail']
})

    

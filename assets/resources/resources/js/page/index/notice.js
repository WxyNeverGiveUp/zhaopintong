/*-----------------------------------------------------------------------------
* @DescriSion: 教师招考与公告页面相关js
* @Version: 	V1.0.0
* @author: 		zhaokaikang
* @date			2015.07.21
* ==NOTES:=============================================
* v1.0.0(2015.07.21):
* 	初始生成 
* ---------------------------------------------------------------------------*/
KISSY.add('page/index/notice',function(S,noticeShow){
	PW.namespace('page.index.notice');
	PW.page.index.notice = function(param){
		new noticeShow(param);
	}
},{
	requires:['notice/noticeShow']
})

/*--------------------------------------------------------------------------*/
KISSY.add('notice/noticeShow',function(S){
	var
		$ = S.all,
		on = S.Event.on,
		indexIO = PW.io.index.index,
		el = {
			J_notice_search : '.notice-search',
			J_serach_btn : '.serach-btn',
			J_search_type : '.search-type',
			J_select_province : '.select-province',
			J_select_city : '.select-city'
		}
		Pagination = PW.mod.Pagination;

	function noticeShow(param){
		this.opts = param;
		this.pagination;
		this.init();
	}

	S.augment(noticeShow,{
		init:function(){
			this._pagination();
			this._addEventListener();
		},
		_pagination:function(extraParam){
			var 
				that = this,
				opts = that.opts;

			that.pagination = Pagination.client(opts);
		},
		_addEventListener:function(){
			var 
				cityId = 0,
				searchWord = '',
				searchTypeId = 0,
				that = this,
				opts = that.opts;
			$(el.J_serach_btn).on('click',function(ev){
				searchWord = $(el.J_notice_search).val();
				searchTypeId = $(ev.currentTarget).prev().prev().children('option:selected').val();
				S.mix(opts, {
							extraParam: {
								searchWord: searchWord,
								searchTypeId: searchTypeId
							}
				});
				that.pagination.reload(opts);
				
			});

			$(el.J_notice_search).on('keypress',function(ev){
				if(ev.keyCode == 13){
					searchWord = $(el.J_notice_search).val();
					if(cityId != 0){
						searchTypeId = cityId;
					}
					S.mix(opts, {
								extraParam: {
									searchWord: searchWord,
									searchTypeId: searchTypeId
								}
					});
					that.pagination.reload(opts);
				}
			});

			$(el.J_search_type).on('change',function(ev){
				searchTypeId = $(ev.currentTarget).children('option:selected').val();
				S.mix(opts, {
							extraParam: {
								searchWord: searchWord,
								searchTypeId: searchTypeId
							}
				});
				that.pagination.reload(opts);
			});

            $(el.J_notice_search).on('change',function(ev){
                searchWord = $(el.J_notice_search).val();
            });

            $(el.J_select_province).on('change',function(ev){
            	var provinceId = $(ev.currentTarget).children('option:selected').val();
            	if(provinceId == 0){
            		S.mix(opts, {
							extraParam: {
								searchWord: searchWord,
								searchTypeId: 0
							}
					});
					that.pagination.reload(opts);
					$(el.J_select_city).html('');
	            	$(el.J_select_city).prepend('<option value=0>全部</option>');
            	}
            	else{
            		indexIO.getCity({provinceId:provinceId},function(rs,data,msg){
	            		if(rs){
	            			$(el.J_select_city).html('');
	            			var optionHtml;
	            			S.each(data,function(item){
	            				optionHtml = '<option value='+item.id+'>'+item.name+'</option>';
	            				$(el.J_select_city).append(optionHtml);
	            			})
	            			optionHtml = '<option value=0>全部</option>';
	            			$(el.J_select_city).prepend(optionHtml);
	            		}
            		})
            	}
            	
            });
            
            $(el.J_select_city).on('change',function(ev){
            	cityId = $(ev.currentTarget).children('option:selected').val();
            	S.mix(opts, {
							extraParam: {
								searchWord: searchWord,
								searchTypeId: cityId
							}
				});
				that.pagination.reload(opts);
            })
		}
	});
	return noticeShow;
},{
	requires:['mod/pagination','event','sizzle','io/index/index']
})
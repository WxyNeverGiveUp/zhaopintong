/*-----------------------------------------------------------------------------
* @Description:     省市联动部分相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.16
* ==NOTES:=============================================
* v1.0.0(2014.9.16):
     初始生成
  * ---------------------------------------------------------------------------
* v2.0.0(2015.5.28):
* @author:          xuyihong(597262617@qq.com)
* 添加学科-专业相关联动ajax
* ---------------------------------------------------------------------------*/
KISSY.add('module/linkage',function(S){
	var
		$ = S.all, delegate = S.Event.delegate, fire = S.Event.fire, 
		LinkageIO = PW.io.module.linkage,
		el = {
			proviceHolder:'.J_proviceList',//指向省的下拉列表
			cityHolder:'.J_cityList',//指向市的下拉列表
			institutionHolder:'#J_institutionList',//指向学院的下拉列表
			majorHolder:'#J_majorList',//指向专业的下拉列表
			proviceHolderEmp:'#J_proviceListEmp',//指向省的下拉列表
			cityHolderEmp:'#J_cityListEmp',//指向市的下拉列表
		};

		function linkage(param){
			this.opts = S.merge(el,param);
			this.init();
		}

		S.augment(linkage,S.EventTarget,{
			init:function(){
				this._addEventListener();
			},
			_addEventListener:function(){
				var
					that = this,
					opts = that.opts;

				/*获取省的列表,并且写在相应的proviceHolder中*/
				LinkageIO.getProvice({},function(rs,data,errorMes){
					var
						$proviceHolders = $(el.proviceHolder);
						proviceOptions = '<option value="0">请选择</option>';

					S.each(data,function(provice,o){
						proviceOptions = proviceOptions + '<option value="'+provice.proviceId+'">'+provice.proviceName+'</option>';
					});
					S.each($proviceHolders,function(proviceHolder){
						$(proviceHolder).html(proviceOptions);
						$(proviceHolder).val(0);
					});					
				});

				/*当省改变时*/
				delegate(document,'change',opts.proviceHolder,function(evt){
					that._changeProvice(evt.target);
				});
				delegate(document,'change',opts.institutionHolder,function(evt){
					that._changeInstitution(evt.target);
				});
				delegate(document,'change',opts.proviceHolderEmp,function(evt){
					that._changeProviceEmp(evt.target);
				});
			},
			
			/*改变省*/
			_changeProvice:function(proviceHolder){
				var
					that = this,
					proviceId,proviceName,
					opts = that.opts,
					selectOption = '<option value="0">请选择</option>';

				proviceId = $(proviceHolder).val();
				proviceName = $(proviceHolder).one('option:selected').text();

				that.fire('changeProviceCallBack',{
					proviceId:proviceId,
					proviceName:proviceName
				});

				if(proviceId > 0){
					that._getCity(proviceId,proviceHolder);
				}else{
					$(opts.cityHolder).html(selectOption);
				}
			},
			/*根据省的id获取市的列表*/
			_getCity:function(proviceId,proviceHolder){
				var
					that = this,
					opts = that.opts,
					para = {provinceId:proviceId};
					S.log(para);
				LinkageIO.getCity(para,function(rs,data,errorMes){
					if(rs){
						that._updateCityList(data,proviceHolder);
					}else{
						S.log(errorMes);
					}
				});
			},
			/*更新市的列表*/
			_updateCityList:function(cityList,proviceHolder){
				var
					that = this,
					opts = that.opts,
					cityOptions = '<option value="0">请选择</option>';

				S.each(cityList,function(city,o){
					cityOptions = cityOptions + '<option value="'+city.cityId+'">'+city.cityName+'</option>';
				});
				$(proviceHolder).next('select').html(cityOptions);
				$(proviceHolder).next('select').val(0);
			},
			/*改变省Emp*/
			_changeProviceEmp:function(proviceHolderEmp){
				var
					that = this,
					proviceId,proviceName,
					opts = that.opts,
					selectOption = '<option value="0">请选择</option>';

				proviceId = $(proviceHolderEmp).val();
				proviceName = $(proviceHolderEmp).one('option:selected').text();

				that.fire('changeProviceCallBack',{
					proviceId:proviceId,
					proviceName:proviceName
				});

				if(proviceId > 0){
					that._getCityEmp(proviceId);
				}else{
					$(opts.cityHolderEmp).html(selectOption);
				}
			},
			/*根据省的id获取市的列表Emp*/
			_getCityEmp:function(proviceId){
				var
					that = this,
					opts = that.opts;
				LinkageIO.getCityEmp({provinceId:proviceId},function(rs,data,errorMes){
					if(rs){
						that._updateCityListEmp(data);
					}else{
						S.log(errorMes);
					}
				});
			},
			/*更新市的列表Emp*/
			_updateCityListEmp:function(cityList){
				var
					that = this,
					opts = that.opts,
					cityOptions = '<option value="0">请选择</option>';
				S.each(cityList,function(city,o){
					cityOptions = cityOptions + '<option value="'+city.cityId+'">'+city.cityName+'</option>';
				});
				$(opts.cityHolderEmp).html(cityOptions);
				$(opts.cityHolderEmp).val(0);
			},
			/*改变学院*/
			_changeInstitution:function(institutionHolder){
				var
					that = this,
					institutionId,institutionName,
					opts = that.opts,
					selectOption = '<option value="0">请选择</option>';

				institutionId = $(institutionHolder).val();
				institutionName = $(institutionHolder).one('option:selected').text();

				that.fire('changeinstitutionCallBack',{
					institutionId:institutionId,
					institutionName:institutionName
				});

				if(institutionId > 0){
					that._getMajor(institutionName);
				}else{
					$(opts.majorHolder).html(selectOption);
				}
			},
			/*根据学院的id获取专业的列表*/
			_getMajor:function(institutionName){
				var
					that = this,
					opts = that.opts;
				LinkageIO.getMajor({institutionName:institutionName},function(rs,data,errorMes){
					if(rs){
						that._updateMajorList(data);
					}else{
						S.log(errorMes);
					}
				});
			},
			/*更新专业的列表*/
			_updateMajorList:function(majorList){
				var
					that = this,
					opts = that.opts,
					majorOptions = '<option value="0">请选择</option>';
				S.each(majorList,function(major,o){
					majorOptions = majorOptions + '<option value="'+major.majorId+'">'+major.majorName+'</option>';
				});
				$(opts.majorHolder).html(majorOptions);
				$(opts.majorHolder).val(0);
			}
		});

		return linkage;
},{
	requires:['io/module/linkage/linkage']
});
/*-----------------------------------------------------------------------------
* @Description:     职位订阅页面相关js
* @Version:         1.0.0
* @author:          wangpeiqi
* @date             2015.6.16
* ==NOTES:=============================================
* v1.0.0(2015.6.16):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/job_apply/job_subscription' , function(S, jobplaceShow){
	PW.namespace('page.job_apply.jobSubscription');
	PW.page.job_apply.jobSubscription = function(param){
		new jobplaceShow(param);
	}
},{
	requires:['jobSubscription/jobplaceShow']
});
/*---------------------------------------------------------------------------*/
KISSY.add('jobSubscription/jobplaceShow',function(S){
	var $ = S.all, 
		DOM = S.DOM,
		node = S.node,
		Defender = PW.mod.Defender,
		jobapplyIO = PW.io.job_apply.jobSubscription,
		el = {
			"J_namewarp" : "#jobnamewarp",
			"J_placewarp": "#jobplacewarp",
			"J_name"     : "#jobname",
			"J_place"    : "#jobplace",
			"J_select"   : "#job-select",
			"place_sel"  : "#place-select",
			"F_submit"   : "#submit",
			"F_cencle"   : "#cencle",
			"btn_sure"   : ".btn-sure",
			"btn_cencle" : ".btn-cencle",
			J_order_form : ".order-form"
		};

		
	function jobplaceShow(param){
		this.init();
		this.valid = Defender.client(el.J_order_form,{
				showTip:false
			});
	}

	S.augment(jobplaceShow,{
		init:function(){
			this._createSelectIO();
			this._createLiIO();
			this.clickjob();
			this.clickjobplace();

		},
		_createLi:function(data,callback){
			var myopts = "";
			S.each(data , function(item){
				myopts = '<li><input type="radio" name="myjob" value="'+item.posName+'">' + item.posName +'</input></li>';
				$(el.J_select+" ul").append(myopts);
			});
			callback();
		},
		_createLiIO:function(){
			var that = this;
		 	jobapplyIO.createLiIO('jobname',function(rs,data,Msg){
				if(rs)
					that._createLi(data,that.placeselect);
				else
					alert(Msg);
			});
		},
		_createSelect:function(data){	
			S.each(data , function(item){
				if(item.other=="hot"){
					var hotcity = '<li>' + item.name + '</li>';
					$("#hotplace").append(hotcity);

				} else {
					var myopts = '<option>' + item.name +'</option>';
					for(var key in item.cities){
						myopts += '<option>'+item.cities[key]+'</option>';
					} 
					var optionHtml = '<li><div><select>' + myopts + '</select></div><i class="ico2 pos2"></i></li>';
					$("#otherpalce").append(optionHtml);
				}
			});	

		},
		_createSelectIO:function(){
			var that = this ;
			jobapplyIO.createSelectIO('data',function(rs,data,Msg){
					that._createSelect(data);
				
			})
		},
		//点击选择职位出现弹出框
		clickjob:function(){
			var myselectall = DOM.create("<div>",{id:"job-select",'class':"all-select-pos zindex",css:{display:"none"}});
			var innerselect = '<div><span>请选择职位名称</span><span id="jobclose">×</span></div><ul></ul><button class="btn-sure">确定</button><button class="btn-cencle">取消</button>';
			DOM.html(myselectall, innerselect, true);
			$(el.J_namewarp).append(myselectall);
			S.one(el.J_name).on('click', function(e) {
				$(el.J_select).css("display","block");
				$("#place-select").css("display","none");
			});
		
		//选择职位确定
			S.one(el.btn_sure).on('click', function(e) {
				e.preventDefault();
				var radioval = $("input[name='myjob']:checked").val();
				$("#jn").val(radioval);
				$(el.J_select).css("display","none");
			});
		
		//取消选择职位
			S.one(el.btn_cencle).on('click', function(e) {
				e.preventDefault();
				$(el.J_select).css("display","none");
			});
			S.one("#jobclose").on('click', function(e) {
				$(el.J_select).css("display","none");
			});
		},

		//选择工作地点弹出框
		clickjobplace:function(){
			var myselectall = DOM.create("<div>",{id:"place-select",'class':"all-select-pos place-select",css:{display:"none"}});
			var innerselect = '<h4>热门城市</h4><ul id="hotplace" class="clearfix"></ul><hr><h4>其他城市</h4><ul id="otherpalce" class="clearfix"></ul><hr>';
			DOM.html(myselectall, innerselect, true);
			$(el.J_placewarp).append(myselectall);


			//选择工作地点
			S.one(el.J_place).on('click', function(e) {
				$("#place-select").css("display","block");
				$(el.J_select).css("display","none");		
			});
		},
		placeselect:function(){
			var that = this;
			$("#hotplace li").on('click', function(e) {
				$("#jp").val(DOM.text(this));
				$("#place-select").css("display","none");
			});
			$("#otherpalce li select").on('change', function(ev) {
				var 
					place = $(ev.currentTarget).children('option:selected').text();
				console.log(place);
				$("#jp").val(place);
				$("#place-select").css("display","none");
			});
			
		}

			
		
	});
	return jobplaceShow;
},{
	requires:['sizzle', 'mod/pagination' , 'io/job_apply/job_subscription','node','dom' ,'mod/defender']
});


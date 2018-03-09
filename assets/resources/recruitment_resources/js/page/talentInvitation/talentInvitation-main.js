
	// 变量声明
	// 对象盒子
	var el = {
		J_school:'.state-school',//学校
		J_major:'.state-major',//专业
		J_qualification:'.state-qualification',//学历
		J_year:'.second-line',//毕业年份
		J_place:'.state-place',//生源地
	};

	// 加载函数
    // _getTr();
    show();

    var nums=8;
    var recordCount=$("#recordCount").val();
    var pageLength=Math.ceil(recordCount/nums);
    var last_pageLength=pageLength;
    var currentPage=1;
    var keyword="";
    //点击放大镜图标
    $('.search-btn').click(function() {
        keyword = $(".search-name").val();
        _getTr();
    });

    //向搜索框绑定回车事件
    $("input[type='text']").keypress(function(e){
        if (event.keyCode == 13) {
            event.cancelBubble = true;
            event.returnValue = false;
            $(this).parents("div").find(".search-btn").click();
        }
    });

	//五个下拉框
    var school=-1;
	var major=-1;
	var qualification=-1;
	var time=-1;
	var place=-1;


    function _getTr(curr) {
        school = $(".state-school").val();
        major = $(".state-major").find("option:selected").val();
        qualification = $(".state-qualification").find("option:selected").val();
        time = $(".second-line").find("option:selected").val();
        place = $(".state-place").find("option:selected").val();
        keyword = $(".search-name").val();
        var path_url = _ajax.url.talentInvitation.main.list;
        $.ajax({
            type:"get",
            url:path_url,
            dataType:"json",
            data:{
                schoolName: school,
                majorId: major,
                degreeId: qualification,
                year: time,
                locationId: place,
                currentPage:curr,
                keyword:keyword
            },
            beforeSend: function(data){
            	$(".tip").removeClass('none');
                $(".query-tr").remove();
            },
            success:function(data) {
                $(".tip2").addClass('none');
                $(".tip").addClass('none');
                var tr = "";
                if (data[0].dataCount != 0) {
                    $.each(data,function(index,val){
                        $.each(val.data,function(i,o) {
                            tr += '<tr class="query-tr"><td style="display: none" class="userId">'+o.user_id+
                             '</td><td><img src="' + o.head_url +
                             '"/></td><td class="text-left"><p class="name">' + o.realname +
                             '<span class="nbsp1">' + o.year +
                             '届</span></p><p class="university">' + o.school_name +
                             '<span class="nbsp2">' + o.major_name +
                             '</span></p><p class="place">生源地<span class="specific">' + o.account_place +
                             '</span><span class="education">学历</span><span class="specific">' + o.degree +
                             '</span></p></td><td class="date"><p>' +o.intent_city +
                             '</p><p>'+o.intent_salary+'</p><p>' +o.intent_require+
                             '</p></td><td><p class="button resume"><a href="detail/detail/user_id/' +o.user_id+
                             '">查看简历</a></p><p class="button invite" data-invite="'+o.is_invited+'">邀请投递</p></td></tr>';
                        });
                    $(".query-table").append(tr);
                    recordCount=val.dataCount;
                    pageLength=Math.ceil(recordCount/nums);
                        if(pageLength!=last_pageLength){
                            show();
                            last_pageLength=pageLength;
                        }
                    });
                    // 对已经邀约过的人改变样式
                    $('p[data-invite="1"]').css({
                        background: '#ccc',
                    }).text('已邀请');
                }
                else{
                    $(".tip2").removeClass('none');
                }
            },
            error:function(){
                console.log('异常，获取JSON数据异常');
            }
        });
    }

    function show(curr) {
        // 分页开始
        layui.use(['layer', 'laypage'], function () {
            var layer = layui.layer,
                laypage = layui.laypage;
            //调用分页
            laypage({
                cont: 'paging',     //分页的div的id
                skip: true ,        //可以跳跃
                pages: pageLength,   //得到总页数
                //curr: currentPage,   //当前页
                jump: function (obj) {  //当前跳跃的的页数
                    var curr = obj.curr;
                    currentPage=curr;
                    _getTr(curr);
                }
            });
        });
    }

	//下拉框发生改变
	$(".five-option .five").change(function(){
		school = $(".state-school").val();
		major = $(".state-major").find("option:selected").val();
        qualification = $(".state-qualification").find("option:selected").val();
		time = $(".second-line").find("option:selected").val();
		place = $(".state-place").find("option:selected").val();
		_getTr();
	});

    $(".query-table").on('click',".invite",function(){
			var userId=$(this).parents("tr").find(".userId").text();
			var flag = confirm("确定要邀请投递吗?");
            var path_url = _ajax.url.talentInvitation.main.invite;
			if (flag) {

				$.ajax({
					type:"get",
					url:path_url,
					dataType:"json",
					data:{
			          user_id:userId
					},
					success:function(data) {

						$.each(data,function(index,val) {
							var code=val.code;
							if (code == 0) {
								alert("邀请投递成功");
								_getTr(currentPage);
							}else if(code == 1){
								alert("已经邀约,请勿重复邀请");
							}
						})
					},
					error:function(){
						console.log("异常，获取JSON数据异常");
					}
				});
			}
		});


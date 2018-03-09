
	// 变量声明
	// 对象盒子
	var el = {
		J_state: '.state',//邀约状态
	};
	

	// 加载函数
    var nums=8;
    var recordCount=$("#recordCount").val();
    var pageLength=Math.ceil(recordCount/nums);
    var last_pageLength=pageLength;
    var currentPage=1;
	show();
    // _getTr();

    var status=0;

	
	function _getTr(curr) {
        status = $(".state").find("option:selected").val();
        var path_url = _ajax.url.talentInvitation.record.list;
		$.ajax({
			type:"get",
			url:path_url,
			dataType:"json",
			data:{
                currentPage:curr,
                status: status
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
                	$.each(data,function(index,val) {
						$.each(val.data,function function_name(i,o) {
							tr +='<tr class="query-tr"><td style="display: none" class="userId">'+o.user_id+
                        	'</td><td><img src="'+o.head_url+
                        	'"/></td><td class="text-left"><p class="name">'+o.realname+
                        	'<span class="nbsp1">'+o.year+
                        	'届</span></p><p class="university">'+o.major_name+
                        	'<span class="nbsp2">'+o.school_name+
                        	'</span></p><p class="place">生源地<span class="specific">'+o.account_place+
                        	'</span><span class="education">学历</span><span class="specific">'+o.degree+
                        	'</span></p></td><td class="date">'+o.created_time+
                        	'</td><td><p class="button"><a href="detail/detail/user_id/' +o.user_id+
                        	'">查看简历</a></p><p class="button remove">移出列表</p></td></tr>';
						});
                        $(".query-table").append(tr);
                        recordCount=val.dataCount;
                        pageLength=Math.ceil(recordCount/nums);
                        if(pageLength!=last_pageLength){
                            show();
                            last_pageLength=pageLength;
                        }
                    });
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
//					curr: currentPage,   //当前页
                jump: function (obj) {  //当前跳跃的的页数
                    var curr = obj.curr;
                    currentPage=curr;
                    _getTr(curr);
                }
            });
        });
    }
	
	//下拉框发生改变
	$(".state").change(function(){
		status = $(".state").find("option:selected").val();
		_getTr();
	});

    $(".query-table").on('click',".remove",function(){
        var userId=$(this).parents("tr").find(".userId").text();
        var flag = confirm("确定要移出列表吗?");
        var path_url = _ajax.url.talentInvitation.record.del;
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
                            alert("移出列表成功");
                            _getTr(currentPage);
                        }
                    })
                },
                error:function(){
                    console.log("异常，获取JSON数据异常");
                }
            });
        }
    });


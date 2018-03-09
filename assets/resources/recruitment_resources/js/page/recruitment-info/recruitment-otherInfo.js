/**
 * Created by xiaozhi on 2017/7/28.
 */
// 搜索表单开始
$(".search-btn").click(function(){
    $(".search-form").submit();
    return false;
});
// 搜索表单结束
// 分页开始
var nums=10;//每页显示的记录数
var jobName=$("#job-name").val();
var companyUserName=$("#companyUserName").val();
var recordCount=$("#recordCount").val();
var pageLength=Math.ceil(recordCount/nums);
function show(curr){       //把后台的数据展示出来
    var path_url = _ajax.url.recruitment_info.otherInfo.list;
    $.ajax({
        url:path_url,
        type:"get",
        dataType:"json",
        data:{
            currentPage:curr,
            keyword:jobName,
            companyUserName:companyUserName
        },
        success:function(data){
            var dataCount = data[0].dataCount; // 总记录数
            if (dataCount != 0){
                $(".data").addClass("data-none");
                $("#tbody").empty();
                var tr="";
                $.each(data,function(index,val){
                    var keyword=val.keyword;
                    $.each(val.items,function(i,o){
                        var publish1 = { // 发布状态hook 
                            "0": "未发布",
                            "1": "已发布"
                        } 
                        var publish2 = { // 审核状态hook
                            "-1": "待审核",
                            "0": "未通过",
                            "1": "已通过"
                        } 
                        tr+="<tr><td>"+o.name+"</td><td>"+o.positionType+"</td><td>"+o.degree+"</td><td>"+o.recruitment_num+"</td><td>"+ o.company_user_name +"</td><td>"+publish1[o.is_publish]+"</td><td>"+publish2[o.is_ok]+"</td><td class='operation'><a href='"+ _ajax.url.recruitment_info.otherInfo.detail + o.id +"&currentPage="+curr+"&keyword="+keyword+ "' class='look-detail'>查看</a></td></tr>";
                    });
                    $("#tbody").append(tr);
                });
            }
            else{
                $(".data").removeClass("data-none");
            }
        },
        error:function(){
            console.log("json数据异常！");
        }
    });
};
// 分页结束

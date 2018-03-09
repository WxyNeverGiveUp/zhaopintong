/**
 * Created by xiaozhi on 2017/7/28.
 */
// 搜索表单开始
$(".search-btn").click(function(){
    $(".search-form").submit();
    return false;
});


// 搜索表单结束
// 获取url中传递的参数，为了获取当前页数开始
var Request = new Object();//获取url中的参数
Request = GetRequest();
var currentPage = Request['currentPage'];//当前页
var companyUserName= Request['companyUserName'];
function GetRequest() {
    //url例子：XXX.aspx?ID=" + ID + "&Name=" + Name；
    var url = location.search; //获取url中"?"符以及其后的字串
    var theRequest = new Object();
    if(url.indexOf("?") != -1)//url中存在问号，也就说有参数。
    {
        var str = url.substr(1);
        strs = str.split("&");
        for(var i = 0; i < strs.length; i ++)
        {
            theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
        }
    }
    return theRequest;
}
// 获取url参数结
// 分页开始
var nums=10;//每页显示的记录数
var jobName=$("#job-name").val();
var companyUserName=$("#companyUserName").val();
var recordCount=$("#recordCount").val();
var pageLength=Math.ceil(recordCount/nums);
function show(curr){       //把后台的数据展示出来
    var path_url = _ajax.url.recruitment_info.info.list;
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
                        if (o.is_publish == 0) {
                            tr+="<tr><td>"+o.name+"</td>"+
                            "<td>"+o.positionType+"</td>"+
                            "<td>"+o.degree+"</td>"+
                            "<td>"+o.recruitment_num+"</td>"+
                            "<td>"+publish1[o.is_publish]+"</td>"+
                            "<td>"+publish2[o.is_ok]+"</td>"+
                            "<td class='operation'>"+
                            "<a href='"+ _ajax.url.recruitment_info.info.detail + o.id +"&currentPage="+curr+"&keyword="+keyword+ "' class='look-detail'>查看</a>"+
                            "<a href='"+ _ajax.url.recruitment_info.info.update + o.id + "&currentPage="+curr+"&keyword="+keyword+ "'>修改</a>"+
                            "<a href='javascript:;' class='publish' data-positionId="+o.id+">发布</a>"+
                            "<a href='javascript:;' class='cancle-publish' id="+o.id+">取消发布</a>"+
                            "<a href='javascript:;' class='delete' id="+o.id+">删除</a>"+
                            "</td></tr>";
                        }
                        else{
                            tr+="<tr><td>"+o.name+"</td>"+
                            "<td>"+o.positionType+"</td>"+
                            "<td>"+o.degree+"</td>"+
                            "<td>"+o.recruitment_num+"</td>"+
                            "<td>"+publish1[o.is_publish]+"</td>"+
                            "<td>"+publish2[o.is_ok]+"</td>"+
                            "<td class='operation'>"+
                            "<a href='"+ _ajax.url.recruitment_info.info.detail + o.id +"&currentPage="+curr+"&keyword="+keyword+ "' class='look-detail'>查看</a>"+
                            "<a href='javascript:;' onclick=alert('招聘信息已发布,无法修改!')>修改</a>"+
                            "<a href='javascript:;' class='publish' data-positionId="+o.id+">发布</a>"+
                            "<a href='javascript:;' class='cancle-publish' id="+o.id+">取消发布</a>"+
                            "<a href='javascript:;' class='delete' id="+o.id+">删除</a>"+
                            "</td></tr>";
                        }
                        
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

// 取消发布start
$("body").on('click',"a.cancle-publish",function(event){
    if($(this).attr("value")==0) {
        alert("您还未发布！");
    } else if($(this).parent().prev().prev().text()=="未发布") {
        alert("您还未发布！");
    }
    else {
        $(".shade").show();
        // 弹窗展示在浏览器窗口最中间
        var top = ($(window).height() - $(".popup").height())/2;
        var left = ($(window).width() - $(".popup").width())/2;
        var scrollTop = $(document).scrollTop();
        var scrollLeft = $(document).scrollLeft();
        $(".popup").css( { position : 'absolute', 'top' : top + scrollTop, left : left + scrollLeft } ).show();
        var position=$(event.target).parent().parent().children().eq(0).text();
        $("#positionName").val(position);
        var $target=$(event.target)
        var positionId=$target.attr("id");
        $("input.positionId").val(positionId);
        $("#submit").click(function(){
            return checkNull();
        });
        // 验空start
        function checkNull(){
            if($("#cancelReason").val()==""){
                $("#cancelReason").addClass("red");
                return false;
            }
            else{
                $(".shade").hide();
                $(".popup").hide();
                return true;
            }
        }
    }

});
// 验空end
$("body").on("click","#cancle",function(){
    $(".shade").hide();
    $(".popup").hide();
});
// 取消发布end
// 删除操作开始
$("body").on("click","a.delete",function(event){
    $(".shade").show();
     // 弹窗展示在浏览器窗口最中间
    var top = ($(window).height() - $(".popup-delete").height())/2;   
    var left = ($(window).width() - $(".popup-delete").width())/2;   
    var scrollTop = $(document).scrollTop();   
    var scrollLeft = $(document).scrollLeft();   
    $(".popup-delete").css( { position : 'absolute', 'top' : top + scrollTop, left : left + scrollLeft } ).show();   
    var $target=$(event.target);
    var Id=$target.attr("Id");
    var curr=$("input.currentPage").val();
    var keyword=$("#job-name").val();
    var hrefValue= _ajax.url.recruitment_info.info.del + Id+"&currentPage="+curr+"&keyword="+keyword;
    $(".confirm").attr("href",hrefValue);
    $("body").on("click","a.confirm",function(){
        $(".shade").hide();
        $(".popup-delete").hide();
        $target.parent().parent().remove();
    });
    $("body").on("click","a.cancel",function(){
        $(".shade").hide();
        $(".popup-delete").hide();
    });
});
// 删除操作结束

/*列表页发布招聘信息ajax*/
$("body").on("click","a.publish",function(event){
    var that = $(this),
        positionId = that.attr('data-positionId'),
        path_url = _ajax.url.recruitment_info.info.publish;

    $.ajax({
        url: path_url,
        type: 'get',
        dataType: 'json',
        data: {
            positionId: positionId
        },
        success: function(data) {
            that.parent().prev().prev().text('已发布');
        },
        error:function(){
            console.log("json数据异常！");
        }
    })
    
});
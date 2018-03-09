/**
 * Created by wangjf on 2017/8/5.
 */

// 搜索表单结束
// 获取url中传递的参数，为了获取当前页数开始
// var Request = new Object();//获取url中的参数
// Request = GetRequest();
// var currentPage = Request['currentPage'];//当前页
// var last_update= Request['last_update'];
// function GetRequest() {
//     //url例子：XXX.aspx?ID=" + ID + "&Name=" + Name；
//     var url = location.search; //获取url中"?"符以及其后的字串
//     var theRequest = new Object();
//     if(url.indexOf("?") != -1)//url中存在问号，也就说有参数。
//     {
//         var str = url.substr(1);
//         strs = str.split("&");
//         for(var i = 0; i < strs.length; i ++)
//         {
//             theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
//         }
//     }
//     return theRequest;
// }
// 获取url参数结束
// 分页开始
var nums=8;//每页显示的记录数
var last_update=$("#last_update").val();
var recordCount=$("#recordCount").val();

var pageLength=Math.ceil(recordCount/nums);
function show(curr){       //把后台的数据展示出来
    var path_url = _ajax.url.recruitment_info.lastDeliver.list;
    $.ajax({
        url:path_url,
        type:"get",
        dataType:"json",
        data:{
            currentPage:curr,
            name:last_update

        },
        success:function(data){
            // var dataCount = data[0].dataCount; // 总记录数
            // if (dataCount != 0){
            //     $(".data").addClass("data-none");
                $("#tbody ").empty();
                var tr="";
                $.each(data,function(index,val){
                    $.each(val.data,function(i,o){
                        // var positionId=o.id;
                        tr += '<tr class="query-tr"><td><img src=" '+ o.head_url +
                        	' "/></td><td class="text-left"><p class="name">' + o.realname +
                            '<span class="nbsp1">' + o.year +
                            '届</span></p><p class="university">' + o.school_name  +
                            '<span class="nbsp2">' + o.major_name +
                            '</span></p><p class="place">生源地<span class="specific">' + o.account_place +
                            '</span><span class="education">学历</span><span class="specific">' + o.degree +
                            '</span></p></td><td class="position">' +o.deliver_position +
                            '</td><td class="date">'+o.create_time+
                            '</td><td><p class="button"><a href="'+ _ajax.url.recruitment_info.lastDeliver.detail +o.user_id+
                            '">查看简历</a></p></td></tr>';
                   });
                    $("#tbody").append(tr);
                });
            // }
            // else{
            //     $(".data").removeClass("data-none");
            // }
        },
        error:function(){
            console.log("json数据异常！");
        }
    });
};
// 搜索表单开始
$(".search-btn").click(function(){
    $(".search-form").submit();
    return false;
});
// 分页结束






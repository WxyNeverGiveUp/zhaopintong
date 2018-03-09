var major1=-1, // 默认值
    major2=-1, // 默认值
    major3=-1, // 默认值
    year=-1, // 默认值
    province=-1, // 默认值
    sort=-1, // 默认值
    data = []; // 调用数据的数组


/*获取省*/
_getProvince();
function _getProvince(){
    var pro="";
    $.ajax({
        url: 'http://www.dsjyw.net/api/nenu/getNenuInfo',
        type: 'GET',
        dataType: 'json',
        success: function (data) {

            $.each(data.data, function(index, val) {
                if (val.provinceCode) {
                    pro += '<option value=' + val.provinceCode + '>' + val.provinceName + '</option>';
                }

            });
            $(".state-province").append(pro);
        }
    })
}
/*学历联动*/
//如果学历发生变动
$(".major1").change(function() {
    $(".major2").empty();
    $(".major2").append('<option value="-1">请先选择学历</option>');

    //获取选择的学历的id
    var Id = $(this).find('option:selected').val();
    $.ajax({
        url: '/api/nenu/getNenuInfo',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            $.each(data.data, function(index, val) {
                var major2 = "";
                if (Id == val.qualificationId) {
                    $.each(val.majorDivision, function(i,o) {
                        major2 += '<option value =' + o.majorDivisionId + '>' + o.majorDivision + '</option>';
                    });
                }
                $(".major2").append(major2);
            });
        }
    })
});
//如果大专业发生变动
$(".major2").change(function() {
    $(".major3").empty();
    $(".major3").append('<option value="-1">请先选择专业大类</option>');

    var Id = $(this).find('option:selected').val();
    $.ajax({
        url: '/api/nenu/getNenuInfo',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            $.each(data.data, function(index, val) {
                var major3 = "";
                if (Id != val.qualificationId) {
                    $.each(val.majorDivision, function(i, o) {
                        if (Id == o.majorDivisionId) {
                            $.each(o.majorClass, function(idx, od) {
                                major3 += '<option>' + od.majorClass + '</option>'
                            });
                        }
                    });
                }
                $(".major3").append(major3);
            });
        }
    });
});
/*查询*/
_getTr(); // 默认触发一次查询
function _getTr(curr) {
    major1 = $(".major1").find("option:selected").val();
    major2 = $(".major2").find("option:selected").val();
    major3 = $(".major3").find("option:selected").val();
    year=$(".second-line").find("option:selected").val();
    province=$(".state-province").find("option:selected").val();
    sort=$(".state-sort").find("option:selected").val();

    $.ajax({
        type:"get",
        url: '/api/nenu/listJson',
        dataType:"json",
        data:{
            major1: major1,
            major2: major2,
            major3: major3,
            year: year,
            province: province,
            sort: sort,
            currentPage: curr,
        },
        beforeSend: function(data){
            $(".tip").removeClass('none');
            $(".query-tr").remove();
        },
        success:function(allData) {
            data = allData.data;
            $(".tip2").addClass('none');
            $(".tip").addClass('none');
            if (data.dataCount != 0) {
                $(function() {
                    // 分页开始
                    layui.use(['layer', 'laypage'], function () {
                        var layer = layui.layer,
                            laypage = layui.laypage;
                        //调用分页
                        laypage({
                            cont: 'paging',
                            count: data.length,
                            limit: 10,
                            pages: Math.ceil(data.length/10),
                            jump: function(obj){
                                //模拟渲染
                                document.getElementById('query-body').innerHTML = function(){
                                    var arr = [],
                                        tr = "", // 存放数据的dom
                                        thisData = data.concat().splice(obj.curr*obj.limit - obj.limit, obj.limit);
                                    layui.each(thisData, function(index, o){
                                        tr = '<tr class="query-tr"><td style="display: none" class="userId">'+o.stuNumber+
                                            '</td><td class="text-left"><p class="name">' + o.name +
                                            '<span class="nbsp1">' + o.sex +
                                            '</span></p><p class="university">' + o.majorName +

                                            '<span class="education">学历</span><span class="specific">' + o.majorQualification +
                                            '</span></p><p class="place">生源地<span class="specific">' + o.originPlace +
                                            '</span></p></td><td class="date"><p>预期月薪：' +o.monthlyPay +
                                            '</p><p>第一志愿省：'+o.firstProvince+'</p><p>第一志愿市：' +o.firstCity+
                                            '</p></td><td><p class="button invite" data-invite="'+o.stuNumber+'">邀请投递</p></td></tr>';
                                        arr.push(tr);
                                    });
                                    return arr.join('');
                                }();
                            }
                        });
                    });
                });
                // 对已经邀约过的人改变样式
                $('p[data-invite="1"]').css({
                    background: '#ccc',
                }).text('已邀请');
            }
            else{
                $(".tip2").removeClass('none');
            }
            /*分页*/
        },
        error:function(){
            console.log('异常，获取JSON数据异常');
        }
    });
}
$(".six-option .six").change(function(){
    _getTr();
});
/*邀请投递按钮*/
$(".query-table").on('click',".invite",function(){
    var userId=$(this).parents("tr").find(".userId").text(),
        flag = confirm("确定要邀请投递吗?");
    if (flag) {

        $.ajax({
            type:"get",
            url: 'http://www.dsjyw.net/api/nenu/invite',
            dataType:"json",
            data:{
                stuNum:userId
            },
            success:function(data) {

                $.each(data,function(index,val) {
                    var code = val.code;
                    if (code == 0) {
                        alert("邀请投递成功");
                        // _getTr(currentPage);
                    }else if(code == 1){
                        alert("已经邀约,请勿重复邀请");
                    }else if(code == 2){
                        alert("邀约失败，该学生未在联盟网中进行过注册，不能进行邀约");
                    }
                })
            },
            error:function(){
                console.log("异常，获取JSON数据异常");
            }
        });
    }
});
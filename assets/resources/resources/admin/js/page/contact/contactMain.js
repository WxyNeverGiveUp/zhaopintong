
//点击删除按钮
$(".del").on('click', function () {
    var flag = confirm("确定要删除当前联系人吗?");
    if (flag) {
        $.ajax({
            type: "get",
            url: path_url,
            dataType: "json",
            success: function (data) {
                console.log("success");
            },
            error: function () {
                console.log("异常，获取JSON数据异常");
            }
        });
    }
});

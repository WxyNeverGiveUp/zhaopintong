$(function(){

	$(".btn").on('click',function() {
    	var name=$(".name").val();
    	var sex = $(".sex").find("option:selected").val();
    	var telephone=$(".telephone").val();
    	var email=$(".email").val();
    	var sort = $(".sort").find("option:selected").val();
    	var classmate = $(".classmate").find("option:selected").val();
    	var phone=$(".phone").val();
        $.ajax({
            url: 'http://www.dsjyw.net/api/nenu/getNenuInfo',
            type: 'GET',
            dataType: 'json',
            data: {
                name:name,
                sex:sex,
                telephone:telephone,
                email:email,
                sort:sort,
                classmate:classmate,
                phone:phone,
            },
            success: function (data) {
                console.log("success");
            }
        })
    });

})

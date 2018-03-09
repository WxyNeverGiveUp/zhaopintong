$(function(){
    // 职务对应联动
    var infoContact;//定义联系人类型为全局变量，验证函数中需要判别值
    var otherContact;
    $("#info-contact").change(function(){
        infoContact=$("#info-contact").is(':checked');
        otherContact=$("#other-contact").is(':checked');
        if(infoContact && otherContact){
            $(".contact-sex").show();
            $(".contact-classmate").show();
            $(".contact-position").show();
            $(".contact-telephone").show();
            $(".contact-sex input").addClass("required");
            $(".contact-classmate input").addClass("required");
            $(".contact-position input").addClass("required");
        }
        else if(infoContact){
            $(".contact-sex").show();
            $(".contact-classmate").show();
            $(".contact-position").hide();
            $(".contact-position input").removeClass("required");
            $(".contact-telephone").hide();
            $(".contact-telephone input").removeClass("required");
        }
        else if(otherContact){
            $(".contact-position").show();
            $(".contact-telephone").show();
            $(".contact-sex").hide();
            $(".contact-sex input").removeClass("required");
            $(".contact-classmate").hide();
            $(".contact-classmate input").removeClass("required");
        }
        else{
            $(".contact-sex").hide();
            $(".contact-classmate").hide();
            $(".contact-position").hide();
            $(".contact-telephone").hide();
        }
    });
    $("#other-contact").change(function(){
        infoContact=$("#info-contact").is(':checked');
        otherContact=$("#other-contact").is(':checked');
         if(infoContact && otherContact){
            $(".contact-sex").show();
            $(".contact-classmate").show();
            $(".contact-position").show();
            $(".contact-telephone").show();
            $(".contact-sex input").addClass("required");
            $(".contact-classmate input").addClass("required");
            $(".contact-position input").addClass("required");
        }
        else if(infoContact){
            $(".contact-sex").show();
            $(".contact-classmate").show();
            $(".contact-position").hide();
            $(".contact-position input").removeClass("required");
            $(".contact-telephone").hide();
            $(".contact-telephone input").removeClass("required");
        }
        else if(otherContact){
            $(".contact-position").show();
            $(".contact-telephone").show();
            $(".contact-sex").hide();
            $(".contact-sex input").removeClass("required");
            $(".contact-classmate").hide();
            $(".contact-classmate input").removeClass("required");
        }
        else{
            $(".contact-sex").hide();
            $(".contact-classmate").hide();
            $(".contact-position").hide();
            $(".contact-telephone").hide();
        }
    });    
    // 验证手机号
    function isPhoneNo(phone) { 
        var pattern = /^1[3|4|5|7|8][0-9]{9}$/;
        return pattern.test(phone); 
    }
    //验证邮箱 
    function isEmailNo(email) { 
         var pattern = /^(\w)+(\.\w+)*@(\w)+((\.\w{2,3}){1,3})$/;
         return pattern.test(email); 
    }
    // 验证固定电话
    function isTelePhone(telePhone) { 
        var pattern = /^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/;
        return pattern.test(telePhone); 
    }
    // 验证函数
    function formValidate() {
        var flag,//验证必填项
            flag1,//验证手机号码
            flag2,//验证邮箱
            flag3=false,//验证固定电话
            flag4,//验证公司
            flag5;//联系人类别必填
        // 所有必填项验证                    
        $(".required").each(function(){
            var textVal=$(this).val();
            if(textVal.length == 0){
                $(this).css('border','1px solid #F61E1E');
                flag=true;
            }
            else{
                flag = false;
                $(this).css('border','1px solid #ccc');
            }
        });
        // 判断手机号码
        if(isPhoneNo($('#contact-mobile').val()) == false) { 
            $('#contact-mobile').css('border','1px solid #F61E1E');
            flag1=true;
        }
        else{
            flag1 = false;
            $(this).css('border','1px solid #ccc');
        }
        // 判断邮箱
        if(isEmailNo($('#contact-email').val()) == false){
            $('#contact-email').css('border','1px solid #F61E1E');
            flag2=true;
        }
        else{
            flag2 = false;
            $(this).css('border','1px solid #ccc');
        }

        //验证固定电话
        if($('#contact-telephone').val().length ==""){
            flag3 = false;
            $(this).css('border','1px solid #ccc');
        }
        else if(isTelePhone($('#contact-telephone').val()) == false) { 
            $('#contact-telephone').css('border','1px solid #F61E1E');
            flag3=true;
        }
        // 验证公司
        if($("#contact-company").val()=="0"){
            flag4=true;
            $("#contact-company").next().css('border','1px solid #F61E1E');
        }
        else{
            flag4 = false;
            $("#contact-company").next().css('border','1px solid #ccc');
        }
        //联系人类别必填
        if(!infoContact&&!otherContact){
            $("#info-contact").prev().css("color","red");
            flag5=true;
        }
        else{
            $("#info-contact").prev().css("color","#000");
            flag5=false;
        }
        // 如果没有错误则提交
        if(flag1 || flag2 || flag || flag3 || flag4 || flag5) {
            return false;
        }
        else {
          $("form").submit();
        }
    }
    $('form').submit(function() {
      return formValidate();
    });
});
$(function(){
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
    // 验证函数
    function formValidate() {
        var flag,//验证必填项
            flag1,//验证手机号码
            flag2,//验证邮箱
            flag3;//验证联系人类别
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
        })
        // 联系人类型下拉框验证
        if($("#contact-type").val()=="0"){
            $("#contact-type").css('border','1px solid #F61E1E');
            flag3=true;
        }
        else{
            $(this).css('border','1px solid #ccc');
            flag3=false;
        }
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
        // 如果没有错误则提交
        if(flag1 || flag2 || flag || flag3) {
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
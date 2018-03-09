<?php
 function send_sms($content,$cellphone){
     //提交短信
     $post_data = array();
     $post_data['userid'] = 12456;
     $post_data['account'] = 'zhuoyin';
     $post_data['password'] = '123456';
     //$post_data['content'] = '【东北高师就业联盟网】同学您好，近期宣讲会：上达电子（深圳）有限公司宣讲会，时间:2015-09-23 08:00，地点:就业中心信息发布厅（本部北苑三楼A15);上海德萨科电子技术有限公司长春分公司,时间:2015-09-23 09:30，地点:净月就业中心信息发布厅（学生活动中心2楼）';
     $post_data['content'] = $content;
     $post_data['mobile'] = $cellphone;
     $post_data['sendtime'] = ''; //不定时发送，值为0，定时发送，输入格式YYYYMMDDHHmmss的日期值
     $url='http://www.qf106.com/sms.aspx?action=send';
     $o='';
     foreach ($post_data as $k=>$v)
     {
         $o.="$k=".urlencode($v).'&';//短信内容需要用urlencode编码下
     }
     $post_data=substr($o,0,-1);
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_POST, 1);
     curl_setopt($ch, CURLOPT_HEADER, 0);
     curl_setopt($ch, CURLOPT_URL,$url);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
     $result = curl_exec($ch);
 }
?>
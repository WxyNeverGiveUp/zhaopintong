 (function(){
     var
         loc = window.location,
         host = loc.host,
         port = loc.port,
         $website = 'http://' + host + '/assets/resources/';

     PW_CONFIG = {
         host :  host,
         libUrl : $website+'puiresources/',//
         libTag:'2012080142',
         appTag: '201208142',
         pkgs:[{
             name:'app',
             path:$website+'resources/js/',//
             charset:'utf-8'
         }],
         //组件库的配置参数
         modSettings:{
             dialog:{
                 topLayer: 1,
                 hasOverlay: true
             },
             defender: {
                 theme: 'inline'
             }
         },
         appParams:{
             //登录
             login:{
                 loginCheckUrl: $website +'user/isLogin' //接口具体使用参看接口文档
             },
             //收藏
             fav:{
                 favAddUrl: $website + 'vip/bookmark/ajax/add'
             }
         }
     };
 })()

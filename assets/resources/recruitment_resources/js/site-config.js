(function(){

    var site = {
        website:'http://www.dsjyw.net/', // 静态站点地址
        // website:'http://dsjyw.myjoin.cn/', // 线上测试地址
        //静态资源地址
        // staticWebsite: 'http://www.appraisal.com/ssm-check/Home/src/main/webapp/resources/'
    }
    // 页面内容
    _ajax = {
        status: 0, //0-前端调试，1-后端调试, 2-后端部署
        website: site.website,
        dynamicWebsite: site.dynamicWebsite, // jsp动态
        //统一错误信息入口
        msg:{
            0: '网络加载错误'
        },
        //ajax地址信息
        url: {
            /*---公用接口---*/
            city_linkage: site.website + 'recruitEntrance/recruitmentInfo/recruitment/CityJson',  // 省市联动
            code: site.website + 'recruitEntrance/recruitEntrance/sendCode', // 发送手机验证码
            imgCode: site.website + 'util/Captcha/Getcode', // 发送图片验证码
            checkImgCode: site.website + 'util/captcha/check?', // 验证图片验证码
            

            /*---私用接口---*/
            // 注册页面
            user:{
               // 未注册单位账号注册页面
               unregistered: {
                    code: site.website + 'recruitEntrance/recruitEntrance/companyDaimaJson', // 判断18位信用代码是否重复
                    phone: site.website + 'recruitEntrance/recruitEntrance/PhoneJson', // 判断电话号码是否重复
               },
               // 已入驻单位账号注册页面
               registered: {
                  code: site.website + 'recruitEntrance/recruitEntrance/companyDaimaJson', // 判断18位信用代码是否重复
                  phone: site.website + 'recruitEntrance/recruitEntrance/PhoneJson', // 判断电话号码是否重复
                  queryName: site.website + 'recruitEntrance/recruitEntrance/QueryNameByDaima', // 根据信用代码查询公司
               }
            },
            // 公司信息页面
            company_info:{
                // 单位成员
                company_member: {
                    del: site.website + 'recruitEntrance/company/companyLoginUser/del', // 联系人删除 动态节点添加操作
                    queryByType: site.website + 'recruitEntrance/company/companyLoginUser/queryByType', // 类别筛选
                }
            },
            // 人才邀约页面
            talentInvitation:{
                // 师大人才
                nenu: {
                    list: site.website + 'recruitEntrance/talentInvitation/discover/listJson', // 加载列表
                    invite: site.website + 'recruitEntrance/talentInvitation/discover/invite', // 邀请人才
                },
                // 发现人才
                main: {
                    list: site.website + 'recruitEntrance/talentInvitation/discover/listJson', // 加载列表
                    invite: site.website + 'recruitEntrance/talentInvitation/discover/invite', // 邀请人才
                },
                // 邀约记录
                record: {
                    list: site.website + 'recruitEntrance/talentInvitation/record/listJson', // 加载列表
                    del: site.website + 'recruitEntrance/talentInvitation/record/remove', // 移除列表
                }
            },
            // 招聘信息列表
            recruitment_info:{
                // 申请
                apply: {
                    major: site.website + 'recruitEntrance/recruitmentInfo/recruitment/MinorSpecialtyJson', // 选择多专业
                },
                // 本单位招聘发布管理
                info: {
                    list: site.website + 'recruitEntrance/recruitmentInfo/recruitment/json', // 加载列表
                    del: site.website + 'recruitEntrance/recruitmentInfo/recruitment/del?id=', //删除
                    detail: site.website + 'recruitEntrance/recruitmentInfo/recruitment/detail/?id=', // 查看详情
                    update: site.website + 'recruitEntrance/recruitmentInfo/recruitment/toEdit?id=', // 修改
                    publish: site.website + 'recruitEntrance/recruitmentInfo/publish/jsonPublish', // 发布招聘信息
                },
                // 单位其他招聘信息
                otherInfo: {
                    list: site.website + 'recruitEntrance/recruitmentInfo/recruitment/json', // 加载列表
                    detail: site.website + 'recruitEntrance/recruitmentInfo/recruitment/detail/?id=', // 查看详情
                },
                // 最近投递
                lastDeliver: {
                    // 加载列表
                    list: site.website + 'recruitEntrance/recruitmentInfo/recruitment/listAllJson', // 加载列表
                    detail: site.website + 'recruitEntrance/talentInvitation/detail/detail?user_id=', // 查看详情
                }
            }
        },
    }
})();
(function(){

    var host = window.location.host;

    var site ={
        website:'http://'+host+'/',//站点地址
        staticWebsite: 'http://'+host+'/assets/resources/', // 前端服务器地址
        puiWebsite: 'http://'+host+'/assets/resources/puiresources/' //pui地址
    }

    _pw_env = {
        status: 0, //0-前端调试，1-后端调试, 2-后端部署
        website: site.website,
        staticWebsite: site.staticWebsite,
        puiWebsite: site.puiWebsite,
        tag: '',
        pkgs:[
            {
                name: 'io',
                path: site.staticWebsite + 'resources/js/'
            },
            {
                name: 'widget',
                path: site.staticWebsite + 'resources/js'

            },
            {
                name: 'module',
                path: site.staticWebsite + 'resources/js/'
            },
            {
                name: 'page',
                path: site.staticWebsite + 'resources/js/'
            }
        ],
        preload: ['sizzle','event'],//预加载模块
        //对pui各个组件的一个
        modSettings:{
            notifier: {
                top: 100
            },
            dialog:{
                // opacity: 0.1,
                position: 'fixed',
                theme: 'white',
                title: '提示信息',
                themeUrl: site.staticWebsite + 'resources/css/widget/core.css'
            },
            defender:{
                themeUrl: site.staticWebsite + 'resources/css/widget/core.css'  
            },
            scroll:{
                cursorborderradius: 0,
                cursorcolor: '#3d3d3d'
            },
            tooltip:{
                position: { 
                    my: 'tc',
                    at: 'bc' //options: tl,tc,tr, rt,rc,rb, bl,bc,br,lt,lc,lb 
                },
                styles:{
                    uri: site.staticWebsite + 'resources/css/widget/core.css'
                }
            }
        },
        //统一错误信息入口
        msg:{
        },
        //地址信息
        url:{
            module:{
                linkage:{
                    getProvice:site.website+'user/cache/linkProvince',
                    getCity:site.website+'user/cache/linkCity'
                }
            },
            /*公司*/
            company:{
                company:{
                    //右侧单位列表发出的url
                    getJoblist:site.website+'company/company/searchJson' ,
                    // 获取最新的关注数发出的url
                    getFollowNumber:site.website+'company/company/concern' ,
                    //生成弹出层地点li发出的url
                    createLocationLi:site.website+'company/company/location' ,
                    //生成弹出层行业li发出的url
                    createIndustryLi:site.website+'company/company/industry'
                },
                jobDetail:{
                     // 获取最新的关注数发出的url
                    getFollowNumber:site.website+'company/company/concern' ,
                    isCollect:site.website+'position/position/concern',
                    getDay:site.website+'job/resume/day'
                },
                post:{
                    //获取同一个单位发表的招聘职位
                    getJoblist:site.website+'company/company/positionJsonByCompany'
                },
                companyPreach:{
                    getPreachList:site.website+'company/company/cTJson',
                    isEnroll:site.website+'company/company/enrollCT',
                    getDemandPreach:site.website+'company/company/preachJson'
                },
                remoteInterview:{
                    getRemoteInterviewList:site.website+'company/remoteInterview/json',
                    isEnroll:site.website+'company/remoteInterview/enroll',
                    isRightTime:site.website+'company/remoteInterview/isRightTime'
                },
                experience:{
                    getExperienceList:site.website+'company/interviewExperience/json',
                    getPraiseNum:site.website+'company/interviewExperience/praise'
                },
                comment:{
                    getCommentList:site.website+'company/companyComment/json',
                    getPraiseNum:site.website+'company/companyComment/praise'
                }
            },
            /*招聘*/
            recruitment:{
                education:{
                    //获取右侧单位列表发出的url
                    getJoblist:site.website+'position/position/searchJson' ,
                    //生成弹出层职位类型li发出的url
                    createPositionTypeLi:site.website+'position/position/positionType',
                    //生成弹出层专业li发出的url
                    createMajorLi:site.website+'position/position/positionSpecialty',
                    //生成弹出层单位性质li发出的url
                    createPropertyLi:site.website+'position/position/property',
                    //生成弹出层地点li发出的url
                    createLocationLi:site.website+'position/position/location',
                    //当职位被收藏或取消收藏时发出的url
                    isCollect:site.website+'position/position/concern'
                }
            },   
            job_apply:{
                jobSubscription:{
                    createSelect:site.staticWebsite+'js/prov-and-city.json',
                    createLi:site.website+'job/order/position'
                },
                myHome:{
                    addSpecialty:site.website+'job/job/addspecialperformance',
                    delSpecialty:site.website+'job/job/deletespecialperformance',
                    getCity:site.website+'job/job/provincecity',
                    isEdited:site.website+'job/job/printbasic',
                    isEduEdited:site.website+'job/job/printspecialty',
                    getMajor:site.website+'job/job/studyspecialty',
                    putValidCode:site.website+'code/validateCode'
                },
                collect:{
                    postInfo:site.website+'job/position/json',
                    collect:site.website+'job/position/concern',
                    getCompanylist:site.website+'job/company/json',
                    isCollect:site.website+'job/company/concern'
                },
                signup:{
                    preachList:site.website+'careerTalk/careerTalk/searchJson',
                    signup:site.website+'careerTalk/careerTalk/enroll2'
                },
                editSubscribe:{
                    delSub:site.website+'job/order/del'
                },
                phone:{
                    delPhone:site.website+'job/study/delPhone'
                },
                editResume:{
                    getBasicInfo:site.website+'job/resume/basicinfo',
                    getYear:site.website+'job/resume/year',
                    getDay:site.website+'job/resume/day',
                    hasBasicInfo:site.staticWebsite+'mock/hasBasicInfo.json',

                    hasContactInfo:site.staticWebsite+'mock/hasContactInfo.json',
                    getContactInfo:site.website+'job/resume/phoneemail',

                    getMajorClassify:site.website+'job/resume/companytradess',
                    delEduExperience:site.website+'job/resume/deleteeducation',
                    getEduInfo:site.website+'job/resume/education',

                    getCerti:site.website+'job/resume/certificate',
                    delCerti:site.website+'job/resume/deletecertificate',

                    getPosiInfo:site.website+'job/resume/schoolduty',
                    getSchool:site.website+'job/resume/school',
                    delSchoolPosi:site.website+'job/resume/deleteschoolduty',

                    getAwardInfo:site.website+'job/resume/award',
                    delSchoolAward:site.website+'job/resume/deleteaward',

                    getLanguages:site.website+'job/resume/language',
                    getExams:site.website+'job/resume/exam',
                    getLanguageInfo:site.website+'job/resume/languages',
                    delLanguage:site.website+'job/resume/deletelanguage',
                    delExam:site.website+'job/resume/deallanguageshan',

                    delIntern:site.website+'job/resume/deletetraining',
                    getInternInfo:site.website+'job/resume/training',
                    getCity:site.website+'job/resume/provincecity',
                    //getIndustry:site.website+'?r=job/resume/studyspecialty',
                    getIndustry:site.website+'job/resume/companytrade',
                    getPosiType:site.website+'job/resume/position',

                    getWorkInfo:site.website+'job/resume/workexperience',
                    delWork:site.website+'job/resume/deleteworkexperience',

                    getProjectInfo:site.website+'job/resume/projectexperience',
                    delProject:site.website+'job/resume/deleteprojectexperience',

                    getTrainInfo:site.website+'job/resume/trainingexperience',
                    delTrain:site.website+'job/resume/deletetrainingexperience',

                    getApplyInfo:site.website+'job/resume/job',
                    delApply:site.website+'job/resume/deletejob',

                    delAttachment:site.website+'job/resume/deletefile',

                    getEditedArray:site.website+'job/resume/finishtime',

                    delSkill:site.website+'job/resume/deleteskill',
                    getSelectedSkill:site.website+'job/resume/selectskill',
                    putSkills:site.website+'job/resume/putskill',
                    getDetail:site.website+'job/resume/detailskill'
                }
            },
            // 宣讲会
            preach:{
                preach:{
                    //生成弹出层行业li发出的url
                    createIndustryLi:site.website+'user/cache/trade',
                    enroll:site.website+'careerTalk/careerTalk/enroll2',
                    getPreachList:site.website+'careerTalk/careerTalk/searchJson',
                    isRightTime:site.website+'careerTalk/careerTalk/isRightTime'
                },
                preachCalendar:{
                    getPreachList:site.website+'careerTalk/careerTalk/calSearchJson',
                    getPreachDay:site.website+'careerTalk/careerTalk/calJson' 
                }
            },
            //毕业生
            graduate:{
                graduate:{
                    createSchoolLi:site.website+'graduate/graduate/school',
                    getGraduateList:site.website+'graduate/graduate/listJson',
                    //createIndustryLi:site.website+'mock/industryLi.json',
                    //生成弹出层专业li发出的url
                    createMajorLi:site.website+'graduate/graduate/specialty',
                    //生成弹出层地点li发出的url
                    createLocationLi:site.website+'graduate/graduate/location'
                }
            },
            //首页
            index:{
                index:{
                    getPreachDay:site.website+'careerTalk/careerTalk/indexCalJson',
                    getCity:site.website+'teacherRecruitment/teacherRecruitment/getCityJson'
                },
                notice:{
                    getNoticeList:site.website+'announcement/announcement/searchJson'
                }
            },
            //用户登录，注册
            user:{
                user:{
                    //isNameRegistered:site.website+'?r=user/user/UserReg',
                    isEmailRegistered:site.website+'user/user/email',
                    isLogin:site.website+'user/user/login',
                    isRegister:site.website+'user/user/reg',
                    putEmail:site.website+'user/forgetPass/toEmail',
                    getUserInfo:site.staticWebsite+'mock/userInfo.json',
                    isModifySuc:site.staticWebsite+'mock/isModifySuc.json',
                    enterJobApply:site.website+'job/job',
                    getCity:site.website+'user/message/city',
                    getMajor:site.website+'user/message/specialType'
                }
            },
            entrance:{
                phone:{
                    del: site.website+'test/api-data/001.json'
                }
            }
        }
    }
})()

/*-----------------------------------------------------------------------------
* @Description:     接待管理部分车辆管理相关js
* @Version:         1.0.0
* @author:          shenj(1073805310@qq.com)
* @date             2014.9.24
* ==NOTES:=============================================
* v1.0.0(2014.9.24):
     初始生成
* ---------------------------------------------------------------------------*/
KISSY.add('page/reception/vehicle',function(S,search,selectAll,suggest,del){
    PW.namespace('page.vehicle');
    PW.page.vehicle = function(param){
        new search(param);
        new selectAll(param);
        new suggest(param);
        new del(param);
    };
},{
    requires:['vehicle/search','module/selectAll','vehicle/suggest','vehicle/del']
});
/*--------------------------------查询--------------------------------*/
KISSY.add('vehicle/search',function(S){
    var
        $ = S.all, on = S.Event.on, DOM = S.DOM, query = DOM.query,
        Pagination = PW.mod.Pagination,
        Calendar = PW.mod.Calendar,
        el = {
            searchForm:'.J_searchForm',//指向查询的表单
            searchBtn:'.J_searchBtn'//指向查询按钮
        };

    function search(param){
        this.opts = param;
        this.init();
    }

    S.augment(search,{
        init:function(){
            this._paging({});
            this._addCalendar();
            this._addEventListener();
        },
        /*分页*/
        _paging:function(param){
            var
                that = this,
                opts = that.opts,
                extraParam = S.merge(opts,{extraParam:param});
            Pagination.client(extraParam);
        },
        /*添加日历*/
        _addCalendar:function(){
            S.each(query('.date'),function(i,o){
                Calendar.client({
                    renderTo: i, //默认只获取第一个
                    select: {
                        rangeSelect: false, //是否允许区间选择
                        dateFmt: 'YYYY-MM-DD',
                        showTime: false //是否显示时间
                    }
                });
            });
        },
        _addEventListener:function(){
            var
                that = this;
            /*点击查询按钮*/
            on(el.searchBtn,'click',function(evt){
                that._search();
            });
        },
        /*查询*/
        _search:function(){
            var
                that = this,
                info = that._serialize();
            that._paging(info);
        },
        /*表单序列化*/
        _serialize:function(){
            var
                info = {};
            info = DOM.serialize(el.searchForm);
            return info
        }
    });

    return search;
},{
    requires:['mod/pagination','mod/calendar']
});
/*-------------------------------------suggest------------------------------------*/
KISSY.add('vehicle/suggest',function(S){
    var
        suggest = PW.module.suggest;

    function suggest(param){
        new suggest(param);
    }

    return suggest;
},{
    requires:['module/suggest']
});
/*---------------------------------------批量删除-----------------------------------*/
KISSY.add('vehicle/del',function(S){
    var
        $ = S.all, on = S.Event.on,
        VehicleHandler = PW.module.reception.vehicle,
        el = {
            delBatchBtn:'.J_delBatch',//指向批量删除按钮
            vehicleList:'#J_template'
        };

    function del(param){
        this.init();
    }

    S.augment(del,{
        init:function(){
            this._addEventListener();
        },
        _addEventListener:function(){
            var
                that = this;
            /*点击批量删除按钮*/
            on(el.delBatchBtn,'click',function(evt){
                that._delVehicle();
            });
        },
        /*删除车辆信息*/
        _delVehicle:function(){
            VehicleHandler.del(el);
        }
    });

    return del;
},{
    requires:['module/reception/vehicle']
});
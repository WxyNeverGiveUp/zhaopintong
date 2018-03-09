/*-----------------------------------------------------------------------------
* @Description:  表单全选组件，针对后台老是有写全选的问题，此处单独提出一个组件，目的是实现高可配置的全选，反选，取消全部的组件(selectall.js)
* @Version:     V1.0.3
* @author:      simon(406400939@qq.com)
* @date         2012.9.3
* ==NOTES:=============================================
* v1.0.0(2012.9.3):
*     初始生成 
* v1.0.1(2012.12.18):
*     修复：
*         修复后期添加节点无法选择的bug
* v1.0.3(2013.04.30):
*     添加toggle选择类型，并且将全选合并到一个函数中
*     代码写的比较复杂，后期找时间优化,
*     所有trigger的选择都放到了所有的页面节点中查找
* v1.0.4(2013-10-13):
*     toggleTrigger如果在控制的checkbox点击的情况下，自动判断当前是否选中
*     如果设置disabled了，则所有的trigger变灰
* ---------------------------------------------------------------------------*/
    

KISSY.add('mod/selectall', function(S, selectAll) {
    PW.namespace('selectAll');
    PW.namespace('SelectAll');
    PW.namespace('mod.Selectall')
    PW.selectall = PW.Selectall = PW.mod.Selectall = function(opts) {
        return new selectAll(opts);
    };
}, {
    requires : ['selectall/base']
})

KISSY.add('selectall/base', function(S) {
    var DOM = S.DOM, get = DOM.get, query = DOM.query, Event = S.Event, on = Event.on, 
        CONFIG = {
            root : '',
            select : '',
            filter : '',
            allTrigger : '.pw-select-all',
            cancelTrigger : '.pw-cancel-all',
            invertTrigger : '.pw-invert',
            toggleTrigger: '.pw-toggle'
        };

    function selectAll(param) {
        var that = this;
        this.opts = S.merge(CONFIG, param);
        this.checkboxs;
        this.init();
    }

    S.augment(selectAll, S.EventTarget);
    S.augment(selectAll, {
        init : function() {
            var that = this;
            //全选生效的根节点
            that.rootDOM = get(this.opts.root);
            //按钮
            that.selectAllBtn = get(that.opts.allTrigger);
            that.cancelBtn = get(that.opts.cancelTrigger);
            that.invertBtn = get(that.opts.invertTrigger);
            that.toggleTrigger = get(that.opts.toggleTrigger);
            //组件可运行状态
            that.status = true;
            //绑定事件
            that._bindEvt();
        },
        /**
         *启用选择功能 
         */
        enable : function() {
            var that = this;
            if (!that.status) {
                that._bindEvt();
                that.status = true;
            }
        },
        /**
         *禁用选择功能 
         */
        disable : function(){
            var that = this;
            if (that.status && that.status == true) {
                that._unbindEvt();
                that.toggleTrigger.disabled = true;
                that.status = false;
            }
        },
        /**
         *事件绑定
         */
        _bindEvt: function(){
            var that = this;
            that._bindRegisteredEvent();
            that._bindSelectAll();
            that._bindCancel();
            that._bindInvert();
            that._bindToggle();
        },
        /**
         *删除绑定事件 
         */
        _unbindEvt: function(){
            var that = this;
            Event.detach(that.selectAllBtn,'click');
            Event.detach(that.cancelBtn,'click');
            Event.detach(that.invertBtn,'click');
            Event.detach(that.toggleTrigger,'click');
        },
        _bindRegisteredEvent: function(){
            var
                that = this,
                checkboxs;
            that._getCheckBox();
            checkboxs = that.checkboxs;
            //添加已注册checkboxes的点击
            on(checkboxs, 'click', function(ev){
                var 
                    t = ev.target;
                that.toggleTrigger.checked = that.hasSelectedAll() ? true : false;
            })
        },
        _bindSelectAll : function() {
            var that = this, trigger = that.selectAllBtn, checkboxs;
           
            on(trigger,'click',function(evt){
                that._selectAll();
                that.fire('all',{select:that});
            })
        },
        _bindCancel : function() {
             var that = this, trigger = that.cancelBtn, checkboxs;
            
            
            on(trigger,'click',function(evt){
                //获取合法checkbox
                that._unSelectAll();
                that.fire('cancel',{select:that});
            })
        },
        _bindInvert : function() {
            var that = this, trigger = that.invertBtn, checkboxs;
            
            on(trigger,'click',function(evt){
                //获取合法checkbox
                that._getCheckBox();
                checkboxs = that.checkboxs;
                S.each(checkboxs, function(o){
                    o.checked = (o.checked) ? false: true;
                });
                that.fire('invert',{select:that});
            })
        },
        _bindToggle: function(){
            var
                that = this,
                trigger = that.toggleTrigger,
                ckbs;
            if(!trigger) return;
            //判断如果toggle trigger的类型为checkbox，如果是，则使用checkbox判断，否则，根据当前全选判断
            if(trigger.nodeName.toLowerCase() == 'input' && trigger.getAttribute('type') == 'checkbox'){
                on(trigger, 'click', function(evt){
                    ckbs = that._getCheckBox();
                    if(!trigger.checked){
                        that._unSelectAll();
                        that.fire('toggle',{type:'unselect',select:that});
                    }else{
                        that._selectAll();
                        that.fire('toggle',{type:'select',select:that});
                    }
                })
            }else{
                on(trigger, 'click', function(evt){
                    ckbs = that._getCheckBox();
                    if(that.hasSelectedAll()){
                        that._unSelectAll();
                        that.fire('toggle',{type:'unselect',select:that});
                    }else{
                        that._selectAll();
                        that.fire('toggle',{type:'select',select:that});
                    }
                })
            }
        },
        hasSelectedAll: function(){
            var
                that = this,
                ckbs = that._getCheckBox(),
                rs = true;
            for(var i = 0, l = ckbs.length; i < l; i++){
                var ckb = ckbs[i];
                if(!ckb.checked){
                    rs = false;
                    break;
                }
                continue;
            }
            return rs;
        },
        _selectAll: function(){
            var
                that = this,
                ckbs = that._getCheckBox()
            S.each(ckbs, function(ckb){
                ckb.checked = true;
            })
        },
        _unSelectAll: function(){
            var
                that = this,
                ckbs = that._getCheckBox();
            S.each(ckbs, function(ckb){
                ckb.checked = false;
            })
        },
        _getCheckBox : function() {
            //获取符合要求的checkbox
            var that = this, tmpArr = query(that.opts.select, that.rootDOM);
            //获取过滤掉的checkbox
            that._getIllegalCheckboxs();
            //筛选
            that.checkboxs = S.filter(tmpArr, that._filter, that);
            return that.checkboxs;
        },
        /**
         * 获取所有过滤掉的checkbox
         */
        _getIllegalCheckboxs : function() {
            var that = this, filter = query(that.opts.filter, that.rootDOM);
            that.illegalCheckbox = [];
            S.each(filter, function(o) {
                if (o.nodeName.toLowerCase() == 'input' && DOM.prop(o, 'type') == 'checkbox') {
                    that.illegalCheckbox.push(o);
                } else {
                    var cld = query(that.opts.select, o);
                    if (cld.length > 0) {
                        S.each(cld,function(o){
                            that.illegalCheckbox.push(o);
                        });
                    }
                }
            })
        },
        /**
         * 筛选函数
         * @param {Object} o
         */
        _filter : function(o) {
            var that = this, illegalCheckbox = that.illegalCheckbox, flag = true;
            if (o.nodeName.toLowerCase() == 'input' && DOM.prop(o, 'type') == 'checkbox') {
                 for(var i = 0; i < illegalCheckbox.length; i++){
                     var eo = illegalCheckbox[i];
                     if (eo == o) {
                        flag = false;
                        break;
                    }
                 }
                 return flag;
            }
        }
    })
    return selectAll;
}, {
    requires : ['core', 'sizzle']
});


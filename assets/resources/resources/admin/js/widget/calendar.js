/*-----------------------------------------------------------------------------
* @Description:     使用定制的calendar处理
* @Version:         1.0.0
* @author:          simon(406400939@qq.com)
* @date             2014.5.26
* ==NOTES:=============================================
* v1.0.0(2014.5.26):
     初始生成
* ---------------------------------------------------------------------------*/

KISSY.add('widget/calendar', function(S){
    var
        DOM = S.DOM, get = DOM.get, query = DOM.query, $ = S.all,
        el = {
            calendar: 'input[data-type="date"],input[data-type="time"]'
        },
        SCROLL_KEY = 'calendar';

    PW.namespace('widget.Calendar');

    PW.widget.Calendar = S.merge(PW.mod.Calendar,{
        refresh: function(){
            var that = this;
            S.each(query(el.calendar), function(c){
                var
                    type = DOM.attr(c, 'data-type'),
                    fmt = DOM.attr(c, 'data-format'),
                    sd = parseInt(DOM.attr(c, 'data-selected-date')) || S.now();

                if(DOM.attr(el.calendar,"data")==0){
                    DOM.data(c, SCROLL_KEY, that.client({
                        renderTo: c,
                        select:{
                            showTime: false,
                            dateFmt: fmt,
                            selected: new Date(sd)
                        },
                        style:{
                            startDay: new Date(sd)
                        }
                    }))
                }else{
                    DOM.data(c, SCROLL_KEY, that.client({
                        renderTo: c,
                        select:{
                            showTime: true,
                            dateFmt: fmt,
                            selected: new Date(sd)
                        },
                        style:{
                            startDay: new Date(sd)
                        }
                    }))
                }
            })

            return that;
        }
        //client 在mod/calendar中实现了
    })

    //初始化
    S.ready(function(){
        PW.widget.Calendar.refresh();
    })
},{
    requires:[
        'mod/calendar',
        'mod/moment'
    ]
})
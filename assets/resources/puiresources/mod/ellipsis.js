/*-----------------------------------------------------------------------------
* @Description:     文本阶段
* @Version:         1.0.0
* @author:          simon(406400939@qq.com)
* @date             2013.9.20
* ==NOTES:=============================================
* v1.0.0(2013.9.20):
* 文本截断
*  -当给出节点中文本长度达到给定长度之后，截断，并添加'...'
* 使用方法：
*  只需要在节点上添加 data-ellipsis="3|..." 
* ---------------------------------------------------------------------------*/

KISSY.add('mod/ellipsis', function(S){
    var 
        DOM = S.DOM, query = DOM.query, get = DOM.get, $ = S.all,
        SELECT_ATTR = 'data-ellipsis',
        ORIGINAL_TEXT_STORE_KEY = 'originalText';

    function init(){
        S.ready(function(){
            var els = query('*[data-ellipsis]');
            els.each(function(el){
                ellipsis(el);
            })
        })
    }


    function ellipsis(el){
        var 
            t = DOM.text(el),
            setting = DOM.attr(el, 'data-ellipsis').split('|'),
            threshold = parseInt(setting[0]) || 30,
            suffixText = setting[1] || '...';
        if(DOM.data(el, ORIGINAL_TEXT_STORE_KEY)) return;
        storeOriginalText(el);
        spliceText(el,threshold, suffixText)

    }
    function storeOriginalText(el){
        var t = DOM.text(el);
        DOM.data(el, ORIGINAL_TEXT_STORE_KEY, t);
    }

    function spliceText(el, threshold, suffixText){
        var
            t = DOM.text(el),
            shortenText;
        if(t.length <= threshold) return;
        shortenText = t.substr(0, threshold) + suffixText;
        shortenText = S.escapeHTML(shortenText);
        DOM.html(el, shortenText);
    }

    PW.namespace('mod.Ellipsis')
    PW.mod.Ellipsis = init;
}, {
    requires: ['core', 'sizzle']
})
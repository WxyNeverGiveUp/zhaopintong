var x = 5,y = 5
        var xin = true, yin = true
        var step = 1.5
        var delay = 18
        var obj=document.getElementById("ad")
        function floatAD() {
        var L=240
        var T=80
        var R= window.screen.width-obj.offsetWidth
        var B = window.screen.height-obj.offsetHeight
        obj.style.left = x + document.body.scrollLeft-4*obj.offsetWidth+"px"
        obj.style.top = y+ document.body.scrollTop-obj.offsetHeight+"px"
        x = x + step*(xin?1:-1)
        if (x < L) { xin = true; x = L}
        if (x > R){ xin = false; x = R}
        y = y + step*(yin?1:-1)
        if (y < T) { yin = true; y = T }
        if (y > B) { yin = false; y = B }
        }
        var itl= setInterval("floatAD()", delay)
        obj.onmouseover=function(){clearInterval(itl)}
        obj.onmouseout=function(){itl=setInterval("floatAD()", delay)}
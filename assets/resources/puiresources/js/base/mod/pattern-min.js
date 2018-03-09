KISSY.add("mod/pattern",function(a,b){PW.namespace("mod.Pattern");PW.mod.Pattern=function(){return new b();};},{requires:["pattern/core"]});KISSY.add("pattern/core",function(a){var c={isCommonUser:function(d){return/^[a-zA-Z]{1}([a-zA-Z0-9]|[._-]){5,19}$/.test(d);},isPassword:function(d){return/^(\w){6,20}$/.test(d);},isEmail:function(d){return/^[0-9a-zA-Z_\-\.]+@[0-9a-zA-Z_\-]+(\.[0-9a-zA-Z_\-]+)+$/.test(d);},isNull:function(d){return d.length==0;},notNull:function(d){return !/^\s*$/.test(d);},isNumber:function(d){return/^\d+$/.test(d);},isTelephone:function(d){return/^\d{3,4}-\d{7,8}(-\d{3,4})?$/.test(d);},isImage:function(d){return/^.+(\.jpg|\.JPG|\.gif|\.GIF|\.png|.PNG|\.x\-png|\.bmp|\.BMP)$/.test(d);},isEnglish:function(d){return/^[a-z]*$/.test(d);},isHttp:function(d){return/^http\:\/\/([^\s]+)$/.test(d);},isFlv:function(d){return/^.+(\.flv|\.FLV|\.Flv)$/.test(d);},isFloat:function(d){return/^(-?\.?\d+)(\.\d+)?$/.test(d);},isQQ:function(d){return/^[1-9]\d{4,13}$/.test(d);},isMobile:function(d){return/^[1]{1}[3458]{1}[0-9]{9}$/.test(d);},isChinese:function(d){return/^[\u4e00-\u9faf]+$/.test(d);},isUrl:function(d){return/[a-zA-z]+:\/\/[^\s]*/.test(d);},isZipCode:function(d){return/^[1-9][0-9]{5}$/.test(d);},isIP:function(d){return/^((1?\d?\d|(2([0-4]\d|5[0-5])))\.){3}(1?\d?\d|(2([0-4]\d|5[0-5])))$/.test(d);},scale:function(g,f,d,e){if(!/^(-?\.?\d+)(\.\d+)?$/.test(g)){return false;}g=parseFloat(g,10);f=parseFloat(f,10);d=parseFloat(d,10);return(e&&parseInt(e,10)==1)?(g>=f&&g<=d):(g>f&&g<d);},length:function(h,g,d,f){var e=h.length;g=parseInt(g,10);d=parseInt(d,10);return(f&&parseInt(f,10)==1)?(e>=g)&&(e<=d):(e>g)&&(e<d);},isStartWith:function(){},isEndWith:function(){},has:function(){},notin:function(d,e){}};function b(d){this.form=d;}a.augment(b,{test:function(d,g){var f=this;try{if(a.isRegExp(d)){return d.test(g);}return(a.isString(d)&&/^[is](\w)*$/.test(d)&&c[d])?f.valid(d,g):false;}catch(e){a.log(e);return false;}},valid:function(i,h){var d=this,e,g,f="";e=d.cutToken(i);g=d.getRPN(e);return d.doValid(g,h);},doValid:function(g,f){var d=this,e=[];a.each(g,function(h){if(h!="|"&&h!="&"){e.push(h);}else{var j=e.pop(),i=e.pop();if(h=="&"){e.push(d.subValid(j,f)&&d.subValid(i,f));}else{e.push(d.subValid(j,f)||d.subValid(i,f));}}});if(e.length!=1){throw ("计算错误，请检查传入样式是否争取");}return d.subValid(e[0],f);},cutToken:function(d){var e=/\[|\]|\||&|[a-zA-Z0-9,()_-]+/ig,f;f=d.match(e);f=a.filter(f,function(g){return/^\s*$/.test(g)?false:true;});return f==null?"":f;},getRPN:function(i){var g=this,d=["#"],h=[],f,e;a.each(i,function(j){if(j!="["&&j!="]"&&j!=" "&&j!="|"&&j!="&"){h.push(j);}else{if(j=="["){d.push(j);}else{if(j=="]"){while((f=d.pop())!="["){h.push(f);}}else{if(j=="|"||j=="&"){e=d[d.length-1];if(e=="["||e=="#"){d.push(j);}else{if(e=="|"||e=="&"){do{f=d.pop();h.push(f);}while(d[d.length-1]!="#"&&d[d.length-1]!="[");d.push(j);}}}}}}});while((f=d.pop())!="#"){h.push(f);}return h;},subValid:function(d,i){if(a.isBoolean(d)){return d;}var f=d,h=[i],e=d.search("\\("),g=d.search("\\)");if(e>0&&g>0){f=d.substr(0,e);h=h.concat(d.substr(e+1,g-e-1).split(","));}return(c[f])?c[f].apply(this,h):"false";}});return b;});
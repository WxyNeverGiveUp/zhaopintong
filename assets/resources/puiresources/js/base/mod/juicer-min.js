KISSY.add("mod/juicer",function(b,d){var d=function(){var f=[].slice.call(arguments);f.push(d.options);if(f[0].match(/^\s*#([\w:\-\.]+)\s*$/igm)){f[0].replace(/^\s*#([\w:\-\.]+)\s*$/igm,function(i,j){var g=document;var h=g&&g.getElementById(j);f[0]=h?(h.value||h.innerHTML):i;});}if(arguments.length==1){return d.compile.apply(d,f);}if(arguments.length>=2){return d.to_html.apply(d,f);}};var e={escapehash:{"<":"&lt;",">":"&gt;","&":"&amp;",'"':"&quot;","'":"&#x27;","/":"&#x2f;"},escapereplace:function(f){return e.escapehash[f];},escaping:function(f){return typeof(f)!=="string"?f:f.replace(/[&<>"]/igm,this.escapereplace);},detection:function(f){return typeof(f)==="undefined"?"":f;}};var c=function(f){if(console){if(console.warn){console.warn(f);return;}if(console.log){console.log(f);return;}}throw (f);};var a=function(j,g){j=j!==Object(j)?{}:j;if(j.__proto__){j.__proto__=g;return j;}var h=function(){};var k=Object.create?Object.create(g):new (h.prototype=g,h);for(var f in j){if(j.hasOwnProperty(f)){k[f]=j[f];}}return k;};d.__cache={};d.version="0.6.1-stable";d.settings={};d.tags={operationOpen:"{@",operationClose:"}",interpolateOpen:"\\${",interpolateClose:"}",noneencodeOpen:"\\$\\${",noneencodeClose:"}",commentOpen:"\\{#",commentClose:"\\}"};d.options={cache:true,strip:true,errorhandling:true,detection:true,_method:a({__escapehtml:e,__throw:c},{})};d.tagInit=function(){var f=d.tags.operationOpen+"each\\s*([^}]*?)\\s*as\\s*(\\w*?)\\s*(,\\s*\\w*?)?"+d.tags.operationClose;var h=d.tags.operationOpen+"\\/each"+d.tags.operationClose;var i=d.tags.operationOpen+"if\\s*([^}]*?)"+d.tags.operationClose;var j=d.tags.operationOpen+"\\/if"+d.tags.operationClose;var n=d.tags.operationOpen+"else"+d.tags.operationClose;var o=d.tags.operationOpen+"else if\\s*([^}]*?)"+d.tags.operationClose;var k=d.tags.interpolateOpen+"([\\s\\S]+?)"+d.tags.interpolateClose;var l=d.tags.noneencodeOpen+"([\\s\\S]+?)"+d.tags.noneencodeClose;var m=d.tags.commentOpen+"[^}]*?"+d.tags.commentClose;var g=d.tags.operationOpen+"each\\s*(\\w*?)\\s*in\\s*range\\((\\d+?)\\s*,\\s*(\\d+?)\\)"+d.tags.operationClose;d.settings.forstart=new RegExp(f,"igm");d.settings.forend=new RegExp(h,"igm");d.settings.ifstart=new RegExp(i,"igm");d.settings.ifend=new RegExp(j,"igm");d.settings.elsestart=new RegExp(n,"igm");d.settings.elseifstart=new RegExp(o,"igm");d.settings.interpolate=new RegExp(k,"igm");d.settings.noneencode=new RegExp(l,"igm");d.settings.inlinecomment=new RegExp(m,"igm");d.settings.rangestart=new RegExp(g,"igm");};d.tagInit();d.set=function(g,k){var j=this;var f=function(i){return i.replace(/[\$\(\)\[\]\+\^\{\}\?\*\|\.]/igm,function(m){return"\\"+m;});};var l=function(m,n){var i=m.match(/^tag::(.*)$/i);if(i){j.tags[i[1]]=f(n);j.tagInit();return;}j.options[m]=n;};if(arguments.length===2){l(g,k);return;}if(g===Object(g)){for(var h in g){if(g.hasOwnProperty(h)){l(h,g[h]);}}}};d.register=function(h,g){var f=this.options._method;if(f.hasOwnProperty(h)){return false;}return f[h]=g;};d.unregister=function(g){var f=this.options._method;if(f.hasOwnProperty(g)){return delete f[g];}};d.template=function(f){var g=this;this.options=f;this.__interpolate=function(h,m,j){var i=h.split("|"),l=i[0]||"",k;if(i.length>1){h=i.shift();k=i.shift().split(",");l="_method."+k.shift()+".call({}, "+[h].concat(k)+")";}return"<%= "+(m?"_method.__escapehtml.escaping":"")+"("+(!j||j.detection!==false?"_method.__escapehtml.detection":"")+"("+l+")) %>";};this.__removeShell=function(i,h){var j=0;i=i.replace(d.settings.forstart,function(o,l,n,m){var n=n||"value",m=m&&m.substr(1);var k="i"+j++;return"<% ~function() {for(var "+k+" in "+l+") {if("+l+".hasOwnProperty("+k+")) {var "+n+"="+l+"["+k+"];"+(m?("var "+m+"="+k+";"):"")+" %>";}).replace(d.settings.forend,"<% }}}(); %>").replace(d.settings.ifstart,function(k,l){return"<% if("+l+") { %>";}).replace(d.settings.ifend,"<% } %>").replace(d.settings.elsestart,function(k){return"<% } else { %>";}).replace(d.settings.elseifstart,function(k,l){return"<% } else if("+l+") { %>";}).replace(d.settings.noneencode,function(l,k){return g.__interpolate(k,false,h);}).replace(d.settings.interpolate,function(l,k){return g.__interpolate(k,true,h);}).replace(d.settings.inlinecomment,"").replace(d.settings.rangestart,function(n,m,o,l){var k="j"+j++;return"<% ~function() {for(var "+k+"="+o+";"+k+"<"+l+";"+k+"++) {{var "+m+"="+k+"; %>";});if(!h||h.errorhandling!==false){i="<% try { %>"+i;i+='<% } catch(e) {_method.__throw("Juicer Render Exception: "+e.message);} %>';}return i;};this.__toNative=function(i,h){return this.__convert(i,!h||h.strip);};this.__lexicalAnalyze=function(l){var k=[];var p=[];var o="";var h=["if","each","_","_method","console","break","case","catch","continue","debugger","default","delete","do","finally","for","function","in","instanceof","new","return","switch","this","throw","try","typeof","var","void","while","with","null","typeof","class","enum","export","extends","import","super","implements","interface","let","package","private","protected","public","static","yield","const","arguments","true","false","undefined","NaN"];var n=function(s,r){if(Array.prototype.indexOf&&s.indexOf===Array.prototype.indexOf){return s.indexOf(r);}for(var q=0;q<s.length;q++){if(s[q]===r){return q;}}return -1;};var j=function(q,i){i=i.match(/\w+/igm)[0];if(n(k,i)===-1&&n(h,i)===-1&&n(p,i)===-1){if(typeof(window)!=="undefined"&&typeof(window[i])==="function"&&window[i].toString().match(/^\s*?function \w+\(\) \{\s*?\[native code\]\s*?\}\s*?$/i)){return q;}if(typeof(global)!=="undefined"&&typeof(global[i])==="function"&&global[i].toString().match(/^\s*?function \w+\(\) \{\s*?\[native code\]\s*?\}\s*?$/i)){return q;}if(typeof(d.options._method[i])==="function"){p.push(i);return q;}k.push(i);}return q;};l.replace(d.settings.forstart,j).replace(d.settings.interpolate,j).replace(d.settings.ifstart,j).replace(d.settings.elseifstart,j).replace(/[\+\-\*\/%!\?\|\^&~<>=,\(\)]\s*([A-Za-z_]+)/igm,j);for(var m=0;m<k.length;m++){o+="var "+k[m]+"=_."+k[m]+";";}for(var m=0;m<p.length;m++){o+="var "+p[m]+"=_method."+p[m]+";";}return"<% "+o+" %>";};this.__convert=function(i,j){var h=[].join("");h+="'use strict';";h+="var _=_||{};";h+="var _out='';_out+='";if(j!==false){h+=i.replace(/\\/g,"\\\\").replace(/[\r\t\n]/g," ").replace(/'(?=[^%]*%>)/g,"\t").split("'").join("\\'").split("\t").join("'").replace(/<%=(.+?)%>/g,"';_out+=$1;_out+='").split("<%").join("';").split("%>").join("_out+='")+"';return _out;";return h;}h+=i.replace(/\\/g,"\\\\").replace(/[\r]/g,"\\r").replace(/[\t]/g,"\\t").replace(/[\n]/g,"\\n").replace(/'(?=[^%]*%>)/g,"\t").split("'").join("\\'").split("\t").join("'").replace(/<%=(.+?)%>/g,"';_out+=$1;_out+='").split("<%").join("';").split("%>").join("_out+='")+"';return _out.replace(/[\\r\\n]\\s+[\\r\\n]/g, '\\r\\n');";return h;};this.parse=function(i,h){var j=this;if(!h||h.loose!==false){i=this.__lexicalAnalyze(i)+i;}i=this.__removeShell(i,h);i=this.__toNative(i,h);this._render=new Function("_, _method",i);this.render=function(l,k){if(!k||k!==g.options._method){k=a(k,g.options._method);}return j._render.call(this,l,k);};return this;};};d.compile=function(g,f){if(!f||f!==this.options){f=a(f,this.options);}try{var h=this.__cache[g]?this.__cache[g]:new this.template(this.options).parse(g,f);if(!f||f.cache!==false){this.__cache[g]=h;}return h;}catch(i){c("Juicer Compile Exception: "+i.message);return{render:function(){}};}};d.to_html=function(g,h,f){if(!f||f!==this.options){f=a(f,this.options);}return this.compile(g,f).render(h,f._method);};d.set({"tag::operationOpen":"{@","tag::operationClose":"}","tag::interpolateOpen":"&{","tag::interpolateClose":"}","tag::noneencodeOpen":"&&{","tag::noneencodeClose":"}","tag::commentOpen":"{#","tag::commentClose":"}"});typeof(module)!=="undefined"&&module.exports?module.exports=d:this.juicer=d;PW.juicer=d;b.juicer=d;return d;});
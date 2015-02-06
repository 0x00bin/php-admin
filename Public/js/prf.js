// Partial refresh

var PRF = {
    log : function(msg) {
        if (console && console.log) {
            console.log(msg);
        }
    },
    topmenus : [],
    init: function() {
        var init_topmenus = function(topmenus) {
            $(".menu li").each(function(){
                topmenus.push(this.id);
            });
        };
        init_topmenus(PRF.topmenus);

        PRF.onHashChange();
    },

    //
    refresh_topmenus : function (QueryString) {
        var getStyle = function(i, actived) {
            var style = "";
            var active_ext = "";
            if (i == 0) {
                style = "first";
                active_ext = " first_";
            } else if (i == PRF.topmenus.length - 1) {
                style = "end";
                active_ext = " end_";
            }
            if (actived) {
                style =  style + active_ext + "current";
            }
            return style;

        }
        //var isActived = false;
        var setActived = function(i, menuid) {
            PRF.log("menuid:" + menuid + " curr_id " + curr_id);
            var active = false;
            if (menuid.indexOf(curr_id) != -1) {
                active = true;
                //isActived = true;
                PRF.refresh_submenus(menuid);
                PRF.log("isActived");
            }
            $("#"+menuid).removeClass();
            $("#"+menuid).addClass(getStyle(i, active));
            PRF.log(menuid + " set class " + getStyle(i, active));
        }

        var curr_id = QueryString["app"].toLowerCase() + "_" + QueryString["mod"].toLowerCase();
        if (PRF.mappings[curr_id]) {
            curr_id = PRF.mappings[curr_id];
        }
        PRF.log("");
        $.each(PRF.topmenus, setActived);
    },

    submenus:{},
    mappings:{},
    setSubmenus : function(submenus, mappings) {
        this.submenus = submenus;
        this.mappings = mappings;
    },

    refresh_submenus : function(menuid) {
        if (!this.submenus[menuid]) {
            PRF.log(submenus + "no submenus");
            return;
        }
        $("#sub_menus").html();
        var html = "";
        $.each(this.submenus[menuid], function(name, value) {
            var url = APP + '#!' + PRF.path2Query(name);
            html += '<li><a href="'+url+'">'+value+'</a></li>';
        });
        $("#sub_menus").html(html);
    },

    refresh_location_guide : function() {
         var top_name = $(".menu .first_current a").html();
        if (!top_name) {
            top_name = $(".menu .current a").html();
            if (!top_name) {
                top_name = $(".menu .end_current a").html();
            }
        }

        var sub_name = $("#sub_name").html();
        $("#location_guide").html(top_name + " > " + sub_name);
    },

    pidx2name : [
        'app',
        'mod',
        'act'
    ],
    path2Query: function(path) {
        var paths = path.split("/");
        var query = [];
        $.each(paths, function(i, value) {
            query.push(PRF.pidx2name[i]+"="+ value);
        });
        return query.join("&");
    },
    //
    onHashChange : function() {
        if (location.hash == "") {
            return;
        }
        var param = location.hash.split("!");

        var url = APP + "?" + param[1];
        var request = new QueryString(param[1]);

        PRF.refresh_topmenus(request);
        $.get(url, function(response){
            //$("#Right").html(response);
            setInnerHTML("Right", response);
            PRF.update(request);
        });
    },

    //
    curr_url : '',
    last_url : '',
    update:function(request) {
         // 更新公共变量
        URL = APP + location.hash;

        PRF_MOD = APP + location.hash.split("&act")[0];
        MOD = PRF_MOD.replace(/#!/, '?');

        this.refresh_location_guide();

        this.last_url = this.curr_url;
        this.curr_url = location.href;

        // 修改edit的backurl
        if (request["act"] == "edit") {
            $("#backurl").val(this.last_url);
        }
    },
    switchUrl:function(url) {
        return url.replace(/\?/, '#!');
    },
}

function QueryString(str)
{
    var params = str.split("&");
    for(var i = 0;i < params.length; i++){
        idx = params[i].indexOf("=");
        if(idx != -1){
            name  = params[i].substring(0,idx);
            value = params[i].substr(idx+1);
            this[name] = value;
        }
    }
}
/*
 * 描述：跨浏览器的设置 innerHTML 方法
 *       允许插入的 HTML 代码中包含 script 和 style
 * 作者：kenxu <ken@ajaxwing.com>
 * 日期：2006-06-26
 * 参数：
 *    el: 合法的 DOM 树中的节点
 *    htmlCode: 合法的 HTML 代码
 * 经测试的浏览器：ie5+, firefox1.5+, opera8.5+
 */
window.__el_stack = [];
var setInnerHTML = function (el, htmlCode) {
    el = document.getElementById(el);
    window.__el_stack.push(el);
    var ua = navigator.userAgent.toLowerCase();
    if (ua.indexOf('msie') >= 0 && ua.indexOf('opera') < 0) {
        htmlCode = '<div style="display:none">for IE</div>' + htmlCode;
        htmlCode = htmlCode.replace(/<script([^>]*)>/gi, '<script$1 defer>');
        el.innerHTML = htmlCode;
        el.removeChild(el.firstChild);
    } else {
        var el_next = el.nextSibling;
        var el_parent = el.parentNode;
        el_parent.removeChild(el);
        if (ua.indexOf('gecko') >= 0) {
            htmlCode += '<script>window.__el_stack.pop();<//script>';
        }
        el.innerHTML = htmlCode;
        if (el_next) {
            el_parent.insertBefore(el, el_next)
        } else {
            el_parent.appendChild(el);
        }
    }

    if (ua.indexOf('gecko') < 0) {
        window.__el_stack.pop();
    }

    eavlJS(htmlCode,"<script>","</s"+"cript>") ;
}

document.write = function() {
    if (window.__el_stack.length > 0) {
        var el = window.__el_stack[window.__el_stack.length - 1];
    } else {
        var el = document.getElementsByTagName('body')[0];
    }
    for (var i = 0; i < arguments.length; i++) {
        if (typeof arguments[i] == 'string') {
            var el2 = el.appendChild(document.createElement('div'));
            setInnerHTML(el2, arguments[i]);
        }
    }
}

function eavlJS(html, start, end) {
    //把不同的script标签替换为一样的
    html = html.replace(/<script type=\'text\/javascript\'>/g,"<script>").replace(/<script type=\"text\/javascript\">/g,"<script>");
    var regexp  = new RegExp(start+"((.|\r\n)*?)"+end,"g");
    var strings = html.match(regexp);
    var objs    = new Array();
    var regexp2 = new RegExp("(^"+start+"|"+end+"$)","g");

    var js = "";
    if (strings != null) {
        for( var i=0; i< strings.length; i++){
            objs[i] = strings[i].replace(regexp2,'');
        }
        var strScript = objs.join(";").replace(/\\\"/g,"\"").replace(/\\\'/g,"'");
        js += strScript+";"
    }

    eval(js);
    return js;
}

/* innerhtml.js
 * Copyright Ma Bingyao <andot@ujn.edu.cn>
 * Version: 1.9
 * LastModified: 2006-06-04
 * This library is free.  You can redistribute it and/or modify it.
 * http://www.coolcode.cn/?p=117
 */

var global_html_pool = [];
var global_script_pool = [];
var global_script_src_pool = [];
var global_lock_pool = [];
var innerhtml_lock = null;
var document_buffer = "";

function set_innerHTML(obj_id, html, time) {
    if (innerhtml_lock == null) {
        innerhtml_lock = obj_id;
    }
    else if (typeof(time) == "undefined") {
        global_lock_pool[obj_id + "_html"] = html;
        window.setTimeout("set_innerHTML('" + obj_id + "', global_lock_pool['" + obj_id + "_html']);", 10);
        return;
    }
    else if (innerhtml_lock != obj_id) {
        global_lock_pool[obj_id + "_html"] = html;
        window.setTimeout("set_innerHTML('" + obj_id + "', global_lock_pool['" + obj_id + "_html'], " + time + ");", 10);
        return;
    }

    function get_script_id() {
        return "script_" + (new Date()).getTime().toString(36)
          + Math.floor(Math.random() * 100000000).toString(36);
    }

    document_buffer = "";

    document.write = function (str) {
        document_buffer += str;
    }
    document.writeln = function (str) {
        document_buffer += str + "\n";
    }

    global_html_pool = [];

    var scripts = [];
    html = html.split(/<\/script>/i);
    for (var i = 0; i < html.length; i++) {
        global_html_pool[i] = html[i].replace(/<script[\s\S]*$/ig, "");
        scripts[i] = {text: '', src: '' };
        scripts[i].text = html[i].substr(global_html_pool[i].length);
        scripts[i].src = scripts[i].text.substr(0, scripts[i].text.indexOf('>') + 1);
        scripts[i].src = scripts[i].src.match(/src\s*=\s*(\"([^\"]*)\"|\'([^\']*)\'|([^\s]*)[\s>])/i);
        if (scripts[i].src) {
            if (scripts[i].src[2]) {
                scripts[i].src = scripts[i].src[2];
            }
            else if (scripts[i].src[3]) {
                scripts[i].src = scripts[i].src[3];
            }
            else if (scripts[i].src[4]) {
                scripts[i].src = scripts[i].src[4];
            }
            else {
                scripts[i].src = "";
            }
            scripts[i].text = "";
        }
        else {
            scripts[i].src = "";
            scripts[i].text = scripts[i].text.substr(scripts[i].text.indexOf('>') + 1);
            scripts[i].text = scripts[i].text.replace(/^\s*<\!--\s*/g, "");
        }
    }

    var s;
    if (typeof(time) == "undefined") {
        s = 0;
    }
    else {
        s = time;
    }

    var script, add_script, remove_script;

    for (var i = 0; i < scripts.length; i++) {
        var add_html = "document_buffer += global_html_pool[" + i + "];\n";
        add_html += "document.getElementById('" + obj_id + "').innerHTML = document_buffer;\n";
        script = document.createElement("script");
        if (scripts[i].src) {
            script.src = scripts[i].src;
            if (typeof(global_script_src_pool[script.src]) == "undefined") {
                global_script_src_pool[script.src] = true;
                s += 2000;
            }
            else {
                s += 10;
            }
        }
        else {
            script.text = scripts[i].text;
            s += 10;
        }
        script.defer = true;
        script.type =  "text/javascript";
        script.id = get_script_id();
        global_script_pool[script.id] = script;
        add_script = add_html;
        add_script += "document.getElementsByTagName('head').item(0)";
        add_script += ".appendChild(global_script_pool['" + script.id + "']);\n";
        window.setTimeout(add_script, s);
        remove_script = "document.getElementsByTagName('head').item(0)";
        remove_script += ".removeChild(document.getElementById('" + script.id + "'));\n";
        remove_script += "delete global_script_pool['" + script.id + "'];\n";
        window.setTimeout(remove_script, s + 10000);
    }

    var end_script = "if (document_buffer.match(/<\\/script>/i)) {\n";
    end_script += "set_innerHTML('" + obj_id + "', document_buffer, " + s + ");\n";
    end_script += "}\n";
    end_script += "else {\n";
    end_script += "document.getElementById('" + obj_id + "').innerHTML = document_buffer;\n";
    end_script += "innerhtml_lock = null;\n";
    end_script += "}";
    window.setTimeout(end_script, s);
}
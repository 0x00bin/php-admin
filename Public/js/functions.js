/**
 * 获取网页的宽度和高度
 * @param  {string} get    需要的宽（w）或高（h）
 * @return 如果参数get存在，则返回相应宽或高，如果get没有写则返回数组
 */
function getBodySize(get) {
    var bodySize = [];
    bodySize['w']=($(document).width()>$(window).width())? $(document).width():$(window).width();
    bodySize['h']=($(document).height()>$(window).height())? $(document).height():$(window).height();
    return get?bodySize[get]:bodySize;
}

/**
 * 解码html编码的字符串
 * @param  {string}   str       编码的字符串
 * @return {string}
 */
function htmldecode(str) {
    var s = "";
    if (str.length == 0) return "";

    s = str.replace(/&amp;/g,"&");
    s = s.replace(/&lt;/g,   "<");
    s = s.replace(/&gt;/g,   ">");
    s = s.replace(/&nbsp;/g, "    ");
    s = s.replace(/'/g,      "\'");
    s = s.replace(/&quot;/g, "\"");
    s = s.replace(/<br>/g,   "\n");
    s = s.replace(/&#39;/g,  "\'");

    return    s;
}
// 解析url 获取url参数
function getUrlParams() { 
    var args = new Object(); 
    var query = location.search.substring(1);
    var pairs = query.split("&");
    for (var i = 0; i < pairs.length; i++) { 
        var pos = pairs[i].indexOf('=');
        if (pos == -1) continue;
        var argname = pairs[i].substring(0, pos);
        var value = pairs[i].substring(pos + 1);
        value = decodeURIComponent(value);
        args[argname] = value;
    }    
    return args;
}

/**
 * 通用AJAX提交
 * @param  {string}   url       表单提交地址
 * @param  {string}   form      待提交的表单对象或ID
 * @param  {function} callback  回调函数
 */
function ajax_submit(url, form, callback, reload){
    if(!form||form==''){
        var form="form";
    }
    
    if(!url||url==''){
        var url = document.URL;
    }
    
    if (typeof reload === "undefined") {
        var reload = true;    
    }
    $(form).ajaxSubmit({
        url:url,
        type:"POST",
        success:function(data, status) {

            if(data.status==1){
                if (callback) {
                    return callback(data);
                }
		if (data.title) {
                    popup.success(data.info, data.title);
		} else {
		    popup.success(data.info);
		}
                /*setTimeout(function(){
                    popup.close("asyncbox_success");
                },2000);*/
            }else{
                popup.error(data.info);
                /*setTimeout(function(){
                    popup.close("asyncbox_error");
                },2000);*/
            }
            if(data.url&&data.url!=''){
                //console.log(htmldecode(data.url));
                setTimeout(function(){
                    top.window.location.href = htmldecode(data.url);
                },2000);
            }
            if(data.status==1&&data.url=='' && reload){
                setTimeout(function(){
                    top.window.location.reload();
                },1000);
            }
        }
    });
    return false;
}
/**
 * 检测字符串是否是电子邮件地址格式
 * @param  {string} value    待检测字符串
 */
function isEmail(value){
    var Reg =/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
    return Reg.test(value);
}

/**
 * 和PHP一样的时间戳格式化函数
 * @param  {string} format    格式
 * @param  {int}    timestamp 要格式化的时间 默认为当前时间
 * @return {string}           格式化的时间字符串
 */
function date(format, timestamp){
    var a, jsdate=((timestamp) ? new Date(timestamp*1000) : new Date());
    var pad = function(n, c){
        if((n = n + "").length < c){
            return new Array(++c - n.length).join("0") + n;
        } else {
            return n;
        }
    };
    var txt_weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var txt_ordin = {
        1:"st",
        2:"nd",
        3:"rd",
        21:"st",
        22:"nd",
        23:"rd",
        31:"st"
    };
    var txt_months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var f = {
        // Day
        d: function(){
            return pad(f.j(), 2)
        },
        D: function(){
            return f.l().substr(0,3)
        },
        j: function(){
            return jsdate.getDate()
        },
        l: function(){
            return txt_weekdays[f.w()]
        },
        N: function(){
            return f.w() + 1
        },
        S: function(){
            return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th'
        },
        w: function(){
            return jsdate.getDay()
        },
        z: function(){
            return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0
        },

        // Week
        W: function(){
            var a = f.z(), b = 364 + f.L() - a;
            var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;
            if(b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b){
                return 1;
            } else{
                if(a <= 2 && nd >= 4 && a >= (6 - nd)){
                    nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
                    return date("W", Math.round(nd2.getTime()/1000));
                } else{
                    return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
                }
            }
        },

        // Month
        F: function(){
            return txt_months[f.n()]
        },
        m: function(){
            return pad(f.n(), 2)
        },
        M: function(){
            return f.F().substr(0,3)
        },
        n: function(){
            return jsdate.getMonth() + 1
        },
        t: function(){
            var n;
            if( (n = jsdate.getMonth() + 1) == 2 ){
                return 28 + f.L();
            } else{
                if( n & 1 && n < 8 || !(n & 1) && n > 7 ){
                    return 31;
                } else{
                    return 30;
                }
            }
        },

        // Year
        L: function(){
            var y = f.Y();
            return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0
        },
        //o not supported yet
        Y: function(){
            return jsdate.getFullYear()
        },
        y: function(){
            return (jsdate.getFullYear() + "").slice(2)
        },

        // Time
        a: function(){
            return jsdate.getHours() > 11 ? "pm" : "am"
        },
        A: function(){
            return f.a().toUpperCase()
        },
        B: function(){
            // peter paul koch:
            var off = (jsdate.getTimezoneOffset() + 60)*60;
            var theSeconds = (jsdate.getHours() * 3600) + (jsdate.getMinutes() * 60) + jsdate.getSeconds() + off;
            var beat = Math.floor(theSeconds/86.4);
            if (beat > 1000) beat -= 1000;
            if (beat < 0) beat += 1000;
            if ((String(beat)).length == 1) beat = "00"+beat;
            if ((String(beat)).length == 2) beat = "0"+beat;
            return beat;
        },
        g: function(){
            return jsdate.getHours() % 12 || 12
        },
        G: function(){
            return jsdate.getHours()
        },
        h: function(){
            return pad(f.g(), 2)
        },
        H: function(){
            return pad(jsdate.getHours(), 2)
        },
        i: function(){
            return pad(jsdate.getMinutes(), 2)
        },
        s: function(){
            return pad(jsdate.getSeconds(), 2)
        },
        //u not supported yet

        // Timezone
        //e not supported yet
        //I not supported yet
        O: function(){
            var t = pad(Math.abs(jsdate.getTimezoneOffset()/60*100), 4);
            if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t;
            return t;
        },
        P: function(){
            var O = f.O();
            return (O.substr(0, 3) + ":" + O.substr(3, 2))
        },
        //T not supported yet
        //Z not supported yet

        // Full Date/Time
        c: function(){
            return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P()
        },
        //r not supported yet
        U: function(){
            return Math.round(jsdate.getTime()/1000)
        }
    };

    return format.replace(/[\\]?([a-zA-Z])/g, function(t, s){
        if( t!=s ){
            // escaped
            ret = s;
        } else if( f[s] ){
            // a date function exists
            ret = f[s]();
        } else{
            // nothing special
            ret = s;
        }
        return ret;
    });
}

function doadd_regex(id) {
    var url = APP + "?app=resource&mod=regex&act=add&command_id="+id;
    if (typeof PRF !== "undefined") {// for prf support
        url = PRF.switchUrl(url);
    }
    location.href = url;
}

function doview_regex(id) {
    var url = APP + "?app=resource&mod=regex&act=index&_search=1&command_id="+id;
    if (typeof PRF !== "undefined") {// for prf support
        url = PRF.switchUrl(url);
    }
    location.href = url;
}

function oncheckbox(ckbid, tabid){
    $("#"+ckbid).click(function(){
        var status = $(this).prop('checked');
        $("#"+tabid+" tbody input[type='checkbox']").prop("checked",status);
        $("#"+ckbid).prop("checked",status);
    });
}

function doedit(id){
	location.href = PRF_MOD+"&act=edit&id="+id;
}

function doview_result(id) {
    do_act_url({"task_id":id}, "app=inspect&mod=result&act=index&_search=1");
}

function do_act_url(params, urlparam) {
    var url = APP + "?" + urlparam;
    if (params) {
        for(var col in params){
            url += "&" + col + "=" + params[col];
        }
    }
    
    if (typeof PRF !== "undefined") {// for prf support
        url = PRF.switchUrl(url);
    }
    location.href = url;    
}

function doact(act, params, extparam){
    var url = PRF_MOD + "&act="+act;
    if (params) {
        for(var col in params){
            url += "&" + col + "=" + params[col];
        }
    }
    if (extparam) {
        url += "&" + extparam;
    }
	location.href = url;
}

function doact_ajax(act, params, extparam){
    var url = MOD + "&act="+act;
    if (params) {
        for(var col in params){
            url += "&" + col + "=" + params[col];
        }
    }
    if (extparam) {
        url += "&" + extparam;
    }
	//location.href = url;
	ajax_submit(url, "form", false, false);
}

function dodelete(id){
	var keyValue;
	if (id) {
		keyValue = id;
	} else {
		//keyValue = getSelectCheckboxValues();
	}

	if (!keyValue) {
		alert('请选择删除项！');
		return false;
	}

	popup.confirm('确认要删除吗 ?','确认框',function(action){
        //confirm 返回三个 action 值，分别是 'ok'、'cancel' 和 'close'。
        if(action == 'ok'){
            ajax_submit(MOD+"&act=delete&id="+keyValue);
        }
　  });

}

// var sc = SelectChanger.create("select_id", "parent_id", url, callback);
// sc.init(); in document.ready for init
// 关联下拉列表，实现关联触发

var SelectChanger = {
    create: function(change_id, parent_id, url, data_callback) {
        var sc = {};
        var parent_id_    = parent_id;
        var change_id_    = change_id;
        sc.request_url = function() {
            var val = $('#'+parent_id_).val();
            if (!url) url = MOD  + '&act=select&dname='+ parent_id_;
            var d_url =  url + val;
            var _this = this;
            $.get(d_url,
                function(response) {
                    try {
                        var datas = eval('(' + response + ')');
                        if (data_callback) {
                            data_callback(datas);
                        } else {
                            _this.create_change(datas);
                        }
                    }catch(e) {
                        alert(e);
                    }
                }
            );        
        };
        sc.change_parent = function(change_callback) {
            if (change_callback) {
                var val = $('#'+parent_id_).val();
                change_callback(val);
            } else {
                this.request_url();    
            }
        };
        sc.create_change = function(datas) {
            var changer = $('#'+change_id_);
            var type = changer.get(0).type;

            if (type == "select-one") {
                this.create_select(changer, datas);
            }

            if (type == "checkbox") {
                this.create_checkbox(changer, datas);
            }
        };

        sc.create_select = function(changer, datas) {
            changer.find('option').remove();//.end();
            changer.append('<option value="0">请选择</option>');
            $.each(datas, function(i, v){
        	   changer.append('<option value="'+i+'">'+ v +'</option>');
        	});

            $("#"+change_id_+" option[value="+$('#curr_'+change_id_).val()+"]").attr("selected",true);
        };
        sc.create_checkbox = function(changer, datas) {
            changer.find('checkbox').remove();//.end();
            $.each(datas, function(i, v){
        	   changer.append('<checkbox name="'+change_id_+'" value="'+i+'">'+v+" ");
        	});
        };

        sc.load_values = function(datas) {
            $.each(datas, function(i, v){
        	    if (i != "id" && $("#"+i).length>0) {
                    $("#"+i).val(v);
                }
        	});
        };
        sc.init = function(change_callback) {
            this.change_parent(change_callback);
            var _this = this;
            $("#"+parent_id_).change(
                function() {
                   _this.change_parent(change_callback);
                });
        };
        return sc;
    }
};

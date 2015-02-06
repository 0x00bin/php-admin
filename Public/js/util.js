//js util function on base jquery

var Loading = {
    init:false,
    id:"_loading",
    style:"display:none;border:1px solid #D1D1D1;padding:4px;top:10px;right:10px;width:90px;position:absolute;float:right;z-index:99;",
    show:function() {
        if (!this.init) {
            var loading = '<div id="'+this.id+'" style="'+this.style+'">' +
                    '<img src="'+PUBLIC +'/Images/loading.gif" style="float:left;" />&nbsp;'+
                    '<span style="color:#000; font-size:12px">loading ...</span></div>';
            $('body').append(loading);

            this.init = true;
        }
        $('#'+this.id).fadeIn();
    },
    close:function() {
        if (this.init) {
            $('#'+this.id).fadeOut();
        }
    }
};

// var rs = RelatedSelect.create("act_name", "app_name", "cmsg_acts_");
// rs.init(); in document.ready for init
// 关联下拉列表，实现关联触发
var RelatedSelect = {
    create: function(select_id, parent_id, dname_prefix) {
        var rs = {};
        var parent_id_    = parent_id;
        var select_id_    = select_id;
        var curr_select_  = "curr_" + select_id;
        var dname_prefix_ = dname_prefix? dname_prefix : "";
        rs.change_parent = function() {
            var val = $('#'+parent_id_).val();
            var d_url = MOD  + '&act=select&format=json&dname='+ dname_prefix_ + val;
            var _this = this;
            $.get(d_url,
                function(response) {
                    try {
                        var datas = eval('(' + response + ')');
                        _this.create_select(datas);
                    }catch(e) {
                        alert(e);
                    }
                }
            );
        };
        rs.create_select = function() {
            var my_select = $('#'+select_id_);
            my_select.find('option').remove();//.end();
            my_select.append('<option value="0">请选择</option>');
            $.each(datas, function(i, v){
        	   my_select.append('<option value="'+i+'">'+ v +'</option>');
        	});

            my_select.val($('#'+curr_select_).val());
        };
        rs.init = function() {
            this.change_parent();
            var _this = this;
            $("#"+parent_id_).change(
                function() {
                   _this.change_parent();
                });
        };
        return rs;
    }
};

// 对Date的扩展，将 Date 转化为指定格式的String
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
// 例子：
// (new Date()).format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
// (new Date()).format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18
Date.prototype.format = function (fmt) { //author: meizz
    var o = {
        "M+": this.getMonth() + 1, //月份
        "d+": this.getDate(), //日
        "h+": this.getHours(), //小时
        "m+": this.getMinutes(), //分
        "s+": this.getSeconds(), //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "S": this.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}

String.prototype.format = function() {
    var formatted = this;
    for (var i = 0; i < arguments.length; i++) {
        var regexp = new RegExp('\\{'+i+'\\}', 'gi');
        formatted = formatted.replace(regexp, arguments[i]);
    }
    return formatted;
};

var getLength = function(o) {
    var len = 0;
    for(var i in o){
        if (o.hasOwnProperty(i)){
            len++;
        }
    }
    return len;
}
function is_string(val) {
    return (typeof val == 'string'  || ( val && (typeof val.substr == 'function')));
}
// {0}:id {1}:name
var btn_tpl = '<span><input type="button" name="text" id="{0}"  value="{1}" class="bu ui-widget-content ui-corner-all" /></span>';
function access_acts(btns, need_auth) {
    var o_btns = $("#btns");
    for(var i in btns) {
        if (!need_auth || access.hasOwnProperty(i.toUpperCase())) {
            if ( is_string(btns[i]) ) {
                o_btns.append(btn_tpl.format(i, btns[i]));
            } else {
                var func = btns[i].func_ || '';
                if (jQuery.isFunction(func)) {
                    func(btn_tpl.format(i, btns[i].name));
                    //o_btns.append(btn_tpl.format(i, btns[i].name));
                } else {
                    for(var j in btns[i]) {
                        o_btns.append(btn_tpl.format(j, btns[i][j]));
                    }
                }
            }
        }
    }
}
// clone tr struct is
// <table id="tab_id">
//   <tr>
//      <td><input id=port_id_0>...</td>
//      ...
//      <td> <a clone_btn><a remove_btn></td>
//   </tr>
//   ...
// </table>
function clone_element(o, empty) {
    var tr = o.parent().parent();
    var prop = _get_prop(tr);

    var idx = tr.parent().find(":input[name='"+prop+"id[]']").size();
    var _o  = tr.clone(true);

     var inputs = _o.find(":input");
     inputs.each(function(i){
        this.id = _get_id(this.id, idx);
        if (empty || _is_id(this.id)) {
            this.value = "";
        }
     });
     _o.appendTo(tr.parent());

    _o.find("a").eq(1).show();
    _o.find("a").eq(0).remove();
}

function _get_prop(o)
{
    var idstr = o.find(":input:first").attr("id");
    return idstr.split("_", 1).shift();
}

// port_id_0
function _is_id(idstr) {
    var ids = idstr.split("_");
    return ids[1] == 'id';
}
// port_id_0
function _get_id(idstr, idx) {
     var ids = idstr.split("_");
     ids[2] = idx;
     return ids.join("_");
}

// 计算对象居中需要设置的left和top值
// 参数：
//  _w - 对象的宽度
//  _h - 对象的高度
function getCenterPos(_w,_h)
{
   var de = document.documentElement;

   // 获取当前浏览器窗口的宽度和高度
   // 兼容写法，可兼容ie,ff
   var w = self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
   var h = (de&&de.clientHeight) || document.body.clientHeight;

   // 获取当前滚动条的位置
   // 兼容写法，可兼容ie,ff
   var st= (de&&de.scrollTop) || document.body.scrollTop;

   var topp=0;
   if(h>_h)
     topp=(st+(h - _h)/2);
   else
     topp=st;

   var leftp = 0;
   if(w>_w)
     leftp = ((w - _w)/2);

   // 左侧距，顶部距
   return {left:leftp,top:topp};
}
var Ptr = {
    create:function(val) {
        var obj = new Object;
        obj.value = val;
        obj.get = function() {
            return obj.value;
        };
        obj.set = function(val) {
            obj.value = val;
        }
        return obj;
    }
};


function array_search(needle, haystack, argStrict) {
    var strict = !! argStrict;
    var key = "";
    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return key;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return key;
            }
        }
    }

    return false;
}

function in_array (needle, haystack, argStrict) {
    // Checks if the given value exists in the array
    //
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/in_array
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: vlado houba
    // +   input by: Billy
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: true
    // *     example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
    // *     returns 2: false
    // *     example 3: in_array(1, ['1', '2', '3']);
    // *     returns 3: true
    // *     example 3: in_array(1, ['1', '2', '3'], false);
    // *     returns 3: true
    // *     example 4: in_array(1, ['1', '2', '3'], true);
    // *     returns 4: false

    var key = '',
        strict = !! argStrict;

    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }

    return false;
}

// @param (Array) tabs tabid
function hide_btns(tabs) {
    for(var i = 0; i < tabs.length; ++i) {
        $("#"+tabs[i]+" tr:first a:eq(1)").hide();
        $("#"+tabs[i]+" tr:first").nextAll().find("a:eq(0)").hide();
    }
}

// 移除递交及hidden等信息
// 只展示value 去掉input select 等表单元素
function remove_inputs(tabid, btnid) {
   $("#"+btnid).remove();
   $("#"+tabid).find("input:hidden,text,select,textarea").each(function(){
        if ($(this).attr("nodeName") == "SELECT"){
            $(this).parent().append($(this).find("option:selected").text());
        }else{
            $(this).parent().append($(this).val());
        }
        $(this).remove();
    });
}
function remove_element(o){
    var tr = o.parent().parent();
    var prop = _get_prop(tr);

    var id = tr.find(":input[name="+prop+"id[]]").attr("value");
    if (id.length > 0) {
        // ajax请求 删除记录
        var server_id = jQuery('#id').val();
        var server_st = jQuery('#st').val();
        var val = '';
        if (prop == 'port') {
            val = tr.find(":input[name="+prop+"ip[]]").attr("value");
        } else {
            val = id;
        }

        var url = APP + '&mod=server&act=delpart&part='+prop+'&part_id=' +id + '&server_id='+server_id+'&val='+val+'&st='+server_st;
        return JGUtils._confirm_request("确认要删除该记录？", url, null, function() {
            tr.remove();
        });

    } else {
         tr.remove();
    }
}

function remove_element_cb(o, callback){
    var tr = o.parent().parent();
    var prop = _get_prop(tr);

    var id = tr.find(":input[name="+prop+"id[]]").attr("value");
    if (id.length > 0) {
        if (callback) callback(tr, id, prop);
    } else {
         tr.remove();
    }

}

/**
 *
 * @param url
 * @param id
 * @param sel
 * @param first 第一个下拉列表的默认值
 */
function refresh_select(url, params, sel){
	$("#" + sel).val("");
	var str_params = "";
	var first = arguments[3] ? arguments[3] : "All";
	if(params instanceof Array){
		for(var p in params){
			str_params += "&params[]=" + params[p];
		}
	}else{
		str_params = "&params[]=" + params;
	}
	url = url + str_params;
	$.getJSON(url,function(data){
		if(data){
			$("#" + sel).empty();
			$("#"+sel).append("<option value=''>" + first + "</option>")
			$.each(data,function(i, o){
				$("#"+sel).append("<option value='" + o.id + "'>" + o.name + " </option>")
			});
		}
	});
}

function rebuild_select(select_, data, first){
	$("#"+ select_).empty();
	first = (typeof(first) == "undefined")? '请选择':first;
	if(data){
		$("#"+select_).append("<option value=''>" + first + "</option>")
		$.each(data,function(i, o){
			$("#"+select_).append("<option value='" + o.id + "'>" + o.name + " </option>")
		});
	}
}

function fill_values(datas) {
    for(var i in datas) {
        if (datas.hasOwnProperty(i)) {
            var tagName = $("#"+i).attr('tagName');
            switch(tagName) {
            case 'INPUT':
                 $("#"+i).val(datas[i]);
                 break;
            case 'SELECT':
                rebuild_select(i,datas[i]);
                break;
            case 'SPAN':
                $("#"+i).html(datas[i]);
                break;
            default:
                alert(tagName);
            }
        }
    }
}

function refresh_text(url, params, text){
	$("#"+text).val("");
	var str_params = "";
	if(params instanceof Array){
		for(var p in params){
			str_params += "&params[]=" + params[p];
		}
	}else{
		str_params = "&params[]=" + params;
	}
	url = url + str_params;
	$.getJSON(url,function(data	){
		if(data!=null && data!=undefined && !data.err){
			$("#"+text).val(data);
		}else{
			alert('获取' + text + "失败: " + data);
		}
	});
}

function clear_select(sel){
	var first = arguments[1] ? arguments[1] : "All";
	$("#" + sel).empty();
	$("#"+sel).append("<option value=''>" + first + "</option>")
}

function bind(f)
{
    if (f === null)
        return function() {};

    var args = Array.prototype.slice.call(arguments);
    args.shift();

    return function ()
    {
        var argsCopy = args.slice(0);
        var start = expandArgs(argsCopy, arguments);
        return f.apply(null, argsCopy.concat(Array.prototype.slice.call(arguments, start)));
    };
}

/*function foo(a, b, c)
{
    alert(a + ', ' + b + ', ' + c);
}*/

//bind(foo, 1)(2, 3);  // 1, 2, 3 bind(foo, 1, 2)(3);  // 1, 2, 3 bind(foo, _3, _2, _1)(1, 2, 3);  // 3, 2, 1 bind(foo, _2, _2, _2)(1, 2, 3);  // 2, 2, 2 bind(foo, _2, 10)(1, 2, 3);  // 2, 10, 3

function bindMember(f, o)
{
    if (f === null)
        return function() {};
    else if (o === null)
        return f;

    var args = Array.prototype.slice.call(arguments);
    args.shift();
    args.shift();

    return function ()
    {
        var start = 0;

        if (o.constructor == BindArg)
        {
            var arg = o.index;
            o = arguments[arg - 1];
            if (arg > start)
                start = arg;
        }

        var argsCopy = args.slice(0);
        start = expandArgs(argsCopy, arguments, start);
        return f.apply(o, argsCopy.concat(Array.prototype.slice.call(arguments, start)));
    };
}

/*var o = { name : 'someobject' };
function foo(a, b, c)
{
    alert(this.name + ', ' + a + ', ' + b + ', ' + c);
}
*/
//bindMember(foo, o, 1)(2, 3);  // someobject, 1, 2, 3 bindMember(foo, o, 1, 2)(3);  // someobject, 1, 2, 3 bindMember(foo, o, _3, _2, _1)(1, 2, 3);  // someobject, 3, 2, 1 bindMember(foo, _1, 10, _2, _3)(o, 1, 2);  // someobject, 10, 1, 2

function BindArg(i)
{
    this.index = i;
}

for (var i=1; i < 10; ++i)
{
    var arg = new BindArg(i);
    this['_' + i] = arg;
}

function expandArgs(args, arguments, start)
{
    if (start == null)
        start = 0;

    for (var i=0; i < args.length; ++i)
    {
        if (args[i] && args[i].constructor == BindArg)
        {
            var arg = args[i].index;
            if (arg > 0 && arg <= arguments.length)
            {
                args[i] = arguments[arg - 1];
                if (arg > start)
                    start = arg;
            }
            else                 args[i] = null;
        }
    }

    return start;
}
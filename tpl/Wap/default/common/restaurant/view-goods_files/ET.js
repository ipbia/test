(function () {
    var loadingGif = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMwAAADMCAYAAAA/IkzyAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDE0IDc5LjE1MTQ4MSwgMjAxMy8wMy8xMy0xMjowOToxNSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RTI4NkM3RUIyQ0M5MTFFNEIyNUI5MDA5OTE3MDA5ODQiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RTI4NkM3RUEyQ0M5MTFFNEIyNUI5MDA5OTE3MDA5ODQiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoyMzQ0QkZFMTAyQUExMUU0OEVGNkYyNzBFQTkyMkRGQSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoyMzQ0QkZFMjAyQUExMUU0OEVGNkYyNzBFQTkyMkRGQSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PuH6OEsAABVHSURBVHja7J0HmF1V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";

    var template = '<div class="et-msg-main">' +
        '<div class="et-msg-body"><div class="et-msg-title"></div>' +
        '<div class="et-msg-icon"></div><div class="et-msg-content"></div>' +
        '<div class="et-msg-btn"></div></div></div>';

    var btnHtml = '<a href="javascript:;" class="et-btn et-sm-btn">{text}</a>';

    var toastHtml = '<div class="et-msg-toast-main"><div></div></div>';

    var toastInitTime = 300;
    //初始化
    var $msg = $(template);
    var $toast = $(toastHtml);

   $(function(){
       $(document.body).append($msg);
       $(document.body).append($toast);
   });

    var $msgBody = $msg.find(".et-msg-body");
    var $title = $msg.find(".et-msg-title");
    var $icon = $msg.find(".et-msg-icon");
    var $content = $msg.find(".et-msg-content");
    var $msgBtn = $msg.find(".et-msg-btn");
    var shadeClick = false, toastTimer, msgTimer;

    var contentH = $(window).height() * 0.8 * 0.8;

    $msg.click(function () {
        if (shadeClick) {
            ETMsg.hide();
        }
    });
    $msgBody.click(function (event) {
        event.stopPropagation();
    });


    window.ETMsg = {
        /**
         * alert 弹出框
         */
        alert: function (title, content, clickFun, ident) {
            resetMsg("alert");
            ident && $msg.addClass(ident);
            clickFun = clickFun || ETMsg.hide;
            $title.html(title);
            $content.html(content);
            var $btn = $msgBtn.find("a");
            $btn.off("click");
            $btn.click(function () {
                clickFun();
            });
            ETMsg.show();
        },
        /**
         * 确认框
         */
        confirm: function (title, content, confirmFun, cancelFun, ident) {
            resetMsg("confirm");
            ident && $msg.addClass(ident);
            confirmFun = confirmFun || ETMsg.hide;
            cancelFun = cancelFun || ETMsg.hide;
            $title.html(title);
            $content.html(content);
            var $btn1 = $msgBtn.find("a:eq(0)");
            var $btn2 = $msgBtn.find("a:eq(1)");
            $btn1.off("click");
            $btn1.click(function () {
                confirmFun();
            });
            $btn2.off("click");
            $btn2.click(function () {
                cancelFun();
            });
            ETMsg.show();
        },
        loading: function (content, hideTime, ident) {
            resetMsg("icon");
            $msg.addClass("et-msg-loading");
            ident && $msg.addClass(ident);
            hideTime = hideTime || false;
            $content.html(content);
//            $icon.append('<i class="icon-arrows-cw load_rota_animation"></i>');
//            $icon.append('<div class="loading loadingImg" src="' + loadingGif + '"/>');
            $icon.append('<div class="loading loadingImg"></div>');
            ETMsg.show();
            isNaN(hideTime.toString()) ? shadeClick = hideTime : ETMsg.hide(hideTime);
        },
        error: function (content, hideTime, ident) {
            resetMsg("icon");
            $msg.addClass("et-msg-error");
            ident && $msg.addClass(ident);
            hideTime = hideTime || false;
            $content.html(content);
            $icon.append('<i class="icon-cancel"></i>');
            isNaN(hideTime.toString()) ? shadeClick = hideTime : ETMsg.hide(hideTime);
            ETMsg.show();
        },
        ok: function (content, hideTime, ident) {
            resetMsg("icon");
            $msg.addClass("et-msg-ok");
            ident && $msg.addClass(ident);
            hideTime = hideTime || false;
            $content.html(content);
            $icon.append('<i class="icon-ok"></i>');
            isNaN(hideTime.toString()) ? shadeClick = hideTime : ETMsg.hide(hideTime);
            ETMsg.show();
        },
        warn: function (content, hideTime, ident) {
            resetMsg("icon");
            $msg.addClass("et-msg-warn");
            ident && $msg.addClass(ident);
            hideTime = hideTime || false;
            $content.html(content);
            $icon.append('<i class="icon-attention"></i>');
            isNaN(hideTime.toString()) ? shadeClick = hideTime : ETMsg.hide(hideTime);
            ETMsg.show();
        },
        none: function (content, hideTime, ident) {
            resetMsg("icon");
            $msg.addClass("et-msg-none");
            ident && $msg.addClass(ident);
            hideTime = hideTime || false;
            $content.html(content);
            $icon.append('<i class="icon-help-circled"></i>');
            isNaN(hideTime.toString()) ? shadeClick = hideTime : ETMsg.hide(hideTime);
            ETMsg.show();
        },
        toast: function (content, time, ident) {
            $toast.removeAttr("class").addClass("et-msg-toast-main");
            $toast.addClass("et-msg-toast");
            ident && $toast.addClass(ident);
            $toast.find("div:eq(0)").html(content);
            ETMsg.showToast(time);
        },
        defined: function (options) {
            var short = options.short || ""; //快捷初始化
            var icon = options.icon || null; //显示的图标
            var btnArr = options.btnArr || null; //按钮数组
            var title = options.title || null; //标题
            var content = options.content || null; //内容
            var ident = options.ident || null; //class 标识符
            var isRotate = options.isRotate || false;

            resetMsg(short);
            shadeClick = options.shadeClick || false;
            $msg.addClass("et-msg-defined");
            ident && $msg.addClass(ident);
            title ? show($title).html(title) : hide($title);
            content ? show($content).html(content) : hide($content);
            if (icon) {
                show($icon);
                if(title) $icon.css("padding-top", 5);
                var $i = getExten(icon);
                isRotate && $i.addClass("load_rota_animation");
                $icon.append($i);
            } else {
                hide($icon)
            }
            if (btnArr && btnArr.length) {
                for (var i = 0, l = btnArr.length; i < l; i++) {
                    (function () {
                        var o = btnArr[i];
                        var $btn = $(btnHtml.replace("{text}", o.text || "按钮"));
                        $btn.addClass(o.css);
                        $msgBtn.append($btn);
                        o.fun = o.fun ? o.fun : ETMsg.hide;
                        $btn.click(function () {
                            o.fun();
                        });
                    })();
                }
            } else {
                hide($msgBtn);
            }
            ETMsg.show();
        },
        hide: function (time) {
            clearTimeout(msgTimer);
            time = time || 0;
            msgTimer = setTimeout(function () {
                $msgBody.css({
                    'transform': 'scale(0)',
                    webkitTransform: 'scale(0)'
                });
                setTimeout(function () {
                    var $i = $msg.find(".et-msg-icon:visible i, .et-msg-icon:visible img");
                    if ($i.length && $i.hasClass("load_rota")) {
                        $i.removeClass("load_rota");
                    }
                    $msg.css("display", "none");
                }, 110);
            }, time);

        },
        show: function () {
            clearTimeout(msgTimer);
            $msg.css("display", "block");
            resetContentHeight();
            var $img = $icon.find("img");
            if ($img.length) {
                var img = new Image();
                img.onload = function () {
                    resetContentHeight();
                };
                img.src = $img[0].src;
            }
            setTimeout(function () {
                $msgBody.css({
                    'transform': 'scale(1)',
                    webkitTransform: 'scale(1)'
                });
                var $i = $msg.find(".et-msg-icon:visible i, .et-msg-icon:visible img");
                if ($i.length && $i.hasClass("load_rota_animation")) {
                    $i.hide().show(0, function () {
                        $i.addClass("load_rota");
                    });
                }
            }, 10);
        },
        showToast: function (time) {
            time = time || 2000;
            clearTimeout(toastTimer);
            $toast.stop().css("display", "none").fadeIn(toastInitTime, function () {
                toastTimer = setTimeout(function () {
                    ETMsg.hideToast();
                }, time);
            });
        },
        hideToast: function () {
            clearTimeout(toastTimer);
            $toast.stop().fadeOut(toastInitTime);
        }
    };

    var resetMsg = function (type) {
        shadeClick = false;
        $msg.removeAttr("class").addClass("et-msg-main");
        $msgBody.removeAttr("style").css({
            'transform': 'scale(0)',
            webkitTransform: 'scale(0)'
        });
        $content.removeAttr("style").css({'max-height': contentH + 'px'});
        $icon.html("");
        show($icon);
        show($title);
        show($msgBtn);
        show($content);
        $msgBtn.html("");
        switch (type) {
            case 'alert':
                hide($icon);
                $msgBtn.append(btnHtml.replace("{text}", "确定"));
                $msg.addClass("et-msg-alert");
                break;
            case 'confirm':
                hide($icon);
                $msgBtn.append(btnHtml.replace("{text}", "确定"))
                    .append(btnHtml.replace("{text}", "取消"));
                $msg.addClass("et-msg-confirm");
                break;
            case 'icon':
                hide($title);
                hide($msgBtn);
                $msgBody.css("width", 130);
                $content.css("padding-top", 5);
                $icon.css("padding-top", 10);
                $msg.addClass("et-msg-icon-item");
                break;
        }
    };
    var show = function ($obj) {
        if ($obj.hasClass("et-msg-btn")) {
            $obj.css({
                'display': "box",
                'display': "-webkit-box"
            });
        } else {
            $obj.css("display", "block");
        }
        return $obj;
    };

    var hide = function ($obj) {
        $obj.css("display", "none");
        return $obj;
    };

    var resetContentHeight = function () {
        var msgH = $msgBody.outerHeight();
        $msgBody.css("marginTop", msgH / 2 * -1);
    };

    var getExten = function (str) {
        if (str instanceof Image) {
            return str;
        } else if (str.indexOf(".") == -1 && str.indexOf("base64,") == -1) {
            return $('<i class="' + str + '"></i>');
        } else {
            return $('<img src="' + str + '"/>');
        }
    };
})();
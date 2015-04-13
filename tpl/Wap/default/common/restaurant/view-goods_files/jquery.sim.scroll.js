jQuery.easing['jswing'] = jQuery.easing['swing'];
jQuery.extend(jQuery.easing,
    {
        def: 'easeOutQuad',
        swing: function (x, t, b, c, d) {
            //alert(jQuery.easing.default);
            return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
        }, easeOutQuad: function (x, t, b, c, d) {
        return -c * (t /= d) * (t - 2) + b;
    }
    });

(function ($) {
    $.simScroll = function (element, options) {

        // 插件的默认选项
        var defaults = {
            width: "100%",
            height: "100%",
            stopDoc: true,
            zIndex: 999,
            iPhone: true,
            showScroll: true
        };

        var plugin = this;

        plugin.settings = {};

        var $element = $(element), // jQuery版本的DOM元素的引用
            element = element;    // 实际的DOM元素的引用

        plugin.init = function () {
            plugin.settings = $.extend({}, defaults, options);
            // 初始化代码

            if (plugin.settings.iPhone) {
                if (!plugin.isiPhone()) return false;
            }
            $("html,body").css({width: "100%", height: "100%"});
            var $ssb = $(".sim-scroll-bar");
            if (!$ssb.length && plugin.settings.showScroll) {
                $ssb = $('<div class="sim-scroll-bar" style="width: 4px;background:#000;border-radius: 4px;position: fixed;right: 2px; top: 0;opacity:0;z-index: ' + plugin.settings.zIndex + '"></div>');
                $(document.body).append($ssb);
            }
            plugin.settings.$ssb = $ssb;

            if (plugin.settings.stopDoc) {
                window.addEventListener("touchmove", function (event) {
                    event.stopPropagation();
                    event.preventDefault();
                }, false);
            }

            $element.css({
                width: plugin.settings.width,
                height: plugin.settings.height,
                overflowY: "auto",
                overflowX: "hidden"
            });



            element.addEventListener("touchstart", function (event) {
                event.stopPropagation();
//                event.preventDefault();
                plugin.settings.obj_height = $element.outerHeight(true);
                plugin.settings.isTouch = true;
                plugin.settings._oldDate = new Date();
                plugin.settings._isMove = false;
                $element.stop();
                if (plugin.settings.showScroll) {
                    plugin.settings.$ssb.stop(true, true);
                    var hb = plugin.settings.obj_height / element.scrollHeight;
                    plugin.settings.ssb_height = plugin.settings.obj_height * hb;
                    plugin.settings.$ssb.css({opacity: 0.3, height: plugin.settings.ssb_height, display: "block"});
                }
                plugin.settings.startScrollTop = element.scrollTop;
                var touch = event.targetTouches[0];
                plugin.settings._timeStamp = event.timeStamp;
                plugin.settings._pageY = touch.pageY;
            }, false);
            element.addEventListener("touchmove", function (event) {
//                event.stopPropagation();
//                event.preventDefault();
                if (plugin.settings.isTouch) {
                    var date = new Date();
                    if(date - plugin.settings._oldDate < 30){
                        plugin.settings._isMove = false;
                        return false;
                    }else{
                        plugin.settings._isMove = true;
                    }
                    var touch = event.targetTouches[0];
                    if (plugin.settings.move_pageY) {
                        var touchD = touch.pageY - plugin.settings.move_pageY;
                        plugin.settings.direction = touchD > 0 ? "down" : "top";
                        var newTop = (plugin.settings.startScrollTop || 0) + touchD * -1;
                        if(newTop < 0) newTop = 0;
                        if(newTop > element.scrollHeight - plugin.settings.obj_height) newTop = element.scrollHeight - plugin.settings.obj_height;
                        element.scrollTop = newTop;
                        plugin.settings.startScrollTop = newTop;

                        plugin.settings.move_timeStamp = event.timeStamp;

                        plugin.settings.$ssb.css("top", newTop /  (element.scrollHeight - plugin.settings.obj_height) * (plugin.settings.obj_height - plugin.settings.ssb_height));
                    }
                    plugin.settings.move_pageY = touch.pageY;
                }
            }, false);
            element.addEventListener("touchend", function (event) {
//                event.stopPropagation();
//                event.preventDefault();
                if(!plugin.settings._isMove) return false;
                plugin.settings.isTouch = false;
                var timeStamp = event.timeStamp;
                var timeTemp = timeStamp - (plugin.settings._timeStamp || 0);
                if (timeTemp <= 200) {
                    var distance = plugin.settings.move_pageY - plugin.settings._pageY;
                    distance = distance * (1 + timeTemp / 200) * -1;
                    if (distance > 300) distance = 300;
                    if (distance < -300) distance = -300;

                    distance = plugin.settings.startScrollTop + distance;
                    if (distance < 0) distance = 0;
                    if(distance > element.scrollHeight - plugin.settings.obj_height) distance = element.scrollHeight - plugin.settings.obj_height;

                    $element.animate({scrollTop: distance}, 150, "easeOutQuad");

                    plugin.settings.$ssb.animate({top: distance /  (element.scrollHeight - plugin.settings.obj_height) * (plugin.settings.obj_height - plugin.settings.ssb_height)}, 150, "easeOutQuad");
                }
                plugin.settings.$ssb.delay(1000).fadeOut(500);
                plugin.settings.move_pageY = null;
            }, false);
        };

        plugin.isiPhone = function () {
            var userAgentInfo = navigator.userAgent;
            if (userAgentInfo.indexOf("iPhone") != -1) return "iPhone";
            if (userAgentInfo.indexOf("iPad") != -1) return "iPad";
            return false;
        };

        plugin.init();


    };

    $.fn.simScroll = function (options) {

        return this.each(function () {
            if (undefined == $(this).data('simScroll')) {
                var plugin = new $.simScroll(this, options);
                $(this).data('simScroll', plugin);
            }
        });
    }

})(jQuery);
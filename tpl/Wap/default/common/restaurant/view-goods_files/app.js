App = window.App || {};

(function ($) {
    if (typeof avalon != 'undefined') {
        // 禁用阿瓦隆的依赖管理
        avalon.config({loader: false});

        // 设置定界符
        avalon.config({
            interpolate: ["{%", "%}"]
        });

        /**
         * 活的ViewModel的快捷方式
         * @param name
         * @returns {*}
         */
        avalon.vm = function (name) {
            return avalon.vmodels[name];
        };

        /**
         * 转发事件
         * @param vm
         * @param event
         * @param type Sting 转发类型 down|up
         */
        avalon.relay = function (vm, event, type) {
            type = typeof type === 'undefined' ? 'down' : type;
            var fn = function () {
                var args = Array.prototype.slice.call(arguments);
                vm.$unwatch(event, fn);
                args.unshift((type ? type + '!' : '') + event);
                vm.$fire.apply(vm, args);
                vm.$watch(event, fn);
            };
            vm.$watch(event, fn);
        };

        /**
         * 向下广播事件
         * @param vm
         * @param event
         */
        avalon.capture = function (vm, event) {
            var args = Array.prototype.slice.call(arguments, 2);
            event = 'down!' + event;
            args.unshift(event);
            vm.$fire.apply(vm, args);
        };

        /**
         * 向上冒泡事件
         * @param vm
         * @param event
         */
        avalon.bubble = function (vm, event) {
            var args = Array.prototype.slice.call(arguments, 2);
            event = 'up!' + event;
            args.unshift(event);
            vm.$fire.apply(vm, args);
        };

        /**
         * 榨取vm中真实的数据
         * @param vm
         * @returns {*}
         */
        avalon.extract = function (vm) {
            if (!vm.$model) {
                return vm;
            }

            var res = typeof vm.$model === 'object' ? {} : [];
            avalon.each(vm.$model, function (k, v) {
                res[k] = v;
            });

            return res;
        };
    }

    /**
     * 事件监听记录
     * @type {{}}
     * @private
     */
    var _events = {};

    /**
     * 实用函数
     */
    App.util = {
        /**
         * 获取URL查询对象
         * @returns {Object}
         */
        getUrlParmas: function () {
            var args = {};
            var query = location.search.substring(1);//获取查询串
            var pairs = query.split("&");//在逗号处断开
            for (var i = 0; i < pairs.length; i++) {
                var pos = pairs[i].indexOf('=');//查找name=value
                if (pos == -1)   continue;//如果没有找到就跳过
                var argname = pairs[i].substring(0, pos);//提取name
                var value = pairs[i].substring(pos + 1);//提取value
                args[argname] = unescape(value);//存为属性
            }
            return args;
        },

        /**
         * 触发事件
         *
         * 如果最后一个参数是函数,视为事件处理完成后需要执行的动作, 即""下一步函数""
         * 如果有多个监听函数,每个监听函数都需要执行 next() 方法
         * "下一步函数"会在所有的监听函数执行完成之后执行
         * 如果任何一个监听函数没有执行next(),"下一步函数"不会执行
         *
         * 如果触发的事件传递了"下一步函数", 但是没有定义该事件的监听, 则立即执行"下一步函数"
         *
         * 监听函数一般不返回任何值
         * 但是:
         * 如果任何一个监听函数返回false, 则终止所有后面的监听函数的执行, 如果有"下一步函数", 也不执行
         * 如果任何一个监听函数返回true, 则终止所有后面的监听函数的执行, 如果有"下一步函数", 立即执行
         */
        fire: function () {
            var args = Array.prototype.slice.call(arguments);
            var event = args.shift();
            var _next = null;

            if (args.length && typeof args[args.length - 1] === 'function') {
                // 最后一个参数是函数,最终操作函数
                _next = args.pop();
            }

            if (typeof _events[event] !== 'undefined' && _events[event].length > 0) {
                // 有监听函数
                var count = _events[event].length;

                if (_next) {
                    // 有下一步函数
                    var nc = count;

                    var next = function () {
                        nc--;
                        if (nc === 0) {
                            // 执行真正的操作
                            _next && _next();
                            _next = null; // 避免最后一个监听函数,多次调用
                        }
                    };

                    args.push(next);
                }

                for (var i = 0; i < count; i++) {
                    var cbk = _events[event][i];
                    var res = cbk.apply(this, args);
                    if (res === false) {
                        break;
                    } else if (res === true) {
                        _next && _next();
                        break;
                    }
                }
            } else {
                // 没有监听函数
                _next && _next();
            }
        },

        /**
         * 绑定事件
         */
        on: function (event, callback) {
            if (typeof _events[event] === 'undefined') {
                _events[event] = [];
            }
            _events[event].push(callback);
        },

        /**
         * 格式化日期
         * @param date
         * @param format
         * @returns {*}
         */
        formatDate: function (date, format) {
            var o = {
                "M+": date.getMonth() + 1, //month
                "d+": date.getDate(),    //day
                "h+": date.getHours(),   //hour
                "H+": date.getHours(),   //hour
                "m+": date.getMinutes(), //minute
                "s+": date.getSeconds(), //second
                "q+": Math.floor((date.getMonth() + 3) / 3),  //quarter
                "S": date.getMilliseconds() //millisecond
            };

            if (/(y+)/.test(format)) {
                format = format.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
            }

            for (var k in o) {
                if (new RegExp("(" + k + ")").test(format)) {
                    format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
                }
            }
            return format;
        },
        /**
         * 保证一个函数只被执行一次
         * @param func
         * @returns {Function}
         */
        once: function (func) {
            var ran = false, memo;
            return function () {
                if (ran) return memo;
                ran = true;
                memo = func.apply(this, arguments);
                func = null;
                return memo;
            };
        },

        /**
         * 空函数
         */
        noop: function () {
        },

        syncRepeat: function (run, times, cbk) {
            var next = function () {
                if (times <= 0) {
                    cbk && cbk();
                    return;
                }
                run(next);
                times--;
            };
            next();
        },

        nextTick: (function() {
            if (typeof setImmediate === 'function') {
                return function (fn) {
                    // not a direct alias for IE10 compatibility
                    setImmediate(fn);
                };
            }
            else {
                return function (fn) {
                    setTimeout(fn, 0);
                };
            }
        })()
    };

    // 别名
    App.on = App.util.on;
    App.fire = App.util.fire;

    App.ui = App.ui || {};

    /**
     * 关闭微信(浏览器)窗口
     * @param cbk
     */
    App.ui.close = function (cbk) {
        cbk = cbk || function () {
        };
        if (typeof WeixinJSBridge !== 'undefined') {
            WeixinJSBridge.invoke('closeWindow', {}, cbk);
        } else {
            window.close();
            cbk();
        }
    };

    /* Cross browser popstate */
    (function () {
        // for browsers only
        if (typeof window === "undefined") return;

        /**
         * 历史记录
         * @private
         */
        var _history = [];
        var _handle = true;

        // 事件
        App.event = App.event || {};
        App.event.Popstate = 'appPop';

        var listen = window.addEventListener,
            doc = document;

        function pop(hash) {
            if (!_handle) {
                return;
            }

            hash = hash.type ? location.hash : hash;

            if (typeof _history[1] !== 'undefined' && _history[1] == hash) { // 存在历史记录,只处理后退事件
                _history.shift();
                App.fire(App.event.Popstate, typeof _history[0] === 'undefined' ? '' : _history[0]);
            } else {
                App.fire(App.event.Popstate, '');
            }
        }

        listen("popstate", pop, false);

        /**
         * 操作页面URL
         * @param hash
         * @param replace 是否替换当前历史记录
         * @param clean 是否清楚所有历史记录
         */
        App.util.route = function (hash, replace, clean) {
            clean = typeof clean === 'undefined' ? false : !!clean;

            var _logic = function () {
                if (replace) {
                    _history.length && _history.shift();
                    history.pushState && history.replaceState(0, '', hash ? hash : '#');
                } else {
                    history.pushState && history.pushState(0, '', hash ? hash : '#');
                }

                // 写入历史
                _history.unshift(hash);

                // fire
                App.fire(App.event.Popstate, hash);
            };

            if (clean) {
                _handle = false;

                App.util.syncRepeat(
                    function (next) {
                        setTimeout(function () {
                            _history.shift();
                            history.back();
                            next();
                        }, 10);
                    },
                    _history.length - 1,
                    function () {
                        _handle = true;
                        _logic();
                    });

            } else {
                _logic();
            }


        };

        // 别名
        App.route = App.util.route;

        /*
         * --------------------------
         * 平台信息
         * --------------------------
         */
        if (typeof App.platform == 'undefined') {
            App.platform = {};
        }
        if (typeof App.platform.setting == 'undefined') {
            App.platform.setting = {};
        }

        /**
         * 获取配置
         * @param key
         * @returns {*}
         */
        App.platform.getSetting = function(key) {
            return typeof App.platform.setting[key] !== 'undefined'? App.platform.setting[key] : null
        };

        /**
         * 是否拥有厨房端
         * @returns {boolean}
         */
        App.platform.hasCook = function() {
            return App.platform.getSetting('has_cook') == 1;
        };

        /**
         * 是否拥有服务员端
         * @returns {boolean}
         */
        App.platform.hasWaiter = function() {
            return App.platform.getSetting('has_waiter');
        };

        /**
         * 服务员是否可以填写就餐费用
         * */
        App.platform.writeMoney = function(){
            return App.platform.getSetting('write_money');
        };
        /**
         * 服务员是否可以选择支付方式
         * */
        App.platform.selectPayType = function(){
            return App.platform.getSetting('select_pay_type');
        };

        /**
         * 支付时机
         * @returns {int} 0:先付款 1:后付款
         */
        App.platform.payOccasion = function() {
            var v = App.platform.getSetting('pay_occasion');
            v = v? parseInt(v, 10) : 0;
            return v;
        };


        /*
         * --------------------------
         * 店铺信息
         * --------------------------
         */
        if (typeof App.shop == 'undefined') {
            App.shop = {};
        }
        if (typeof App.shop.setting == 'undefined') {
            App.shop.setting = {};
        }

        /**
         * 获取配置
         * @param key
         * @returns {*}
         */
        App.shop.getSetting = function(key) {
            return typeof App.shop.setting[key] !== 'undefined'? App.shop.setting[key] : null
        };
    })();

})(jQuery);

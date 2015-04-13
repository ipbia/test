;(function() {
    document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
        //WeixinJSBridge.call('hideToolbar');
    });

    avalon.define('App', function(vm) {
        vm.appMember = App.member;
        vm.$platform = App.platform;
        vm.$shop = App.shop;

        // 转发事件
        avalon.relay(vm, App.event.CartChanged);

        /**
         * 安全的输出
         * 例如有时候ms-src遇到undefined的时候会有额外请求,使用$safe()就不会有问题了
         * @param v
         * @param def
         * @returns {*}
         */
        vm.$safe = function(v, def) {
            def = typeof def === 'undefined'? '' : def;
            return v? v : def;
        };
    });

    avalon.vm('App').appMember.$watch('valid', function() {
        avalon.nextTick(function() {
            if (avalon.vm('App').appMember.valid == false) {
                // 用户无效
                App.fire(App.event.MemberInvalid);
            }
        });
    });
})();
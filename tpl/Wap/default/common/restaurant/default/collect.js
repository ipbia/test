;
(function () {

    avalon.define('Collect', function (vm) {

        vm.goods = [];
        vm.spec = {};
        vm.cache = [];

        /**
         * 初始化,读取收藏
         */
        vm.$init = function () {
            vm.goods = [];
            vm.spec = [];
            vm.cache = [];

            App.api.getMemberCollect(function (json) {
                if (json.status) {
                    vm.goods = json.data.goods;
                    vm.spec = json.data.spec;
                }
            });
        };

        /**
         * 点击事件
         */
        vm.onTap = function (id, sid, price, goods, spec) {
            console.log(id, sid);
            var found = false;
            avalon.each(vm.cache, function(i, c) {
                if (c && c.id == id && c.sid == sid) {
//                  // 已经添加过了,删除之
                    vm.removeCache(i);
                    found = true;
                }
            });

            // 添加到购物车缓存
            if (!found) {
                vm.addToCache(id, sid, price, goods, spec);
            }
        };

        /**
         * 添加到购物车
         */
        vm.addToCart = function () {
            var vmCart = avalon.vm('Cart');
            App.fire(App.event.AddCollectToCart, function() {
                avalon.each(vm.cache, function (_, c) {
                    vmCart.add(c.id, c.sid, c.price, c.$goods, c.$spec);
                });
            });
        };

        /**
         * 添加到缓存
         * @param id
         * @param sid
         * @param price
         * @param goods
         * @param spec
         */
        vm.addToCache = function (id, sid, price, goods, spec) {
            // 触发事件,使view可以有机会执行规格选择
            App.fire(App.event.AddCollectToCartCache, id, sid, price, goods, spec, function (spec) {
                if (typeof spec === 'undefined') {
                    // 如果没有传递规格,则使用默认规格
                    spec = vm.$defaultSpec(id);
                }

                vm.cache.push({id: id, sid: sid, price: price, $goods: goods, $spec: spec});
            });
        };

        /**
         * 获得默认规格
         * @param id
         * @returns {*}
         */
        vm.$defaultSpec = function (id) {
            if (typeof vm.spec[id] === 'undefined' || vm.spec[id].length == 0) {
                return {};
            }

            // 查找默认规格
            for (var i = 0; i < vm.spec[id].length; i++) {
                var s = vm.spec[id][i];
                if (s.spec_id == 0) {
                    return s;
                }
            }

            // 否则返回第一个规格
            return typeof vm.spec[id][0] !== 'undefined' ? vm.spec[id][0] : {};
        };

        /**
         * 删除缓存
         * @param i
         */
        vm.removeCache = function (i) {
            vm.cache.removeAt(i);
        };

        /**
         * 是否被选中
         * @param id
         */
        vm.selected = function (id) {
            for (var i = 0; i < vm.cache.length; i++) {
                if (vm.cache[i].id == id) {
                    return true;
                }
            }

            return false;
        };

        /**
         * 删除收藏
         * @param id
         * @param gid
         */
        vm.remove = function (id, gid) {
            App.fire(App.event.DelMemberCollect, function() {
                App.api.delMemberCollect(gid, function() {
                    avalon.each(vm.goods, function(i, v) {
                        if (v.id == id) {
                            vm.goods.removeAt(i);
                        }
                    });
                });
            });
        }
    });

})();
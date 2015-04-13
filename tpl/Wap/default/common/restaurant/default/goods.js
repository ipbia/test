/**
 * 商品总控制器
 */
(function ($) {
    avalon.define('Goods', function (vm) {
        // 捕获MenuChanged事件,转发MenuChanged事件
        avalon.relay(vm, App.event.MenuChanged);
    });
})(jQuery);


/**
 * GoodsMenu控制器
 */
(function ($) {
    var goodsMenu = avalon.define('GoodsMenu', function (vm) {
        vm.menus = [];
        vm.activeMenu = 0;
        vm.activeMenuInfo = {};

        /**
         * 初始化菜单
         */
        vm.$init = function (mid) {
            App.fire(App.event.MenuLoading);
            App.api.getMenu(function (json) {
                if (json.status) {
                    goodsMenu.menus = json.data;
                    App.util.nextTick(function() {
                        App.fire(App.event.MenuLoadSuccess);
                    });
                    if (mid !== null) {
                        var _mid = goodsMenu.menus.$model[0].id;
                        if (mid) {
                            avalon.each(goodsMenu.menus, function (_, m) {
                                if (m.id == mid) {
                                    _mid = m.id;
                                }
                            });
                        }
                        goodsMenu.activeMenu = _mid;
                    }
                } else {
                    App.fire(App.event.MenuLoadError, json.msg);
                }
            }, function () {
                App.fire(App.event.MenuLoadError, '加载失败');
            }, function() {
                App.fire(App.event.MenuLoaded);
            });
        };

        /**
         * 点击顶级菜单
         * @param e
         */
        vm.tap = function (e) {
            var id = avalon(this).data('id');
            var menu;
            vm.menus.forEach(function (m) {
                if (m.id == id) {
                    menu = m;
                }
            });
            if (menu && menu.children) {
                vm.activeMenu = menu.children[0].id;
            } else {
                vm.activeMenu = id;
            }

            App.fire(App.event.MenuClicked, this);
        };

        /**
         * 点击子菜单
         * @param e
         */
        vm.tapChild = function (e) {
            vm.activeMenu = avalon(this).data('id');
            App.fire(App.event.MenuClicked, this);
            e.stopPropagation();
        };

        // 如果当前菜单发生改变,触发菜单改变事件
        // 冒泡到顶级控制器处理
        vm.$watch('activeMenu', function (mid) {
            avalon.bubble(vm, App.event.MenuChanged, mid);
            App.fire(App.event.MenuChanged, mid);
            vm.$eachMenus(function(m) {
                if (m.id == mid) {
                    vm.activeMenuInfo = m;
                }
            })
        });

        vm.$eachMenus = function(cbk, data) {
            data = data || vm.menus;
            avalon.each(data, function(_, m) {
                cbk(m);
                if (m.children) {
                    vm.$eachMenus(cbk, m.children);
                }
            });
        }

    });
})(jQuery);

/**
 * 商品列表控制器
 */
(function ($) {
    var vmGoodsList = avalon.define('GoodsList', function (vm) {

        vm.list = [];

        vm.spec = {};

        vm.add = function (id, sid, price, goods, spec) {
            avalon.vm('Cart').add(id, sid, price, goods, spec);
            vm.$updateNumber(id, sid);
        };

        vm.sub = function (id, sid) {
            avalon.vm('Cart').sub(id, sid);
            vm.$updateNumber(id, sid);
        };

        vm.remove = function (id, sid) {
            avalon.vm('Cart').remove(id, sid);
            vm.$updateNumber(id, sid);
        };

        vm.$getNumber = function (id, sid) {
            return avalon.vm('Cart').$getNumber(id, sid) || 0;
        };

        vm.$updateNumber = function (id, sid) {
            var number = avalon.vm('Cart').$getNumber(id, sid) || 0;
            if (vm.spec[id]) {
                vm.spec[id].forEach(function (s) {
                    if (s.id == sid) {
                        s.number = number;
                    }
                });
            }
        };

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
            return typeof vm.spec[id][0] !== 'undefined'? vm.spec[id][0] : {};
        };
    });

    // 加载商品列表
    vmGoodsList.$watch(App.event.MenuChanged, function (id) {
        App.fire(App.event.MenuGoodsLoading);
        vmGoodsList.list = [];
        vmGoodsList.spec = {};
        App.api.getMenuGoods(id, function (json) {
            if (json.status) {
                avalon.each(json.data.spec, function (spid, spec) {
                    avalon.each(spec, function (_, s) {
                        s.number = vmGoodsList.$getNumber(spid, s.id);
                    });
                });
                vmGoodsList.spec = json.data.spec;
                vmGoodsList.list = json.data.goods;
                App.fire(App.event.MenuGoodsLoadSuccess);
            }
        }, function () {
            App.fire(App.event.MenuGoodsLoadError);
        }, function() {
            App.fire(App.event.MenuGoodsLoaded);
        });
    });


})(jQuery);


/**
 * 商品详情控制器
 */
(function ($) {

    avalon.define('GoodsView', function (vm) {

        vm.goods = {};
        vm.spec = [];
        vm.currentSpecId = 0;
        vm.$cache = false; // 是否缓存购物车,直到执行加入购物车动作
        vm.cart = []; // [{id:1, sid:1, price:12, number:1},]

        /**
         * 初始化
         * @param id 商品id
         * @param isCache 是否购物车缓存模式
         */
        vm.$init = function (id, isCache) {
            vm.goods = {};
            vm.spec = [];
            vm.currentSpecId = 0;
            vm.$cache = typeof isCache !== 'boolean' ? isCache : false;
            vm.cart = [];

            App.fire(App.event.GoodsLoading);
            App.api.getGoods(id, function (json) {
                if (json.status) {
                    vm.goods = json.data.goods;
                    vm.spec = json.data.spec;
                    App.fire(App.event.GoodsLoadSuccess);
                    avalon.nextTick(function() {
                        // 默认选中第一个规格
                        vm.currentSpecId = vm.$defaultSpec().id;
                    });
                } else {
                    App.fire(App.event.GoodsLoadError, json.msg);
                }
            }, function () {
                App.fire(App.event.GoodsLoadError, '加载商品失败');
            }, function() {
                App.fire(App.event.GoodsLoaded);
            });
        };

        vm.$getSpec = function(id) {
            var spec;

            avalon.each(vm.spec, function (_, s) {
                if (s.id == id) {
                    spec = s;
                }
            });

            return spec;
        };

        vm.$getSpecPrice = function(id) {
            var spec = vm.$getSpec(id);
            return spec? (spec.original_price || spec.price) : spec;
        };

        vm.add = function () {
            if (typeof vm.goods.id === 'undefined' || !vm.currentSpecId) {
                return;
            }

            var spec = vm.$getSpec(vm.currentSpecId);

            if (spec) {
                if (vm.$cache) {
                    // 缓存模式
                    var found = false;
                    avalon.each(vm.cart, function (_, cart) {
                        if (cart.id == vm.goods.id && cart.sid == vm.currentSpecId) {
                            found = true;
                            cart.number++;
                        }
                    });
                    if (!found) {
                        vm.cart.push({id: vm.goods.id, sid: spec.id, price: spec.price, number: 1});
                    }
                } else {
                    // 直接加入购物车
                    avalon.vm('Cart').add(vm.goods.id, spec.id, spec.price, vm.goods.$model, spec.$model);
                }
            }

            // hack 触发重新数量
            var _id = vm.currentSpecId;
            vm.currentSpecId = 0;
            vm.currentSpecId = parseInt(_id, 10);
        };

        vm.sub = function () {
            if (typeof vm.goods.id !== 'undefined' && vm.currentSpecId) {
                if (vm.$cache) {
                    // 缓存模式
                    avalon.each(vm.cart, function (i, cart) {
                        if (cart.id == vm.goods.id && cart.sid == vm.currentSpecId) {
                            cart.number--;
                            if (cart.number <= 0) {
                                vm.cart.removeAt(i);
                            }
                        }
                    });
                } else {
                    // 直接减少购物车
                    avalon.vm('Cart').sub(vm.goods.id, vm.currentSpecId);
                }
            }

            // hack 触发重新数量
            var _id = vm.currentSpecId;
            vm.currentSpecId = 0;
            vm.currentSpecId = parseInt(_id, 10);
        };

        vm.remove = function () {
            if (typeof vm.goods.id !== 'undefined' && vm.currentSpecId) {
                avalon.vm('Cart').remove(vm.goods.id, vm.currentSpecId);
            }
        };

        vm.getNumber = function () {
            if (typeof vm.goods.id !== 'undefined') {
                var goods;
                if (vm.$cache) {
                    avalon.each(vm.cart, function (_, g) {
                        if (g.sid == vm.currentSpecId) {
                            goods = g;
                        }
                    });
                } else {
                    goods = avalon.vm('Cart').get(vm.goods.id, vm.currentSpecId);
                }

                return goods ? goods.number : 0;
            }

            return 0;
        };

        vm.getTotal = function () {
            if (typeof vm.goods.id !== 'undefined') {
                var goods;
                if (vm.$cache) {
                    avalon.each(vm.cart, function (_, g) {
                        if (g.sid == vm.currentSpecId) {
                            goods = g;
                        }
                    });
                } else {
                    goods = avalon.vm('Cart').get(vm.goods.id, vm.currentSpecId);
                }

                return goods ? goods.number * goods.price : 0;
            }

            return 0;
        };

        /**
         * 添加用户喜欢
         */
        vm.$like = function() {
            if (vm.goods.is_liked) {
                return;
            }

            var gid = vm.goods.goods_id;
            App.api.addMemberLike(gid, function(json) {
                if (json.status) {
                    App.fire(App.event.AddMemberLikeSuccess);
                } else {
                    App.fire(App.event.AddMemberLikeError, json.msg);
                }
            }, function() {
                App.fire(App.event.ServerError)
            });

            vm.goods.is_liked = true;
            vm.goods.liked++;
            App.fire(App.event.AddMemberLike);
        };

        /**
         * 添加用户收藏
         */
        vm.$collect = function() {
            if (vm.goods.is_collected) {
                return;
            }

            var gid = vm.goods.goods_id;
            App.api.addMemberCollect(gid, function(json) {
                if (json.status) {
                    App.fire(App.event.AddMemberCollectSuccess);
                } else {
                    App.fire(App.event.AddMemberCollectError, json.msg);
                }
            }, function() {
                App.fire(App.event.ServerError)
            });

            vm.goods.is_collected = true;
            App.fire(App.event.AddMemberCollect);
        };

        /**
         * 添加到购物车
         */
        vm.addToCart = function () {
            if (vm.$cache) {
                // 只有缓存购物车模式有此动作
                var vmCart = avalon.vm('Cart');
                avalon.each(vm.cart, function (_, cart) {
                    if (cart.number > 0) {
                        var spec;
                        for (var i = 0; i < vm.spec.length; i++) {
                            if (vm.spec[i].id == cart.sid) {
                                spec = vm.spec[i];
                            }
                        }
                        if (spec) {
                            for (i = 0; i < cart.number; i++) {
                                vmCart.add(cart.id, cart.sid, cart.price, vm.goods.$model, spec.$model);
                            }
                        }
                    }
                });

                // 清空缓存的购物
                vm.cart = [];
            }
        };

        /**
         * 跳转到订单确认页
         */
        vm.orderCheck = function () {
            vm.addToCart();
            location.href = App.url.OrderCheck;
        };

        /**
         * 添加商品并且跳转到订单确认页
         */
        vm.addAndCheck = function() {
            vm.add();
            vm.orderCheck();
        };

        /**
         * 查找默认规格
         * @returns {*}
         */
        vm.$defaultSpec = function () {
            // 查找默认规格
            for (var i = 0; i < vm.spec.length; i++) {
                var s = vm.spec[i];
                if (s.spec_id == 0) {
                    return s;
                }
            }

            // 否则返回第一个规格
            return typeof vm.spec[0] === 'undefined'? {} : vm.spec[0];
        };

        /**
         * 设置当前选中的规格
         * @param id
         */
        vm.$selectSpec = function(id) {
            vm.currentSpecId = id;
        };

        // 监控currentSpecId改变事件,删除购物车缓存中非当前规格ID的下单数据
        vm.$watch('currentSpecId', function (v) {
            if (v !== 0) {
                avalon.each(vm.cart, function (i, c) {
                    if (c.sid != v) {
                        vm.cart.removeAt(i);
                    }
                });
            }
        });

    });

})(jQuery);
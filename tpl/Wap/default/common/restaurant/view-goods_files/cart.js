/**
 * 购物车控制器
 */
(function ($) {
    var storage = localStorage;

    var vmCart = avalon.define('Cart', function (vm) {

        // 缓存的key
        vm.$saveKey = '__cart__' + App.mpid + '_' + App.spid;

        // 总价
        vm.total = 0;

        // 购物车商品数据
        vm.cart = []; // [{id:1, sid:1, price:12, number:1, remark:'', $goods:{}, $spec:{}},]

        // 购物车中的商品数量
        vm.num = 0;

        /**
         * 获取一个购物车商品
         * @param id
         * @param sid
         */
        vm.get = function (id, sid) {
            var goods = null;
            avalon.each(vm.cart, function (_, g) {
                if (g.id == id && g.sid == sid) {
                    goods = g;
                }
            });

            return goods;
        };

        /**
         * 添加或者更新一个商品到购物车
         * @param data
         */
        vm.set = function (data) {
            var g = vm.get(data.id, data.sid);

            if (g) {
                avalon.mix(g, data);
            } else {
                data = avalon.mix({id: 0, sid: 0, price: 0, number: 0, remark:'', $goods:{}, $spec:{}}, data);
                if (data.number > 0) {
                    vm.cart.push(data);
                }
            }

            // 向下广播购物车改变事件
            avalon.bubble(vm, App.event.CartChanged);
            // 触发应用事件
            App.fire(App.event.CartChanged);

            vm.compute();
            vm.save();
        };

        /**
         * 添加一个商品的数量
         * @param id
         * @param sid
         * @param price
         * @param goods
         * @param spec
         */
        vm.add = function (id, sid, price, goods, spec) {
            var g = vm.get(id, sid);
            if (g) {
                g.number++;
            } else {
                g = {id: id, sid: sid, price: price, number: 1, remark:''};
            }

            if (g.number == 1) {
                if (goods) {
                    g.$goods = avalon.extract(goods);
                }

                if (spec) {
                    g.$spec = avalon.extract(spec);
                }
            }

            vm.set(g);
            // 触发应用事件
            App.fire(App.event.CartAdd, g);
            // 广播购物车改变事件
            avalon.bubble(vm, App.event.CartChanged);
        };

        /**
         * 减少一个商品的数量
         */
        vm.sub = function (id, sid) {
            var g = vm.get(id, sid);
            if (g) {
                App.fire(App.event.CartSub, g, function() {
                    if (g.number <= 1) {
                        vm.remove(g.id, g.sid);
                    } else {
                        g.number = Math.max(0, --g.number);
                        vm.compute();
                        vm.save();
                    }
                    // 广播购物车改变事件
                    avalon.bubble(vm, App.event.CartChanged);
                });
            }
        };

        /**
         * 删除一个商品
         * @param id
         * @param sid
         */
        vm.remove = function (id, sid) {
            var g = vm.get(id, sid);
            if (g) {
                App.fire(App.event.CartRemove, g, function() {
                    g.number = 0;
                    vm.cart.remove(g);
                    // 广播购物车改变事件
                    avalon.bubble(vm, App.event.CartChanged);
                    vm.compute();
                    vm.save();
                });
            }
        };

        vm.removeAll = function() {
            vm.cart = [];
            // 广播购物车改变事件
            avalon.bubble(vm, App.event.CartChanged);
            vm.compute();
            vm.save();
        };

        /**
         * 保存购物车数据到缓存
         */
        vm.save = function () {
            var data = [];
            avalon.each(vm.cart, function (i, g) {
                var d = {};
                avalon.each(g, function (k, v) {
                    d[k] = v;
                });

                data.push(d);
            });

            storage.removeItem(vm.$saveKey);
            storage.setItem(vm.$saveKey, JSON.stringify(data));
        };

        /**
         * 加载缓存中的购物车数据
         */
        vm.load = function () {
            var data = JSON.parse(storage.getItem(vm.$saveKey));
            avalon.each(data, function (i, g) {
                vm.set(g);
            });
            vm.compute();
        };

        /**
         * 计算总价格
         */
        vm.compute = function () {
            var total = 0;
            var num = 0;
            avalon.each(vm.cart, function (i, g) {
                total += g.number * g.price;
                num += g.number;
            });

            vm.total = total;
            vm.num = num;

            // 触发购物车为空事件
            if (vm.total === 0) {
                App.fire(App.event.CartEmpty);
            }
        };

        /**
         * 获得一个商品的购物车数量
         * @param id
         * @param sid
         * @returns number
         */
        vm.$getNumber = function(id, sid) {
            var g = vm.get(id, sid);
            return g? g.number : 0;
        };

        /**
         * 设置服务器上的购物车到本地
         * @param cart
         */
        vm.dump = function (cart) {

        };

        /**
         * 设置一个商品的备注
         * @param id
         * @param sid
         * @param remark
         */
        vm.remark = function(id, sid, remark) {
            var g = vm.get(id, sid);
            if (g) {
                g.remark = remark;
                vm.save();
            }
        };

        /**
         * 推出订单,店内合并
         */
        vm.submit = function() {
            var spec = [];

            avalon.each(vm.cart.$model, function(_, cart) {
                spec.push({sid: cart.sid, number:cart.number});
            });

            App.api.cartSubmit(spec, function(json) {
                if (json.status) {
                    vm.removeAll();
                    App.util.fire(App.event.CartSubmitSuccess, json.data);
                } else {
                    App.util.fire(App.event.CartSubmitError, json.msg);
                }
            }, function() {
                App.util.fire(App.event.CartSubmitError, '通讯异常,保存购物车失败');
            });
        };
    });

    // 加载本地数据
    vmCart.load();

})(jQuery);


(function($) {

    avalon.define('CartState', function(vm) {
        // 商品类型
        vm.$types = [];

        // 统计数据
        vm.data = [];

        vm.$init = function() {
            App.api.getGoodsTypes(function(json) {
                if (json.data) {
                    vm.$types = json.data;
                    json.data.forEach(function(d) {
                        vm.data.push({id: d.id, name: d.name, number:0});
                    });
                    vm.$compute();
                }
            }, function() {});
        };

        vm.$compute = function() {
            if (vm.data.length) {
                var vmCart = avalon.vm('Cart');
                // 先清空统计
                avalon.each(vm.data, function(_, d) {
                    d.number = 0;
                });
                // 重新计算
                avalon.each(vmCart.cart, function(_, cart) {
                    avalon.each(vm.$types, function(_, type) {
                        if (type.id == cart.$goods.type_id) {
                            avalon.each(vm.data, function(_, d) {
                                if (d.id == type.id) {
                                    d.number += cart.number;
                                }
                            });
                        }
                    });
                });
            }
        };

        vm.$watch(App.event.CartChanged, vm.$compute);
    });

})(jQuery);


(function($) {
    var vmCart = avalon.vm('Cart');

    var vmCartNumber = avalon.define('CartNumber', function(vm) {
        vm.num = vmCart.num;
    });

    vmCart.$watch('num', function(v) {
        vmCartNumber.num = v;
    });

})(jQuery);
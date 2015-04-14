/**
 * 订单管理模型
 */
;(function($) {

    var vmOrder = avalon.define('Order', function(vm) {

        // 订单生效的优惠
        vm.promotion = [];

        // 订单实际价格
        vm.price = 0;

        // 订单参数
        vm.params = [{'paytype':0}]; // [{key:'xxx', value:mixed},],

        // 优惠券
        vm.coupons = [];

        vm.couponsSelectedNumber = 0;

        vm.$init = function() {
            //获取当前用户优惠券
            App.api.getMemberCoupons(function(json) {
                if (json.status) {
                    avalon.each(json.data, function(_, c){
                        c.selected = false;
                    });
                    vm.coupons = json.data;
                } else {
                    if (json.code == -1) {
                        App.fire(App.event.MemberNotVip, json.msg);
                    }
                }
            });
            vm.compute();
        };
        
        
        /**
         * 获取用户历史订单
         */
        vm.getHistoryList = function() {
            App.api.getOrderHistoryList(function(json) {
                if (json.status) {
                	vm.goods = json.data.goods;
                    vm.spec = json.data.spec;
                }
            });
            vm.compute();
        };
        
        //是否保存用户地址信息
        vm.saveinfo = function(value){
        	vm.setParams('saveinfo', value);
        }
        
      //是否保存用户地址信息
        vm.getSaveinfo = function(){
        	return vm.getParams('saveinfo');
        }

        /**
         * 提交订单
         */
        vm.submit = function() {
            var spec = [];
            var vmCart = avalon.vm('Cart');

            avalon.each(vmCart.cart.$model, function(_, cart) {
                spec.push({id: cart.sid, did:cart.did, count:cart.number, remark:cart.remark});
            });

            var params = {};
            avalon.each(vm.params, function(_, p) {
                params[p.key] = p.value;
            });
            

            App.fire(App.event.OrderSubmitting);
            App.api.submitOrder(spec, params, function(json) {
                if (json.status) {
                	//用户信息失效，重新扫描
                	if(json.code == '1001'){
                		scanQRCode('微信扫一扫餐桌上的二维码!');
                		return;
                	}
                    avalon.vm('Cart').removeAll();
                    App.util.fire(App.event.OrderSubmitSuccess, json.data);
                } else {
                    App.util.fire(App.event.OrderSubmitError, json.msg);
                }
            }, function() {
                App.util.fire(App.event.OrderSubmitError, '通讯异常,提交订单失败');
            });
        };

        /**
         * 现金支付提交
         */
        vm.cashPay = function() {
        	vm.setParams('paymode', 0);
            vm.setParams('payType', 0);
            vm.submit();
        };

        /**
         * 现金支付提交(到店付)
         */
        vm.cashPay = function() {
        	vm.setParams('paymode', 1);
            vm.setParams('paytype', 'Dianfu');
            vm.submit();
        };

        /**
         * 微信支付提交
         */
        vm.wxPay = function() {
        	vm.setParams('paymode', 1);
            vm.setParams('paytype', 'Weixin');
            vm.submit();
        };
        
        /**
         * 财付通即时到帐支付提交
         */
        vm.tenpayComputerPay = function() {
        	vm.setParams('paymode', 1);
            vm.setParams('paytype', 'TenpayComputer');
            vm.submit();
        };
        
        
        /**
         * 货到付款
         */
        vm.daofuPay = function() {
        	vm.setParams('paymode', 1);
            vm.setParams('paytype', 'Daofu');
            vm.submit();
        };
        
        /**
         * 刷卡支付
         */
        vm.cardPay = function() {
            vm.setParams('payType', 2);
            vm.submit();
        };

        /**
         * 代付支付
         */
        vm.helpPay = function() {
            vm.setParams('payType', 4);
            vm.submit();
        };

        /**
         * 余额支付
         */
        vm.balancePay = function() {
        	vm.setParams('paymode', 4);
            vm.submit();
        };

        vm.$computeQueue = [];
        /**
         * TODO 请求机制处理
         */
        vm.compute = function() {
            vm.$compute();
        };

        vm.$compute = function() {
            App.fire(App.event.OrderComputing);
            var spec = [];
            var vmCart = avalon.vm('Cart');

            if (vmCart.total == 0) {
                vm.price = 0;
                vm.promotion = [];
                App.fire(App.event.OrderComputeSuccess);
                return;
            }

            avalon.each(vmCart.cart.$model, function(_, cart) {
                spec.push({sid: cart.sid, number:cart.number});
            });

            var params = {};
            avalon.each(vm.params, function(_, p) {
                params[p.key] = p.value;
            });

            App.api.computeOrder(spec, params, function(json) {
                if (json.status) {
                    if (json.data.error) {
                        // TODO 处理订单错误提示
                    }
                    vm.price = json.data.price;
                    vm.promotion = json.data.promotion;
                    $('.count').hide();
                    $('.loading').show();
                    App.fire(App.event.OrderComputeSuccess);
                }
            }, function() {
                App.fire(App.event.OrderComputeError);
            });
        };

        vm.del = function() {
            avalon.vm('Cart').removeAll();
        };

        /**
         * 设置订单参数
         * @param k
         * @param v
         */
        vm.setParams = function(k, v) {
            var param = vm.getParams(k);
            if (param) {
                param.value = v;
            } else {
                vm.params.push({key: k, value: v});
            }
        };

        /**
         * 获得订单参数
         * @param k
         * @returns {*}
         */
        vm.getParams = function(k) {
            var param = null;
            avalon.each(vm.params, function(i, p) {
                if (p.key == k) {
                    param = p;
                }
            });

            return param;
        };

        /**
         * 选择一个优惠券
         * @param id
         */
        vm.selectCoupons = function(id) {
            var number = 0;
            avalon.each(vm.coupons, function(_, c) {
                if (c.id == id) {
                    c.selected = !c.selected;
                }
                if (c.selected) {
                    number++;
                }
            });
            vm.couponsSelectedNumber = number;
        };

            vm.selectCouponsAndCompute = function(id) {
            vm.selectCoupons(id);
            vm.selectCouponsFinished();
        };

        /**
         * 设置订单的优惠券参数
         */
        vm.selectCouponsFinished = function() {
            var selected = [];
            avalon.each(vm.coupons, function(_, c) {
                if (c.selected) {
                    selected.push(c.id);
                }
            });
            vm.setParams('coupons', selected);
            vm.compute();
        };

        /**
         * 获得已经选择的优惠券
         * @returns {Array}
         */
        vm.selectedCoupons = function() {
            var coupons = [];
            avalon.each(vm.coupons, function(_, c) {
                if (c.selected) {
                    coupons.push((c));
                }
            });

            return coupons;
        };

        /**
         * 获得订单总价
         * @returns float
         */
        vm.cartTotal = function() {
            return avalon.vm('Cart').total;
        }
        
        /**
         * 保存收货人信息
         */
        vm.saveUserInfo = function(username, usertel, useraddress) {
        	vm.setParams('truename', username);
        	vm.setParams('tel', usertel);
        	vm.setParams('address', useraddress);
        }
        
        /**
         * 是否有收货人信息
         */
        vm.haveUserInfo = function(){
        	//如果不支持外卖，则不用校验用户信息
        	if(vm.getSaveinfo() == '0'){
        		return true;
        	}
        	
        	if(vm.getParams('truename') && vm.getParams('tel')
        			&& vm.getParams('address')){
        		return true;
        	}
        	return false;
        }
    });


    // 监控购物车改变,重新计算
    avalon.ready(function() {
        avalon.vm('Cart').$watch('total', function() {
            vmOrder.compute();
        });
    });

})(jQuery);

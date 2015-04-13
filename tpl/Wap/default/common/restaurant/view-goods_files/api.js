;App = window.App || {};

(function($) {

    /**
     * 应用程序接口
     */
    App.api = App.api || {};

    /**
     * 获得商品目录
     */
    App.api.getMenu = function (success, error, aways) {
        $.ajax({
            url: App.url.Menu,
            dataType: 'json',
            success: function (json) {
                success && success.apply(this, arguments);
            },
            error: function() {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 获得某个目录下的商品
     */
    App.api.getMenuGoods = function(mid, success, error, aways) {
        $.ajax({
            url: App.url.MenuGoods + '?mid=' + mid,
            dataType: 'json',
            success: function (json) {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 获取一个商品的信息
     */
    App.api.getGoods = function(id, success, error, aways) {
        $.ajax({
            url: App.url.GoodsInfo + '?id=' + id,
            dataType: 'json',
            success: function (json) {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 获取一个平台商品在店内的信息
     */
    App.api.getPlatformGoods = function(gid, success, error, aways) {
        $.ajax({
            url: App.url.GoodsInfo + '?gid=' + gid,
            dataType: 'json',
            success: function (json) {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 获得商品的分类
     * @param success
     * @param error
     * @param aways
     */
    App.api.getGoodsTypes = function(success, error, aways) {
        $.ajax({
            url: App.url.GoodsType,
            dataType: 'json',
            method: 'post',
            success: function (json) {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 计算订单及优惠
     * @param spec 购物车数据 [{sid:1, number:1},]
     * @param params 其他参数
     * @param success
     * @param error
     * @param aways
     */
    App.api.computeOrder = function(spec, params, success, error, aways) {
        $.ajax({
            url: App.url.OrderCompute,
            dataType: 'json',
            method: 'post',
            data:{spec:spec, params:(params||{})},
            success: function() {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 订单提交
     * @param spec 购物车数据 [{sid:1, number:1, remark:'xxx'},]
     * @param params 其他参数
     * @param success
     * @param error
     * @param aways
     */
    App.api.submitOrder = function(spec, params, success, error, aways) {
        $.ajax({
            url: App.url.OrderSubmit,
            dataType: 'json',
            method: 'post',
            data:{spec:spec, params:(params || {})},
            success: function() {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 获取用户优惠券
     * @param success
     * @param error
     * @param aways
     */
    App.api.getMemberCoupons = function(success, error, aways) {
        $.ajax({
            url: App.url.MemberCoupons,
            dataType: 'json',
            method: 'post',
            success: function() {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 获取用户收藏
     * @param success
     * @param error
     * @param aways
     */
    App.api.getMemberCollect = function(success, error, aways) {
        $.ajax({
            url: App.url.MemberCollectList,
            dataType: 'json',
            method: 'post',
            success: function() {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 添加用户收藏
     * @param gid
     * @param success
     * @param error
     * @param aways
     */
    App.api.addMemberCollect = function(gid, success, error, aways) {
        $.ajax({
            url: App.url.MemberAddCollect,
            data: {gid:gid},
            dataType: 'json',
            method: 'post',
            success: function() {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 删除用户收藏
     * @param gid
     * @param success
     * @param error
     * @param aways
     */
    App.api.delMemberCollect = function(gid, success, error, aways) {
        $.ajax({
            url: App.url.MemberDelCollect,
            data: {gid:gid},
            dataType: 'json',
            method: 'post',
            success: function() {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 添加用户喜欢的商品
     * @param gid
     * @param success
     * @param error
     * @param aways
     * @constructor
     */
    App.api.addMemberLike = function(gid, success, error, aways) {
        $.ajax({
            url: App.url.MemberAddLike,
            data: {gid:gid},
            dataType: 'json',
            method: 'post',
            success: function() {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 保存购物车
     * @param spec 购物车数据 [{sid:1, number:1},]
     * @param success
     * @param error
     * @param aways
     */
    App.api.cartSubmit = function(spec, success, error, aways) {
        $.ajax({
            url: App.url.CartSubmit,
            dataType: 'json',
            method: 'post',
            data:{spec:spec},
            success: function() {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 购物车已合并
     * @param id
     * @param success
     * @param error
     * @param aways
     */
    App.api.cartCombined = function(id, success, error, aways) {
        $.ajax({
            url: App.url.CartCombined,
            dataType:'json',
            method: 'post',
            data: {id: id},
            success: function() {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     * 通知代付成功
     * @param id
     * @param success
     * @param error
     * @param aways
     */
    App.api.orderHelpPayNotice = function(id, success, error, aways) {
        $.ajax({
            url: App.url.OrderHelpPayNotice,
            dataType:'json',
            method: 'post',
            data: {id: id},
            success: function() {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    };

    /**
     *
     */
    App.api.memberCall = function(success, error, aways) {
        $.ajax({
            url: App.url.MemberCall,
            dataType:'json',
            method: 'post',
            success: function() {
                success && success.apply(this, arguments);
            },
            error: function () {
                error && error.apply(this, arguments);
            },
            complete: function() {
                aways && aways.apply(this, arguments);
            }
        });
    }

})(jQuery);
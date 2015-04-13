App = window.App || {};

(function ($) {

    App.event = App.event || {};

    /**
     * 各种事件名
     */
    App.event = $.extend(App.event, {
        ServerError: 'appServerError', // 服务器错误

        MenuLoading: 'appMenuLoading', // 商品目录加载中
        MenuLoadSuccess: 'appMenuLoadSuccess', // 商品目录加载成功
        MenuLoadError: 'appMenuLoadError', // 商品目录加载失败
        MenuLoaded: 'appMenuLoaded', // 商品目录加载完成
        MenuChanged: 'appMenuChanged',   // 商品目录改变
        MenuClicked: 'appMenuClicked',   // 商品目录被点击

        MenuGoodsLoading: 'appMenuGoodsLoading', // 目录的商品加载中
        MenuGoodsLoadSuccess: 'appMenuGoodsLoadSuccess', // 目录的商品加载成功
        MenuGoodsLoadError: 'appMenuGoodsLoadError', // 目录的商品加载失败
        MenuGoodsLoaded: 'appMenuGoodsLoaded', // 目录的商品加载完成

        GoodsLoading: 'appGoodsLoading', // 商品加载中
        GoodsLoadSuccess: 'appGoodsLoadSuccess', // 商品加载成功
        GoodsLoadError: 'appGoodsLoadError', // 商品加载失败
        GoodsLoaded: 'appGoodsLoaded', // 商品加载完成

        CartChanged: 'appCartChanged',   // 购物车改变
        CartAdd: 'appCartAdd', // 购物车添加, 参数:goods
        CartSub: 'appCartSub', // 购物车减少, 参数:goods, next(必须调用next()才能执行购物车减少操作)
        CartEmpty: 'appCartEmpty', // 购物车为空
        CartRemove: 'appCartRemove', // 删除购物车商品, 参数:goods(vm)

        OrderSubmitting: 'appOrderSubmitting',   // 订单提交中
        OrderSubmitError: 'appOrderSubmitError',   // 订单提交失败
        OrderSubmitSuccess: 'appOrderSubmitSuccess',   // 订单提交成功
        OrderComputing: 'appOrderComputing',   // 订单计算中
        OrderComputeSuccess: 'appOrderComputeSuccess', // 订单计算成功
        OrderComputeError: 'appOrderComputeError', // 订单计算失败

        CartSubmitError: 'appCartSubmitError',   // 购物车提交失败
        CartSubmitSuccess: 'appCartSubmitSuccess',   // 订单提交成功

        MemberNotVip: 'appMemberNotVip', // 发现用户不是VIP用户
        MemberInvalid: 'appMemberInvalid', // 用户信息无效

        AddCollectToCartCache: 'appAddCollectToCartCache', // 添加用户收藏到购物车缓存
        AddCollectToCart: 'appAddCollectToCart', // 添加收藏的商品到购物车
        AddMemberCollect: 'appAddMemberCollect', // 添加用户收藏
        AddMemberCollectSuccess: 'appAddMemberCollectSuccess', // 添加用户收藏成功
        AddMemberCollectError: 'appAddMemberCollectError', // 添加用户收藏失败, 参数:msg
        DelMemberCollect: 'appDelMemberCollect', // 删除用户收藏

        AddMemberLike: 'appAddMemberLike', // 添加用户喜欢
        AddMemberLikeSuccess: 'appAddMemberLikeSuccess', // 添加用户喜欢成功
        AddMemberLikeError: 'appAddMemberLikeError', // 添加用户喜欢失败, 参数:msg
    });

})(jQuery);
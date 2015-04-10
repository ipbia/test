<?php if (!defined('THINK_PATH')) exit();?><!doctype html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"><meta content="telephone=no" name="format-detection"><title><?php echo ($company["shortname"]); ?></title><!-- 基础样式 --><link rel="stylesheet" type="text/css" href="<?php echo RES;?>/restaurant/default/wwbui.css"><!-- 页面样式 --><link rel="stylesheet" type="text/css" href="<?php echo RES;?>/restaurant/default/fontello.css?1427772810" /><link rel="stylesheet" type="text/css" href="<?php echo RES;?>/restaurant/default/ET.css?1427772810" /><link rel="stylesheet" type="text/css" href="<?php echo RES;?>/restaurant/default/style.css?1427772810" /></head><body><div class="container" id="j-container" ms-controller="App"><div ms-controller="Goods"><!-- S 侧边内容 --><div class="g-side" id="j-side" ms-controller="GoodsMenu"><div class="sideMenu j-sideMenu anim"><ul><li ms-repeat="menus"
                ms-class-1="item-shen item:el.type == 0"
                ms-class-2="diver:el.type == 1"
                ms-class-3="hasSub:el.children"><a href="javascript:;" ms-if="el.type == 0" ms-on-click="tap" ms-data-id="el.id"><div class="pic" ms-css-background-image="url('{%el.icon%}')"></div><p class="title">{%el.name%}</p></a></li><li class="diver"></li><li class="item-yh" style="display: none;"><a href="#"><div class="pic"></div><p class="title">优惠活动</p></a></li><li class="item-hy"><a href="<?php echo U('Wap/Restaurant/orderCheck',array('token'=>$token));?>"><div class="pic"></div><p class="title">会员中心</p></a></li></ul><div style="height: 44px;"></div></div><div id="wapper1" class="wapper "><div class="wapper-inner j-content1"><div class="subSideMenu j-subSideMenu f-n"><!-- S 子菜单 --><ul class="f-dn"
                    ms-repeat="menus"
                    ms-if-loop="el.children"><li ms-data-pid="el.id"
                        ms-data-id="cl.id"
                        ms-class="item:cl.type == 0"
                        ms-class-1="active:cl.id == activeMenu"
                        ms-class-2="diver:cl.type == 1"
                        ms-if="el.children"
                        ms-repeat-cl="el.children"
                        ms-on-click="tapChild"><div class="pic" ms-if="cl.type == 0"><img ms-src="{%cl.icon%}"/></div><p class="title" ms-if="cl.type == 0">{%cl.name%}</p></li></ul><ul class="f-n" style="display:block;"><li class="item-home j-backMainMenu"><a href="javascript:;"><div class="pic"></div><p class="title">主菜单</p></a></li></ul><div style="height: 44px;"></div><!-- E 子菜单 --></div><!--<a id="btn1" href="javascript:;">加载内容</a>--></div></div></div><!-- E 侧边内容 --><div class="g-main" id="j-main"><div class="rongqi"><!-- S 购物车按钮 --><a  ms-controller="CartNumber" ms-if="num" class="btn-cart" href="<?php echo U('Wap/Restaurant/orderCheck',array('token'=>$token));?>"><span>{%num%}</span></a><!-- E 购物车按钮 --><div id="wapper2" class="j-goods-list"><div class="j-content2"><!-- S 分类说明文字 --><div class="m-descri" ms-controller="GoodsMenu"><h2><span>{%activeMenuInfo.name%}</span></h2><h3>BEAST GARDEN</h3><p ms-if="activeMenuInfo.intro">{%activeMenuInfo.intro%}</p><div class="small">Coffee | Light Food | Cocktail & Beverage</div></div><!-- E 分类说明文字 --><!-- S 菜单列表 --><ul class="m-menuList-ul" ms-controller="GoodsList"><li ms-repeat="list"><div class="m-menuList"><a ms-href="#view-goods-{%el.id%}"
                                   class="j-view-goods m-menuList-link" ms-data-id="el.id"><div class="img" ms-data-id="el.id"><img ms-src="$safe(el.cover)"/></div><div class="text"><div class="title"><h2>{%el.title%}<!-- <span class="tag">新品</span>--></h2><p>{%el.sub_title%}</p></div><div class="price">￥{%$defaultSpec(el.id).price%}</div></div><div class="dengdai" ms-if="el.need_wait == 1 && el.disabled == 0"><div class="text2"><p>爱我的人多，需要等我哟</p></div></div><div class="dengdai dengdai2" ms-if="el.disabled == 1"><div class="text2"><p>被吃光了</p></div></div></a><div class="action"><div class="choose" ms-if="el.disabled == 0"><a
                                            ms-click="add(el.id, $defaultSpec(el.id).id, $defaultSpec(el.id).price, el, $defaultSpec(el.id))"><i
                                            class="u-icon u-icon-choose"></i><p>加入购物车</p></a></div></div></div></li></ul><!-- E 菜单列表 --><img src="<?php echo RES;?>/restaurant/default/img-mainFooter.png?1427772810"  /><style>
                        .copyright{
                            padding: 10px 0; text-align: center;
                            font: 12px/1 "Helvetica Neue", Helvetica, STHeiTi, sans-serif;
                            color: #999;
                            text-shadow: 1px 1px 0 #fff;
                        }
                        .copyright a{ color: #999; text-decoration: none;}
                    </style><div class="copyright"><a href="http://www.31by.com">高级餐饮</a>&copy;<a href="http://www.31by.com">商必营</a></div></div></div><!-- S 菜品详细 --><div id="j-goods" class="f-dn f1" ms-controller="GoodsView"><div ms-if="goods.id" class="m-menuDetail"
         ms-css-background-image="url('{%$safe(goods.img?goods.img[0]:goods.cover)%}')"><!-- S 描述信息 --><div class="desc"><!--<h2><span>菜品介绍</span></h2>--><div class="text"><div class="title"><h3>{%goods.title%}</h3><div class="subTit">{%goods.sub_title%}</div></div><div class="price"><span>￥</span>{%$defaultSpec().price%}
                </div><p>{%goods.intro%}</p></div><div class="plAction"><ul class="f-cb"><li class="pl" id="j-pl" ms-data-goods-id="goods.goods_id"><a ms-href="#view-comment-{%goods.goods_id%}"><span
                            class="j-plNum">{%goods.comment_number%}</span>条评论</a></li><li class="xh" ms-class-1="active:goods.is_liked"><a href="javascript:;"
                                                                         ms-on-click="$like()"><span
                            class="j-xhNum">{%goods.liked%}</span>人喜欢</a></li><li class="sc" ms-class-1="active:goods.is_collected"><a href="javascript:;"
                                                                             ms-on-click="$collect()">收藏我</a></li></ul></div></div><!-- E 描述信息 --><div class="btn-addCart" ms-if="goods.disabled == 0"><a href="javascript:;" ms-click="add()"><i class="u-icon u-icon-choose"></i><p>加入购物车</p></a></div></div></div><!-- E 菜品详细 --><!-- S 评论 --><div id="wapper3" class="j-pl-list f-dn"><div class="wapper-inner" ms-controller="GoodsView"><div class="m-pinglun"><div class="m-pinglun-box"><div class="m-menuList"><div class="img"><img ms-src="$safe(goods.cover, '')" alt=""/></div><div class="text"><div class="title"><h2>{%goods.title%}</h2><p>{%goods.sub_title%}</p></div><div class="price">￥{%$defaultSpec().price%}</div></div></div><div class="plAction"><ul class="f-cb"><!--<li class="pl return"><a href="javascript:;" class="j-return-goods-view">返回</a></li>--><li class="xh" ms-class-1="active:goods.is_liked"><a href="javascript:;"
                                                                             ms-on-click="$like()"><span
                                class="j-xhNum">{%goods.liked%}</span>人喜欢</a></li><li class="sc" ms-class-1="active:goods.is_collected"><a href="javascript:;"
                                                                                 ms-on-click="$collect()">收藏我</a></li></ul></div><div class="pinglun-list"><div class="pinglun-list-inner" id="commentArea"></div></div></div></div></div></div><!-- E 评论 --></div></div><!-- E 主体内容 --></div></div><script src="http://libs.baidu.com/jquery/1.10.0/jquery.min.js"></script><script type="text/javascript">
    !window.jQuery && document.write('<script src="<?php echo RES;?>/restaurant/asset/jquery-1.10.0.min.js"><\/script>');
</script><!-- <script src="<?php echo RES;?>/restaurant/default/wechat.api.js?20150120"></script> --><!-- <script src="<?php echo RES;?>/restaurant/default/v.share2.js"></script> --><script>
    App = window.App || {};

    App = {
        mpid: '<?php echo ($wxuser["uid"]); ?>',//用户id
        pfid: '<?php echo ($wxuser["id"]); ?>',//平台id
        spid: '<?php echo ($wxuser["id"]); ?>',//店铺id
        member: {
            valid:!!'<?php echo ($fans["id"]); ?>',
            mid:'<?php echo ($fans["id"]); ?>',
            nickname:'<?php echo ($fans["wechaname"]); ?>',
            avatar:'<?php echo ($fans["portrait"]); ?>'
        },
        url: {
            Index: '<?php echo U('Wap/Store/index',array('token'=>$token));?>',

            GoodsIndex: '<?php echo U('Wap/Restaurant/index',array('token'=>$token));?>',
            Menu: '<?php echo U('Wap/Restaurant/menus',array('token'=>$token));?>',//'<?php echo RES;?>/restaurant/goods/menu.json',
            MenuGoods: '<?php echo U('Wap/Restaurant/menu_goods',array('token'=>$token));?>',
            GoodsView: '',
            GoodsInfo: '<?php echo U('Wap/Restaurant/goodsInfo',array('token'=>$token));?>',
            GoodsType: '',

            OrderPreview: '/site/order/preview',
            OrderCheck: '<?php echo U('Wap/Restaurant/orderCheck',array('token'=>$token));?>',
            OrderCompute: '<?php echo U('Wap/Restaurant/orderCompute',array('token'=>$token));?>',
            OrderSubmit: '<?php echo U('Wap/Restaurant/orderSubmit',array('token'=>$token));?>',
            OrderHistoryList: '<?php echo U('Wap/Restaurant/orderHistoryList',array('token'=>$token));?>',
            OrderView: '<?php echo U('Wap/Restaurant/orderView',array('token'=>$token));?>',
            OrderSuccess: '<?php echo U('Wap/Restaurant/orderSuccess',array('token'=>$token));?>',
            OrderSuccessHelpPay: '<?php echo U('Wap/Restaurant/orderSuccessHelpPay',array('token'=>$token));?>',
            OrderHelpPayNotice: '<?php echo U('Wap/Restaurant/orderHelpPayNotice',array('token'=>$token));?>',

            MemberCoupons: '<?php echo U('Wap/Restaurant/memberCoupons',array('token'=>$token));?>',
            MemberCollectList : '<?php echo U('Wap/Restaurant/memberCollectList',array('token'=>$token));?>',
            MemberAddCollect : '<?php echo U('Wap/Restaurant/addCollect',array('token'=>$token));?>',
            MemberDelCollect : '<?php echo U('Wap/Restaurant/delCollect',array('token'=>$token));?>',
            MemberAddLike : '<?php echo U('Wap/Restaurant/addlike',array('token'=>$token));?>',
            MemberCall: '<?php echo U('Wap/Restaurant/memberCall',array('token'=>$token));?>',

            CartSubmit: '<?php echo U('Wap/Restaurant/cartSubmit',array('token'=>$token));?>',
            CartCombine: '<?php echo U('Wap/Restaurant/cartCombine',array('token'=>$token));?>',
            CartCombined: '<?php echo U('Wap/Restaurant/cartCombined',array('token'=>$token));?>'
        },
        config: {
            sectionContainer: '#section-container',
            sectionSelector: ' > div.section',
            articleSelector: '.article'
        }
    };

    App.debug = false;
    App.platform = {"id":"<?php echo ($wxuser["id"]); ?>","mpid":"<?php echo ($wxuser["uid"]); ?>","name":"<?php echo ($company["shortname"]); ?>","setting":{"wechat_is_affirm":"1","pay_occasion":"0","has_worker":"1","write_money":"1","select_pay_type":"1","has_cook":"1","cook_list":"0"},"logo":"<?php echo ($company["logourl"]); ?>","intro":"<?php echo ($company["intro"]); ?>","status":"1","created_at":"1406469841"};
    App.shop = {"id":"<?php echo ($wxuser["id"]); ?>","platform_id":"1","name":"<?php echo ($company["shortname"]); ?>","address":"<?php echo ($company["address"]); ?>","telephone":"<?php echo ($company["tel"]); ?>","cover":"<?php echo ($company["logourl"]); ?>","intro":"<?php echo ($company["intro"]); ?>","map":"<?php echo ($company["longitude"]); ?>,<?php echo ($company["latitude"]); ?>","promotion_on":"1","warning_time":"1800","status":"1","created_at":"1413535119","updated_at":"1425628121","deleted_at":null,"pay_notice_tpl":"XdFsPSyaHqY-Knj-dmXNfHbQlpS1Z1z6x2TvOQB8Pac"};

    //微信官方api组件
    App.wechat = new wechatAPI();
    App.wechat.mpid = <?php echo ($wxuser["uid"]); ?>;
    App.wechat.shareData = {
        shareImageUrl: "<?php echo ($company["logourl"]); ?>",
        shareLink: '<?php echo U('Wap/Store/index',array('token'=>$token));?>',
        shareTitle: "<?php echo ($company["shortname"]); ?>",
        shareContent: "<?php echo ($company["intro"]); ?>"
    };
    App.wechat.shareInit();
</script><script type="text/javascript" src="<?php echo RES;?>/restaurant/default/avalon.modern.js"></script><script type="text/javascript" src="<?php echo RES;?>/restaurant/default/app.js"></script><script type="text/javascript" src="<?php echo RES;?>/restaurant/default/event.js"></script><script type="text/javascript" src="<?php echo RES;?>/restaurant/default/api.js"></script><script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "http://hm.baidu.com/hm.js?7fb5cc546877a40647ea552760f6447f";
        var s = document.getElementsByTagName("head")[0];
        s.appendChild(hm);
    })();
</script><script type="text/javascript" src="<?php echo RES;?>/restaurant/default/app(1).js?1427772810"></script><script type="text/javascript" src="<?php echo RES;?>/restaurant/default/jquery.sim.scroll.js?1427772810"></script><script type="text/javascript" src="<?php echo RES;?>/restaurant/default/ET.js?1427772810"></script><script type="text/javascript" src="<?php echo RES;?>/restaurant/default/app(2).js?1427772810"></script><script type="text/javascript" src="<?php echo RES;?>/restaurant/default/cart.js?1427772810"></script><script type="text/javascript" src="<?php echo RES;?>/restaurant/default/goods.js?1427772810"></script><script src="<?php echo RES;?>/restaurant/default/ibox.js"></script><script src="<?php echo RES;?>/restaurant/default/comment.js"></script><script>
    (function () {
        // 解决返回主菜单后,再点击已经高亮的菜单
        App.on(App.event.MenuClicked, function (node) {
            var $parent = $(node).closest('li');
            if ($parent.length && $parent.hasClass('hasSub') && $parent.hasClass('active')) {
                App.ui.tabMenuSub();
            }
        });

        // 菜单商品加载时间过长,现实加载提示
        var timeid = 0;
        App.on(App.event.MenuGoodsLoading, function () {
            timeid = setTimeout(function () {
                ETMsg.loading();
            }, 1000);
        });
        App.on(App.event.MenuGoodsLoaded, function () {
            if (timeid) {
                clearTimeout(timeid);
            }
            ETMsg.hide();
        });

        // 单个商品加载时间过长,现实加载提示
        var timeid2 = 0;
        App.on(App.event.GoodsLoading, function () {
            timeid2 = setTimeout(function () {
                ETMsg.loading();
            }, 1000);
        });
        App.on(App.event.GoodsLoaded, function () {
            if (timeid2) {
                clearTimeout(timeid2);
            }
            ETMsg.hide();
        });
    })();

    $(function () {
        avalon.vm('GoodsMenu').$init();

        var $goods = $('#j-goods');
        var $goodsList = $('.j-goods-list');
        var $plList = $('.j-pl-list');

        // 页面载入时,触发一次hash改变事件
        App.util.route('', true, true);

        App.on(App.event.MenuClicked, function (node) {
            App.route('', true, true);
        });

        // 绑定链接
        $(document).on("click", ".j-view-goods[href^='#']", function (e) {
            App.route($(this).attr("href"));
        });
        $(document).on("click", ".pl a[href^='#']", function (e) {
            App.route($(this).attr("href"));
        });
        $(document).on("click", "[href^='#']", function (e) {
            e.preventDefault();
        });

        
      //如果有商品id,则直接跳转到商品详情
        if(<?php echo ($goods_id); ?>){
        	$goods.show();
            $plList.hide();
            $goodsList.hide();
            avalon.vm('GoodsView').$init(<?php echo ($goods_id); ?>);
        }
      
        var commentInit = false;
        App.on(App.event.Popstate, function (path) {
            var id;
            switch (true) {
                // 查看商品
                case path.indexOf('#view-goods') !== -1:
                    id = path.split('-');
                    if (id.length === 3) {
                        id = id[2];
                        $goods.show();
                        $plList.hide();
                        $goodsList.hide();
                        avalon.vm('GoodsView').$init(id);
                    }
                    break;

                // 查看评论
                case path.indexOf('#view-comment') !== -1:
                    $goodsList.hide();
                    $goods.hide();
                    $plList.show();

                    id = path.split('-');
                    if (id.length === 3) {
                        id = id[2];

                        $('#comment_list').empty();

                        //cmt.config.styleUrl = 'http://static.31by.com/asset/lib/css/cmt.css';
                        if (!commentInit) {
                            window.comment = new window.comment();
                            window.comment.config.mpid = '<?php echo ($wxuser["uid"]); ?>';
                            window.comment.config.item = 'restaurant';
                            window.comment.config.item_id = id;
                            window.comment.config.token = '<?php echo ($token); ?>';
                            window.comment.config.getCommentUrl = "<?php echo U('Wap/Restaurant/getcomment',array('token'=>$token));?>";
                            window.comment.config.postCommentUrl = "<?php echo U('Wap/Restaurant/postcomment',array('token'=>$token));?>";
                            window.comment.config.member_id = '<?php echo ($fans["id"]); ?>';
                            window.comment.config.member_nickname = '<?php echo ($fans["wechaname"]); ?>';
                            window.comment.config.member_avator = '<?php echo ($fans["portrait"]); ?>';
                            window.comment.config.page = '1';

                            window.comment.display();
                            commentInit = true;
                        } else {
                            window.comment.config.pull = true;
                            window.comment.config.display_type = '2';
                            window.comment.config.item_id = id;
                            window.comment.config.page = '1';
                            window.comment.get();
                        }
                    }
                    break;

                default:
                    $goods.hide();
                    $plList.hide();
                    $goodsList.show();
            }
        });

        var timer = true;
        App.on(App.event.CartAdd, function () {

            if (timer) {
                timer = false;
                $('.btn-cart span').addClass('a-bounce');
                setTimeout(function () {
                    $('.btn-cart span').removeClass('a-bounce').hide(0).show(0, function () {
                        timer = true;
                    });

                }, 500);
            }


        });
    });


</script></body><script type="text/javascript">
window.shareData = {  
            "moduleName":"Restaurant",
            "moduleID":"<?php echo (intval($_GET['id'])); ?>",
            "imgUrl": "<?php echo ($company["logourl"]); ?>", 
            "timeLineLink": "<?php echo C('site_url'); echo U('Restaurant/index',array('token'=>$_GET['token']));?>",
            "sendFriendLink": "<?php echo C('site_url'); echo U('Restaurant/index',array('token'=>$_GET['token']));?>",
            "tTitle": "<?php echo ($company["shortname"]); ?>",
            "tContent": "<?php echo ($company["intro"]); ?>"
        };
</script><?php echo ($shareScript); ?></html>
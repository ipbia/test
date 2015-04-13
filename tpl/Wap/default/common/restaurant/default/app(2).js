;App = window.App || {};

// 全局变量
App.global = {};

(function($) {

    App.ui = App.ui || {};

    // UI
    var isMask = false;
    var oCnt=$('#j-container');
    var oSide=$('#j-side');
    var oMain=$('#j-main');
    var oSideMenu=$('.j-sideMenu');
    var oSubSideMenu=$('.j-subSideMenu');
    var oContent1=$('.j-content1');
    var oRongqi=$('.rongqi');
    var iH=$(window).height();
    var subMenuShowed = false;


    /**
     * 初始化菜单
     * @returns {{}}
     */
    App.ui.fnPageInit = function() {
        oCnt.height(iH);
        oSide.height(iH);
        oMain.height(iH);
        oRongqi.height(iH);
        oSideMenu.css('min-height', iH);


        /*function loaded(){
            setTimeout(function () {
                // App.global.side = new iScroll('wapper1', {vScrollbar: false});
                App.global.main = new IScroll('#wapper2', {
                    vScrollbar: false,
                    useTransform: false,
                    onBeforeScrollStart: function (e) {
                        var target = e.target;
                        while (target.nodeType != 1) target = target.parentNode;

                        if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
                            e.preventDefault();
                    }
                });

                App.global.comment = new IScroll('#wapper3', {
                    vScrollbar: false,
                    useTransform: false,
                    onBeforeScrollStart: function (e) {
                        var target = e.target;
                        while (target.nodeType != 1) target = target.parentNode;

                        if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
                            e.preventDefault();
                    }
                });
            }, 300);
        }

        window.addEventListener("load",loaded,false);*/
    };

//    App.ui.scrollable = function(id) {
//        return new IScroll(id, {
//            vScrollbar: false,
//            useTransform: false,
//            onBeforeScrollStart: function (e) {
//                var target = e.target;
//                while (target.nodeType != 1) target = target.parentNode;
//
//                if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
//                    e.preventDefault();
//            }
//        });
//    };

    /**
     * 显示子菜单
     */
    App.ui.tabMenuSub = function (ev) {
        if (!subMenuShowed) {
            subMenuShowed = true;
            oSideMenu.removeClass('anim-slideLeftIn-mainMenu');
            oSideMenu.addClass('anim-slideLeftOut-mainMenu');
            oSubSideMenu.fadeIn();

            setTimeout(function () {
                oSideMenu.removeClass('anim-slideLeftOut-mainMenu');
                oSideMenu.css('-webkit-transform', 'translatex(-48px)');
            }, 500);

            var mainH = oSideMenu.height();
            var subH = oSubSideMenu.height();
            oContent1.height(Math.max(mainH, subH));
        }
//        App.global.side.refresh();

//        var curTarget=ev.currentTarget;
//        oSideMenu.find('.item').removeClass('active');
//        $(curTarget).addClass('active');
    };

    /**
     * 显示主菜单
     */
    App.ui.tabMenuMain = function (ev) {
        if (subMenuShowed) {
            subMenuShowed = false;
            oSideMenu.removeClass('anim-slideLeftOut-mainMenu');
            oSideMenu.addClass('anim-slideLeftIn-mainMenu');
            oSubSideMenu.fadeOut();

            setTimeout(function () {
                oSideMenu.removeClass('anim-slideLeftIn-mainMenu');
                oSideMenu.css('-webkit-transform', 'translatex(0)');
            }, 500);
        }
    };


    // 绑定目录改变事件，重新初始化菜单结构
    App.util.on(App.event.MenuChanged, function(e, type) {
        type = type || 'main';
        if (type == 'main') {
            App.ui.fnPageInit();
        } else {
            // App.ui.fnPageInitSub();
        }
    });

})(jQuery);

$(function () {
    App.ui.fnPageInit();

    // 切换菜单到子菜单
//    $(document).on('click', '.j-sideMenu .item', function (ev) {
//        if( $(this).hasClass('hasSub') ) {
//            App.ui.tabMenuSub(ev);
//        }
//
//        $('.j-sideMenu .item').removeClass('active');
//        $(this).addClass('active');
//    });
    App.on(App.event.MenuChanged, function(id) {
        var $child = $('.j-subSideMenu li[data-id='+id+']');
        $('.j-sideMenu .item').removeClass('active');
        if ($child.length) {
            $('.j-subSideMenu > ul').removeClass('f-n').addClass('f-dn');
            $child.parent('ul').removeClass('f-dn').addClass('f-n');
            var pid = $child.data('pid');
            $('.j-sideMenu a[data-id='+pid+']').parent('li').addClass('active');
            App.ui.tabMenuSub();
        } else {
            // 点击了没有子菜单的顶级菜单
            $('.j-sideMenu a[data-id='+id+']').parent('li').addClass('active');
        }
    });

    // 切换菜单到主菜单
    $(document).on('click', '.j-backMainMenu', function (ev) {
        App.ui.tabMenuMain(ev);
    });

    // 子菜单点击
    $(document).on('click', '.j-subSideMenu li.item', function () {
        $('.j-subSideMenu li.item').removeClass('active');
        $(this).addClass('active');

        // do something 子菜单加载内容
    });


//    // 喜欢，收藏
//    (function () {
//
//        $('.plAction .xh').click(function () {
//            var num = $(this).find('.j-xhNum');
//
//            if ( $(this).hasClass('active') ) {
//                $(this).removeClass('active');
//
//                var str = parseInt(num.text());
//                str--
//                num.text(str);
//
//                // ajax
//
//            } else {
//                $(this).addClass('active');
//
//                var str = parseInt(num.text());
//                str++
//                num.text(str);
//
//                // ajax
//            }
//        });
//
//        $('.plAction .sc').click(function () {
//            if ( $(this).hasClass('active') ) {
//                $(this).removeClass('active');
//
//                // ajax
//            } else {
//                $(this).addClass('active');
//            }
//        });
//
//    })();

    // 选择优惠券
    (function () {
        var iH=$(window).height();

        $('.youhuiquan-inner').height(iH);

        $('.j-chooseYhq').click(function () {
            $('.youhuiquan').addClass('anim-slideRightIn');
        });

        $('.j-backToOrder').click(function () {
            hideYhq();
        });

        $('.j-shiyongyhq').click(function () {
            hideYhq();
        });

        $('.youhuiquan-list li').click(function () {
            var _child=$(this).find('.xz');

            if ( $(this).hasClass('active') ) {
                $(this).removeClass('active');
                _child.text('选择');
            } else {
                $(this).addClass('active');
                _child.text('取消');
            }

        });

        function hideYhq() {
            $('.youhuiquan').removeClass('anim-slideRightIn').addClass('anim-slideRightOut');
        }

    })();

    // 选择优惠券,备注添加
    (function () {
        var iH=$(window).height();

        $('.youhuiquan-inner').height(iH-166);
        $('.beizhuBox-inner').height(iH);


        $('.j-chooseYhq').click(function () {
//            $('.youhuiquan').addClass('anim-slideRightIn');
            $('.g-main').scrollTop(0);
            $('.youhuiquan').show(1, function () {
                $(this).css('marginLeft', 0);
            });
            setTimeout(function () {
                $('.rongqi').hide();
            }, 1000);
        });

        $('.j-backToOrder').click(function () {
            hideBox1();
        });

        $('.j-shiyongyhq').click(function () {
            hideBox1();
        });

        $('.youhuiquan-list li').click(function () {
            var _child=$(this).find('.xz');

            if ( $(this).hasClass('active') ) {
                $(this).removeClass('active');
                _child.text('选择');
            } else {
                $(this).addClass('active');
                _child.text('取消');
            }

        });

        function hideBox1() {
//            $('.youhuiquan').removeClass('anim-slideRightIn').addClass('anim-slideRightOut');
            $('.youhuiquan').css('marginLeft', '100%');
            $('.rongqi').show();
            setTimeout(function () {
                $('.youhuiquan').hide();
            }, 1000);
        }

        function hideBox2() {
//            $('.beizhuBox').removeClass('anim-slideRightIn').addClass('anim-slideRightOut');
            $('.beizhuBox').css('marginLeft', '100%');
            $('.rongqi').show();
            setTimeout(function () {
                $('.beizhuBox').hide();
            }, 1000);
        }
        
        function hideBox3() {
//          $('.userBox').removeClass('anim-slideRightIn').addClass('anim-slideRightOut');
          $('.userBox').css('marginLeft', '100%');
          $('.rongqi').show();
          setTimeout(function () {
              $('.userBox').hide();
          }, 1000);
      }


        // 提交备注
        var strbz = '';
        var target = null;

        $('.j-bzClose').click(function () {
            hideBox2();
        });

        $(document).on('click', '.j-beizhu', function () {
            target = $(this);
            var vmel = avalon.vm('Cart').get(target.data('id'), target.data('sid'));
            strbz = '';
            if (vmel) {
                strbz = vmel.remark;
            }

            $('.j-bzText').val(strbz);
//            $('.beizhuBox').addClass('anim-slideRightIn');
//            $('.beizhuBox').show().css('marginLeft', 0);
            $('.g-main').scrollTop(0);
            $('.beizhuBox').show(1, function () {
                $(this).css('marginLeft', 0);
            });
            setTimeout(function () {
                $('.rongqi').hide();
            }, 1000);
        });

        $('.j-bzSub').click(function () {

            hideBox2();

            var str = $('.j-bzText').val();
            avalon.vm('Cart').remark(target.data('id'), target.data('sid'), str);

            if (str != '') {
                target.attr('data-bz', str);
                target.addClass('active');
            } else {
                target.attr('data-bz', str);
                target.removeClass('active');
            }

            target = null;

        });
        
     // 提交收货人信息
        var target = null;

        $('.j-userClose').click(function () {
        	validateUser();
        });

        $(document).on('click', '.j-user', function () {
            target = $(this);
            showUserBox();
        });
        
        $(document).on('click', '#j-btn-submit', function () {
        	if(!avalon.vm('Order').haveUserInfo()){
        		showUserBox();
        	}
        });
        
        function showUserBox(){
        	$('.g-main').scrollTop(0);
            $('.userBox').show(1, function () {
                $(this).css('marginLeft', 0);
            });
            setTimeout(function () {
                $('.rongqi').hide();
            }, 1000);
        }
        

        $('.j-userSub').click(function () {
        	validateUser();
        });
        
        function validateUser(){
        	var username = $('.j-usernameText').val();
            var usertel = $('.j-usertelText').val();
            var useraddress = $('.j-useraddressText').val();
            
            if(username == ""){
            	ETMsg.toast('收货人姓名不能为空!');
            	return;
            }
            
            var isMobile=/^(?:13\d|15\d|18\d)\d{5}(\d{3}|\*{3})$/;
            if(!isMobile.test(usertel)){
            	ETMsg.toast('收货人手机格式错误!');
            	return;
            }
            if(useraddress == ""){
            	ETMsg.toast('收货人地址不能为空!');
            	return;
            }
            
            avalon.vm('Order').saveUserInfo(username, usertel, useraddress);
            target = null;
            
            hideBox3();
        }

    })();


    // 添加购物车成功提示
    (function() {
        App.on(App.event.CartAdd, function () {
            ETMsg.toast('添加成功');
        });
    })();


});


/*
 * share(分享) require:jquery
 *
 */
(function () {
	
	window.ETShare = (function (type) {
		
		//.j-share-timeline分享给朋友圈
		//.j-share-friends分享给朋友
		//.j-share-subscribe关注我们
		var oImgShare = $('<img id="imgShare" class="imgShare" style="position: fixed; top: 0; right: 0; z-index: 99999; display: none; max-width: 100%;" />');
		var oMask = $('<div id="maskShare" style="display:none; position: fixed;z-index: 2000;left: 0;top: 0;width: 100%;height: 100%;background: black; opacity:0;"></div>');
		var imgUrl = [
			'http://static.weiwubao.com/asset/lib/images/share/share-timeline.png', 
			'http://static.weiwubao.com/asset/lib/images/share/share-friends.png', 
			'http://static.weiwubao.com/asset/lib/images/share/share-subscribe.png'
		];
		
		$(document).on('click', '.j-share-timeline', function () {
        	show(0);
		});
	
		$(document).on('click', '.j-share-friends', function () {
			show(1);
		});
	
		$(document).on('click', '.j-share-subscribe', function () {
			show(2);
		});
	
		$(document).on('touchstart', '.imgShare, #maskShare', function () {
			hide();
		});
		
		var show = function (type) {									
			oImgShare.attr('src', imgUrl[type]);
			oImgShare.show();
			oMask.show().stop().animate({'opacity':0.8});
			oImgShare.stop().animate({'opacity': 1});
		};
		
		var hide = function () {			

			oImgShare.stop().animate({'opacity': 0}, 500, function () {
				$(this).css('display', 'none');
			});
			oMask.stop().animate({'opacity': 0}, 500, function () {
				$(this).css('display', 'none');		
			});
			
		};
		
		var init = function () {
			$('body').append(oImgShare);
			$('body').append(oMask);			
		};
		init();
		
		return {
			show: show,
			hide: hide			
		}
		
	})();
	
    
})();
document.write('<script src="./tpl/Wap/default/common/restaurant/default/jweixin-1.0.0.js"></script>');
function wechatAPI() {
	this.shareData = {
		shareImageUrl: "",
		shareLink: "",
		shareTitle: "",
		shareContent: "",
		shareContentTimeline: ""
	};
	/**
	 * 分享结果状态 cancel confirm/ok
	 */
	this.shareStatus = '';
	/**
	 * 分享类型 0:未知 1:给朋友 2:到朋友圈
	 */
	this.shareType = '0';
	/**
	 * 平台id
	 */
	this.mpid = '0';
	/**
	 * 业务模块 event repast wedding page archive trafficSearch
	 */
	this.item = 'page';
	/**
	 * 业务模块数据id
	 */
	this.itemId = '0';
	this.network = 'unknow';
	this.isSign = false;

	this.configInit = function () {
		var _this = this;
		if (!this.mpid || this.mpid == 0) {
			return false;
		}
		if (this.isSign) {
			return;
		}
		//初始化分享配置
		$.ajax({
			url: './tpl/Wap/default/common/restaurant/ajax/jsapi_init.json',
			dataType: 'json',
			method: 'get',
			data: {mpid: this.mpid},
			success: function (json) {
				if (json.status == 1) {
					wx.config({
						debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
						appId: json.data.appid, // 必填，公众号的唯一标识
						timestamp: json.data.timestamp, // 必填，生成签名的时间戳
						nonceStr: json.data.nonceStr, // 必填，生成签名的随机串
						signature: json.data.signature, // 必填，签名，见附录1
						jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'startRecord', 'stopRecord', 'onVoiceRecordEnd', 'playVoice', 'pauseVoice', 'stopVoice', 'onVoicePlayEnd', 'uploadVoice', 'downloadVoice', 'chooseImage', 'previewImage', 'uploadImage', 'downloadImage', 'translateVoice', 'getNetworkType', 'openLocation', 'getLocation', 'hideOptionMenu', 'showOptionMenu', 'hideMenuItems', 'showMenuItems', 'hideAllNonBaseMenuItem', 'showAllNonBaseMenuItem', 'closeWindow', 'scanQRCode', 'openProductSpecificView', 'addCard', 'chooseCard', 'openCard'] // 需要使用的JS接口列表 'chooseWXPay',
					});
					_this.isSign = true;
					//配置信息错误
					wx.error(function (res) {
						_this.isSign = false;
						//alert('jsapi初始化失败:' + res.errMsg);
					});
				} else {
					//alert(json.message);
				}
			},
			error: function () {
				//alert('jsapi初始化通讯失败');
			},
			complete: function () {
			}
		});
	};
	/**
	 * 开始录音
	 */
	this.startRecord = function (callback) {
		this.configInit();
		wx.ready(function () {
			wx.startRecord();
			wx.onVoiceRecordEnd({
				//录音时间超过一分钟没有停止的时候会执行 complete 回调
				complete: function (res) {
					var localId = res.localId;
					callback(localId);
				}
			});
		});
	};

	this.stopRecord = function (callback) {
		wx.stopRecord({
			success: function (res) {
				var localId = res.localId;
				callback(localId);
			}
		});
	};
	this.playVoice = function (localId, callback) {
		wx.playVoice({
			localId: localId
		});
		wx.onVoicePlayEnd({
			success: function (res) {
				callback(localId);
			}
		});
	};
	this.pauseVoice = function (localId) {
		wx.pauseVoice({
			localId: localId
		});
	};
	this.stopVoice = function (localId) {
		wx.stopVoice({
			localId: localId
		});
	};
	this.uploadVoice = function (localId, callback) {
		var _this = this;
		wx.uploadVoice({
			localId: localId,
			isShowProgressTips: 1, // 默认为1，显示进度提示
			success: function (res) {
				var serverId = res.serverId; // 返回音频的服务器端ID
				//callback(serverId);
				//AJAX下载
				$.ajax({
					url: 'http://www.31by.com/ajax/mp/download_media/',
					dataType: 'json',
					method: 'get',
					data: {mpid: _this.mpid, serverId: serverId},
					success: function (json) {
						callback(json);
					},
					error: function () {
						//alert('jsapi初始化通讯失败');
					},
					complete: function () {
					}
				});
			}
		});
	};
	/**
	 * 获取访客的地图坐标信息
	 */
	this.getLocation = function (callback) {
		this.configInit();
		wx.ready(function () {
			wx.getLocation({
				success: function (res) {
					var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
					var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
					var speed = res.speed; // 速度，以米/每秒计
					var accuracy = res.accuracy; // 位置精度
					callback(res);
				}
			});
		});
	};
	/**
	 * 在地图上标注指定位置
	 */
	this.openLocation = function (data) {
		this.configInit();
		if (!this.isSign) {
			alert('无法打开地图组件');
		}
		wx.openLocation({
			latitude: data.latitude, // 纬度，浮点数，范围为90 ~ -90
			longitude: data.longitude, // 经度，浮点数，范围为180 ~ -180。
			name: data.name, // 位置名
			address: data.address, // 地址详情说明
			scale: 15, // 地图缩放级别,整形值,范围从1~28。默认为最大
			infoUrl: data.infoUrl // 在查看位置界面底部显示的超链接,可点击跳转
		});
	}
	/**
	 * 分享初始化
	 * @returns {object}
	 */
	this.shareInit = function () {
		this.configInit();
		var _this = this;

		if (!_this.shareData.shareContentTimeline) {
			_this.shareData.shareContentTimeline = _this.shareData.shareTitle;
		}
		wx.ready(function () {
			//显示微信中网页右上角按钮
			_this.showTopTool();
			wx.hideMenuItems({
				menuList: ["menuItem:setFont", "menuItem:copyUrl", "menuItem:openWithSafari", "menuItem:originPage"] // 要隐藏的菜单项，所有menu项见附录3
			});
			//分享给好友
			wx.onMenuShareAppMessage({
				title: _this.shareData.shareTitle,
				desc: _this.shareData.shareContent,
				link: _this.shareData.shareLink,
				imgUrl: _this.shareData.shareImageUrl,
				type: '', //分享类型,music、video或link，不填默认为link
				dataUrl: '', //如果type是music或video，则要提供数据链接，默认为空
				success: function () {
					_this.shareStatus = 'ok';
					_this.saveShare(1, _this.shareData.shareLink);
				},
				cancel: function () {
					_this.shareStatus = 'cancel';
				},
				complete: function () {
					_this.shareType = 1;
					_this.shareCallback();
				},
				trigger: function () {
					this.title = _this.shareData.shareTitle;
					this.desc = _this.shareData.shareContent;
					this.link = _this.shareData.shareLink;
					this.imgUrl = _this.shareData.shareImageUrl;
				}
			});
			//分享到朋友圈
			wx.onMenuShareTimeline({
				title: _this.shareData.shareContentTimeline, // 分享标题
				link: _this.shareData.shareLink, // 分享链接
				imgUrl: _this.shareData.shareImageUrl, // 分享图标
				success: function () {
					_this.shareStatus = 'ok';
					_this.saveShare(2, _this.shareData.shareLink);
				},
				cancel: function () {
					_this.shareStatus = 'cancel';
				},
				complete: function () {
					_this.shareType = 2;
					_this.shareCallback();
				},
				trigger: function () {
					this.title = _this.shareData.shareContentTimeline;
					this.link = _this.shareData.shareLink;
					this.imgUrl = _this.shareData.shareImageUrl;
				}
			});
			//分享给qq好友
			wx.onMenuShareQQ({
				title: _this.shareData.shareTitle,
				desc: _this.shareData.shareContent,
				link: _this.shareData.shareLink,
				imgUrl: _this.shareData.shareImageUrl,
				success: function () {
					_this.shareStatus = 'ok';
					_this.saveShare(4, _this.shareData.shareLink);
				},
				cancel: function () {
					_this.shareStatus = 'cancel';
				},
				complete: function () {
					_this.shareType = 4;
					_this.shareCallback();
				},
				trigger: function () {
					this.title = _this.shareData.shareTitle;
					this.desc = _this.shareData.shareContent;
					this.link = _this.shareData.shareLink;
					this.imgUrl = _this.shareData.shareImageUrl;
				}

			});
			//分享到腾讯微博
			wx.onMenuShareWeibo({
				title: _this.shareData.shareTitle,
				desc: _this.shareData.shareContent,
				link: _this.shareData.shareLink,
				imgUrl: _this.shareData.shareImageUrl,
				success: function () {
					_this.shareStatus = 'ok';
					_this.saveShare(3, _this.shareData.shareLink);
				},
				cancel: function () {
					_this.shareStatus = 'cancel';
				},
				complete: function () {
					_this.shareType = 3;
					_this.shareCallback();
				},
				trigger: function () {
					this.title = _this.shareData.shareTitle;
					this.desc = _this.shareData.shareContent;
					this.link = _this.shareData.shareLink;
					this.imgUrl = _this.shareData.shareImageUrl;
				}
			});
		});
	};

	/**
	 * 保存分享记录
	 * @param {int} type 分享类型 1:给朋友 2:朋友圈
	 * @param {string} url 分享的链接地址
	 * @returns {undefined}
	 */
	this.saveShare = function (type, url) {
		$.ajax({
			url: 'http://www.weiwubao.com/ajax/share/save_record/',
			dataType: 'json',
			method: 'get',
			data: {mpid: this.mpid, share_type: type, share_url: url, item: this.item, item_id: this.itemId},
			success: function (json) {
				return json.status;
			},
			error: function () {
			},
			complete: function () {
			}
		});
	};
	/**
	 * 使用微信原生方式预览图片 全局
	 * @returns {undefined}
	 */
	this.imagePreview = function () {
		document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
			var srcList = [];
			$.each($('img'), function (i, item) {
				if (item.src) {
					srcList.push(item.src);
					$(item).click(function (e) {
						WeixinJSBridge.invoke("imagePreview", {
							"urls": srcList,
							"current": this.src
						});
					});
				}
			});
		});
	};
	this.nativePreview = function (srcList) {
		document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
			$.each($('.nativePreview'), function (i, item) {
				$(item).click(function (e) {
					WeixinJSBridge.invoke("imagePreview", {
						"urls": srcList,
						"current": this.url
					});
				});
			});
		});
	};

	/**
	 * 图片预览 传参
	 * @param {type} cls
	 * @returns {undefined}
	 */
	this.imagePreviewGroup = function (cls) {
		document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
			if (!cls) {
				cls = 'nativeImagePreview';
			}
			var srcList = [];
			var list = $('.' + cls);
			$.each($('.' + cls), function (j, obj) {
				srcList[j] = [];
				$.each($('.' + cls).eq(j).find('img'), function (i, item) {
					if (item.src) {
						srcList[j].push(item.alt);
						$(item).click(function (e) {
							WeixinJSBridge.invoke("imagePreview", {
								"urls": srcList[j],
								"current": this.alt
							});
						});
					}
				});
			});
		});
	};
	/**
	 * 隐藏底部工具条
	 * @returns {undefined}
	 */
	this.hideBottomTool = function () {
		document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
			WeixinJSBridge.call('hideToolbar');
		});
	};
	/**
	 * 隐藏右上角工具条
	 * @returns {undefined}
	 */
	this.hideTopTool = function () {
		this.configInit();
		wx.ready(function () {
			wx.hideOptionMenu();
		});
	};
	/**
	 * 显示右上角工具条
	 * @returns {undefined}
	 */
	this.showTopTool = function () {
		this.configInit();
		wx.ready(function () {
			wx.showOptionMenu();
		});
	};
	/**
	 * 扫描二维码
	 * @returns {undefined}
	 */
	this.scanQrcode = function () {
		wx.scanQRCode({
			desc: '请扫描二维码',
			needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
			scanType: ["qrCode", "barCode"], // 可以指定扫二维码还是一维码，默认二者都有
			success: function (res) {
				var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
			}
		});
	};
	/**
	 * 发送邮件
	 * @returns {undefined}
	 */
	this.sendEmail = function () {
		WeixinJSBridge.invoke("sendEmail", {
			"title": "邮件标题",
			"content": "邮件内容"
		}, function (e) {
			//alert(e.err_msg);
		});
	};
	/**
	 * 关闭当前窗口
	 * @returns {undefined}
	 */
	this.closeWindow = function () {
		WeixinJSBridge.call('closeWindow');
	};
	/**
	 * 获取网络状态
	 * @returns {undefined}
	 */
	this.getNetwork = function () {
		var _this = this;
		document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
			WeixinJSBridge.invoke('getNetworkType', {},
				function (e) {
					//WeixinJSBridge.log(e.err_msg);
					var str = e.err_msg;
					strs = str.split(":"); //字符分割
					_this.network = strs[1];
					_this.callback();
				});
		});
	};
	this.callback = function () {
	};
	this.shareCallback = function () {
	};
}

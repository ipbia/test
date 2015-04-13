function comment() {
	this.config = {
		mpid: '',
		item: '',
		item_id: '',
		member_id: '',
		member_nickname: '',
		member_avator: '',
		page: 1,
		display_type: 1, //展示形式 1存在自定义代码 2 公共
		styleUrl: 'http://static.weiwubao.com/asset/lib/css/comment.css'
	};
	var mpArr = [800000, 800194, 800181, 666666];
	/***初始化***/
	this.init = function() {
		this.config['pull'] = true;
		//加载css样式
	};
	//加载评论模块html
	this.display = function() {
		this.config.display_type = '2';
		this.config.pull = true;

		var html = '<link rel="stylesheet" type="text/css" href="' + this.config.styleUrl + '">';
		html += '<link rel="stylesheet" type="text/css" href="http://static.weiwubao.com/asset/apps/plugins/repast/default/css/vlink.reply.css">';
		html += '<script src="http://static.weiwubao.com/asset/lib/js/swipe.js"></script>';
		html += '<script src="http://static.weiwubao.com/asset/lib/vlink/js/module/vlink.reply.js"></script>';
		html += '<div class="comment">';
		html += '<div class="comment_head">评论(<span id="comment_total">0</span>)<a class="comment_post" href="javascript:;">我要说两句</a></div>';
		html += '<div class="comment_list" id="comment_list">';
		html += '</div>';
		html += '<div class="comment_loadmore"><a id="loadMoreComment" href="javascript:;">更多</a></div>';
		html += '</div>';
		html += '</div>';
		//发表评论
		html += '<div id="replyBox">';
		html += '<div class="bd">';
		html += '<textarea class="replyBox-textarea j-replyBox-textarea" placeholder="说说你的感受吧" id="comment_content"></textarea>';
		html += '<div class="replyBox-action f-cb">';
		html += '<div class="replyBox-action-bq"><a class="j-expressionToogle" href="javascript:;"></a></div>';
		html += '<div class="replyBox-action-btn">';
		html += '<a class="btn1 j-closeReplyBox" href="javascript:;">取消</a><a class="btn2" href="javascript:;" id="comment_send">发送</a>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '<div class="ft">';
		html += '<div class="replyBox-expression">';
		html += '<div id=\'mySwipe\' class=\'swipe\'>';
		html += '<div class=\'swipe-wrap\'>';
		html += '<div>';
		html += '<ul class="qqExpressionList qqExpressionList1 f-cb">';
		html += '<li title="0"></li>';
		html += '<li title="1"></li>';
		html += '<li title="2"></li>';
		html += '<li title="3"></li>';
		html += '<li title="4"></li>';
		html += '<li title="5"></li>';
		html += '<li title="6"></li>';
		html += '<li title="7"></li>';
		html += '<li title="8"></li>';
		html += '<li title="9"></li>';
		html += '<li title="10"></li>';
		html += '<li title="11"></li>';
		html += '<li title="12"></li>';
		html += '<li title="13"></li>';
		html += '<li title="14"></li>';
		html += '<li title="15"></li>';
		html += '<li title="16"></li>';
		html += '<li title="17"></li>';
		html += '</ul>';
		html += '</div>';
		html += '<div>';
		html += '<ul class="qqExpressionList qqExpressionList2 f-cb">';
		html += '<li title="18"></li>';
		html += '<li title="19"></li>';
		html += '<li title="20"></li>';
		html += '<li title="21"></li>';
		html += '<li title="22"></li>';
		html += '<li title="23"></li>';
		html += '<li title="24"></li>';
		html += '<li title="25"></li>';
		html += '<li title="26"></li>';
		html += '<li title="27"></li>';
		html += '<li title="28"></li>';
		html += '<li title="29"></li>';
		html += '<li title="30"></li>';
		html += '<li title="31"></li>';
		html += '<li title="32"></li>';
		html += '<li title="33"></li>';
		html += '<li title="34"></li>';
		html += '<li title="35"></li>';
		html += '</ul>';
		html += '</div>';
		html += '<div>';
		html += '<ul class="qqExpressionList qqExpressionList3 f-cb">';
		html += '<li title="36"></li>';
		html += '<li title="37"></li>';
		html += '<li title="38"></li>';
		html += '<li title="39"></li>';
		html += '<li title="40"></li>';
		html += '<li title="41"></li>';
		html += '<li title="42"></li>';
		html += '<li title="43"></li>';
		html += '<li title="44"></li>';
		html += '<li title="45"></li>';
		html += '<li title="46"></li>';
		html += '<li title="47"></li>';
		html += '<li title="48"></li>';
		html += '<li title="49"></li>';
		html += '<li title="50"></li>';
		html += '<li title="51"></li>';
		html += '<li title="52"></li>';
		html += '<li title="53"></li>';
		html += '</ul>';
		html += '</div>';
		html += '<div>';
		html += '<ul class="qqExpressionList qqExpressionList4 f-cb">';
		html += '<li title="54"></li>';
		html += '<li title="55"></li>';
		html += '<li title="56"></li>';
		html += '<li title="57"></li>';
		html += '<li title="58"></li>';
		html += '<li title="59"></li>';
		html += '<li title="60"></li>';
		html += '<li title="61"></li>';
		html += '<li title="62"></li>';
		html += '<li title="63"></li>';
		html += '<li title="64"></li>';
		html += '<li title="65"></li>';
		html += '<li title="66"></li>';
		html += '<li title="67"></li>';
		html += '<li title="68"></li>';
		html += '<li title="69"></li>';
		html += '<li title="70"></li>';
		html += '<li title="71"></li>';
		html += '</ul>';
		html += '</div>';
		html += '<div>';
		html += '<ul class="qqExpressionList qqExpressionList5 f-cb">';
		html += '<li title="72"></li>';
		html += '<li title="73"></li>';
		html += '<li title="74"></li>';
		html += '<li title="75"></li>';
		html += '<li title="76"></li>';
		html += '<li title="77"></li>';
		html += '<li title="78"></li>';
		html += '<li title="79"></li>';
		html += '<li title="80"></li>';
		html += '<li title="81"></li>';
		html += '<li title="82"></li>';
		html += '<li title="83"></li>';
		html += '<li title="84"></li>';
		html += '<li title="85"></li>';
		html += '<li title="86"></li>';
		html += '<li title="87"></li>';
		html += '<li title="88"></li>';
		html += '<li title="89"></li>';
		html += '</ul>';
		html += '</div>';
		html += '<div>';
		html += '<ul class="qqExpressionList qqExpressionList6 f-cb">';
		html += '<li title="90"></li>';
		html += '<li title="91"></li>';
		html += '<li title="92"></li>';
		html += '<li title="93"></li>';
		html += '<li title="94"></li>';
		html += '<li title="95"></li>';
		html += '<li title="96"></li>';
		html += '<li title="97"></li>';
		html += '<li title="98"></li>';
		html += '<li title="99"></li>';
		html += '<li title="100"></li>';
		html += '<li title="101"></li>';
		html += '<li title="102"></li>';
		html += '<li title="103"></li>';
		html += '<li title="104"></li>';
		html += '</ul>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '<ul class="swipeNav f-cb">';
		html += '<li class="active"></li>';
		html += '<li></li>';
		html += '<li></li>';
		html += '<li></li>';
		html += '<li></li>';
		html += '<li></li>';
		html += '</ul>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		$('#commentArea').append(html);
		//初始化消息
		comment.get();
		//加载更多评论
		$('#loadMoreComment').click(function() {
			comment.get();
		});
		//发送评论
		$('#comment_send').click(function() {
			comment.send();
		});
		//调出评论框
		$('.comment_post').click(function() {
			V.reply.show();
		});
		//关闭评论框
		$('.j-closeReplyBox').click(function() {
			V.reply.close();
		});
	};
	/*****评论提交*****/
	this.send = function() {
		var content = $('#comment_content').val();
		if (!content || content == 0) {
			$.iBox.error('评论内容不能为空');
			return false;
		}
		this.post(content);
	};
	/*****保存评论数据*****/
	this.post = function(content) {
		var _this = this;
		var member_id = this.config['member_id'];
		var mpid = this.config['mpid'];
		var item = this.config['item'];
		if (!member_id || member_id == 0) {
			alert('访客无法发布评论');
			return false;
		}
		$.iBox.loading("数据保存中...");
		$.get(
			"http://www.weiwubao.com/ajax/comment/post/", {item: this.config['item'], itemId: this.config['item_id'], mpid: this.config['mpid'], mid: member_id, content: content},
		function(json) {
			if (json.status == 0) {
				$.iBox.error(json.message);
			} else {
				//移除沙发图片
				$('#shafa').remove();
				$.iBox.close();
				if ((mpArr.in_array(mpid) && item == 'vote') || _this.config['display_type'] == 2) {
					var list = '<li class="item" data-id="' + json.c_id + '">';
					list += '<img class="tx" src="' + _this.config['member_avator'] + '"/>';
					list += '<div class="con">';
					list += '<div class="mt"><span class="fr">刚刚</span>' + _this.config['member_nickname'] + '</div>';
					list += '<div class="mc">' + json.content + '</div>';
					list += '</div>';
					list += '</li>';
				} else {
					var list = '<div class="commIner f-cb" data-id="' + json.c_id + '">';
					list += '<div class="pic"><img src="' + _this.config['member_avator'] + '" /></div>';
					list += ' <div class="txt"><h3 class="name">' + _this.config['member_nickname'] + '</h3>';
					list += '<p>' + json.content + '</p>';
					list += '</div>';
					list += '</div>';
				}
				$("#comment_list").prepend(list);
				$('#comment_content').val('');
				V.reply.close();
			}
		},
			'json'
			);
	};
	/*****拉取帖子列表*****/
	this.get = function() {
		if (this.config['pull'] == false) {
			return false;
		}
		var _this = this;
		$.iBox.loading("评论加载中...");
		//$("#loadMoreComment").html('加载中...');
		$.get("http://www.weiwubao.com/ajax/comment/get/", {item: this.config['item'], itemId: this.config['item_id'], page: this.config['page'], mpid: this.config['mpid']},
		function(json) {
			$.iBox.close();
			//$("#loadMoreComment").html('更多');
			$('#comment_total').html(json.total);
			if (json.status == 0 || _this.config['page'] > json.page_total) {
				if (_this.config['page'] == 1) {
					//$("#comment_list").append('<li>暂无评论</li>');
					//$.iBox.error("暂无评论");
				} else {
					//$.iBox.error("没有更多的评论了");
				}
				$("#loadMoreComment").remove();
				_this.config['pull'] = false;
			} else {
				//移除沙发图片
				$('#shafa').remove();

				if (_this.config['page'] == json.page_total) {
					$("#loadMoreComment").remove();
					_this.config['pull'] = false;
				}
				_this.config['page']++;
				var list = '';
				for (i = 0; i < json.quantity; i++) {
					var result = json.data[i];

					if ((mpArr.in_array(_this.config['mpid']) && _this.config['item'] == 'vote') || _this.config['display_type'] == 2) {
						list += '<li class="item" data-id="' + result['id'] + '">';
						list += '<img class="tx" src="' + result['avator'] + '"/>';
						list += '<div class="con">';
						list += '<div class="mt"><span class="fr">' + result['date'] + '</span>' + result['nickname'] + '</div>';
						list += '<div class="mc">' + result['content'] + '</div>';
						list += '</div>';
						list += '</li>';
					} else {
						list += '<div class="commIner f-cb" data-id="' + result['id'] + '">';
						list += '<div class="pic"><img src="' + result['avator'] + '" /></div>';
						list += ' <div class="txt"><h3 class="name">' + result['nickname'] + '</h3>';
						if (result['reply_id'] != 0) {
							list += '<p>回复： <span>野兽王子：</span>' + result['content'] + '</p>';
						} else {
							list += '<p>' + result['content'] + '</p>';
						}
						list += '</div>';
						list += '</div>';
					}
				}
				$('#comment_list').append(list);
			}
		},
			'json'
			);
	};

	Array.prototype.in_array = function(e) {
		for (k = 0; k < this.length; k++)
		{
			if (this[k] == e)
				return true;
		}
		return false;
	};
}
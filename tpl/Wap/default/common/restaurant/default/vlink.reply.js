if(!window.V){
	window.vlink=V={};
}

V.reply=(function(){		
						
		function initReply(){
									
			// $(document).on('click', '', function(){});
			
			$(document).on('click', '.j-expressionToogle', function(){
				var elem=document.getElementById('mySwipe');
				var swipeNav=document.querySelectorAll('.swipeNav > li');
				var mySwipe = Swipe(elem, {
				  // startSlide: 4,
				  // auto: 3000,
				   continuous: false,
				  // disableScroll: true,
				  // stopPropagation: true,
				  // callback: function(index, element) {},
				   transitionEnd: function(index, element) {
						for(var i=0;i<swipeNav.length;i++){
							swipeNav[i].className='';
						}
						swipeNav[index].className='active'; 
					}
				});
				
				
				if($(this).hasClass('active')){
					$(this).removeClass('active');
					$('#replyBox').css('height','auto');
				}else{
					$(this).addClass('active');
					$('#replyBox').css('height',303);				
					$('.j-replyBox-textarea').trigger('focus');
					
					setTimeout(function(){
						$('.j-replyBox-textarea').trigger('blur');
						$(document).trigger('click');
						mySwipe.slide(mySwipe.getPos());
					},100);
				}
			});
		
			$(document).on('touchend', '.qqExpressionList li', function(ev){
	
				var str='[E:'+$(this).attr('title')+']';
				var oldHtml=$('.j-replyBox-textarea').val();
		
				$('.j-replyBox-textarea').val(oldHtml+str);	
			});	

			$(document).on('click', '.j-reply', function(){
				showReply();
			});

			$(document).on('click', '.j-closeReplyBox', function(){
				closeReply();			
			});
		}
		
		function createMask(){
			if($('#maskReply').length){
				$('#maskReply').show();
			}else{
				var str='<div id="maskReply" style="position:fixed; z-index:1200; left:0; right:0; top:0; bottom:0; opacity:0.7; background-color:#000;"></div>';
				$('body').append(str);
			}			
		}
		
		function closeReply(){
			$('#maskReply').hide();
			$('#replyBox').css({'height':'auto','visibility':'hidden'});
						
			$('.j-expressionToogle').removeClass('active');
		}
		
		function showReply(){
			$('#replyBox').css('visibility','visible');
			$('#comment_content').focus();
			createMask();
		}
		
		initReply();
		
		return{
			close: closeReply,
			show: showReply,
			init: initReply
		}
})();
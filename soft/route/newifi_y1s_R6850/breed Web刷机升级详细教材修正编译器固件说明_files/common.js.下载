$(document).ready(function() {
		//小提示
		$('[data-toggle="tooltip"]').tooltip();

		//返回顶部
		$(window).scroll(function() {
			if ($(this).scrollTop() != 0) {
				$("#toTop").fadeIn()
			} else {
				$("#toTop").fadeOut()
			}
		});
		$("#toTop").click(function() {
			$("body,html").animate({
				scrollTop: 0
			}, 800)
		})
		//验证文章评论数据
		$('.btn-add').click(function(){
			$("#name").parent(".input-group").removeClass("has-error");
			$("#email").parent(".input-group").removeClass("has-warning");
			$("#email").parent(".input-group").removeClass("has-error");
			$("#content-text").parent(".form-group").removeClass("has-error");
			$("#txt_check").parent(".input-group").removeClass("has-error");
			var jump = $("#jump").val();
			var send = $("input[name='send']:checked").val();
			var rember = $("input[name='rember']:checked").val();
			var id = $("#aid").val();
			var title = $("#atitle").val();
			var name=$('#name').val();
			var ac_url =$('#url').val();
			var email=$('#email').val();
			var content_text=$('#content-text').val();
			var txt_check=$('#txt_check').val();
			if(!name){
				$("#name").parent(".input-group").addClass("has-error");
				return false;
			}else if(!email){
				$("#email").parent(".input-group").addClass("has-warning");
				return false;
			}else if(!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email)){
				$("#email").parent(".input-group").addClass("has-error");
				return false;
			}else if(!content_text){
				$("#content-text").parent(".form-group").addClass("has-error");
				return false;
			}else if(!txt_check){
				$("#txt_check").parent(".input-group").addClass("has-error");
				return false;
			}
			$(".btn-add").attr("disabled",true);
			$.ajax({
				type:"post",
				url:"/Article/addArticleContent",
				async:true,
				dataType:'json',
				data:{"ac_pid":id,"title":title,"ac_name":name,"ac_email":email,"ac_url":ac_url,"ac_content":content_text,"txt_check":txt_check,"send":send,"rember":rember},
				success:function(data){
					if(data.att == 1){
						$("#txt_check").parent(".input-group").addClass("has-error");
						$(".btn-add").removeAttr('disabled');
						return false;						
					}else{
						window.location.href=jump;
					}
				}
			});
		});
		//验证说说评论数据
		$('.btn-add-say').click(function(){
			$("#name").parent(".input-group").removeClass("has-error");
			$("#email").parent(".input-group").removeClass("has-warning");
			$("#email").parent(".input-group").removeClass("has-error");
			$("#content-text").parent(".form-group").removeClass("has-error");
			$("#txt_check").parent(".input-group").removeClass("has-error");
			var jump = $("#jump").val();
			var send = $("input[name='send']:checked").val();
			var rember = $("input[name='rember']:checked").val();
			var id = $("#sid").val();
			var title = $("#stitle").val();
			var name=$('#name').val();
			var sc_url =$('#url').val();
			var email=$('#email').val();
			var content_text=$('#content-text').val();
			var txt_check=$('#txt_check').val();
			if(!name){
				$("#name").parent(".input-group").addClass("has-error");
				return false;
			}else if(!email){
				$("#email").parent(".input-group").addClass("has-warning");
				return false;
			}else if(!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email)){
				$("#email").parent(".input-group").addClass("has-error");
				return false;
			}else if(!content_text){
				$("#content-text").parent(".form-group").addClass("has-error");
				return false;
			}else if(!txt_check){
				$("#txt_check").parent(".input-group").addClass("has-error");
				return false;
			}
			$(".btn-add-say").attr("disabled",true);
			$.ajax({
				type:"post",
				url:"/Feel/addFeelContent",
				async:true,
				dataType:'json',
				data:{"sc_pid":id,"title":title,"sc_name":name,"sc_email":email,"sc_url":sc_url,"sc_content":content_text,"txt_check":txt_check,"send":send,"rember":rember},
				success:function(data){
					if(data.att == 1){
						$("#txt_check").parent(".input-group").addClass("has-error");
						$(".btn-add-say").removeAttr('disabled');
						return false;						
					}else{
						window.location.href=jump;
					}
				}
			});
		});
		//相册特效
		$('.fancybox-buttons').fancybox({
			openEffect  : 'none',
			closeEffect : 'none',	
			prevEffect : 'none',
			nextEffect : 'none',	
			closeBtn  : false,	
			helpers : {
				title : {
					type : 'inside'
				},
				buttons	: {}
			},
			afterLoad : function() {
				this.title = '第 ' + (this.index + 1) + '张 共 ' + this.group.length + (this.title ? ' - ' + this.title : '张');
			}
		});

		//验证相册评论数据
		$('.btn-add-album').click(function(){
			$("#name").parent(".input-group").removeClass("has-error");
			$("#email").parent(".input-group").removeClass("has-warning");
			$("#email").parent(".input-group").removeClass("has-error");
			$("#content-text").parent(".form-group").removeClass("has-error");
			$("#txt_check").parent(".input-group").removeClass("has-error");
			var jump = $("#jump").val();
			var send = $("input[name='send']:checked").val();
			var rember = $("input[name='rember']:checked").val();
			var id = $("#alid").val();
			var alname = $("#alname").val();
			var name=$('#name').val();
			var alc_url =$('#url').val();
			var email=$('#email').val();
			var content_text=$('#content-text').val();
			var txt_check=$('#txt_check').val();
			if(!name){
				$("#name").parent(".input-group").addClass("has-error");
				return false;
			}else if(!email){
				$("#email").parent(".input-group").addClass("has-warning");
				return false;
			}else if(!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email)){
				$("#email").parent(".input-group").addClass("has-error");
				return false;
			}else if(!content_text){
				$("#content-text").parent(".form-group").addClass("has-error");
				return false;
			}else if(!txt_check){
				$("#txt_check").parent(".input-group").addClass("has-error");
				return false;
			}
			$(".btn-add-album").attr("disabled",true);
			$.ajax({
				type:"post",
				url:"/Album/addAlbumContent",
				async:true,
				dataType:'json',
				data:{"alc_pid":id,"alname":alname,"alc_name":name,"alc_email":email,"alc_url":alc_url,"alc_content":content_text,"txt_check":txt_check,"send":send,"rember":rember},
				success:function(data){
					if(data.att == 1){
						$("#txt_check").parent(".input-group").addClass("has-error");
						$(".btn-add-album").removeAttr('disabled');
						return false;						
					}else{
						window.location.href=jump;
					}
				}
			});
		});
		
		//验证申请链接数据
		$('.btn-add-link').click(function(){
			$("#name").parent(".input-group").removeClass("has-error");
			$("#email").parent(".input-group").removeClass("has-warning");
			$("#email").parent(".input-group").removeClass("has-error");
			$("#content-text").parent(".form-group").removeClass("has-error");
			$("#txt_check").parent(".input-group").removeClass("has-error");
			var jump = $("#jump").val();
			var name=$('#name').val();
			var l_url =$('#url').val();
			var email=$('#email').val();
			var content_text=$('#content-text').val();
			var txt_check=$('#txt_check').val();
			if(!name){
				$("#name").parent(".input-group").addClass("has-error");
				return false;
			}else if(!email){
				$("#email").parent(".input-group").addClass("has-warning");
				return false;
			}else if(!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email)){
				$("#email").parent(".input-group").addClass("has-error");
				return false;
			}else if(!content_text){
				$("#content-text").parent(".form-group").addClass("has-error");
				return false;
			}else if(!txt_check){
				$("#txt_check").parent(".input-group").addClass("has-error");
				return false;
			}
			$(".btn-add-link").attr("disabled",true);
			$.ajax({
				type:"post",
				url:"/Friends/addLink",
				async:true,
				dataType:'json',
				data:{"l_name":name,"l_email":email,"l_url":l_url,"l_content":content_text,"txt_check":txt_check,"l_sort":100,"l_view":0},
				success:function(data){
					if(data.att == 1){
						$("#txt_check").parent(".input-group").addClass("has-error");
						$(".btn-add-link").removeAttr('disabled');
						return false;						
					}else{
						window.location.href=jump;
					}
				}
			});
		});
		
		//验证留言数据
		$('.btn-add-gust').click(function(){
			$('.form-control-feedback').addClass('hidden');
			$("#name").parent(".input-group").removeClass("has-error");
			$("#email").parent(".input-group").removeClass("has-warning");
			$("#email").parent(".input-group").removeClass("has-error");
			$("#content-text").parent(".form-group").removeClass("has-error");
			$("#txt_check").parent(".input-group").removeClass("has-error");
			var jump = $("#jump").val();
			var send = $("input[name='send']:checked").val();
			var rember = $("input[name='rember']:checked").val();
			var name=$('#name').val();
			var g_url =$('#url').val();
			var email=$('#email').val();
			var content_text=$('#content-text').val();
			var txt_check=$('#txt_check').val();
			if(!name){
				$("#name").parent(".input-group").addClass("has-error");
				$("#name").parent(".input-group").find('span').removeClass('hidden');
				return false;
			}else if(!email){
				$("#email").parent(".input-group").addClass("has-warning");
				$("#email").parent(".input-group").find('span').removeClass('hidden');
				return false;
			}else if(!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email)){
				$("#email").parent(".input-group").addClass("has-error");
				$("#email").parent(".input-group").find('span').removeClass('hidden');
				return false;
			}else if(!content_text){
				$("#content-text").parent(".form-group").addClass("has-error");
				return false;
			}else if(!txt_check){
				$("#txt_check").parent(".input-group").addClass("has-error");
				$("#txt_check").parent(".input-group").find('span').removeClass('hidden');
				return false;
			}
			$(".btn-add-gust").attr("disabled",true);
			$.ajax({
				type:"post",
				url:"/Gust/addContent",
				async:true,
				dataType:'json',
				data:{"g_name":name,"g_email":email,"g_url":g_url,"g_content":content_text,"txt_check":txt_check,"send":send,"rember":rember},
				success:function(data){
					if(data.att == 1){
						$("#txt_check").parent(".input-group").addClass("has-error");
						$("#txt_check").parent(".input-group").find('span').removeClass('hidden');
						$(".btn-add-gust").removeAttr('disabled');
						return false;						
					}else{
						window.location.href=jump;
					}
				}
			});
		});
});
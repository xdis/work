$(function(){
	/*
	*		参数说明
	*		baseUrl:	【字符串】表情路径的基地址
	*		pace:		【数字】表情弹出层淡入淡出的速度
	*		dir:		【数组】保存表情包文件夹名字
	*		text:		【二维数组】保存表情包title文字
	*		num:		【数组】保存表情包表情个数
	*		isExist:	【数组】保存表情是否加载过,对于加载过的表情包不重复请求。
	*/
	var rl_exp = {
		baseUrl:	'/Public/Img/Face/',
		pace:		200,
		dir:		['mr','gnl','lxh','bzmh'],
		text:[			/*表情包title文字，自己补充*/
			[
				'mr:/0', 
				'mr:/1', 'mr:/2', 'mr:/3', 'mr:/4', 'mr:/5', 'mr:/6', 'mr:/7', 'mr:/8', 'mr:/9', 'mr:/10', 'mr:/11', 'mr:/12', 'mr:/13', 
				'mr:/14', 'mr:/15', 'mr:/16', 'mr:/17', 'mr:/18', 'mr:/19', 'mr:/20', 'mr:/21', 'mr:/22', 'mr:/23', 'mr:/24', 'mr:/25', 'mr:/26', 
				'mr:/27', 'mr:/28', 'mr:/29', 'mr:/30', 'mr:/31', 'mr:/32', 'mr:/33', 'mr:/34', 'mr:/35', 'mr:/36', 'mr:/37', 'mr:/38', 'mr:/39', 
				'mr:/40', 'mr:/41', 'mr:/42', 'mr:/43', 'mr:/44', 'mr:/45', 'mr:/46', 'mr:/47', 'mr:/48', 'mr:/49', 'mr:/50', 'mr:/51', 'mr:/52', 
				'mr:/53', 'mr:/54', 'mr:/55', 'mr:/56', 'mr:/57', 'mr:/58', 'mr:/59', 'mr:/60', 'mr:/61', 'mr:/62', 'mr:/63', 'mr:/64', 'mr:/65', 
				'mr:/66', 'mr:/67', 'mr:/68', 'mr:/69', 'mr:/70', 'mr:/71', 'mr:/72', 'mr:/73', 'mr:/74', 'mr:/75', 'mr:/76', 'mr:/77', 'mr:/78', 
				'mr:/79', 'mr:/80', 'mr:/81', 'mr:/82', 'mr:/83', 'mr:/84'
			],
			[
				'gnl:/0', 
				'gnl:/1', 'gnl:/2', 'gnl:/3', 'gnl:/4', 'gnl:/5', 'gnl:/6', 'gnl:/7', 'gnl:/8', 'gnl:/9', 'gnl:/10', 'gnl:/11', 'gnl:/12', 'gnl:/13', 
				'gnl:/14', 'gnl:/15', 'gnl:/16', 'gnl:/17', 'gnl:/18', 'gnl:/19', 'gnl:/20', 'gnl:/21', 'gnl:/22', 'gnl:/23', 'gnl:/24', 'gnl:/25', 'gnl:/26', 
				'gnl:/27', 'gnl:/28', 'gnl:/29', 'gnl:/30', 'gnl:/31', 'gnl:/32', 'gnl:/33', 'gnl:/34', 'gnl:/35', 'gnl:/36', 'gnl:/37', 'gnl:/38', 'gnl:/39', 
				'gnl:/40', 'gnl:/41', 'gnl:/42', 'gnl:/43', 'gnl:/44', 'gnl:/45'
			],
			[
				'lxh:/0', 
				'lxh:/1', 'lxh:/2', 'lxh:/3', 'lxh:/4', 'lxh:/5', 'lxh:/6', 'lxh:/7', 'lxh:/8', 'lxh:/9', 'lxh:/10', 'lxh:/11', 'lxh:/12', 'lxh:/13', 
				'lxh:/14', 'lxh:/15', 'lxh:/16', 'lxh:/17', 'lxh:/18', 'lxh:/19', 'lxh:/20', 'lxh:/21', 'lxh:/22', 'lxh:/23', 'lxh:/24', 'lxh:/25', 'lxh:/26', 
				'lxh:/27', 'lxh:/28', 'lxh:/29', 'lxh:/30', 'lxh:/31', 'lxh:/32', 'lxh:/33', 'lxh:/34', 'lxh:/35', 'lxh:/36', 'lxh:/37', 'lxh:/38', 'lxh:/39', 
				'lxh:/40', 'lxh:/41', 'lxh:/42', 'lxh:/43', 'lxh:/44', 'lxh:/45', 'lxh:/46', 'lxh:/47', 'lxh:/48', 'lxh:/49', 'lxh:/50', 'lxh:/51', 'lxh:/52', 
				'lxh:/53', 'lxh:/54', 'lxh:/55', 'lxh:/56', 'lxh:/57', 'lxh:/58', 'lxh:/59', 'lxh:/60', 'lxh:/61', 'lxh:/62', 'lxh:/63', 'lxh:/64', 'lxh:/65', 
				'lxh:/66', 'lxh:/67', 'lxh:/68', 'lxh:/69', 'lxh:/70', 'lxh:/71', 'lxh:/72', 'lxh:/73', 'lxh:/74', 'lxh:/75', 'lxh:/76', 'lxh:/77', 'lxh:/78', 
				'lxh:/79', 'lxh:/80', 'lxh:/81', 'lxh:/82'
			],
			[
				'bzmh:/0', 
				'bzmh:/1', 'bzmh:/2', 'bzmh:/3', 'bzmh:/4', 'bzmh:/5', 'bzmh:/6', 'bzmh:/7', 'bzmh:/8', 'bzmh:/9', 'bzmh:/10', 'bzmh:/11', 'bzmh:/12', 'bzmh:/13', 
				'bzmh:/14', 'bzmh:/15', 'bzmh:/16', 'bzmh:/17', 'bzmh:/18', 'bzmh:/19', 'bzmh:/20', 'bzmh:/21', 'bzmh:/22', 'bzmh:/23', 'bzmh:/24', 'bzmh:/25', 'bzmh:/26', 
				'bzmh:/27', 'bzmh:/28', 'bzmh:/29', 'bzmh:/30', 'bzmh:/31', 'bzmh:/32', 'bzmh:/33', 'bzmh:/34', 'bzmh:/35', 'bzmh:/36', 'bzmh:/37', 'bzmh:/38', 'bzmh:/39', 
				'bzmh:/40', 'bzmh:/41', 'bzmh:/42', 'bzmh:/43', 'bzmh:/44', 'bzmh:/45', 'bzmh:/46', 'bzmh:/47', 'bzmh:/48', 'bzmh:/49', 'bzmh:/50', 'bzmh:/51', 'bzmh:/52', 
				'bzmh:/53', 'bzmh:/54', 'bzmh:/55', 'bzmh:/56', 'bzmh:/57', 'bzmh:/58', 'bzmh:/59', 'bzmh:/60', 'bzmh:/61', 'bzmh:/62', 'bzmh:/63', 'bzmh:/64', 'bzmh:/65', 
				'bzmh:/66', 'bzmh:/67', 'bzmh:/68', 'bzmh:/69'
			]
		],
		num:		[84,46,82,69],
		isExist:	[0,0,0,0],
		bind:	function(i){
			$("#rl_bq .rl_exp_main").eq(i).find('.rl_exp_item').each(function(){
				$(this).bind('click',function(){
					rl_exp.insertText(document.getElementById('content-text'),'['+$(this).find('img').attr('title')+']');
					$('#rl_bq').fadeOut(rl_exp.pace);
				});
			});
		},
		/*加载表情包函数*/
		loadImg:function(i){
			var node = $("#rl_bq .rl_exp_main").eq(i);
			for(var j = 0; j<rl_exp.num[i];j++){
				var domStr = 	'<li class="rl_exp_item">' + 
									'<img src="' + rl_exp.baseUrl + '' + rl_exp.dir[i] + '/' + j + '.gif" alt="' + rl_exp.text[i][j] + 
									'" title="' + rl_exp.text[i][j] + '" />' +
								'</li>';
				$(domStr).appendTo(node);
			}
			rl_exp.isExist[i] = 1;
			rl_exp.bind(i);
		},
		/*在textarea里光标后面插入文字*/
		insertText:function(obj,str){
			obj.focus();
			if (document.selection) {
				var sel = document.selection.createRange();
				sel.text = str;
			} else if (typeof obj.selectionStart == 'number' && typeof obj.selectionEnd == 'number') {
				var startPos = obj.selectionStart,
					endPos = obj.selectionEnd,
					cursorPos = startPos,
					tmpStr = obj.value;
				obj.value = tmpStr.substring(0, startPos) + str + tmpStr.substring(endPos, tmpStr.length);
				cursorPos += str.length;
				obj.selectionStart = obj.selectionEnd = cursorPos;
			} else {
				obj.value += str;
			}
		},
		init:function(){
			$("#rl_bq > ul.rl_exp_tab > li > a").each(function(i){
				$(this).bind('click',function(){
					if( $(this).hasClass('selected') )
						return;
					if( rl_exp.isExist[i] == 0 ){
						rl_exp.loadImg(i);
					}
					$("#rl_bq > ul.rl_exp_tab > li > a.selected").removeClass('selected');
					$(this).addClass('selected');
					$('#rl_bq .rl_selected').removeClass('rl_selected').hide();
					$('#rl_bq .rl_exp_main').eq(i).addClass('rl_selected').show();
				});
			});
			/*绑定表情弹出按钮响应，初始化弹出默认表情。*/
			$("#rl_exp_btn").bind('click',function(){
				if( rl_exp.isExist[0] == 0 ){
					rl_exp.loadImg(0);
				}
				var w = $(this).position();
				//alert(w.left);position:absolute;display:none;z-index:1000;
				//$('#rl_bq').css({left:w.left+350,top:w.top-50}).fadeIn(400);
				$('#rl_bq').css({"position":"absolute","display":"none","z-index":"100"}).fadeIn(400);
			});
			/*绑定关闭按钮*/
			$('#rl_bq a.close').bind('click',function(){
				$('#rl_bq').fadeOut(rl_exp.pace);
			});
			/*绑定document点击事件，对target不在rl_bq弹出框上时执行rl_bq淡出，并阻止target在弹出按钮的响应。*/
			$(document).bind('click',function(e){
				var target = $(e.target);
				if( target.closest("#rl_exp_btn").length == 1 )
					return;
				if( target.closest("#rl_bq").length == 0 ){
					$('#rl_bq').fadeOut(rl_exp.pace);
				}
			});
		}
	};
	rl_exp.init();	//调用初始化函数。
});
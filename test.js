// JavaScript Document
$(function(){
	var oldHeight=0;
	$(window).resize(function(){
		var wHeight=$(".file_index_wrapper").height();
		if(wHeight==oldHeight){}
		else{
			//alert(wHeight);
		}
		$(".file_index_main").height(wHeight-180);
	});
	//$(".file_index_main").height(wHeight-180);
});
//左侧菜单
		function updateLeftMenu(dir){
			var dirs=dir.split("/");
			var ts1='';
			var ts2='';
			if(dir=='.'){
				ts1='';
			}else{
				for(var i=0;i<dirs.length;i++){
					ts1=ts1+">ul>li[name='"+dirs[i]+"']";
				}
			}
			
			switch($.cookie("location")){
				case "file":
					ts2=".widget_leftMenu>ul:first-child>li:first-child";
					break;
				case "rub":
					ts2=".widget_leftMenu>ul:first-child>li:eq(2)";
					break;
				case 'share':
					ts2=".widget_leftMenu>ul:first-child>li:eq(3)";
					break;
				case "public":
					ts2=".widget_leftMenu>ul:first-child>li:eq(4)";
					break;
			}
			//consolelog(ts2+ts1);
			var $target=$(ts2+ts1);
			//alert("初始化："+dir);
			$.post("getNode.php",{
				name:"我的文档",
				dir:dir,
				location:$.cookie("location")
			},function(data){
				var temp=$.parseJSON(data);
				handleData(temp,$target);
			});
		}

		function handleData(data,target){
			//负责将原数据清理并追击新数据到target，更新selected
			//alert("handledata");
			$("ul",target).remove();
			var $ul=$("<ul/>");
			for(var i=0;i<data.length;i++){				
				$("<li name='"+data[i].name+"' location='"+data[i].location+"' dirs='"+data[i].dir+"' isParent='"+data[i].isParent+"' isOpen='"+data[i].open+"'>"+data[i].name+"</li>").appendTo($ul);
			}
			$ul.appendTo(target);
			target.attr("isOpen","true");
			$(".widget_leftMenu .selected").removeClass("selected");
			target.addClass("selected");
		}
//更新页面内容
	function refreshContentHref(href){
		$(".file_index_right").empty();
		$.get(href,function(data){
			$(".file_index_right").append(data);
		});
	}
	function refreshContentData(data){
		$(".file_index_right").empty();
		$(".file_index_right").append(data);
	}

//文件的新建、删除、重命名、重命名等基础功能
	function handleRename(){
		//alert("触发你了");
		//处理rename元素
		var val=$('.file_content_rename').val();
		var $normal=$('.file_content_rename').parent().parent();
		var type=$normal.attr("type");
		//alert(type);
		//默认认为是新建文件夹
		operation='newFolder';
		if(type=='file'){
			operation='newFile';
		}
		var dir=($.cookie('dir')=='.'?'':$.cookie('dir')+"/")+val;
		$.post("main.php",{
			operation:operation,
			dir:dir
		},function(data){
			var span=$("<span />",{text:data}).appendTo($('.file_content_rename').parent());	
			$normal.attr('dirs',($.cookie('dir')=='.'?'':$.cookie('dir')+"/")+val);
			$('.file_content_rename').remove();
		});
		//如果是文件夹，那么需要更新左侧栏目
	}
	
	
	function share(){
		var dir=$(".file_content_app.selected").attr("dirs");
		if(! dir){
			alert("请选择要分享的文件")
		}else{
		//这个地方需要处理没有选择的异常
			$w=$().WM("open","share.php?dir="+encodeURI(dir));
			$w.find(".titlebartext").text("分享文件");
		}
	};
	function shareAll(){
		var dir=$(".file_content_app.selected").attr("dirs");
		if(! dir){
			alert("请选择要共享的文件")
		}else{
			$.post("main.php",{
				operation:"share",
				to:"all",
				dir:dir
			},function(data){
				alert(data);
			}
			);
		}
	}
	function newFile(type){
		var type=arguments[0]?'file':'folder';
		//alert(type);
		var text=arguments[0]?'新建文件':'新建文件夹';
		//alert("type:"+type+" text:"+text);
		if($('.file_content_rename').length==0){
			var newFile=$("<div class='file_content_app' dirs='test' type='"+type+"'></div>");			
			var img=$("<img/>",{src:"./images/"+type+".png"}).appendTo(newFile);
			var fileName=$("<div class='fileName' />").appendTo(newFile);
			var name=$("<textarea class='file_content_rename' >"+text+"</textarea>").appendTo(fileName);
			newFile.appendTo($(".file_content_apps"));
			name.select();
			//alert("zhixngwan");
		}
	}
	function delFile(type){
		var $file=$('.file_content_app.selected');
		$.post("main.php",{
			operation:'delFile',
			dir:$file.attr('dirs')
		},function(data){
			if(data=="success"){
				$file.remove();
			}else{
				alert(data);
			}
		});
	}

$(function(){
//单机事件处理
	//app单击
	$('body').on("click",".file_content_app",function(e){
		//alert('120');
		e.stopPropagation();
		if($(this).hasClass('selected')){
			if($(this).attr('type')=='folder'){
				//更新内容
				var dir=$(this).attr('dirs');
				var href=encodeURI('content.php?dir='+dir);
				refreshContentHref(href);
				updateLeftMenu(dir);
			}
			else{
				alert('此类文件目前不支持预览');
			}
		}else{
			//alert("out else");
			$('.file_content_app.selected').removeClass('selected');
			$(this).addClass('selected');
		}
		
	});

	$('body').click(function(){
		//当鼠标点击时，如果有rename的文件（夹），应该保存
		//alert("141");
		if($('.file_content_rename').length>0){
			//handle_Rename();
			handleRename();
		}
		hideMenu();
	});
	//右击菜单单击时间
	$('body').off("click",".file_content_download").on('click','.file_content_download',function(){
		//alert("进入下载！");
		hideMenu();
		var dir=$('.file_content_app.selected').attr('dirs');
		//alert(dir);
		var path="";
		path=encodeURI($.cookie("base")+"/"+dir);
		//alert(path);
		window.open("/file/download.php?f="+path);
	});
	$('body').off("click",".file_content_open").on('click','.file_content_open',function(){
		
		hideMenu();
		alert('open');
		
	});
	$('body').off("click",".file_content_edit").on('click','.file_content_edit',function(){
		hideMenu();
		alert('edit');
	});
	$('body').off("click",".file_content_copy").on('click','.file_content_copy',function(){
		hideMenu();
		alert('copy');
	});
	$('body').off("click",".file_content_cut").on('click','.file_content_cut',function(){
		hideMenu();
		alert('cut');
	});
	$('body').off("click",".file_content_paste").on('click','.file_content_paste',function(){
		hideMenu();
		alert('paste');
	});
	$('body').off("click",".file_content_del").on('click','.file_content_del',function(){
		//删除
		hideMenu();

		delFile();
	});
	

	//菜单栏单击事件
	$('body').on('click','#file_content_newFile',function(e){
		//新建文件
		//alert('hi');
		e.stopPropagation();
		//避免连续修改带来的问题
		if($(".file_content_rename").length>0){
			handleRename();
		}
		//alert("hi");
		newFile('file');
	});
	$('body').on('click','#file_content_newFolder',function(e){
		//新建文件夹
		//阻止事件冒泡，因为#file_index_content，document等都有click事件
		//alert("hsi");
		e.stopPropagation();
		//alert("before");
		//避免连续修改带来的问题
		if($(".file_content_rename").length>0){
			handleRename();
		}
		//alert("hi");
		newFile();
	});
	$('body').on('click','#file_content_upload',function(e){
		$w=$().WM("open","upload.php");
		$w.find(".titlebartext").text("上传文件");
	});
	$('body').on('click','#file_content_shareAll',function(e){
		shareAll();
	});
	$("body").on("click","#file_content_share",function(e){
		share();
	});
	//路径单击事件
	$('body').on('click','.file_content_pathItem',function(){
		var $items=$('.file_content_pathItem');
		var dir='';
		var clickedItem=$(this)[0];
		var path=$(this).text().trim();
		
		var homes=['我的文档','垃圾箱','共享给我的','公共文件'];
		var flag=false;//是否是顶层目录
		for(temp in homes){
			if(homes[temp]==path){
				flag=true;
			}
		}
		if(flag){
			dir='.';			
		}else{			
			for(var i=1;i<$items.length;i++){
				var item=$items.eq(i).text();
				dir=dir+item+'/';
				if(clickedItem==$items[i]){
					break;
				}
			}
			dir=dir.substring(0,dir.length-1);
		}
		//alert("初始化"+dir);
		var href=encodeURI('content.php?dir='+dir);
		refreshContentHref(href);
		//更新左侧tree
		updateLeftMenu(dir);

	});
	//新建或者重命名文件（夹）时，按下enter键的处理
	$("body").off('keypress').on('keypress',".file_content_rename",function(e){
		if(e.key=="Enter"){
			handleRename();
		}
	});

//右击菜单处理
	function hideMenu(){
		$('#file_content_dirMenu').hide();
		$('#file_content_fileMenu').hide();
		$('#file_content_contextMenu').hide();
	}
	$('body').off("contextmenu",'.file_content_app').on('contextmenu',".file_content_app",function(e){
		hideMenu();
		e.preventDefault();
		//如果有rename元素，处理
		if($(".file_content_rename").length>0){
			handleRename();
		}
		$('.file_content_app.selected').removeClass('selected');
		$(this).addClass('selected');
		if($(this).attr('type')=='folder'){
			
			$('#file_content_dirMenu').css({'position': "fixed",'top':e.clientY+"px",'left':e.clientX+"px",'z-index':2});
			$('#file_content_dirMenu').show();
		}else{
			$('#file_content_fileMenu').css({"position": "fixed",'top':e.clientY+"px",'left':e.clientX+"px",'z-index':2});
			$('#file_content_fileMenu').show();	
		}
		e.stopPropagation();
	});
	$('body').bind('contextmenu',function(e){
		hideMenu();
		//alert("我在处理body右击事件！");
		e.preventDefault();		
		//如果有rename元素，处理
		if($(".file_content_rename").length>0){
			handleRename();
		}
		var m_left = $(this).offset().left;
		var m_top = $(this).offset().top;
		
		$('#file_content_contextMenu').css({'position': "fixed",'top':e.clientY+"px",'left':e.clientX+"px",'z-index':2}).show();
	});
//页面布局
/*
	$(".file_index_left").resizable({
		handles:"e",
		//ghost:true,
		stop:function(event,ui){
			//alert("hi");
			var width=$(document).width();
			var leftWidth=$(".file_index_left").width();
			$(".file_index_right").width(width-leftWidth);
			$(".file_index_right").css({"left":leftWidth});
		}
		});
*/
//左侧菜单
		//获取初始菜单
		$.get("getNode.php",function(data){
			temp=$.parseJSON(data);
			handleData(temp,$(".widget_leftMenu"));
		});
		//展开我的文档
		$.post("getNode.php",{
			name:"我的文档",
			location:'file',
			dir:'.'
		},function(data){
			var temp=$.parseJSON(data);
			var $target=$(".widget_leftMenu>ul:first-child>li:first-child");
			handleData(temp,$target);
			$target.attr("isOpen","true");
		});
		//这个方法控制显式，左侧列表栏和右侧content内容
		$("body").off("click",".widget_leftMenu ul li")
			.on("click",".widget_leftMenu ul li",function(e){
				//alert("li点击方法!");
				e.stopPropagation();
				//设置是否显示子目录
				if($(this).attr("isOpen")=="true"){
					//alert("true");
					$("ul",this).remove();//这句话貌似是把li中的ul删除
					$(this).attr("isOpen","false");
				}else{
					//alert($(this));
					//alert("name:"+$(this).attr("name")+" location:"+$(this).attr("location")+" dir:"+$(this).attr("dir"));
					var dir=null;
					if(!$(this).attr("dirs")){
						dir=".";
					}else{
						dir	=$(this).attr("dirs");
					}
					//alert("target:"+$(this));
					target=$(this);
					$.post("getNode.php",{
						name:$(this).attr("name"),
						location:$(this).attr("location"),
						dir:dir
					},function(data){
						//alert(data);
						temp=$.parseJSON(data);
						//刷新左边点击事件后的显示数据
						handleData(temp,target);
						target.attr("isOpen","true");
					});
				}
				//设置cookie中的base
				switch($(this).attr("location")){
					case 'file':
						$.cookie("base","./data/"+$.cookie("file_userName"));
						break;
					case 'rub':
						$.cookie("base","./data/"+$.cookie("file_userName")+"/_RUB");
						break;
					case 'share':
						$.cookie("base","./data/"+$.cookie("file_userName")+"/_SHARE");
						break;
					case "public":
						$.cookie("base","./data/_SHARE");
						break;
				}
				
				//设置cookie的location
				$.cookie("location",$(this).attr("location"));
				//设置cookie中的dir
				$.cookie("dir",$(this).attr("dirs"));
				//更新中心内容
				var href=encodeURI("content.php?dir="+$.cookie("dir"));
				//alert("刷新Content数据以前");
				refreshContentHref(href);
				
				//更新菜单的显示，点击后应该高亮，直至点击了另一个
				$(".widget_leftMenu .selected").removeClass("selected");
				$(this).addClass("selected");
		});
		
		$(".file_content_toolbar").mouseover(function(){
			//alert('mouservier');
			$(".file_content_toolbar").css('cursor','pointer');
		});
	//禁止选中文字	
//document的title显示
$(document).tooltip();
});
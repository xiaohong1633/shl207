// JavaScript Document
;(function(window,document,$,undefined){
	//面向对象编程,根据JsonData生成格式良好的表格jQuery对象
	var Table = function(JsonData){
		//每页显示行数
		//var pageSize = 5;
		this.JsonData = JsonData;
		//定义表格头
		this.$table = $("<table class='table table-bordered table-striped'></table>");		
		this.$tbody = $("<tbody></tbody>").appendTo(this.$table);		
		//获取字段头
		var headData = new Array();		
		//生成表格头
		this.ghead = function(){
			//this.$thead = $("<thead></thead>").appendTo(this.$table);
			this.$thead = $("<thead></thead>").prependTo(this.$table);
			//在这里再装载表格头
			for(var key in this.JsonData[0]){
				headData.push(key);	
			}
			//alert(headData.length);
			var $headTr = $("<tr/>");
			for(var i = 0;i<headData.length;i++){
				$("<th>"+headData[i]+"</th>").appendTo($headTr);	
			}
			$headTr.appendTo(this.$thead);
			//垃圾赶快回收啊
			$headTr = null;
			key = null;
			i = null;
		}		
		//生成表格体
		this.gbody = function(){			
			//此函数的功能是将列重名的记录合并，然后将体加入到this.$body中
			for(var j = 0 ;j<this.JsonData.length;j++){
				var $bodyTr = $("<tr/>");
				for(var k = 0;k<headData.length;k++){
					if(JsonData[j][headData[k]] instanceof Array){
						var $sTable = this.gCloumn(this.JsonData[j][headData[k]]);
						var $td = $("<td class='ptd'></td>");
						$sTable.appendTo($td);
						$td.appendTo($bodyTr);
						//垃圾回收
						$sTable = null;
						$td = null;
					}else{
						$("<td>"+this.JsonData[j][headData[k]]+"</td>").appendTo($bodyTr);
					}					
				}	
				$bodyTr.appendTo(this.$tbody);
				//垃圾回收
				k = null;
				$bodyTr = null;
			}
			//垃圾回收
			j = null;
		};
		//生成一列数据
		this.gCloumn = function(JsonData){
			//var JsonData = arrayJSON;
			var $myCTable = $("<table class='table table-bordered ' border='1' cellspacing='0' cellpadding='0' style='border-collapse: collapse;border-width:0px; border-style:hidden;'></table>");
			var $myCTbody = $("<tbody></tbody>").appendTo($myCTable);
			for(var j=0;j<JsonData.length;j++){
				$("<tr><td>"+JsonData[j]+"</td><tr/>").appendTo($myCTbody);	
			}
			//垃圾回收
			j = null;
			$myCTbody = null;			
			return $myCTable;
		}
		
		//返回生成好的$table,Jquery对象 
		this.show = function(pageNum,pageSize){
			//alert("打算跳转到第"+pageNum+"页");
			var Start = (parseInt(pageNum)-1)*parseInt(pageSize);
			var End = parseInt(pageNum)*parseInt(pageSize);
			if(End >= this.JsonData.length ){
				this.JsonData = this.JsonData.slice(Start);	
			}else{
				this.JsonData = JsonData.slice(Start,End);
			}			
			this.ghead();
			this.gbody();
			return this.$table;
		}
		/*this.showCloumn = function(){
			this.gCloumn();
			return this.$table;
		}*/	   	
	}
	//这里开始写分页插件
	var fPage = function(dataLength,pageSize,showNum){
		this.showNum = showNum
		this.pageSize = 10;
		if(pageSize){
			this.pageSize = pageSize;	
		}	
		this.pageNum = (parseInt(dataLength)%parseInt(this.pageSize)==0) ? (parseInt(dataLength)  / parseInt(this.pageSize)) : (Math.floor(parseInt(dataLength) / parseInt(this.pageSize))+1);
		//alert("pageNum:"+this.pageNum);
		this.div = $('<div></div>');
		//style="line-height:1"
		$('<ul class="pager">		<li><span id="pagePrevious"><input type="button" value="上一页" /></span></li>		<li>第<span id="pageNum">'+this.showNum+'</span>页</li>		<li><span  id="pageNext"><input type="button" value="下一页" /></span></li>		<li>&nbsp;&nbsp;共<span id="maxPage">'+this.pageNum+'</span>页&nbsp;&nbsp;</li>		<li>第<input id="skiPageNum" type="text" maxlength=4 size=4  />页</li>		<li><span id="pageSkip"><input   type="button" value="跳转" /></span></li>		</ul>').appendTo(this.div);
		return this.div;
	}
	//<form style="display:inline;line-height:1">
	//插件的实际定义地方
	$.fn.table = function(jsonData){
		//alert(this.attr('id'));
		//这里定义一个事件
		$("body").off("contextmenu","#"+this.attr('id')).on("contextmenu","#"+this.attr('id'),function(e){
				e.preventDefault();
				e.stopPropagation();
				hideMenu();
				
				//alert(e.pageX);
				//alert(e.pageY);
				//获取#contextMenu的长度和宽度
				//alert($("#contextMenu").height());
				//alert($("#contextMenu").width());
				/*alert(window.screen.availHeight);
				alert(window.screen.availWidth);*/
				//alert(document.body.clientHeight);
				//alert(document.body.scrollHeight);				
				//alert(document.body.clientWidth);				
				//alert(document.body.scrollWidth);
				$("#contextMenu").css({'position':'absolute','left':e.pageX+'px','top':e.pageY+"px"});
				$("#contextMenu").show();
					
		});
		$("body").click(function(e){
				//alert('body click!');
				e.preventDefault();
				e.stopPropagation();
				hideMenu();					
		});
		function hideMenu(){
			//$("#contextMenu").css({'display':'none'});
			$("#contextMenu").hide();			
		}
		//跳转到前一页
		$("body").off("click","#pagePrevious").on("click","#pagePrevious",function(e){
			hideMenu()
			e.stopPropagation();
			var nowNum = parseInt($("#pageNum").text())-1;
			if(nowNum>0){
				$("#pageNum").text(nowNum);
				show(nowNum,GjsonData,that);
			}else{
				alert("已经是第一页了！");	
			}
		});
		//跳转到后一页
		$("body").off("click","#pageNext").on("click","#pageNext",function(e){
			hideMenu()
			e.stopPropagation();
			var nowNum = parseInt($("#pageNum").text())+1;
			var maxNum = parseInt($("#maxPage").text());
			//alert(nowNum);
			if(nowNum <= maxNum){
				$("#pageNum").text(nowNum);
				show(nowNum,GjsonData,that);
			}else{
				alert("已经是最后一页了！");
			}
		});
		//跳转到指定页
		$("body").off("click","#pageSkip").on("click","#pageSkip",function(e){
			hideMenu()
			e.stopPropagation();
			var num = $("#skiPageNum").val();
			var maxNum = parseInt($("#maxPage").text());
			if(num > maxNum || num < 1){
				alert("您输入的页码不存在");	
			}else{
				$("#pageNum").text(num);
				show(num,GjsonData,that);
				//alert("跳转到第"+num+"页");	
			}
		});
		//定义事件	
		/*
		//代码功能是完成滚动和预加载
		var flag = true;
		$(window).scroll(function() {
            //var x = e.pageX;
			//var y  = e.pageY;
			if(flag){
			setTimeout(function(){				
				var x = $(window).scrollTop();
				var h = $(window).height();
				var d = $(document).height();				
				if(100+(parseInt(h)+parseInt(x))>=parseInt(d)){
					alert("该加载新数据了！");
				}
				flag = true;
			},10000);
				flag = false;
			}			
        });	*/	
		//调用对象
		/*if(this instanceof $){
			alert("this is JQuery对象！");	
		}else{
			alert("this is 非jQuery对象！");	
			return ;
		}*/
		//alert(this);
		var GpageSize = 20;
		var GjsonData = jsonData;
		var $mpage = null;
		var $mtable = null;
		var that = this;
		
		function show(pageNum,GjsonData,that){
			//$("#div").empty();
			that.empty();
			//alert("清空！");
			var $mpage = new fPage(GjsonData.length,GpageSize,pageNum);
			var $mtable = new Table(GjsonData);	
			var $table = $mtable.show(pageNum,GpageSize);
			//console.log($table);
			//return $("<p>hello world</p>").appendTo($(this));
			//显示表格
			$table.appendTo(that);
			//添加分页
			return $mpage.appendTo(that);
		}
		show(1,GjsonData,that);
		//return $table.appendTo(this);		
	}
	
	
})(window,document,jQuery);
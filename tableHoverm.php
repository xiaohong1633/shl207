<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>多行数据模式</title>
<link rel='stylesheet' href="bootstrap/css/bootstrap.min.css" type='text/css'/>
<script type="text/javascript" src="bootstrap/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="table.js"></script>
<style type="text/css">
	#div{
		width:80%;
		position:relative;
		left:10%;
		margin-right:auto;
	}
	.table{
		margin:0;
		padding:0;	
	}
	.table-bordered > tbody > tr > td.ptd,.table-bordered > tbody > tr > td.ptd tr{
		padding:0;
		margin:0;	
	}
	.pager li > span{
		color: #337ab7;	
	}
	.pager li > span:hover{
		cursor:pointer;
		background-color:#eee;
		text-decoration:none;	
	}
	#contextMenu{
		cursor:auto;
		display:none;			
	}
	#contextMenu ul{
		list-style:outside none none;
		margin:2px 0 0;
		padding:5px 0;
		text-align:left;
		z-index:100;
		position:absolute;
		font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;
		font-size:14px;
		float:left;
		border:1px solid rgba(0,0,0,0.2);
		border-radius:5px;
		background-color:#ffffff;
		background-clip:border-box;
		min-width:110px;
	}
	#contextMenu ul li{
		margin-left:6px;
		margin-bottom:3px;
	}
	#contextMenu ul li:hover{
		color:#337ab7;
		background-color:#eee;
		text-decoration:none;
	}
	table.table th{
		background: #91c5d4 none repeat scroll 0 0 !important;	
	}
	table.table th,table.table td{		
		-moz-border-bottom-colors: none !important;
		-moz-border-left-colors: none !important;
		-moz-border-right-colors: none !important;
		-moz-border-top-colors: none !important;
		border-color: #fff !important;
		border-image: none !important;
		border-style: solid !important;
		border-width: 0 1px 1px 0 !important;
		padding: 5px !important;
		padding:0 !important;	
	}
	table.table td{
		background: #d5eaf0 none repeat scroll 0 0;	
	}
	table.table td:hover{
    	font-weight: bold;	
	}
</style>
<script type='text/javascript'>
function getQueryParameter(key){
	//alert(window.location);
	var url = window.location.href;
	var start = url.indexOf("?");
	//alert("start: "+start);
	var parameter = url.substr(parseInt(start)+1);
	var array = parameter.split("&");
	var PArray = new Array();
	for(var i = 0;i<array.length;i++){
		var temA = array[i].split("=");
		PArray[temA[0]] = temA[1];	
	}
	return PArray[key];
}
//alert(getQueryParameter('name'));
//return false;
$(function(){
	$("body").off('mouseenter','tr').on('mouseenter','tr',function(e){
		e.stopPropagation();
		$(this).children('td').css('background-color','#bcd9e1');;
	});
	$("body").off('mouseleave','tr').on('mouseleave','tr',function(e){
		e.stopPropagation();
		$(this).children('td').css('background-color','#d5eaf0');;	
	});
	$.post("/shl207/mytable/data.json",{
		id:1
	},function(data){
		$("#div").table(data);
	});
});
</script>
</head>

<body>
<div id="div"></div>
<div id="contextMenu">
<ul>
	<li>领导视窗</li>
    <li>向上钻去</li>
    <li>重点项</li>
    <li>显示隐藏项</li>
</ul>
</div>
</body>
</html>
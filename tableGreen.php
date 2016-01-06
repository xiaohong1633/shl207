<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>单行数据模式</title>
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
	.table-bordered > tbody > tr > td.ptd{
		padding:0;	
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
	table.table{
		border-collapse: separate !important;
		border-spacing:2px;
		font-family: "Trebuchet MS",sans-serif !important;
		font-size: 16px !important;
		font-style: normal !important;
		/*font-weight: bold !important;*/
		line-height: 1.4em !important;	
	}
	table th{
		background: rgba(0, 0, 0, 0) -moz-linear-gradient(center bottom , rgb(123, 192, 67) 2%, rgb(139, 198, 66) 51%, rgb(158, 217, 41) 87%) repeat scroll 0 0 !important;
		background-color:rgb(157,217,41);
		border-color: #93ce37 #93ce37 #9ed929 !important;
		border-image: none !important;
		border-style: solid !important;
		border-top-left-radius: 5px !important;
		border-top-right-radius: 5px !important;
		border-width: 1px 1px 3px !important;
		color: #fff !important;
		padding: 15px !important;
		text-shadow: 1px 1px 1px #568f23 !important;	
	}
	table td{
		background-color: #def3ca !important;
		border: 1px solid #e7efe0 !important;
		border-radius: 10px !important;
		color: #666 !important;
		padding: 0px !important;
		text-align: center !important;
		text-shadow: 1px 1px 1px #fff !important;	
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
	$.post("/shl207/data2.json",{
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>表格JQUERY插件</title>
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
		/*border-bottom: 6px solid #ffffff;
   		border-left: 6px solid transparent;
    	border-right: 6px solid transparent;*/
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
	table {
	font-family: verdana,arial,sans-serif !important;
	font-size:12px !important;
	color:#333333 !important;
	border-width: 1px !important;
	border-color: #666666 !important;
	border-collapse: collapse !important;
	;
	}
	table th {
		border-width: 1px !important;
		padding: 8px !important;
		border-style: solid !important;
		border-color: #666666 !important;
		background-color: #dedede !important;
		;
	}
	table td {
		border-width: 1px !important;
		padding: 0px !important;
		border-style: solid !important;
		border-color: #666666 !important;
		background-color: #ffffff !important;
		;
	}
	td:hover{
		background-color:#d4e3e5 !important;
	}
	.pager li   a, .pager li   span{
		-moz-border-radius: 15px;
		-webkit-border-radius: 15px;
		border-radius: 15px;
		position:relative;
		z-index:2;
		/*behavior: url(ie-css3.htc);*/
	}	
</style>
<!--[if lte IE 7]>
<style>
.pager{    
	height:32px;
	line-height:32px;
}
.pager li  a, .pager li span {
	color: #337ab7;
    background-color: #fff;
    border: 0px solid #ddd;
    display: inline-block;
    padding: 5px 14px;
}

#pageNum,#maxPage{
	top:4px;
}
#pageSkip,#pagePrevious,#pageNext{
	position:relative;
	top:7px;
}
</style>
<![endif]-->
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
	//打印测试
	/*for(var key in PArray){
		alert(key+": "+PArray[key]);
	}*/
}
//alert(getQueryParameter('name'));
//return false;
$(function(){
	
	//$.getJSON("http://localhost:8899/JSONP/JSONPServlet",{
	//第一种方式
	/*$.getJSON("http://192.168.1.109:8080/ReportSystem/zonghe_latn.action",{
		id:11
	},function(data,textState,xhr){
		var mjson = eval(data);
		$("#div").table(mjson);
	});*/
	//$.post("/shl207/mytable/data2.json",{ //本机
	$.post("/shl207/data2.json",{	//服务器
		id:1
	},function(data){
		//alert(data);
		$("#div").table(data);
	});
//设置同步请求获取数据第二种方式
/*$.ajax({
	//url:"http://localhost:8899/JSONP/JSONPServlet",
	url:"http://localhost:8080/mytable/data.json",
	type:"POST",
	timeout:100000,
	async:false,
	cache:true,
	data:{
		id:'报表ID',
		vistor:'vistorID'	
	},	
	dataType:"json",
	beforeSend:function(xhr){
		//xhr.setRequestHeader("beforeSend:","hahhahaha");
		//alert("设置请求头！");	
		xhr.setRequestHeader("Access-Control-Allow-Origin", "*");
	},
	complete:function(xhr,textStatus){
		//alert("complete this:");
		
		//alert(this.url);
		console.log(this);	
	},
	success:function(data,textStatus){
		//alert("返回的数据是："+data);
		//alert("返回的描述状态字符串："+textStatus);
		var mjson = eval(data);	
		$("#div").table(mjson);
	},
	error:function(xhr,textStatus,errorThrown){
		alert("错误信息："+textStatus);
		alert("错误信息："+errorThrown);
		//alert("this:"+this);
	},
	contentType:"application/x-www-form-urlencoded",
	dataFilter:function(data,type){
		//不做数据处理
		//alert("过滤信息！");
		return data;	
	},
	global:true,
	ifModified:false,
	jsonp:"jsonpCallback",
	//username:"xiaohong",
	//password:"111",
	processData:true,
	scriptCharset:"utf-8"
});*/
/*//缓存解决方案！
var ls = window.localStorage;
ls.clear();
ls.setItem('num1',json);
var mjson = eval(json);*/
/*var mmjson = '<php echo $json; ?>';
var mjson = eval(mmjson);
//console.log(mjson);
$("#div").table(mjson);*/
/*var myArray=new Array();    ///数组
              myArray[0]="fgh";
              myArray[1]="rt";
              myArray[2]="xc";
              var arrString=JSON.stringify(myArray);    //将myArray对象转化为字符串
              alert(arrString);    //["fgh","rt","xc"]
              var obj1=eval("("+arrString+")");    ///eval将字符串转为json对象，注意在两边加上"("和")"
             alert("数组第1个元素"+obj1[0]);*/
/*$("body").off("click","#div").on("click","#div",function(e){
        alert("hello world!");
		});*/	
		//debugger;
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
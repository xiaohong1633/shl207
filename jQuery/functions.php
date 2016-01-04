<?php
	// 设置此页面的过期时间(用格林威治时间表示)，只要是已经过去的日期即可。 
	header ( " Expires: Mon, 26 Jul 1970 05:00:00 GMT " );
	  // 设置此页面的最后更新日期(用格林威治时间表示)为当天，可以强制浏览器获取最新资料
	 header ( " Last-Modified:" . gmdate ( " D, d M Y H:i:s " ). "GMT " );
	  
	 // 告诉客户端浏览器不使用缓存，HTTP 1.1 协议
	  header ( " Cache-Control: no-cache, must-revalidate " );
	  
	  // 告诉客户端浏览器不使用缓存，兼容HTTP 1.0 协议
	 header ( " Pragma: no-cache " );
 	function generateJsonData(){
		//echo "开始时间".time();
		$result = array();
		for($i = 0;$i<2000;$i++){
			$array = array();
			$array['地区编号']=$i;
			$array['地区名称']='AS00'.$i;
			$array['指标代码']=array("AN001","AN002","AN003","AN004");
			$array['指标名称']=array("移动语音","C网语音","固网语音","3G流量");
			$result[] = $array;				
		}	
		return json_encode($result);
	}
	/*$file = fopen("./data.json","w+");
	fwrite($file,generateJsonData());
	fclose($file);
	echo "write successfully!";*/
	/*$json = generateJsonData();
	$strLength = strlen($json);
	if($strLength){
		echo "json长度：".ceil($strLength/(1024*1024))." mb";
		echo "结束时间：".time();	
	}*/
	function msrand(){
		for($i=0;$i<10;$i++){
			$seed = mt_rand(0,1095);	
			//echo "seed:".$seed;
			$file = fopen("D:/hell.txt",'a+');
			if(!$file){
				echo "文件打开失败！";
				return ;	
			}
			fwrite($file,"第一个要查的指标行数是：".$seed."\r\n");
			fclose($file);
		}
		echo "生成成功！";
	}
	//msrand();
?>
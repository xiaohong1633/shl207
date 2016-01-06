<?php
	class DRAW{
		private $angle_step = 3;
		//private $font_used = "c:/windows/fonts/simhei.ttf";
		private $font_used = "../tt/simsun.ttc";
		function __construct(){}
		//获取$clr对应的暗色
		public function draw_getDarkColor($img,$clr){
			$rgb = imagecolorsforindex($img, $clr);
			return array($rgb["red"]/2,$rgb["green"]/2,$rgb["blue"]/2);
		}
		//求角度对应的坐标
		public function draw_getexy($a,$b,$d){
			$d = deg2rad($d);
			$arrayXY = array(round($a*Cos($d)),round($b*Sin($d)));
			//var_dump($arrayXY);
			return $arrayXY;
		}
		//画椭圆弧函数
		public function draw_arc($img,$ox,$oy,$a,$b,$sd,$ed,$clr){
			$n = ceil(($ed-$sd)/$this->angle_step);
			$d = $sd;
			//这下面要注意，$x0,$y0有可能有负数
			list($x0,$y0) = $this->draw_getexy($a,$b,$d);
			for($i = 0 ;$i < $n;$i++){
				$d = ($d+$this->angle_step)>$ed?$ed:($d+$this->angle_step);
				list($x,$y) = $this->draw_getexy($a,$b,$d);
				imageline($img, $x0+$ox, $y0+$oy, $x+$ox, $y+$oy, $clr);
				$x0 = $x;
				$y0 = $y;
			}
			//imagepng($img);
		}
		//画扇面
		public function draw_sector($img,$ox,$oy,$a,$b,$sd,$ed,$clr){
			$n = ceil(($ed-$sd)/$this->angle_step);
			$d = $sd;
			list($x0,$y0) = $this->draw_getexy($a,$b,$d);
			imageline($img,$x0+$ox,$y0+$oy,$ox,$oy,$clr);
			for($i = 0;$i<$n;$i++){
				$d = ($d+$this->angle_step)>$ed?$ed:($d+$this->angle_step);
				list($x,$y) = $this->draw_getexy($a,$b,$d);				
				imageline($img, $x+$ox, $y+$oy, $x0+$ox, $y0+$oy, $clr);
				$x0 = $x;
				$y0 = $y;
			}
			imageline($img, $x0+$ox, $y0+$oy, $ox, $oy, $clr);
			list($x,$y) = $this->draw_getexy($a/2,$b/2,($sd+$d)/2);
			imagefill($img, $x+$ox, $y+$oy, $clr);
			//imagepng($img);
		}
		//画3D扇面
		public function draw_sector3d($img,$ox,$oy,$a,$b,$v,$sd,$ed,$clr){
			//echo "crl:".$clr;
			$this->draw_sector($img,$ox,$oy,$a,$b,$sd,$ed,$clr);
			//echo "<br/>";
			if($sd<180){
				//echo "<br>darkcolor<br/>";
				list($R,$G,$B) = $this->draw_getDarkColor($img,$clr);
				$clr = imagecolorallocate($img, $R, $G, $B);
				//echo " darkcolor:".$clr;
				if($ed>180) $ed = 180;
				list($sx,$sy) = $this->draw_getexy($a,$b,$sd);
				$sx+=$ox;
				$sy+=$oy;
				list($ex,$ey) = $this->draw_getexy($a,$b,$ed);
				$ex+=$ox;
				$ey+=$oy;
				//$color = imagecolorallocate($img, 200, 100, 100);
				//
				//$var = imageline($img, 12, 12, 788, 788, $color);
				//echo "<br/>sx:$sx sy:$sy v:$v ex:$ex ey:$ey var:$var<br/>";
				imageline($img, $sx, $sy, $sx, $sy+$v, $clr);
				imageline($img, $ex, $ey, $ex, $ey+$v, $clr);
				$this->draw_arc($img,$ox,$oy+$v,$a,$b,$sd,$ed,$clr);
				list($sx,$sy) = $this->draw_getexy($a,$b,($sd+$ed)/2);
				$sy += $oy+$v/2;
				$sx += $ox;
				imagefill($img,$sx,$sy,$clr);
			}
			//imagepng($img);
		}
		//画图形
		public function drawPie($array,$a,$b,$v,$font=12){
			if(!count($array)){
				//header("")
				echo("无数据需要展现");
				return 0;
			}
			$n = count($array);
			$total = 0;
			for($i = 0;$i<$n;$i++){
				if(!is_numeric($array[$i]['value'])){
					echo "第$i项不是数字,请修正重试！";
					return ;
				}
				$total += $array[$i]['value'];
			}
			$fw = imagefontwidth($font);
			$fh = imagefontheight($font);
			$width = 2*$a + 20;
			$height = $b*2+20+$n*($font+4)+$v;
			$img = imagecreatetruecolor($width, $height);
			$bg = imagecolorallocate($img, 255, 255, 255);
			$textColor = imagecolorallocate($img, 0, 0, 0);
			imagefill($img, 0, 0, $bg);
			$st = 0;
			$et = 0;
			$ox = $a+10;
			$oy = $b+10;
			$fontx = 0;
			$fonty = 0;
			for($k = 0;$k<$n;$k++){
				//首先是从0度开始画，若超过180则调用扇面，不超过则调用3D面
				//需要注意的是，在交界除的情况处理
				//1计算百分比弧度
				$object = $array[$k];
				
				$arc = round(($object['value'])/$total*360);
				//echo "arc:".round(($object['value'])/$total*360);
				$st = $et;
				$et = $et + $arc;
				//echo "<br/>color:".$object['color']."<br/>";
				if($st>=180){
					//echo "st >180 zhixing st:$st et:$et total:$total<br/> ";
					$this->draw_sector($img,$ox,$oy,$a,$b,$st,$et,$object['color']);
				}else if ($et<=180) {
					# code...
					//echo "et <180 zhixing st:$st et:$et total:$total arc:$arc<br/>";
					$this->draw_sector3d($img,$ox,$oy,$a,$b,$v,$st,$et,$object["color"]);
				}else{
					//echo "else >180 zhixing st:$st et:$et<br/>";
					$this->draw_sector3d($img,$ox,$oy,$a,$b,$v,$st,180,$object["color"]);
					$this->draw_sector($img,$ox,$oy,$a,$b,180,$et,$object['color']);
				}
				//写字啦啦啦
				$fontx = 10;
				$fonty = 2*$b+10+$v+$k*($font+4);
				$percent = round(($object['value']/$total/100)*10000)."%";
				$temp = $object['name'].":".$object['value']."($percent)";
				$str = iconv('utf-8', 'utf-8', $temp);
				imagefilledrectangle($img, $fontx, $fonty, $fontx+$fw, $fonty+$fh, $object['color']);
				imagettftext($img, $font, 0, $fontx+$fw+4, $fonty+$fh, $textColor, $this->font_used,$str);
			}
			imagepng($img);			
		}
	}

	//测试语句
	$img = imagecreatetruecolor(800, 800);
	$bk = imagecolorallocate($img, 255, 255, 255);
	$color = imagecolorallocate($img, 200, 100, 100);
	imagefill($img, 0, 0, $bk);
	header("Content-type:image/png");
	

	$draw = new DRAW();
	//var_dump($draw->draw_getDarkColor($img,0xffffff));
	//var_dump($draw->draw_getexy(400,300,100));
	//$draw->draw_arc($img,400,400,400,300,20,160,$color);
	//$draw->draw_sector($img,400,400,400,300,23,234,$color);
	$dataArray = array(
		array("name"=>"江苏省","value"=>12,"color"=>0x99ff00),
		array("name"=>"安徽省","value"=>32,"color"=>0xff6666),
		array("name"=>"福建省","value"=>11,"color"=>0x0099ff),
		array("name"=>"广东省","value"=>43,"color"=>0xff99ff),
		array("name"=>"广西省","value"=>54,"color"=>0xffff99),
		array("name"=>"河北省","value"=>16,"color"=>0x99ffff),
		array("name"=>"山东省","value"=>10,"color"=>0xff3333),
		array("name"=>"山西省","value"=>92,"color"=>0x009999),
		array("name"=>"陕西省","value"=>42,"color"=>0xffff00),
		);
	//$draw->draw_sector3d($img,400,400,400,300,20,18,312,0x009999);
	$draw->drawPie($dataArray,400,300,14);
	//imagefilledarc($img, 50, 50, 100, 50, 75, 360 , 0xffff00, IMG_ARC_PIE);
	//imagepng($img);
?>
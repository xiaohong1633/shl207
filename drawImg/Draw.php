<?php
	class Draw{
		public $angle_step = 3; //定义画椭圆弧度时的角度步长。
		public $font_used = "c:/windows/fonts/simhei.ttf";
		function __construct(){}
		//获取$clr对应的暗色。
		public function draw_getdarkcolor($img,$clr){
			$rgb = imagecolorsforindex($img, $clr);
			return array($rgb["red"]/2,$rgb["green"]/2,$rgb["blue"]/2);
		}
		/**
		 * 求角度$d对应的椭圆上的点坐标,$a为横轴长，$b为纵轴长。
		 * 先通过百分比得到弧度，再通过得到的弧度得出终点坐标。
		 * 最后将图形画上去。
		 * @param  [type] $a [椭圆横轴长]
		 * @param  [type] $b [椭圆纵轴长]
		 * @param  [type] $d [要画的弧度]
		 * @return [type]    [反回弧度终点的坐标]
		 */
		public function draw_getexy($a,$b,$d){
			$d = deg2rad($d);
			return array(round($a*Cos($d)),round($b*Sin($d)));
		}
		/**
		 * 画椭圆弧函数
		 * @param  [type] $img [description]
		 * @param  [type] $ox  [description]
		 * @param  [type] $oy  [description]
		 * @param  [type] $a   [description]
		 * @param  [type] $b   [description]
		 * @param  [type] $sd  [description]
		 * @param  [type] $ed  [description]
		 * @param  [type] $clr [description]
		 * @return [type]      [description]
		 */
		public function draw_arc($img,$ox,$oy,$a,$b,$sd,$ed,$clr){
			//得到需要画的步数
			$n = ceil(($ed-$sd)/$this->angle_step);
			$d = $sd;
			//得到终点的xy坐标
			list($x0,$y0) = $this->draw_getexy($a,$b,$d);
			for($i = 0;$i<$n;$i++){
				$d = ($d+$this->angle_step)>$ed?$ed:($d+$this->angle_step);
				list($x,$y) = $this->draw_getexy($a,$b,$d);
				imageline($img,$x0+$ox,$y0+$oy,$x+$ox,$y+$oy,$clr);
				$x0 = $x;
				$y0 = $y;
			} 
			//imagepng($img);
		}
		/**
		 * [drew_sector description]
		 * @param  [type] $img [description]
		 * @param  [type] $ox  [description]
		 * @param  [type] $oy  [description]
		 * @param  [type] $a   [description]
		 * @param  [type] $b   [description]
		 * @param  [type] $ed  [description]
		 * @param  [type] $clr [description]
		 * @return [type]      [description]
		 */
		public function draw_sector($img,$ox,$oy,$a,$b,$sd,$ed,$clr){
			$n = ceil(($ed-$sd)/$this->angle_step);
			$d = $sd;
			list($x0,$y0) = $this->draw_getexy($a,$b,$d);
			imageline($img, $x0+$ox, $y0+$oy, $ox, $oy, $clr);
			for($i = 0;$i < $n;$i++){
				$d = ($d + $this->angle_step)>$ed?$ed:($d+$this->angle_step);
				list($x,$y) = $this->draw_getexy($a,$b,$d);
				imageline($img, $x0+$ox, $y0+$oy, $x+$ox, $y+$oy, $clr);
				$x0 = $x;
				$y0 = $y;
			}
			imageline($img, $x0+$ox, $y0+$oy, $ox, $oy, $clr);
			list($x,$y) = $this->draw_getexy($a/2,$b/2,($d+$sd)/2);
			imagefill($img, $x+$ox, $y+$oy, $clr);
			//imagepng($img);
		}
		//3d扇面， 需要注意的是，php的角度和数学象限角度不一样。是顺时针计算的。
		public function draw_sector3d($img,$ox,$oy,$a,$b,$v,$sd,$ed,$clr){
			$this->draw_sector($img,$ox,$oy,$a,$b,$sd,$ed,$clr);
			if($sd<180){
				list($R,$G,$B) = $this->draw_getdarkcolor($img,$clr);
				$clr = imagecolorallocate($img, $R, $G, $B);
				if($ed>180) $ed = 180;
				list($sx,$sy) = $this->draw_getexy($a,$b,$sd);
				$sx += $ox;
				$sy += $oy;
				list($ex,$ey) = $this->draw_getexy($a,$b,$ed);
				$ex += $ox;
				$ey += $oy;
				imageline($img,$sx,$sy,$sx,$sy+$v,$clr);
				imageline($img, $ex, $ey, $ex, $ey+$v, $clr);
				//echo "sd:".$sd."<br/>"."ed:".$ed;
				$this->draw_arc($img,$ox,$oy+$v,$a,$b,$sd,$ed,$clr);
				list($sx,$sy) = $this->draw_getexy($a,$b,($sd+$ed)/2);
				$sy += $oy+$v/2;
				$sx += $ox;
				imagefill($img,$sx,$sy,$clr);
			}
			imagepng($img);
		}
		//获取索引色
		public function draw_getindexcolor($img,$clr){
			$R = ($clr>>16) & 0xff;
			$G = ($clr>>8) & 0xff;
			$B = ($clr) & 0xff;
			return imagecolorallocate($img, $R, $G, $B);
		}
		//画图主函数
		public function draw_img($datLst,$labLst,$clrLst,$a=200,$b=90,$v=20,$font=10){
			$ox = 5+$a;
			$oy = 5+$b;
			//取得字体的宽度和高度
			$fw = imagefontwidth($font);
			$fh = imagefontheight($font);
			$n = count($datLst);
			$w = 10+$a*2;
			$h = 10+$b*2+$v+($fh+2)*$n;
			//echo "h:".$h." w:".$w;
			$img = imagecreatetruecolor($w, $h);
			//RGB转为索引色
			for($i = 0;$i<$n;$i++){
				$clrLst[$i] = $this->draw_getindexcolor($img,$clrLst[$i]);
			}
			$clrbk = imagecolorallocate($img, 0xff, 0xff, 0xff);
			$clrt = imagecolorallocate($img, 0x00, 0x00, 0x00);
			//填充背景色
			imagefill($img, 0, 0, $clrbk);
			//求和
			$total = 0;
			for($i=0;$i<$n;$i++){
				$total+=$datLst[$i];
			}
			$sd = 0;
			$ed = 0;
			$ly = 10+$b*2+$v;
			//echo "n:".$n;
			//开始画饼
			for($i=0;$i<$n;$i++){
				$sd = $ed+1;
				$ed += ($datLst[$i]/$total)*360;
				//echo "<br/>sd:".$sd." ed:".$ed." $clrLst[$i]<br/>";
				//画圆饼
				//echo '<br/>$this->draw_sector3d($img,'.$ox.','.$oy.','.$a.','.$b.','.$v.','.$sd.','.$ed.','.$clrLst[$i].')<br/>';
				$this->draw_sector3d($img,$ox,$oy,$a,$b,$v,$sd,$ed,$clrLst[$i]);
				//$this->draw_sector3d($img,$ox,$oy,$a,$b,$v,10,340,$clrLst[$i]);
				//echo "";
				//画标签
				imagefilledrectangle($img, 5, $ly, 5+$fw, $ly+$fh, $clrLst[$i]);
				imagerectangle($img, 5, $ly, 5+$fw, $ly+$fh, $clrt);
				//写入汉字
				$str = iconv("utf-8","utf-8",$labLst[$i]);
				$percent = round(($datLst[$i]/$total)/100*10000)."%";				
				$dastr = $str.":".$datLst[$i]."(".$percent.")";
				//echo "<br/>percent:".$percent." datlist:".$datLst[$i]." dastr:".$dastr."x:".(5+2*$fw)." y:".($ly+13)."<br/>";
				imagettftext($img,$font,0,5+2*$fw,$ly+13,$clrt,$this->font_used,$dastr);
				$ly += $fh+2;
				//echo "<br/>第".$i."个扇区<br/>";
			}  
			imagepng($img); 
		}
		
}
	header("Content-type:image/png");
	//$img = imagecreatetruecolor(800, 800);
	//$cor = imagecolorallocate($img, 0, 255, 255);
	//imagefill($img, 0, 0, $cor);
	$draw = new Draw();
	//$draw->draw_arc($img,400,400,300,200,20,340,0x009999);
	//$draw->drew_sector($img,400,400,300,200,20,340,0x009999);
	//$draw->draw_sector3d($img,400,400,300,200,20,72,120,0x009999);
	$datLst = array(30, 20, 20, 20, 10, 20, 10, 20); //数据 
	$labLst = array("浙江省", "广东省", "上海市", "北京市", "福建省", "江苏省", "湖北省", "安徽省"); //标签 
	$clrLst = array(0x99ff00, 0xff6666, 0x0099ff, 0xff99ff, 0xffff99, 0x99ffff,  0x009999,0xff3333); 
	//画图 
	$draw->draw_img($datLst,$labLst,$clrLst);
?>




















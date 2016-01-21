<?php
	class MyDraw{
		//图片的默认长宽，字体，及背景色。
		private $width=400;
		private $height=300;
		private $font = '../tt/simsun.ttc';
		private $bgColor;
		private $textColor;
		private $fontSize;
		//图片，图片类型
		private $image;
		private $imageType='image/jpeg';
		private $ox=0;
		private $oy=0;
		//数据
		private $data=array();
		private $name='';
		private $xLength;
		private $yLength;
		private $beginData;
		private $fengeData;

		public function __construct($w=400,$h=300,$name='示例图片',$data=array(),$beginData=0,$fengeData=10,$imageType='image/jpeg',$fontSize=14){
			$this->width = $w;
			$this->height = $h;
			$this->imageType = $imageType;
			$this->data = $data;
			$this->fontSize=$fontSize;
			$this->name = $name;
			$this->beginData = $beginData;
			$this->fengeData = $fengeData;
			$this->xLength = round(0.9*$this->width) -15;
			$this->yLength = round(0.8*$this->height);
			//$this->ox = $this->width/10;
			//$this->oy = $this->height/10*9;
			$this->image = imagecreatetruecolor($this->width, $this->height);
			$this->bgColor = imagecolorallocate($this->image,255, 255, 255);
			$this->textColor = imagecolorallocate($this->image, 0, 0, 0);
			//echo $returnColor;
			
			imagefill($this->image, 0, 0, $this->bgColor);
			//imagejpeg($this->image);
		}
		//画横竖坐标轴
		//上面十分之一写标题，左面十分之一标数字，下面十分之一些项目
		public function drawXYAxis(){
			//起始点beginX,beginY
			$beginX = $this->width/10;
			$beginY = $this->height/10*9;
			//if(!$this->ox)
			$this->ox = $beginX;
			//if(!$this->oy)
			$this->oy = $beginY;
			//Y轴终点
			$endX1 = $beginX;
			$endY1 = $this->height/10;
			//X轴终点
			$endX2 = $this->width - 15;
			$endY2 = $beginY;
			//画线喽
			imageline($this->image,$beginX, $beginY, $endX1, $endY1, $this->textColor); 
			imageline($this->image,$beginX, $beginY, $endX2, $endY2, $this->textColor);
			//画角喽
			$this->drawConer($endX1,$endY1,1) ;
			$this->drawConer($endX2,$endY2,4) ;
			$this->drawTitle($this->name,14,1);
			
		}
		//画角函数
		public function drawConer($x,$y,$direction){
			//direction 1234,上下左右
			if($direction == 1){
				$endX1 = $x-5;
				$endY1 = $y+5;
				imageline($this->image, $x, $y, $endX1, $endY1, $this->textColor);
				$endX2 = $x+5;
				$endY2 = $y+5;
				imageline($this->image, $x, $y, $endX2, $endY2, $this->textColor);
			}else if($direction == 2){
				$endX1 = $x-5;
				$endY1 = $y-5;
				imageline($this->image, $x, $y, $endX1, $endY1, $this->textColor);
				$endX2 = $x+5;
				$endY2 = $y-5;
				imageline($this->image, $x, $y, $endX2, $endY2, $this->textColor);
			}else if($direction == 3){
				$endX1 = $x-5;
				$endY1 = $y-5;
				imageline($this->image, $x, $y, $endX1, $endY1, $this->textColor);
				$endX2 = $x-5;
				$endY2 = $y+5;
				imageline($this->image, $x, $y, $endX2, $endY2, $this->textColor);
			}else if($direction == 4){
				$endX1 = $x-5;
				$endY1 = $y-5;
				imageline($this->image, $x, $y, $endX1, $endY1, $this->textColor);
				$endX2 = $x-5;
				$endY2 = $y+5;
				imageline($this->image, $x, $y, $endX2, $endY2, $this->textColor);
			}
		}
		//写柱状图名称
		public function drawTitle($title,$fontSize=14,$zh_cn_flag=0){
			//echo "title:".$title;
			//起始位置
			$length = strlen($title);
			//echo $length;
			$beginX=0;
			if($zh_cn_flag){
				$beginX = $this->width/2 - $length*$fontSize/3;
			}else{
				$beginX = $this->width/2 - $length*$fontSize/2;
			}			
			$beginY = $this->height/10-5;
			$title = iconv('utf-8', 'utf-8', $title);
			imagettftext($this->image, $fontSize, 0, $beginX, $beginY, $this->textColor, $this->font, $title);

		}
		//获取最大值和最小值
		public function getMinMax($data){
			$length = count($data);
			//最小值和最大值
			$min = 0;
			$max = 0;
			for($i=0;$i<$length;$i++){
				$tmpData = $data[$i];
				foreach ($tmpData as $key => $value) {
					if($min){
						if($min>$tmpData['value']){
							$min=$tmpData['value'];
						}
					}else{
						$min = $tmpData['value'];
					}
					if($max){
						if($max<$tmpData['value']){
							$max=$tmpData['value'];
						}
					}else{
						$max = $tmpData['value'];
					}
				}
			}
			return array('min'=>$min,'max'=>$max);
		}
		//标格度画虚线
		public function drawDashLine(){
			$data = $this->data;
			$beginData = $this->beginData;
			$fengeData = $this->fengeData;
			$array = $this->getMinMax($data);
			$min = $array['min'];
			$max = $array['max'];
			if($beginData + $fengeData*10 < $max){
				echo '分割不合适！';
				return 0;
			}else if($beginData>$min){
				echo '分割不合适！';
				return 0;
			}
			for($i=0;$i<10;$i++){
				$beginX = $this->ox;
				$everySpaceY = round(0.8*$this->height-15)/10; 
				$beginY = $this->oy-$everySpaceY*($i+1);
				//在起点左侧写字
				$text = $beginData + ($i+1)*$fengeData;
				$length = strlen($text);
				//echo "length:".$length;
				$textBeginX = $beginX - $length*$this->fontSize;
				$textBeginY = $beginY + $this->fontSize/2;
				
				//echo "length:".$length." text:".$text.'<br/>';
				imagettftext($this->image, $this->fontSize, 0, $textBeginX, $textBeginY, $this->textColor, $this->font,$text);
				//起点终点间画虚线
				$endX = $this->width - 15;
				$endY = $beginY;
				$dashLineColor = imagecolorallocate($this->image, 0xc8, 0xc8, 0xc8);
				$white = imagecolorallocate($this->image, 255, 255, 255);
				$style = array($dashLineColor, $dashLineColor, $dashLineColor, $dashLineColor, $dashLineColor, $white, $white, $white, $white, $white); 
				imagesetstyle($this->image, $style);
				//imageline($img, 20, 20, 500, 20, IMG_COLOR_STYLED); 
				imageline($this->image, $beginX, $beginY, $endX, $endY, IMG_COLOR_STYLED);
			}
		}
		//根据一个值获取所在区间纵坐标
		public function getYAxis($dataValue){
			//该字段和上面分割终点Y坐标一致
			$beginY = $this->oy;
			$endY = 0.8*$this->height-15;
			$everySpaceY = (0.8*$this->height-15)/10;
			$array = $this->getMinMax($this->data);
			if($dataValue < $array['min'] || $dataValue > $array['max']){
				echo "数值越界！";
				return 0;
			}
			for($i=0;$i<10;$i++){
				$nextData = $this->beginData + ($i+1)*$this->fengeData;
				//数据上线
				if($dataValue < $nextData){
					$preData = $nextData - $this->fengeData;
					$percent = ($dataValue - $preData) / $this->fengeData;
					$resultY = $this->oy - ($i+$percent)*$everySpaceY  ;
					return $resultY;
				}
			}
			//
		}
		public function drawTextUnder($text,$beginX,$beginY,$everySpaceX,$direction,$zh_cn_flag=0){
			//$direction 0 水平 1竖直写入
			//$zh_cn_flag 0英文 1 汉语
			//strlen字符串中间位置
			$strlen = 0;
			if($zh_cn_flag == 0){
				$strlen = strlen($text)/2;
			}else if($zh_cn_flag == 1){
				$strlen = strlen($text)/3;
			}
			if($direction == 0){
				$beginX = $beginX-$strlen;
				$beginY = $beginY+$this->fontSize+5;
				imagettftext($this->image, $this->fontSize, 0, $beginX, $beginY, $this->textColor, $this->font, $text);
			}
		}
		//画柱状图并在下面写字
		public function drawColumn(){
			$data = $this->data;
			//首先计算数组长度
			$arrayLength = count($data);
			if(!$arrayLength){
				echo '数组异常！';
				return 0;
			}
			$everySpaceX = (round($this->width*0.9)-15)/(2*$arrayLength + 1);
			for($i=0;$i<$arrayLength;$i++){
				$beginX = $this->ox + $everySpaceX*($i*2+1);
				$beginY = $this->oy;

				$endX = $beginX+$everySpaceX;
				$endY = $this->getYAxis($data[$i]['value']);
				imagefilledrectangle($this->image, $beginX, $beginY, $endX, $endY, $data[$i]['color']);
				imageline($this->image, $beginX, $beginY, $endX, $beginY, $this->textColor);
				//将字体写到对应柱状下面
				$this->drawTextUnder($data[$i]['name'],$beginX,$beginY,$everySpaceX,0,1);
			}

		}
		//根据data数组画柱状图
		public function outPut(){
			//画坐标轴和标题
			$this->drawXYAxis();
			$this->drawColumn();
			//$this->drawDashLine($this->data,$this->beginData,$this->fengeData);
			$this->drawDashLine();			
			header('Content-Type:'.$this->imageType);
			imagejpeg($this->image);
		}
	}

//类外测试

$dataArray = array(
		array("name"=>"江苏省","value"=>12,"color"=>0x99ff00),
		array("name"=>"安徽省","value"=>32,"color"=>0xff6666),
		array("name"=>"福建省","value"=>11,"color"=>0x0099ff),
		array("name"=>"广东省","value"=>43,"color"=>0xff99ff),
		array("name"=>"广西省","value"=>54,"color"=>0xffff99),
		array("name"=>"河北省","value"=>16,"color"=>0x99ffff),
		array("name"=>"山东省","value"=>10,"color"=>0xff3333),
		array("name"=>"山西省","value"=>92,"color"=>0x009999),
		array("name"=>"陕西省","value"=>42,"color"=>0xffff00)
		);
$img = new MyDraw(800,600,'收入分配图',$dataArray,0,10);
$img->outPut();
//echo strlen("我是中国人");

?>
<?php
	//随机产生数组
	function generateData($length,$min,$max){
		$dataArray = array();
		for($i=1;$i<$length;$i++){
			$data = mt_rand($min,$max);
			array_push($dataArray,array('name'=>$i,'value'=>$data));
		}
		return $dataArray;
	}
	//获取最大值和最小值
	function getMinMax($data){
		$length = count($data);
		//最小值和最大值
		$min = 0;
		$max = 0;
		for($i=0;$i<$length;$i++){
			$tmpData = $data[$i];
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
		return array('min'=>$min,'max'=>$max);
	}
	//定义类
	class DrawArc{
		//有关图形的一些定义
		private $image;
		private $wdith;
		private $height;
		private $bgColor;
		private $fontFile='../tt/simsun.ttc';
		private $fontSize;
		private $texColor;
		private $imageType='image/jpeg';
		private $OX;
		private $OY;
		//y轴终点纵坐标
		private $E_Y;
		//x轴终点x坐标
		private $E_X;
		//有关数据的一些属性
		private $name;
		private $data;
		private $min;
		private $max;
		private $beginValueY;
		private $everySetpY;
		private $beginValueX;
		private $everyStepX;
		//构造函数
		function __construct($width=400,$height=300,$name='示例图片',$data=array(),
			$beginValueY=0,$everySetpY=0,$beginValueX=0,$everyStepX=0,
			$imageType='image/jpeg',$fontSize=14){
			$this->width = $width;
			$this->height = $height;
			$this->name = $name;
			$this->data = $data;
			$this->imageType = $imageType;
			$this->fontSize = $fontSize;
			$minMaxArray = getMinMax($this->data);
			$this->min = $minMaxArray['min'];
			$this->max = $minMaxArray['max'];
			$this->beginValueX = $beginValueX;
			$this->beginValueY = $beginValueY;
			$this->everyStepX = $everyStepX;
			$this->everySetpY = $everySetpY;
			$this->image = imagecreatetruecolor($this->width, $this->height);
			$this->bgColor = imagecolorallocate($this->image, 255, 255, 255);
			imagefill($this->image, 0, 0, $this->bgColor);
			$this->textColor = imagecolorallocate($this->image, 0, 0, 0);
		}
		//设置一些参数
		public function setParameter($name,$value){
			if(property_exists('DrawArc', $name)){
				$this->name = $value;
			}else{
				echo "您设定的属性不存在";
				//$this->name = $value;
				return 0;
			}
		}
		//画横纵坐标轴
		public function drawXYAsis($color=0){
			if($color == 0)
				$color = $this->textColor;
			imageline($this->image, $this->OX, $this->OY, $this->OX, $this->E_Y, $color);
			imageline($this->image, $this->OX, $this->OY, $this->E_X, $this->OY, $color);
			$this->drawConer($this->OX,$this->E_Y,1);
			$this->drawConer($this->E_X,$this->OY,4);
			$this->drawTitle(1);
		}
		//画箭头
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
		public function drawTitle($zh_cn_flag=0,$fontSize=0){
			if(!$fontSize)
				$fontSize = $this->fontSize;
			$title = $this->name;
			//起始位置
			$length = strlen($title);
			$beginX=0;
			if($zh_cn_flag){
				$beginX = $this->width/2 - $length*$fontSize/3;
			}else{
				$beginX = $this->width/2 - $length*$fontSize/2;
			}			
			$beginY = $this->height/10-5;
			//将字体转化为utf-8版本的，在不同环境下需要转，我这里转不转都可以
			$title = iconv('utf-8', 'utf-8', $title);
			imagettftext($this->image, $fontSize, 0, $beginX, $beginY, $this->textColor, $this->fontFile, $title);
			//echo 'fontSize:'.$fontSize.' beginx:'.$beginX.' beginY:'.$beginY.' textColor:'.$this->textColor.
			//' fontFile:'.$this->fontFile.' fitle:'.$title;
		}
		//标格度画横虚线
		public function drawDashLine($color=0){
			$data = $this->data;
			$beginData = $this->beginValueY;
			$fengeData = $this->everySetpY;
			$min = $this->min;
			$max = $this->max;
			//分割条数
			$fNum = ceil(($max-$beginData)/$fengeData);
			if($beginData>$min){
				echo '分割不合适！';
				return 0;
			}
			//减去5的作用是在上方留出15个像素
			$everySpaceY = ($this->OY -$this->E_Y-15) / $fNum; 
			for($i=0;$i<$fNum;$i++){
				$beginX = $this->OX;				
				$beginY = $this->OY-$everySpaceY*($i+1);
				//在起点左侧写字
				$text = $beginData + ($i+1)*$fengeData;
				$length = strlen($text);
				//echo "length:".$length;
				$textBeginX = $beginX - $length*$this->fontSize;
				$textBeginY = $beginY + $this->fontSize/2;				
				//echo "textBeginX:".$textBeginX." BeginY:".$beginY.'<br/>';
				imagettftext($this->image, $this->fontSize, 0, $textBeginX, $textBeginY, $this->textColor, $this->fontFile,$text);
				//起点终点间画虚线
				$endX = $this->E_X;
				$endY = $beginY;
				if($color==0){
					$color = imagecolorallocate($this->image, 0xc8, 0xc8, 0xc8);
				}
				$dashLineColor = $color;
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
			$beginY = $this->OY;
			$endY = $this->E_Y+15;
			$everyStepValueY = $this->everySetpY;
			if($dataValue < $this->min || $dataValue > $this->max){
				echo "数值越界！";
				return 0;
			}
			$fNum = ceil(($this->max-$this->beginValueY)/$everyStepValueY);
			$everyStepY = ($beginY - $endY) / $fNum;
			for($i=0;$i<$fNum;$i++){
				$nextData = $this->beginValueY + ($i+1)*$everyStepValueY;
				//数据上线
				if($dataValue < $nextData){
					$preData = $nextData - $everyStepValueY;
					$percent = ($dataValue - $preData) / $everyStepValueY;
					$resultY = $this->OY - ($i+$percent)*$everyStepY;
					return $resultY;
					/*echo 'max:'.$this->max.' beginValueY:'.$this->beginValueY.' everyStepValueY:'.$everyStepValueY.
			' fNum:'.$fNum.' everyStepAxisY:'.$everyStepY.' nextData:'.$nextData.' preData:'.$preData.
			' dataValue:'.$dataValue.' resultY:'.$resultY.'<br/>';*/
				}
			}
		}
		//给一个数组画纵线
		public function drawVerticalLine($data,$color=0){
			//$data = $this->data;
			$color = $this->textColor;
			$dataLength = count($data);
			$everyStepX = ($this->E_X - $this->OX)/($dataLength+1);
			//如果存在键值，表示竖线需要画到一定程度，否则画到顶部
			if(array_key_exists('name', $data[0])||array_key_exists('value', $data[0])){
				//这里有大量的值需要一个一个地画出来
				//判断长度是否大于x轴像素长度
				if($dataLength<($this->E_X - $this->OX)){
					//画一个一个的小长方形
					for($i =0;$i<$dataLength;$i++){
						//起点坐标
						$beginX = $this->OX+$i*$everyStepX;
						$beginY = $this->OY;
						//终点坐标
						$tempData = $data[$i];
						if(@$tempData['color']){
							$color = $tempData['color'];
						}
						//获取值
						$value = $tempData['value'];
						$endX = $beginX+$everyStepX;
						$endY = $this->getYAxis($value);						
						imagefilledrectangle($this->image, $beginX, $beginY, $endX, $endY, $color);
					}
				}else{
					//画带颜色直线
					$everyStepXAxis = ($this->E_X - $this->OX)/$dataLength;
					for($i=0;$i<$dataLength;$i++){
						//起点
						$beginX = $this->OX+($i+1)*$everyStepXAxis;
						$beginY = $this->OY;
						//终点
						$endX = $beginX;
						$tempData = $data[$i];
						if(@$tempData['color']){
							$color = $tempData['color'];
						}
						//获取值
						$value = $tempData['value'];
						$endY = $endY = $this->getYAxis($value);
						imageline($this->image, $beginX, $beginY, $endX, $endY, $color);
					}
				}
				
				
			}else{
				//起点坐标
				$beginX = 0;
				$beginY = $this->OY;
				for($i = 1;$i<=$dataLength;$i++){
					$beginX = $this->OX+$i*$everyStepX;				
					//终点坐标
					$endX = $beginX;
					$endY = $this->E_Y + 15;
					if($color==0){
						$color = imagecolorallocate($this->image, 0xc8, 0xc8, 0xc8);
					}
					$dashLineColor = $color;
					$white = imagecolorallocate($this->image, 0x33, 0x85, 0xff);
					$style = array($dashLineColor, $dashLineColor, $dashLineColor, $dashLineColor, $dashLineColor, $white, $white, $white, $white, $white); 
					imagesetstyle($this->image, $style);
					//imageline($img, 20, 20, 500, 20, IMG_COLOR_STYLED); 
					imageline($this->image, $beginX, $beginY, $endX, $endY, IMG_COLOR_STYLED);
				}
			}			
		}
		//输出图像
		public function outPut(){
			if(!$this->OX){
				$this->OX = 0.1*$this->width;
			}
			if(!$this->E_X){
				$this->E_X = 0.9*$this->width;
			}
			if(!$this->OY){
				$this->OY = 0.9*$this->height;
			}
			if(!$this->E_Y){
				$this->E_Y = 0.1*$this->height;
			}
			$this->drawXYAsis();
			$this->drawDashLine();
			$this->drawVerticalLine($this->data);
			
			header('Content-Type:'.$this->imageType);
			imagejpeg($this->image);
		}

	}
$dataArray = generateData(100,1,99);
$dataArray = array(
		array("name"=>"江苏省","value"=>32,"color"=>0x99ff00),
		array("name"=>"安徽省","value"=>42,"color"=>0xff6666),
		array("name"=>"福建省","value"=>52,"color"=>0x0099ff),
		array("name"=>"广东省","value"=>62,"color"=>0xff99ff),
		array("name"=>"广西省","value"=>72,"color"=>0xffff99),
		array("name"=>"河北省","value"=>82,"color"=>0x99ffff),
		array("name"=>"山东省","value"=>92,"color"=>0xff3333),
		array("name"=>"山西省","value"=>102,"color"=>0x009999),
		array("name"=>"陕西省","value"=>112,"color"=>0xffff00)
		);
for($i=0;$i<800;$i++){
	$num = mt_rand(0,8);
	array_push($dataArray, $dataArray[$num]);
}
//测试代码
//	$width=400,$height=300,$name='示例图片',$data=array(),
//	$beginValueY=0,$everySetpY=0,$beginValueX=0,$everyStepX=0,
//	$imageType='image/jpeg',$fontSize=14
$drImg =  new DrawArc(800,600,'收入分布图',$dataArray,0,10);

$drImg->outPut();
?>
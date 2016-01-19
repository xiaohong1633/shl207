<?php
	class MyDraw{
		//图片的默认长宽，字体，及背景色。
		private $width=400;
		private $height=300;
		private $font = '../tt/simsun.ttc';
		private $bgColor;
		private $textColor;
		//图片，图片类型
		private $image;
		private $imageType='image/jpeg';
		private $ox=0;
		private $oy=0;
		//数据
		private $data=array();

		public function __construct($w=400,$h=300,$imageType='image/jpeg',$data=array()){
			$this->width = $w;
			$this->height = $h;
			$this->imageType = $imageType;
			$this->data = $data;
			$this->image = imagecreatetruecolor($this->width, $this->height);
			$this->bgColor = imagecolorallocate($this->image,255, 255, 255);
			$this->textColor = imagecolorallocate($this->image, 0, 0, 0);


			//echo $returnColor;
			header('Content-Type:'.$imageType);
			imagefill($this->image, 0, 0, $this->bgColor);
			//imagejpeg($this->image);
		}
		//画横竖坐标轴
		//上面十分之一写标题，左面十分之一标数字，下面十分之一些项目
		public function drawXYAxis(){
			//起始点beginX,beginY
			$beginX = $this->width/10;
			$beginY = $this->height/10*9;
			$this->ox = $beginX;
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
			$this->drawTitle('我是中国人',14,1);
			imagejpeg($this->image);
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
				$tmpData = $data[i];
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
		public function drawDashLine($data,$beginData,$fengeData){
			$array = $this->getMinMax($data);
			$min = $array['min'];
			$max = $array['max'];


		}
		//根据data数组画柱状图
		public function drawColumn($data){
			
			
		}
	}

//类外测试
$img = new MyDraw(800,600);
$img->drawXYAxis();


?>
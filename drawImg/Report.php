<?php
class Report{
	public $font = "c:/windows/fonts/simhei.ttf";
	public $imgWidth = 400;
	public $imgHeight = 400;
	public $name = '';
	public $side = '';
	public $bgcor = array(255,255,255);
	public $image = '';
	public $size = 13;
	public $fontA=10;
	public $data = array();

	//构造函数
	public function __construct($width=0,$height=0,$data=array()){
		$this->imgWidth = $width;
		$this->imgHeight = $height;
		$this->data = $data;
	}
	public function setImgName($name){
		$this->name = $name;
	}

	public function setDftColor(){
		imagecolorallocate($this->image, $this->bgcor[0], $this->bgcor[1], $this->bgcor[2]);
	}
	public function GetRndColor(){
		return imagecolorallocate($this->image, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
	}
	//输出函数
	public function outPut(){
		extract($this->getImgSupot(),EXTR_OVERWRITE);
		if($this->save == false){
			header('Content-Type: '.$type);
			$func($this->image);
		}else{
			if(empty($this->name)){
				$this->name = array_keys($this->data);
				$this->name = md5(implode('',$this->name));
			}
			$this->dir = WEBSITE_DIRROOT.$this->dir;
			$this->name = $this->name.'.'.$ext;
			$func($this->image,$this->dir.'/'.$this->name);
		}
	}
	public function getImgSupot(){
		$img_supt = imagetypes();
		if(($img_supt & IMG_GIF) && function_exists('imagegif'))
			return array('type'=>'image/gif','ext'=>'gif','func'=>'imagegif');
		if(($img_supt & IMG_JPG) && function_exists('imagejpeg'))
			return array('type'=>'image/jpeg','ext'=>'jpg','func'=>'imagejpeg');
		if(($img_supt & IMG_PNG) && function_exists('imagepng'))
			return array('type'=>'image/png','ext'=>'png','func'=>'imagepng');
	}
	public function importData($info){
		echo "importData:".empty($this->data).' is_array:'.is_array($this->data);
		empty($this->data) || is_array($this->data) && $this->data = $info;
		var_dump($this->data);
		return 1;
	}
	//画饼
	public function drawPie($name=""){		
		$this->setImgName($name);
		$this->side = $this->imgWidth;
		echo "side: ".$this->side.' empty '.empty($this->data);
		var_dump($this->data);
		if($this->side > 0 && !empty($this->data)){
			echo "hello world! 2";
			$pie_core = $this->side/2;
			$fts_max = 0;
			foreach (array_keys($this->data) as $item) {
				$chk_itm = $item.' '.max($this->data).' 00.00%';
				$fts_box = imagettfbbox($this->size,0, $this->font, $chk_itm);
				$chk_len = $fts_box[4] - $fts_box[6];
				if($chk_len > $fts_max) $fts_max = $chk_len;
			}
			$pie_cAdd = 48 + $fts_max;
			$this->image = imagecreatetruecolor($this->side+$pie_cAdd, $this->side+1);
			if($this->image){
				$this->SetDftColor();
				$dat_sum = array_sum($this->data);
				$arc_beg = array(-180,-90,0,90,180);
				$arc_bet = $arc_beg[array_rand($arc_beg)];

				$fts_add = $this->fontA*2;
				$ftx_beg = $this->side+28;
				$fty_beg = 2;
				$ftx_add = 20;
				$fty_add = 20;
				$fts_cor = imagecolorallocate($this->image, 0, 0, 0);
				$fty_chk = $fty_add * count($this->data);
				while($fty_chk>$this->side + 2){
					$fty_chk = --$fty_add * count($this->data);
				}
				foreach($this->data as $item=>$data){
					$rnd_cor = $this->GetRndColor();
					$arc_pct = number_format($data*100/$dat_sum,2);
					$arc_end = $data*360/$dat_sum+$arc_beg;
					$item = iconv('utf-8','utf-8//ignore',(string)$item);

					imagefilledarc($this->image,$pie_core, $pie_core, $this->side, $this->side, $arc_beg, $arc_end, $rnd_cor, IMG_ARC_PIE);
					imagefilledrectangle($this->image, $ftx_beg, $fty_beg, $ftx_beg+12, $fty_beg+10, $rnd_cor);
					imagettftext($this->image, $this->size, 0, $ftx_beg+$ftx_add, $fty_beg+$this->fontA, $fts_cor, $this->font, 
						$item.' '.$data.' '.$arc_pct.'%');
					$fty_beg += $fty_add;
					$arc_beg = $arc_end;
					$this->outPut();
				}
			}else{
					echo ('画布边长不正确或统计数据为空！');
					exit;
			}
		}
	}
	//测试函数
	public function myTest(){
		$myArray = imagettfbbox(12, 2, $this->font, '我是中国人');
		return $myArray;
	}
}

$w = 640;
$h = 480;
$info = array('春季'=>78,'夏季'=>65,'秋季'=>86,'冬季'=>55);
$obj = new Report(600,600);
if($obj->importData($info)){
	echo "数据加载正确！";
}else{
	echo "数据加载错误！";
};
$obj->drawPie();


//var_dump($obj->myTest());
?>
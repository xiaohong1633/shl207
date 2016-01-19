<?php
class Draw{
	private $width = 0;
	private $height = 0;
	private $font = '../tt/simsun.ttc';
	private $data = array();
	private $fontW,$fontH,$fontA,$fontC;
	private $name='';
	private $unit='';
	private $offset=0,$osSum = 0,$omSum = 0,$xLen=0,$yLen=0,$space=3,$margin=20;
	private $size=9,$side=0,$bgcor=array(255,255,255);
	private $save = false,$dir='./img';
	private $image;
	private $mass = 10;

	public function __construct($width=0,$height=0,$data=array()){
		$this->width = $width;
		$this->height = $height;
		if(!empty($data))$this->data = $data;
		$this->fontW = imagefontwidth(2);
		$this->fontH = imagefontheight(2);
		$this->fontA = imagefontheight($this->size);
		$this->fontA = round($this->fontA/2)+2;
		$this->font = $this->font;
		if(!is_file($this->font)){
			exit('字体文件不存在！');
		}else{
			$this->font = realpath($this->font);
		}
	}
	public function setImgName($name){
		if($name){
			$this->save = true;
			$this->name = $name;
		}		
	}
	public function getMinSize(){
		$fts_len = strlen((string)$this->unit) + 1;
		$this->offset = imagefontwidth(2)*$fts_len;
		$this->osSum = $this->offset + $this->space;
		$this->omSum = $this->offset + $this->margin;
		$this->xLen = $this->width - $this->osSum - $this->omSum;
		$this->yLen = $this->height - $this->osSum - $this->omSum;
		return $this->osSum + $this->omSum;
	}
	public function setDftColor(){
		imagecolorallocate($this->image, $this->bgcor[0], $this->bgcor[1], $this->bgcor[2]);
	}
	//这个是画坐标轴
	public function drawCdtAxes(){
		$min_size = $this->getMinSize();
		if($this->width > $min_size && $this->height > $min_size){
			$this->image = @imagecreate($this->width, $this->height);
			if($this->image){
				$this->setDftColor();
				$cd_cr = imagecolorallocate($this->image, 0, 0, 0);
				$cd_xs = $this->osSum;
				$cd_xe = $this->width - $this->offset;
				$cd_ys = $this->offset;
				$cd_ye = $this->height - $this->osSum;

				imageline($this->image, $cd_xs, $cd_ye, $cd_xe, $cd_ye, $cd_cr);
				imageline($this->image,$cd_xs,$cd_ys,$cd_xs,$cd_ye,$cd_cr);
				imageline($this->image,$cd_xe,$cd_ye,$cd_xe-6,$cd_ye-3,$cd_cr);
				imageline($this->image,$cd_xe,$cd_ye,$cd_xe-6,$cd_ye-3,$cd_cr);
				imageline($this->image,$cd_xs,$cd_ys,$cd_xs-3,$cd_ys+6,$cd_cr);
				imageline($this->image,$cd_xs,$cd_ys,$cd_xs+3,$cd_ys+6,$cd_cr);
			}else{
				exit('画布建立失败！');
			}
		}else{
			exit('画布尺寸设置不正确！');
		}
	}
	public function getImgName(){
		return empty($this->name)?null:WEBSITE_DIRROOT.$this->dir.'/'.$this->name;
	}
	//引入data
	public function importData($data = array()){
		empty($data) || is_array($data) && $this->data = $data;
	}
	//画虚线
	public function setDashLine(){
		$cd_stop = imagecolorallocate($this->image, 255, 255, 255);
		$cd_dash = imagecolorallocate($this->image, 200, 200, 200);
		imagesetstyle($this->image, array($cd_stop,$cd_stop,$cd_stop,$cd_stop,$cd_stop,$cd_stop));
		$this->fontC = imagecolorallocate($this->image, 56, 56, 56);
	}
	public function outPut(){
		extract($this->getImgSupot(),EXTR_OVERWRITE);
		if($this->save == false){
			header('Content-Type:'.$type);
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
	//画柱状图
	public function drawLattice(){
		$this->setDashLine();
		$cdy_each = $this->unit;
		$cdy_each = $this->unit;
		$cdy_yMax = $this->mass * $cdy_each;
		while($cdy_yMax < max($this->data)){
			$cdy_each += $this->unit;
			$cdy_yMax = $cdy_each * $this->mass;
		}
		$this->unit = $cdy_each;
		$cdx_beg = $this->osSum +1;
		$cdx_end = $this->width - $this->omSum;
		$cdy_add = $this->yLen / $this->mass;
		$cdy_beg = $this->omSum;
		for($i = 0;$i<$this->mass;$i++){
			imageline($this->image,$cdx_beg,$cdy_beg,$cdx_end,$cdy_beg,IMG_COLOR_STYLED);
			$fts_txt = (string)$this->unit*($this->mass-$i);
			$ftx_beg = $cdx_beg - $this->fontW * strlen($fts_txt) - $this->space;
			$fty_beg = $cdy_beg - $this->fontH/2;
			imagestring($this->image,2,$ftx_beg,$fty_beg,$fts_txt,$this->fontC);
			$cdy_beg += $cdy_add;
		}
	}
	//获取随机颜色
	public function getRndColor(){
		return imagecolorallocate($this->image, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
	}
	//画柱状图
	public function DrawColumn($name = ""){
		$this->setImgName($name);
		if(!empty($this->data)){
			//这个应该是画坐标轴
			$this->drawCdtAxes();
			$this->drawLattice();
			$cdx_cut = count($this->data);
			$cdx_add = floor($this->xLen/$cdx_cut);
			$cdy_add = $this->yLen / $this->mass;
			imagesetthickness($this->image,floor($cdx_add/2));

			$cdy_end = $this->height - $this->osSum -1;
			$cdx_beg = floor($cdx_add / 2)+$this->osSum;
			foreach($this->data as $item=>$data){
				$cdy_beg = $this->yLen - ($data*$cdy_add / $this->unit) + $this->osSum;
				imageline($this->image,$cdx_beg,$cdy_beg,$cdx_beg,$cdy_end,$this->getRndColor());

				$fts_txt = (string)$data;
				$ftx_beg = $cdx_beg - $this->fontW * strlent($fts_txt) / 2;
				$fty_beg = $cdy_beg - $this->fontH - $this->space;
				imagestring($this->image,2,$ftx_beg,$fty_beg,$fts_txt,$this->fontC);
				$fts_txt = iconv('utf-8','utf-8//IGNORE',(string)$item);
				$fts_box = imagettfbbox($this->size, 0, $this->font, $fts_txt);
				$ftx_beg = $cdx_beg - floor($fts_box[4]-$fts_box[6])/2;
				$fty_beg = $this->height - $this->offset + $this->fontA;
				imagettftext($this->image, $this->size, 0, $ftx_beg, $fty_beg, $this->fontC, $this->font, $fts_txt);
				$cdx_beg += $cdx_add;	
			}
			$this->outPut();
		}else{
			exit('统计数据为空！');
		}
	}
}


	$w = 640; $h = 480; 
    $info = array('春季'=>78, '夏季'=>65, '秋季'=>86, '冬季'=>55);   
    $draw_ins = new Draw($w, $h); 
    //echo "柱状图";
    $draw_ins->ImportData($info); 
    // 柱状图 
    $draw_ins->DrawColumn(); 
    // 线形图 
    //$draw_ins->DrawLine(); 
    // 饼形图 
     //$draw_ins->DrawPie(); 
    unset($draw_ins);

?>





















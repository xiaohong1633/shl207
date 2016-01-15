<?php
class Draw{ 
              public $mass=10, $unit=10, $data=array(), $save=false, $dir='/images/count'; 
              private $width=0, $height=0, $side=0, $bgcor=array(255, 255, 255); 
              private $name, $image, $offset=0, $margin=20, $space=3, $font='../tt/simsun.ttc'; 
              private $fontW, $fontH, $fontC, $fontA, $osSum, $omSum, $xLen, $yLen, $size=9;    
             function __construct($width=0, $height=0, $data=array()) { 
                  $this->width = $width;  
                  $this->height = $height; 
                  if (!empty($data)) $this->data = $data;   
                  $this->fontW = imagefontwidth(2); 
                  $this->fontH = imagefontheight(2); 
                  $this->fontA = imagefontheight($this->size); 
                  $this->fontA = round($this->fontA / 2) + 2; 
   
                  $this->font = $this->font; 
	               if (!is_file($this->font)) { 
	                   exit('字体文件不存在！'); 
	               } else {  
	                   $this->font = realpath($this->font); 
	               } 
	           }   
             public function DrawPie($name="") { 
                 $this->SetImgName($name); 
   
                  $this->side = $this->width; 
                  if ($this->side > 0 && !empty($this->data)) { 
                      $pie_core = $this->side / 2; 
   
                      $fts_max = 0; 
                      foreach (array_keys($this->data) as $item) { 
                          $chk_itm = $item.' '.max($this->data).' 00.00%'; 
                          $fts_box = imagettfbbox($this->size, 0, $this->font, $chk_itm); 
                          $chk_len = $fts_box[4] - $fts_box[6]; 
                          if ($chk_len > $fts_max) $fts_max = $chk_len; 
                      } $pie_cAdd = 48 + $fts_max;  
   
                      $this->image = @imagecreate($this->side + $pie_cAdd, $this->side + 1);  
                      if ($this->image) { 
                             $this->SetDftColor();    
                          $dat_sum = array_sum($this->data); 
                          $arc_beg = array(-180, -90, 0, 90, 180); 
                          $arc_beg = $arc_beg[array_rand($arc_beg)]; 
   
                          $fts_add = $this->fontA * 2; 
                          $ftx_beg = $this->side + 28; $fty_beg = 2; 
                          $ftx_add = 20; $fty_add = 20; 
                          $fts_cor = imagecolorallocate($this->image, 0, 0, 0); 
                          $fty_chk = $fty_add * count($this->data); 
                          while ($fty_chk > $this->side + 2) { 
                                 $fty_chk = --$fty_add * count($this->data); 
                          } 
   
                          foreach ($this->data as $item => $data) { 
                               $rnd_cor = $this->GetRndColor();  
                               $arc_pct = number_format($data * 100 / $dat_sum, 2); 
                               $arc_end = $data * 360 / $dat_sum + $arc_beg; 
                              $item = iconv('utf-8', 'utf-8//ignore', (string)$item); 
  
                              imagefilledarc($this->image, $pie_core, $pie_core, $this->side, $this->side, $arc_beg, $arc_end, $rnd_cor, IMG_ARC_PIE); 
                              imagefilledrectangle($this->image, $ftx_beg, $fty_beg, $ftx_beg + 12, $fty_beg + 10, $rnd_cor); 
                              imagettftext($this->image, $this->size, 0, $ftx_beg + $ftx_add, $fty_beg + $this->fontA, $fts_cor, $this->font, $item.' '.$data.' '.$arc_pct.'%'); 
  
                              $fty_beg += $fty_add; $arc_beg = $arc_end;  
                         }  
   
                          $this->Output(); 
                      } 
                  } else { 
                      exit('画布边长设置不正确或统计数据为空！'); 
                  } 
              } 
   
              public function DrawColumn($name="") { 
                  $this->SetImgName($name); 
   
                  if (!empty($this->data)) { 
                      $this->DrawCdtAxes(); 
                      $this->DrawLattice(); 
   
                      $cdx_cut = count($this->data); 
                      $cdx_add = floor($this->xLen / $cdx_cut);  
                      $cdy_add = $this->yLen / $this->mass; 
                      imagesetthickness($this->image, floor($cdx_add / 2)); 
   
                      $cdy_end = $this->height - $this->osSum - 1; 
                      $cdx_beg = floor($cdx_add / 2) + $this->osSum; 
                      foreach ($this->data as $item => $data) { 
                           $cdy_beg = $this->yLen - ($data * $cdy_add / $this->unit) + $this->omSum; 
                           imageline($this->image, $cdx_beg, $cdy_beg, $cdx_beg, $cdy_end, $this->GetRndColor()); 
   
                           $fts_txt = (string)$data; 
                           $ftx_beg = $cdx_beg - $this->fontW * strlen($fts_txt) / 2; 
                           $fty_beg = $cdy_beg - $this->fontH - $this->space; 
                           imagestring($this->image, 2, $ftx_beg, $fty_beg, $fts_txt, $this->fontC); 
   
                           $fts_txt = iconv('utf-8', 'UTF-8//IGNORE', (string)$item); 
                           $fts_box = imagettfbbox($this->size, 0, $this->font, $fts_txt); 
                           $ftx_beg = $cdx_beg - floor($fts_box[4] - $fts_box[6]) / 2; 
                           $fty_beg = $this->height - $this->offset + $this->fontA;  
                           imagettftext($this->image, $this->size, 0, $ftx_beg, $fty_beg, $this->fontC, $this->font, $fts_txt); 
   
                           $cdx_beg += $cdx_add; 
                      } 
                    
                      $this->Output(); 
                  } else { 
                      exit('统计数据为空！'); 
                  } 
              } 
   
              public function DrawLine($name="") { 
                  $this->SetImgName($name); 
   
                  if (!empty($this->data)) { 
                      $this->DrawCdtAxes(); 
                      $this->DrawLattice(); 
   
                      $dat_idx = 0; 
                      $cd_line = imagecolorallocate($this->image, 255, 0, 0); 
                      $cd_fold = imagecolorallocate($this->image, 0, 0, 255); 
   
                      $cdx_cut = count($this->data) - 1; 
                      $cdx_add = floor($this->xLen / $cdx_cut);  
                      $cdy_add = $this->yLen / $this->mass; 
    
                      $cdx_old = 0; $cdx_beg = $this->osSum; 
                      $cdy_old = 0; $cdy_beg = $this->omSum - 5;  
                      $cdy_end = $this->height - $this->osSum - 1;  
                      foreach ($this->data as $item => $data) { 
                           imagesetthickness($this->image, 1); 
                           $dat_idx > 0 && imageline($this->image, $cdx_beg, $cdy_beg, $cdx_beg, $cdy_end, IMG_COLOR_STYLED); 
                           imagesetthickness($this->image, 2); 
   
                           $cdx_dot = $cdx_beg; 
                           $cdy_dot = $this->yLen - $data * $cdy_add / $this->unit + $this->omSum; 
                           if ($cdx_old > 0 && $cdy_old > 0) {  
                               imageline($this->image, $cdx_old, $cdy_old, $cdx_dot, $cdy_dot, $cd_line); 
                               imagefilledellipse($this->image, $cdx_old, $cdy_old, 6, 6, $cd_fold); 
                           }  
                           imagefilledellipse($this->image, $cdx_dot, $cdy_dot, 6, 6, $cd_fold); 
   
                           $fts_txt = '('.(string)$data.')'; 
                           if ($dat_idx == 0) { 
                               $ftx_beg = $cdx_dot + $this->space + 3; 
                               $fty_beg = $cdy_dot - $this->fontH / 2; 
                           } else { 
                               $ftx_beg = $cdx_dot - $this->fontW * strlen($fts_txt) / 2; 
                               $fty_beg = $cdy_dot - $this->fontH - $this->space * 2; 
                           } 
                           imagestring($this->image, 2, $ftx_beg, $fty_beg, $fts_txt, $this->fontC); 
                             
                           $fts_txt = iconv('utf-8', 'UTF-8', (string)$item); 
                           $fts_box = imagettfbbox($this->size, 0, $this->font, $fts_txt); 
                           $ftx_beg = $cdx_beg - floor($fts_box[4] - $fts_box[6]) / 2; 
                           $fty_beg = $this->height - $this->offset + $this->fontA; 
                           imagettftext($this->image, $this->size, 0, $ftx_beg, $fty_beg, $this->fontC, $this->font, $fts_txt); 
   
                           $dat_idx ++; $cdx_beg += $cdx_add;  
                           $cdx_old = $cdx_dot; $cdy_old = $cdy_dot;  
                      }  
   
                      $this->Output(); 
                  } else { 
                      exit('统计数据为空！'); 
                  } 
              } 
   
              public function GetImgName() { 
                  return empty($this->name) ? null :  
                         WEBSITE_DIRROOT.$this->dir.'/'.$this->name; 
              } 
   
              public function SetImgName($name) { 
                  if (!empty($name)) {  
                      $this->save = true; $this->name = $name; 
                  } 
              } 
   
              public function ImportData($data=array()) { 
                  empty($data) || is_array($data) && $this->data = $data; 
              } 
   
              private function GetMinSize() { 
                  $fts_len = strlen((string)$this->unit) + 1; 
                  $this->offset = imagefontwidth(2) * $fts_len;  
                  $this->osSum = $this->offset + $this->space; 
                  $this->omSum = $this->offset + $this->margin; 
                  $this->xLen = $this->width - $this->osSum - $this->omSum; 
                  $this->yLen = $this->height - $this->osSum - $this->omSum; 
   
                  return $this->osSum + $this->omSum; 
              } 
   
              private function DrawCdtAxes() { 
                  $min_size = $this->GetMinSize(); 
                  if ($this->width > $min_size && $this->height > $min_size) { 
                      $this->image = @imagecreate($this->width, $this->height); 
                      if ($this->image) { 
                          $this->SetDftColor(); 
   
                          $cd_cr = imagecolorallocate($this->image, 0, 0, 0); 
                          $cd_xs = $this->osSum; $cd_xe = $this->width - $this->offset;  
                          $cd_ys = $this->offset; $cd_ye = $this->height - $this->osSum; 
   
                          imageline($this->image, $cd_xs, $cd_ye, $cd_xe, $cd_ye, $cd_cr); 
                          imageline($this->image, $cd_xs, $cd_ys, $cd_xs, $cd_ye, $cd_cr); 
                          imageline($this->image, $cd_xe, $cd_ye, $cd_xe - 6, $cd_ye - 3, $cd_cr); 
                          imageline($this->image, $cd_xe, $cd_ye, $cd_xe - 6, $cd_ye + 3, $cd_cr); 
                          imageline($this->image, $cd_xs, $cd_ys, $cd_xs - 3, $cd_ys + 6, $cd_cr); 
                          imageline($this->image, $cd_xs, $cd_ys, $cd_xs + 3, $cd_ys + 6, $cd_cr); 
                      } else { 
                          exit('画布建立失败！'); 
                      } 
                  } else { 
                      exit('画布尺寸设置不正确！'); 
                  } 
              } 
   
              private function DrawLattice() { 
                  $this->SetDashLine(); 
   
                  $cdy_each = $this->unit; 
                  $cdy_yMax = $this->mass * $cdy_each; 
                  while ($cdy_yMax < max($this->data)) {  
                         $cdy_each += $this->unit;  
                         $cdy_yMax = $cdy_each * $this->mass; 
                  } $this->unit = $cdy_each; 
                  $cdx_beg = $this->osSum + 1;  
                  $cdx_end = $this->width - $this->omSum;  
                  $cdy_add = $this->yLen / $this->mass;  
   
                  $cdy_beg = $this->omSum; 
                  for ($i = 0; $i < $this->mass; $i ++) { 
                       imageline($this->image, $cdx_beg, $cdy_beg, $cdx_end, $cdy_beg, IMG_COLOR_STYLED); 
   
                       $fts_txt = (string)$this->unit * ($this->mass - $i); 
                       $ftx_beg = $cdx_beg - $this->fontW * strlen($fts_txt) - $this->space; 
                       $fty_beg = $cdy_beg - $this->fontH / 2; 
                       imagestring($this->image, 2, $ftx_beg, $fty_beg, $fts_txt, $this->fontC); 
   
                       $cdy_beg += $cdy_add; 
                  } 
              } 
   
              private function SetDftColor() { 
                  imagecolorallocate($this->image, $this->bgcor[0], $this->bgcor[1], $this->bgcor[2]); 
              } 
   
              private function GetRndColor() { 
                  return imagecolorallocate($this->image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)); 
              } 
   
              private function SetDashLine() { 
                  $cd_stop = imagecolorallocate($this->image, 255, 255, 255); 
                  $cd_dash = imagecolorallocate($this->image, 200, 200, 200); 
                  imagesetstyle($this->image, array($cd_stop, $cd_stop, $cd_stop,  
                                                    $cd_dash, $cd_dash, $cd_dash)); 
                  $this->fontC = imagecolorallocate($this->image, 56, 56, 56); 
              } 
   
              private function Output() { 
                  extract($this->GetImgSupot(), EXTR_OVERWRITE); 
   
                  if ($this->save == false) { 
                      header('Content-Type: '.$type); 
                      $func($this->image); 
                  } else { 
                      if (empty($this->name)) { 
                          $this->name = array_keys($this->data); 
                          $this->name = md5(implode('', $this->name)); 
                      } 
                      $this->dir = WEBSITE_DIRROOT.$this->dir; 
                      $this->name = $this->name.'.'.$ext; 
                      $func($this->image, $this->dir.'/'.$this->name); 
                  }  
              } 
   
              private function GetImgSupot() { 
                  $img_supt = imagetypes(); 
                  if (($img_supt & IMG_GIF) && function_exists('imagegif')) 
                      return array('type'=>'image/gif', 'ext'=>'gif', 'func'=>'imagegif'); 
                  if (($img_supt & IMG_JPG) && function_exists('imagejpeg'))  
                      return array('type'=>'image/jpeg', 'ext'=>'jpg', 'func'=>'imagejpeg'); 
                  if (($img_supt & IMG_PNG) && function_exists('imagepng')) 
                      return array('type'=>'image/png', 'ext'=>'png', 'func'=>'imagepng'); 
              } 
   
              function __destruct() { 
                  image_destroy($this->image); 
              } 
        } 
   
        // 使用方法 
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



















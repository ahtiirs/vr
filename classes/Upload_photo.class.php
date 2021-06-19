<?php

//-- new Upload_photo(strint $file_url, [int $max_pic_size_in_bytes]) - Loob uue pildiobjekti muutuja asukohas viidatud failist kui faili puursurus ei ületa teise muutuja väärtust. Vaikimisi piir 5MB
//-- public function resize_photo(int $uus_laius_px, int $uus_kõrgus_px, boolean $säilita_suhe) - muudab objektis oleva pildi suuruse etteantud max suurusega pildiks
//-- public function gen_filename() - genereerin failinime hetke kellaaja põhjal ja lisab sellele sobiva pildilaiendi. Tulemus on muutujas $this->image_new_filename
//-- public function date_to_pic() - lisab objektis olevale pildile originaal pildil oleva EXIF pildistamiskuupäeva
//-- public function save_image_to_file($path) - salvestab objektis oleva pildi eteantud kataloogi objekti loomisel genereeritud nimega. Oma nime kasutada soovides tuleb muutujale $this->image_new_filename anda enne uus väärtus
//-- public function save_orig_image($path) -  salvestab originaalpildifaili etteantud kataloogi kasutades objektis olevat pildifaili nime
//-- public function add_watermark(string $watermark) - lisab objektis levale pildile vesimärgi. Muutujas peab olema viide vesimärgi failile  


    class Upload_photo {

        private $photo_to_upload;
        private $image_file_type;
        private $temp_image;
        private $image_size;
        private $photo_date;
        private $new_temp_image; 
        public $image_new_filename;
        public $error; 

        function __construct($photo_to_upload,$image_max_size = 5 * 1024 * 1024){
            $this->photo_to_upload = $photo_to_upload;
            $this->image_size = $image_max_size;
            $this->get_image_type();
            if (empty($this->error)) $this->is_size_ok($this->image_size);
            if (empty($this->error)) $this->temp_image = $this ->create_image_from_file($this->photo_to_upload["tmp_name"],$this->image_file_type) ;
            $this->gen_filename();
        }
//--------------------- 
        private function create_image_from_file($image,$image_file_type){

            $temp_image = null;
            if($image_file_type == "jpg"){
                $temp_image = imagecreatefromjpeg($image);
            }
            if($image_file_type == "png"){
                $temp_image = imagecreatefrompng($image);
            }
            return $temp_image;
        }
//---------------------
        private function get_image_type(){
            $check = getimagesize($this->photo_to_upload["tmp_name"]);
            if($check !== false){
                if($check["mime"] == "image/jpeg"){
                    $this->image_file_type = "jpg";
                } elseif ($check["mime"] == "image/png"){
                    $this->image_file_type = "png";
                } else {
                    $this->error = "Pole sobiv formaat! Ainult jpg ja png on lubatud! ";
                }
            } else {
                $this->error = "Tegemist pole pildifailiga! ";
            }
        }
//---------------------
        public function gen_filename(){
			$timestamp = microtime(1) * 10000;
			$this->image_new_filename = $timestamp ."." .$this->image_file_type;
        }
//---------------------
        private function is_size_ok($max,$min = 0){
            if ($this->photo_to_upload["size"] > $max) {
                $this->error = "Pilt on lubatust mahukam! ";  
            }
        }
//---------------------
        public function resize_photo($image_max_w, $image_max_h, $keep_ratio) {
			
            $image_w = imagesx($this->temp_image);
            $image_h = imagesy($this->temp_image);
   
            if ($keep_ratio){ 
                if($image_w / $image_max_w > $image_h / $image_max_h){
                    $image_size_ratio = $image_w / $image_max_w;
                } else {
                    $image_size_ratio = $image_h / $image_max_h;
                }
    
                $image_new_w = round($image_w / $image_size_ratio);
                $image_new_h = round($image_h / $image_size_ratio);
    
                $this->new_temp_image  = imagecreatetruecolor($image_new_w, $image_new_h);
                imagecopyresampled($this->new_temp_image , $this->temp_image, 0, 0, 0, 0, $image_new_w, $image_new_h, $image_w, $image_h);
    
            } else {
                if($image_h<$image_w){
                    // Landscape picture
                    $src_x = ($image_w - $image_h) /2;
                    $src_width = $image_h;
                    $src_y = 0;
                    $src_height = $image_h;

                } else {
                    // Portrait picture
                    $src_x = 0;
                    $src_width = $image_w;
                    $src_y = ($image_h - $image_w) /2;
                    $src_height = $image_w;
                }
    
                $image_new_w = $image_max_w;
                $image_new_h = $image_max_h;
            
                $this->new_temp_image = imagecreatetruecolor($image_new_w, $image_new_h);
                imagecopyresampled($this->new_temp_image, $this->temp_image, 0, 0, $src_x, $src_y, $image_new_w, $image_new_h, $src_width, $src_height);
            }
        }

    //--------------------- lisab fotole kirja paremasse ülaserva suunaga allapoole pildistamise kuupäevaga
        public function date_to_pic(){

            @$exif = exif_read_data($this->photo_to_upload["tmp_name"], "ANY_TAG", 0, true);

            if(!empty($exif["DateTimeOriginal"])){
                $this->photo_date = $exif["DateTimeOriginal"];

                $fontName= "../images/arialbd.ttf";
                $y = 20;
                $size = 20;
                $text_color = imagecolorallocatealpha($this->new_temp_image, 255,255,255, 60);//valge, 60% alpha
                imagettftext($this->new_temp_image, $size, -90, 15, $y, $text_color, $fontName, $this->photo_date);

            } else {
                $this->photo_date = NULL;
            }
        }

//---------------------
        public function save_image_to_file($path){
                $notice = null;
                $target = $path.$this->image_new_filename;

                if($this->image_file_type == "jpg"){
                    if(imagejpeg($this->new_temp_image, $target, 90)){
                        
                    } else {
                        $this->error = "Faili ei õnnestunud salvestada! ";
                    }
                }
                if($this->image_file_type == "png"){
                    if(imagepng($this->new_temp_image, $target, 6)){
                       
                    } else {
                        $this->error = "Faili ei õnnestunud salvestada! ";
                    }
                }
                imagedestroy($this->new_temp_image);
                return $notice;
        }
//---------------------        
        public function save_orig_image($path){
            $target = $path.$this->image_new_filename;
            if(!move_uploaded_file($this->photo_to_upload["tmp_name"], $target)){
                $this->error = "Originaalpildi üleslaadimine ebaõnnestus! ";  
            }
        }
//---------------------
        public function add_watermark($watermark){
            $watermark_file_type = strtolower((pathinfo($watermark,PATHINFO_EXTENSION)));
            $watermark_image = $this->create_image_from_file($watermark, $watermark_file_type);
            $watermark_w = imagesx($watermark_image);
            $watermark_h = imagesy($watermark_image);
            $watermark_x = imagesx($this->new_temp_image) - $watermark_w - 10;            
            $watermark_y = imagesy($this->new_temp_image) - $watermark_h - 10;
            imagecopy($this->new_temp_image,$watermark_image,$watermark_x,$watermark_y,0,0,$watermark_w,$watermark_h);
            imagedestroy($watermark_image);
        }


    } //class lõppeb
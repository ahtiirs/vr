<?php
	require_once "usesession.php";
	require_once "../../../conf.php";
	require_once "fnc_general.php";
	require_once "fnc_user.php";
	require_once "classes/clean_input.php";
	
	$photo_upload_error = null;
	$image_file_type = null;
	$image_file_name = null;
	$file_name_prefix = "vr_";
	$file_size_limit = 1 * 1024 * 1024;
	$image_max_w = 600;
	$image_max_h = 400;

	if(isset($_POST["photo_submit"])){
		//var_dump($_POST);
		//var_dump($_FILES);
		//kas üldse on pilt
		$check = getimagesize($_FILES["file_input"]["tmp_name"]);

		if($check !== false){

			//kontrollime, kas aktepteeritud failivorming ja fikseerime laiendi
			if($check["mime"] == "image/jpeg"){
				$image_file_type = "jpg";
			} elseif ($check["mime"] == "image/png"){
				$image_file_type = "png";
			} else {
				$photo_upload_error = "Pole sobiv formaat! Ainult jpg ja png on lubatud!";
			}
		} else {
			$photo_upload_error = "Tegemist pole pildifailiga!";
		}
		
		if(empty($photo_upload_error)){
			//ega pole liiga suur fail
			if($_FILES["file_input"]["size"] > $file_size_limit){
				$photo_upload_error = "Valitud fail on liiga suur! Lubatud kuni 1MiB!";
			}
			
			if(empty($photo_upload_error)){
				//loome oma failinime
				$timestamp = microtime(1) * 10000;
				$image_file_name = $file_name_prefix .$timestamp ."." .$image_file_type;

				$temp_image = null;
				if($image_file_type == "jpg"){
					$temp_image = imagecreatefromjpeg($_FILES["file_input"]["tmp_name"]);
				}
				if($image_file_type == "png"){
					$temp_image = imagecreatefrompng($_FILES["file_input"]["tmp_name"]);
				}
	
				
//-- loome normaalsuuruses pildi säilitades külgede proportsiooni
				$new_temp_image = resize_image($temp_image, 600, 400, true);
				
				//salvestame pikslikogumi mälumuutjujast faili
				$target_file = "../upload_photos_normal/" .$image_file_name;
				
				if($image_file_type == "jpg"){
					if(imagejpeg($new_temp_image, $target_file, 90)){
						$photo_upload_error = "Vähendatud pilt on salvestatud!";
					} else {
						$photo_upload_error = "Vähendatud pilti ei salvestatud!";
					}
				}
				if($image_file_type == "png"){
					if(imagepng($new_temp_image, $target_file, 6)){
						$photo_upload_error = "Vähendatud pilt on salvestatud!";
					} else {
						$photo_upload_error = "Vähendatud pilti ei salvestatud!";
					}
				}


//-- loome pisipildi ruuduna lõigates selle originaalpildi keskelt kahandades 100 pixlile

				$new_temp_image = resize_image($temp_image, 100, 100, false );
				
				//salvestame pikslikogumi faili
				$target_file = "../upload_photos_small/" .$image_file_name;

				if($image_file_type == "jpg"){
					if(imagejpeg($new_temp_image, $target_file, 90)){
						$photo_upload_error = "Vähendatud pilt on salvestatud!";
					} else {
						$photo_upload_error = "Vähendatud pilti ei salvestatud!";
					}
				}
				if($image_file_type == "png"){
					if(imagepng($new_temp_image, $target_file, 6)){
						$photo_upload_error = "Vähendatud pilt on salvestatud!";
					} else {
						$photo_upload_error = "Vähendatud pilti ei salvestatud!";
					}
				}

//-- säilitame ka üleslaetud originaalfaili eraldi kasutas  
				$target_file = "../upload_photos_orig/" .$image_file_name;

				if(move_uploaded_file($_FILES["file_input"]["tmp_name"], $target_file)){
					$photo_upload_error .= " Foto üleslaadimine õnnestus!";
					if (insert_pic_db(Input::str($_FILES["file_input"]["name"]),$image_file_name,Input::str($_POST['alt_text']),Input::int($_POST['privacy_input'])) == 1){
						$photo_upload_error .= "  Foto andmete lisamine andmebaasi õnnestus";
					} else {
						$photo_upload_error .= "  Foto andmete lisamine ebaõnnestus";
					}

				} else {
					$photo_upload_error .= " Foto üleslaadimine ebaõnnestus!";
				}
			}
		}
	}

	


?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>Fotode üleslaadimine</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<p><a href="?logout=1"><?php echo $_SESSION["user_first_name"]." ".$_SESSION["user_last_name"]." --> "; ?> Logi välja</a></p>
	<p><a href="home.php">Avalehele</a></p>
	<hr>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
		<label for="file_input">Vali foto fail! </label>
		<input id="file_input" name="file_input" type="file">
		<br>
		<label for="alt_input">Alternatiivtekst ehk pildi selgitus</label>
		<input id="alt_text" name="alt_text" type="text" placeholder="Pildil on ...">
		<br>
		<label>Privaatsustase: </label>
		<br>
		<input id="privacy_input_1" name="privacy_input" type="radio" value="3" checked>
		<label for="privacy_input_1">Privaatne</label>
		<br>
		<input id="privacy_input_2" name="privacy_input" type="radio" value="2">
		<label for="privacy_input_2">Registreeritud kasutajatele</label>
		<br>
		<input id="privacy_input_3" name="privacy_input" type="radio" value="1">
		<label for="privacy_input_3">Avalik</label>
		<br>
		<input type="submit" name="photo_submit" value="Lae pilt üles!">
	</form>
	<p><?php echo $photo_upload_error; ?></p>
</body>
</html>
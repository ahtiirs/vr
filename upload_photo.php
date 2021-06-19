<?php
	require_once "usesession.php";
	require_once "../../../conf.php";
	require_once "fnc_general.php";
	require_once "fnc_user.php";
	require_once "classes/clean_input.php";
	require_once "classes/Upload_photo.class.php";	
	
	$photo_upload_error = null;
	$file_size_limit = 1 * 1024 * 1024;
	$image_max_w = 600;
	$image_max_h = 400;
	$watermark = "../images/vr_watermark.png";
	$path_small = "../upload_photos_small/";
	$path_normal ="../upload_photos_normal/";
	$path_orig = "../upload_photos_orig/";

	if(isset($_POST["photo_submit"])){

			$photo_upload = new Upload_photo($_FILES["file_input"],$file_size_limit);

			if(empty($photo_upload->error)){
			//-- loome normaalsuuruses pildi säilitades külgede proportsiooni
				$photo_upload->resize_photo($image_max_w,$image_max_h, true);
				$photo_upload_error .= $photo_upload->error;
				
			//-- Lisan vesimärgi
				$photo_upload->add_watermark($watermark);
			
			//-- Lisan pildistamise kuupäeva	
				$photo_upload->date_to_pic();

			//-- Salvestame pildi normal kataloogi	
				$photo_upload->save_image_to_file($path_normal); 
				$photo_upload_error .= $photo_upload->error;

			//-- Loome pisipildi ruuduna lõigates selle originaalpildi keskelt kahandades 100 pixlile
				$photo_upload->resize_photo( 100, 100, false );

			//-- Salvestame pildi small kataloogi
				$photo_upload->save_image_to_file($path_small); 
				$photo_upload_error .= $photo_upload->error;
					
			//-- Säilitame ka üleslaetud originaalfaili eraldi kasutas  
				$photo_upload->save_orig_image($path_orig);
				$photo_upload_error .= $photo_upload->error;
			
			//-- Lisame pildi andmed andmebaasi
				if(empty($photo_upload_error)){
					$check = insert_pic_db($photo_upload->image_new_filename,Input::str($_FILES["file_input"]["name"]),Input::str($_POST['alt_text']),Input::int($_POST['privacy_input']));
				}
			 
				if ($check ==1){
					$photo_upload_error .= "  Foto andmete lisamine andmebaasi õnnestus";
				} else {
					$photo_upload_error .= "  Foto andmete lisamine ebaõnnestus";
				}
			} 
	}

	

//-- HTML osa lehe väljastamiseks

?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
	<title>Veebirakendused ja nende loomine 2021</title>
	<script src="checkImageSize.js"> defer </script>


</head>

<header>
        <?php include("page_detail/nav_bar.php"); ?>
</header>

<body>
	<h1>Fotode üleslaadimine</h1>

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
		<input type="submit" id="photo_submit" name="photo_submit" value="Lae pilt üles!">
	</form>
	<p id="notice"><?php echo $photo_upload_error; ?></p>

	<?php require("page_detail/scripts.php") ?>	
</body>
</html>
<?php
	require_once "usesession.php";
	require_once "../../../conf.php";


	function gallery_pics(){

		$path = "../upload_photos_small/"; //-- Millisest asukohast võetakse pildid
		$select_privacy = 2; // 
	
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
	
		$stmt = $conn -> prepare("SELECT vr21_photos.vr21_photos_id, vr21_photos.vr21_photos_filename, vr21_photos.vr21_photos_alttext, vr21_users.vr21_users_firstname, 
		vr21_users.vr21_users_lastname, vr21_photos.vr21_photos_privacy FROM vr21_photos JOIN vr21_users ON vr21_photos.vr21_photos_userid = vr21_users.vr21_users_id WHERE ((vr21_photos.vr21_photos_privacy <= ?) OR  (vr21_users.vr21_users_id = ?))
		AND vr21_photos.vr21_photos_deleted IS NULL GROUP BY vr21_photos.vr21_photos_id");
		
		echo $conn -> error;
		
		$stmt -> bind_param("ii", $select_privacy,$_SESSION["user_id"]);
		$stmt -> bind_result($photo_id, $photo_filename, $photo_alt_text, $first_name, $last_name, $privacy);
		$stmt -> execute();
	
		while ($stmt -> fetch()) { 
	
			echo '<div class="klassinimi">';
			echo '<img src='.$path .$photo_filename.' alt='.$photo_alt_text.' class="thumb" data-fn='.$photo_filename.' data-id='.$photo_id.'>';
			echo '<p>'.$first_name.' '.$last_name.' Privacy-'.$privacy .'</p>'; 
			echo '</div>'."\n";
			
		}
		$stmt -> close();
		$conn -> close();
	}
	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
	<link rel="stylesheet" href="modal.css">

	<script src="modal.js"> defer </script>

</head>


<body>

	<header>
        <?php include("page_detail/nav_bar.php"); ?>
    </header>

	
  <!--Modaalaken fotogalerii jaoks-->
  <div id="modalarea" class="modalarea">
	<!--sulgemisnupp-->
	<span id="modalclose" class="modalclose">&times;</span>
	<!--pildikoht-->
	<div class="modalhorizontal">
		<div class="modalvertical">
			<p id="modalcaption"></p>
			<img id="modalimg" src="../img/empty.png" alt="galeriipilt">

			<br>
			<div id="rating" class="modalRating">
				<label><input id="rate1" name="rating" type="radio" value="1">1</label>
				<label><input id="rate2" name="rating" type="radio" value="2">2</label>
				<label><input id="rate3" name="rating" type="radio" value="3">3</label>
				<label><input id="rate4" name="rating" type="radio" value="4">4</label>
				<label><input id="rate5" name="rating" type="radio" value="5">5</label>
				<button id="storeRating">Salvesta hinnang!</button>
				<br>
				<p id="avgRating"></p>
			</div>
		</div>
	</div>
  </div>


	<h1>Galerii</h1>

	<hr>
	<div id="gallery">
	<main class="grid">

	<?php gallery_pics() ?>

	</main>
	</div>


	<?php require("page_detail/scripts.php") ?>
</body>
</html>
<?php
require_once "usesession.php";
require_once "fnc_user.php";
require_once "classes/clean_input.php";
require_once "classes/Upload_photo.class.php";	
require_once "../../../conf.php";

if (isset($_REQUEST["change_news_id"])){
    $change_news_id = $_REQUEST["change_news_id"];
    $editing = true;
    $news = edit_news($change_news_id);
    $photo= edit_photo($change_news_id);

} else {
    $editing = false;
    $change_news_id = -1;
} 

$news_input_error = null;
$clean_news_title = "";
$clean_news_content = "";
$clean_news_author = "";
$clean_news_picture = "";
$clean_news_alt_text = "";
$file_size_limit = 1 * 1024 * 1024;
$image_max_w = 600;
$image_max_h = 400;
$path_news = "../upload_photos_news/";


if (isset($_POST["news_submit"])) {

    if (empty($_POST["news_title_input"])){
        $news_input_error = "Uudise pealkiri on puudu! ";
    } else {
        $clean_news_title = addslashes(Input::str($_POST['news_title_input']));
    }

    if (empty($_POST["news_content_input"])){
        $news_input_error .= "Uudise tekst on puudu! ";
    }
    else {
        $clean_news_content = addslashes(Input::str($_POST['news_content_input']));
        $clean_news_author = Input::str($_POST['news_author_input']);
        $clean_news_alt_text = Input::str($_POST['alt_text']);
    }

    if (!empty($_FILES["file_input"]["tmp_name"]) and empty($_POST["alt_text"])  ){
        $news_input_error = "Pildi tekst on puudu! ";
    } else {
        // $clean_news_picture = Input::str($_POST['file_input']);
		$clean_news_alt_text = Input::str($_POST['alt_text']);
    }

	if (!empty($_FILES["file_input"]["tmp_name"]) and !empty($_POST["alt_text"])  ){
	
    
    $photo_upload = new Upload_photo($_FILES["file_input"],$file_size_limit);

	if(empty($photo_upload->error)){
		$photo_upload->resize_photo($image_max_w,$image_max_h, true);
		$news_input_error .= $photo_upload->error;}
		$photo_upload->save_image_to_file($path_news); 
		$news_input_error .= $photo_upload->error;

	}

    if (empty($news_input_error)){

        if($editing){
            $success = store_edited_news($change_news_id,$clean_news_title,$clean_news_content,$clean_news_author,@$photo_upload->image_new_filename,@$clean_news_alt_text);  
        }else {
            $success = store_news($clean_news_title,$clean_news_content,$clean_news_author,@$photo_upload->image_new_filename,@$clean_news_alt_text);            
        }

        if ($success){        
            // $clean_news_title = "";
            // $clean_news_content = "";
            // $clean_news_author = "";
            header("Location: show_news.php");
        } else {
            $news_input_error .= "Uudise andmebaasi lisamine ei õnnestunud";
        }

        $clean_news_title = "";
        $clean_news_content = "";
        $clean_news_author = "";
    
    }

}


?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
	<header>
			<?php include("page_detail/nav_bar.php"); ?>
	</header>

	<h1>Uudiste lisamine</h1>

    <hr>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
    <label for="news_title_input">Uudise pealkiri</label>

    <input type="text" id="news_title_input" class="form-control"  name="news_title_input" value="<?php echo (($editing) ? stripslashes($news['vr21_news_title']): $clean_news_title); ?> " placeholder= "Pealkiri" >
    <!-- <div><input type="button"  onClick="alert('Kui oled oma soovis kindel siis sisesta uudise pealkirjaks !KUSTUTA! ja vajuta salvesta soov');" class="btn btn-danger btn-sm" value="kustuta uudis" ></div> -->

    <label for="news_content_input">Uudise tekst</label>

    <textarea id="news_content_input" class="form-control"  name="news_content_input" placeholder="Uudise tekst" rows="15" ><?php echo (($editing) ? stripslashes($news['vr21_news_content']): $clean_news_content) ; ?></textarea>

    <?php echo (($editing and isset($photo['vr21_news_photo_filename'])) ? "<img src='".$path_news.$photo['vr21_news_photo_filename']."' class='valikpilt' ></img>": "") ; ?>
	<label for="file_input">Vali foto fail! </label>
	<input id="file_input" class="form-control"  multiple name="file_input" type="file" accept= image/jpeg, image/png" >

    <label for="alt_input">Alternatiivtekst ehk pildi selgitus</label>
	<input id="alt_text" class="form-control-file"  name="alt_text" value="<?php echo (($editing) ? $photo['vr21_news_photo_alt_text']:$clean_news_alt_text) ; ?>" type="text"  placeholder="Pildil on ...">

    <label for="news_author">Uudise lisaja nimi</label>

    <input type="text" class="form-control"  id="news_author_input" name="news_author_input" value="<?php echo (($editing) ? $news['vr21_news_author']: $clean_news_author) ; ?>" placeholder="Nimi" >

  
    <input type="submit" class="btn btn-outline-primary uudis_salvesta"  name="news_submit" value="Salvesta uudis">
    <?php if($editing){
        echo '<input type="hidden" id="change_news_id" name="change_news_id" value="'.$change_news_id.'">';}?>    
</form>

<p><?php echo $news_input_error; ?> </p>

<?php require("page_detail/scripts.php") ?>
</body>
</html>

<?php
require_once "usesession.php";
require_once "fnc_user.php";
require_once "../../../conf.php";

$path_news = "../upload_photos_news/";

if (isset($_POST["news_output_num"])) {
    $news_limit = $_POST["news_output_num"];
    }
    else {
        $news_limit = 6;
    }
   

$news_html = read_news($news_limit);

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

  	<h1>Uudised</h1>

    <hr>
	<p>Uudiste arv lehel:</p>
    <form method="POST" id="news_num">
    <!-- lisatud pöördumine scripri poole kohe kui andmete väärtus elemendis muutuvad -->
    <INPUT type="number" min="1" max="15" value="<?php echo $news_limit; ?>" name="news_output_num" onchange="do_submit()">  
    </form>
<div class="container">
<p><?php echo $news_html; ?> </p>
</div>
<script>                                                                                                                // Script mis saadab FORM andmed teele
function do_submit() {
     document.getElementById("news_num").submit();
}
</script>

<?php require("page_detail/scripts.php") ?>
</body>
</html>

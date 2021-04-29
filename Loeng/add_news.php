<?php

require_once "../../../conf.php";
echo $server_host;
// var_dump($_POST);                                          // On olemas ka $_GET - avalik infi saamine  ja  $_REQUEST - sisaldab kõiki meetodeid ja cookisid
$news_input_error = null;


if (isset($_POST["news_submit"])) {
    if (empty($_POST["news_title_input"])){
        $news_input_error = "Uudise pealkiri on puudu! ";
    }
    if (empty($_POST["news_title_input"])){
        $news_input_error .= "Uudise tekst on puudu! ";
    }

    if (empty($news_input_error)){
        //salvestame andmebaasi
        store_news($_POST["news_title_input"],$_POST["news_content_input"],$_POST["news_author_input"]);




    }

}
function store_news($news_title,$news_content,$news_author){
    //$GLOBALS["server_host"]
    //loome ühenduse andmebaasiga
    $conn =  new mysqli ($GLOBALS["server_host"],$GLOBALS["server_user_name"],$GLOBALS["server_password"],$GLOBALS["database"]);
    //valmistan ette SQL käsu
    $conn -> set_charset("utf-8");
    $stmt = $conn ->prepare("INSERT INTO vr21_news (vr21_news_title, vr21_news_content, vr21_news_author) VALUES (?,?,?)");
    echo $conn -> error;
    // i - integer   s - string   d - decimal
    $stmt -> bind_param("sss",$news_title,$news_content,$news_author);
    $stmt -> execute();
    $stmt -> close();
    $conn -> close();
}







?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>Uudiste lisamine</h1>
	<p>See leht on valminud õppetöö raames!</p>
    <hr>
<form method="POST">
    <label for="news_title_input">Uudise pealkiri</label>
    <br>
    <input type="text" id="news_title_input" name="news_title_input" placeholder="Pealkiri" >
    <br>
    <label for="news_content_input">Uudise tekst</label>
    <br>
    <textarea id="news_content_input" name="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"></textarea>
    <br>
    <label for="news_author">Uudise lisaja nimi</label>
    <br>
    <input type="text" id="news_author_input" name="news_author_input" placeholder="Nimi" >
    <br>   
    <input type="submit" name="news_submit" value="Salvesta uudis">
</form>

<p><?php echo $news_input_error; ?> </p>


</body>
</html>

<?php

require_once "../../../conf.php";



function read_news(){
    //$GLOBALS["server_host"]
    //loome ühenduse andmebaasiga
    $conn =  new mysqli ($GLOBALS["server_host"],$GLOBALS["server_user_name"],$GLOBALS["server_password"],$GLOBALS["database"]);
    //valmistan ette SQL käsu
    $conn -> set_charset("utf-8");
    $stmt = $conn ->prepare("SELECT vr21_news_title, vr21_news_content, vr21_news_author FROM vr21_news");
    echo $conn -> error;
    $stmt -> bind_result($news_title_db,$news_content_db,$news_author_db);
    $stmt -> execute();
    $raw_news_html = null;
    while ($stmt -> fetch()) {
        $raw_news_html .= "\n <H2>".$news_title_db."</H2>";
        $raw_news_html .= "\n <P>".nl2br($news_content_db)."</P>";
        $raw_news_html .= "\n <P> Edastas: ";
        if(!empty($news_author_db)){
            $raw_news_html .= $news_author_db;
        } else { 
            $raw_news_html .= "Tundmatu reporter";
        }
        $raw_news_html .= "</P><BR>";
    }
    $stmt -> close();
    $conn -> close();
    return $raw_news_html;
}



$news_html = read_news();



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

<p><?php echo $news_html; ?> </p>


</body>
</html>

<?php

require_once "../../../../conf.php";

if (isset($_POST["news_output_num"])) {
    $news_limit = $_POST["news_output_num"];
    }
    else {
        $news_limit = 3;
    }
   
function read_news($news_limit){
    //$GLOBALS["server_host"]
    //loome ühenduse andmebaasiga

    $conn =  new mysqli ($GLOBALS["server_host"],$GLOBALS["server_user_name"],$GLOBALS["server_password"],$GLOBALS["database"]);  
    //valmistan ette SQL käsu
    $conn -> set_charset("utf-8");
    $stmt = $conn ->prepare("SELECT vr21_news_title, vr21_news_content, vr21_news_author, vr21_news_added FROM vr21_news ORDER BY vr21_news_id DESC LIMIT ?");     //-Lisan SQL päringusse kuulutuse salvestus aja välja nime ja järjestuse tingimuse ja väljastavate kirjete limiidi muutuja
    $stmt -> bind_param("s",$news_limit);                                                                                       //-seon muutuja stringi kujul sql päringu limiidi ?'ga
    echo $conn -> error;                                                                                                        
    $stmt -> bind_result($news_title_db,$news_content_db,$news_author_db,$news_added_db);                                       //-Lisan välja kuulutuse andmebaasi lisamise muutuja
    $stmt -> execute();
    $raw_news_html = null;
    while ($stmt -> fetch()) {
        $raw_news_html .= "\n <H2>".$news_title_db."</H2>";
        $date_of_news = new DateTime($news_added_db);                                                                           //-Teeme andmebaasist saadud kuupäevast objekti 
        $raw_news_html .= "\n <H4>Lisatud: ".$date_of_news->format('d-m-Y H:i:s')."</H4>";                                      //-Kodutöö p1. lisame udise salvestue aja soovikohaselt formaaditult HTML loomisel
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

$news_html = read_news($news_limit);

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
	<p>Uudiste arv lehel:</p>
    <form method="POST" id="news_num">
    <!-- lisatud pöördumine scripri poole kohe kui andmete väärtus elemendis muutuvad -->
    <INPUT type="number" min="1" max="10" value="<?php echo $news_limit; ?>" name="news_output_num" onchange="do_submit()">  
    </form>

<p><?php echo $news_html; ?> </p>

<script>                                                                                                                // Script mis saadab FORM andmed teele
function do_submit() {
     document.getElementById("news_num").submit();
}
</script>
</body>
</html>

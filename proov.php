<?php
require_once "../../../conf.php";

$news_id = 17;


    $conn1 =  new mysqli ($GLOBALS["server_host"],$GLOBALS["server_user_name"],$GLOBALS["server_password"],$GLOBALS["database"]);   

    var_dump($news_id);

    $stmt1 = $conn1 ->prepare("SELECT vr21_news_photo_filename, vr21_news_photo_alt_text FROM vr21_news_photo WHERE vr21_news_photo_news_id =  ? ");    
    $stmt1 -> bind_param("i",$news_id);                                                                                      
 
    echo $conn1 -> error; 
                                                                                              
        $stmt1 -> bind_result($news_photo_filename,$news_photo_alt_text);                                       
 
        $stmt1 -> execute();
 
        while ($stmt1 -> fetch()) {
            echo $news_photo_filename,$news_photo_alt_text;
        }


?>
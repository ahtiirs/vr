<?php

require_once "usesession.php";
// require("classes/SessionManager.class.php");
require_once "../../../conf.php";



function gallery_pics(){

    $path = "../upload_photos_normal/";
    $select_privacy = 2;

    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);

    $stmt = $conn -> prepare("SELECT vr21_photos.vr21_photos_id, vr21_photos.vr21_photos_filename, vr21_photos.vr21_photos_alttext, vr21_users.vr21_users_firstname, 
    vr21_users.vr21_users_lastname FROM vr21_photos JOIN vr21_users ON vr21_photos.vr21_photos_userid = vr21_users.vr21_users_id WHERE vr21_photos.vr21_photos_privacy <= ? 
    AND vr21_photos.vr21_photos_deleted IS NULL GROUP BY vr21_photos.vr21_photos_id");
    
    echo $conn -> error;
    
    $stmt -> bind_param("i", $select_privacy);

    $stmt -> bind_result($photo_id, $photo_filename, $photo_alt_text, $first_name, $last_name);
    $stmt -> execute();
    while ($stmt -> fetch()) { 

        echo '<div class="klassinimi">';
        echo '<img src='.$path .$photo_filename.' alt='.$photo_alt_text.' class="thumb" data-fn='.$photo_filename.' data-id='.$photo_id.'>';
        echo '<p>'.$first_name.' '.$last_name.'</p>'; 
        echo '</div>'."\n";
        
    }
    $stmt -> close();
    $conn -> close();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Galerii</title>
    <style>
    .grid { 
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        grid-gap: 20px;
        align-items: stretch;
        }
        .grid img {
        border: 1px solid #ccc;
        box-shadow: 2px 2px 6px 0px  rgba(0,0,0,0.3);
        max-width: 100%;
        }

    }
  
     </style>
</head>

<body>
<h1>Galerii</h1>   
<main class="grid">
<?php gallery_pics() ?>
</main>
</body>
</html>
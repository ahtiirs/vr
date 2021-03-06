<?php
	//session_start();
	require("classes/SessionManager.class.php");
	SessionManager::sessionStart("vr", 0, "/~ahti.irs/", "tigu.hk.tlu.ee");
	
	require_once "../../../conf.php";
	//require_once "fnc_general.php";
	require_once "fnc_user.php";


    $myname = "Ahti Irs";
    $currenttime = date("d.m.Y H:i:s");                                         // hetke kuupäev ja kellaaeg muutujasse currenttime
    $timehtml="\n <p> Lehe avamise hetk oli:".$currenttime.".</p> \n";          // koostame html osa jaoks vormindatud muutuja kellaaja ja kuupäevaga
    $semesterbegin = new DateTime("2021-1-25");                                 // semestri algusaeg muutujasse
    $semesterend = new DateTime("2021-6-30");                                   // semestri lõpuaeg muutujasse
    $semesterduration = $semesterbegin->diff($semesterend);                     // semestri kestvus kasutades diff funktsiooni alguse ja lõpuaja võrdlemiseks
    $semesterdurationdays = $semesterduration->format("%r%a");                  // muudab ajaformaadi päevadeks (a) ja näitab ka miinusmärki negatiivse ajavahe korral (r) 
 

    $semesterdurhtml = "\n <p>2021 kevadsemestri kestus on "                    // vormindatud muutuja HTML keskel väljastamiseks 
    .$semesterdurationdays ." päeva.</p> \n";
   
    //$today = new DateTime("now");                                             // määratakse tänane kuupäev millega hakatakse võrdlema semestri algust ja lõppu
    $today = date_create();                                                     // määrab mutuja tüübi
    //$today->setDate(2020, 4, 10);                                               // <-- siin saab ise kastetamiseks tänase kuupäeva sisestada minevikku või tulevikku

//----- Semestri kulgemise määramine ----------------------
    $fromsemesterbegin = $semesterbegin->diff($today);                          // diff funk. abiga saame ajavahemiku semestri algusest tänaseni
    $fromsemesterbegindays = $fromsemesterbegin->format("%r%a");                // muudame ajavahemiku arvuks päevades
    
                                                                                // võrdleme kas ajavahemik on vahemikus 0-semestri kestvus või on pikem või hoopis negatiivne
    if($fromsemesterbegindays <= $semesterdurationdays && $fromsemesterbegindays >=0) {
        $semesterprogress = "\n"  .'<p>Semester edeneb: <meter min="0" max="' .$semesterdurationdays 
        .'" value="' .$fromsemesterbegindays .'"></meter></p>' ."\n";           // ajavahemik on lubatud piires, seega semester kestab ja vormindame HTML muutuja mis näitab semetri kulgu
        }    
        else { 
            if ($fromsemesterbegindays <0) 
            {$semesterprogress = "\n <p> Semester pole veel alanud. </p>"; }    // ajavahemik on negatiivne, seega pole semester veel alanud
            else {
            $semesterprogress = "\n <p> Semester on lõppenud. </p>";}           // ajavahemik oli semestrist pikem ja seega semester on lõppenud
        }
    
// ----- Tänase nädalapäeva nimetuse leidmine -------------
    //$weekday_nr=date('w');                                                      // see funk. annab tänase kuupäeva nädalapäeva numbri  vahemikus 0-6, 0 on pühapäev
    //$day_names=['Pühapäev','Esmaspäev','Teisipäev','Kolmapäev','Neljapäev'
    // ,'Reede','Laupäev'];                                                        // moodustame massiivi nädalapevadega
                                                                                // moodustame HTML kujundatud muutuja nädalapäeva väljastuseks
    //$todaysweekdayhtml="<p> Täna on ". $day_names[$weekday_nr].".</p>";

    //----- veel lihtsam variant --------------------------
    setlocale(LC_TIME, 'et_EE.utf8') ;
    $day_name =  strftime('%A') ;
    $todaysweekdayhtml="<p> Täna on ". $day_name.".</p>";

//---------------------------------------------------------

//----- Loeme piltide kataloogi sisu ----------------------

    //$picsdir = "../../../andrus.rinde/vr2021/pics/";
    $picsdir = "../Pildid/";                                                       // Selles kataloogis asuvad pildid 


    $allfiles = array_slice(scandir($picsdir),2);                               // scannitakse massiivi piltide kataloogis olevad failinimed ja lõigatakse esimesed kaks maha
    $allowphototypes=["image/jpeg", "image/png"];                               // massiivi lisatakse lubatud failitüüpide kirjeldused
    $picfiles = [];                                                             // uus tühi massiv pildifailide nimede jaoks
    foreach($allfiles as $file) {                                               // korratkse niikaua kuni allfiles massiivis ridu on
        $fileinfo = getimagesize($picsdir .$file);                              // võtakse massiivist järjekorras failinimed ja tehakse päring faili sisu kohta
        if (isset($fileinfo["mime"])){                                          // kui faili infos on olemas tyyp mime siis tehakse järgnevat 
            if (in_array($fileinfo["mime"], $allowphototypes )){                // kui saliinfo mime sisu esineb lubatud failitüüpide masiivis siis tehakse järgnevat 
                array_push($picfiles,$file);                                    // lisatakse massiivi picfiles faili nimi 
            }
        }
    }

//----- Fotodemassivist kolme suvalise foto valimine ------
    //$photocount = count($picfiles);
    //$photonum = mt_rand(0,$photocount-1);
    $randomphoto = array_rand($picfiles,3);                                     // see funkts. võtab mssivist juhuliku valiku teel 3 elementi ja paneb nende võtmeväärtused randomfoto massivi
//---------------------------------------------------------

//----- Selles sektsioonis on HTML mille vahele on pandud eelnevalt PHP's koostatud muutujad

	//sisselogimine
	$notice = null;
	$email = null;
	$email_error = null;
	$password_error = null;
	if(isset($_POST["login_submit"])){
		//kontrollime, kas email ja password põhimõtteliselt olemas
		
        if (verify_user($_POST["email_input"]) == 1){

            $notice = sign_in($_POST["email_input"], $_POST["password_input"]);

        } else {
            $notice = "Sellist kasutajanime pole";
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

	<!-- <h1><?php echo $myname; ?></h1> -->
	<p>See leht on valminud  õppetöö raames Ahti Irs poolt A. Rinde juhendamisel!</p>

    <hr>
	<h2>Logi sisse</h2>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<label>E-mail (kasutajatunnus):</label><br>
		<input type="email" name="email_input" value="<?php echo $email; ?>"><span><?php echo $email_error; ?></span><br>
		<label>Salasõna:</label><br>
		<input name="password_input" type="password"><span><?php echo $password_error; ?></span><br>
		<input name="login_submit" type="submit" value="Logi sisse!"><span><?php echo $notice; ?></span>
	</form>
	<p>Loo endale <a href="add_user.php">kasutajakonto!</a></p>
	<hr>

    <?php
    echo $semesterprogress;  
    echo $semesterdurhtml;  
    echo $timehtml; 
    echo $todaysweekdayhtml;
    ?>
    <div class="grid gallery">
    <img class="rand_pic" src="<?php echo $picsdir .$picfiles[$randomphoto[0]]; ?>" alt="RIF20">
    <img class="rand_pic" src="<?php echo $picsdir .$picfiles[$randomphoto[1]]; ?>" alt="RIF20">
    <img class="rand_pic" src="<?php echo $picsdir .$picfiles[$randomphoto[2]]; ?>" alt="RIF20">
    </div>
	<?php require("page_detail/scripts.php") ?>
</body>
</html>

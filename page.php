<?php
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
    $today->setDate(2020, 4, 10);                                               // <-- siin saab ise kastetamiseks tänase kuupäeva sisestada minevikku või tulevikku

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
    $picsdir = "Pildid/";                                                       // Selles kataloogis asuvad pildid 

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
?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1><?php echo $myname; ?></h1>
	<p>See leht on valminud õppetöö raames!</p>
    <?php
    echo $semesterprogress;  
    echo $semesterdurhtml;  
    echo $timehtml; 
    echo $todaysweekdayhtml;
    ?>
    <img width="250px" src="<?php echo $picsdir .$picfiles[$randomphoto[0]]; ?>" alt="RIF20">
    <img width="250px" src="<?php echo $picsdir .$picfiles[$randomphoto[1]]; ?>" alt="RIF20">
    <img width="250px" src="<?php echo $picsdir .$picfiles[$randomphoto[2]]; ?>" alt="RIF20">
</body>
</html>

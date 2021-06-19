<?php
	
	function sign_up($name, $surname, $gender, $birth_date, $email, $password){
		$notice = 0;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$stmt = $conn->prepare("INSERT INTO vr21_users (vr21_users_firstname, vr21_users_lastname, vr21_users_birthdate, vr21_users_gender, vr21_users_email, vr21_users_password) VALUES (?,?,?,?,?,?)");
		echo $conn->error;
		//krüpteerime parooli
		//$options = ["cost" => 12, "salt" => substr(sha1(rand()), 0, 22)];
		$options = ["cost" => 12];
		$pwd_hash = password_hash($password, PASSWORD_BCRYPT, $options);
		
		$stmt -> bind_param("sssiss", $name, $surname, $birth_date, $gender, $email, $pwd_hash);
		
		if($stmt -> execute()){
			$notice = 1;
		}
		$stmt -> close();
		$conn -> close();
		return $notice;
	}
	
	function sign_in($email, $password){
		$notice = 0;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$stmt = $conn -> prepare("SELECT vr21_users_id, vr21_users_firstname, vr21_users_lastname, vr21_users_password FROM vr21_users WHERE vr21_users_email = ?");
		echo $conn -> error;
		$stmt -> bind_param("s", $email);
		$stmt -> bind_result($id_from_db, $first_name_from_db, $last_name_from_db, $password_from_db);
		$stmt -> execute();
		//kui leiti
		if($stmt -> fetch()){
			//kas parool klapib
			if(password_verify($password, $password_from_db)){
				//olemegi sisse loginud
				$notice = 1;
				$_SESSION["user_id"] = $id_from_db;
				$_SESSION["user_first_name"] = $first_name_from_db;
				$_SESSION["user_last_name"] = $last_name_from_db;
				$stmt -> close();
				$conn -> close();
				header("Location: home.php");
				exit();
			}
		}
		
		$stmt -> close();
		$conn -> close();
		return $notice;
	}

	function verify_user($email){
		//echo $email;
		$notice = 0;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$stmt = $conn -> prepare("SELECT vr21_users_id FROM vr21_users  WHERE vr21_users_email = ?");
		
		echo $conn -> error;
		$stmt -> bind_param("s", $email);
		$stmt -> execute();
		if($stmt -> fetch()){
			$notice = 1;
		}
		$stmt -> close();
		$conn -> close();
		return $notice;
	}

	function insert_pic_db($pic_name,$pic_orig_name,$alt_text,$pic_privacy){
		$notice = 0;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$stmt = $conn->prepare("INSERT INTO vr21_photos (vr21_photos_userid, vr21_photos_filename, vr21_photos_origname, vr21_photos_alttext, vr21_photos_privacy) VALUES (?,?,?,?,?)");
		$stmt -> bind_param("isssi", $_SESSION["user_id"], $pic_name, $pic_orig_name, $alt_text, $pic_privacy);
		if($stmt -> execute()){
			$notice = 1;
		}
		$stmt -> close();
		$conn -> close();
		return $notice;
	}

	function store_news($news_title,$news_content,$news_author,$news_picture,$news_alt_text){
		$conn =  new mysqli ($GLOBALS["server_host"],$GLOBALS["server_user_name"],$GLOBALS["server_password"],$GLOBALS["database"]);
		$conn -> set_charset("utf-8");
		$stmt = $conn ->prepare("INSERT INTO vr21_news (vr21_news_title, vr21_news_content, vr21_news_author) VALUES (?,?,?)");
		echo $conn -> error;
		$stmt -> bind_param("sss",$news_title,$news_content,$news_author);
		
		if(!$stmt->execute()){
			echo $stmt->error;
			$stmt -> close();
			$conn -> close();
			return false;
		}	
		$last_news_id = $conn->insert_id;
	
		if (!empty($news_picture)){
				$stmt = $conn ->prepare("INSERT INTO vr21_news_photo (vr21_news_photo_news_id, vr21_news_photo_filename, vr21_news_photo_alt_text, vr21_news_photo_owner_id ) VALUES (?,?,?,?)");
				echo $conn -> error;
				$stmt -> bind_param("issi", $last_news_id, $news_picture, $news_alt_text, $_SESSION["user_id"]);
				
				if(!$stmt->execute()){
					echo $stmt->error;
					$stmt -> close();
					$conn -> close();
					return false;
				}
			}
		$stmt -> close();
		$conn -> close();
		return true;
	}


	function read_news($news_limit){
		$path_news = "../upload_photos_news/";
		$conn =  new mysqli ($GLOBALS["server_host"],$GLOBALS["server_user_name"],$GLOBALS["server_password"],$GLOBALS["database"]); 
		$conn1 =  new mysqli ($GLOBALS["server_host"],$GLOBALS["server_user_name"],$GLOBALS["server_password"],$GLOBALS["database"]);   
		$conn -> set_charset("utf-8");
		$conn1 -> set_charset("utf-8");

		$stmt = $conn ->prepare("SELECT vr21_news_title, vr21_news_content, vr21_news_author, vr21_news_added, vr21_news_id FROM vr21_news ORDER BY vr21_news_id DESC LIMIT ?");    
		$stmt -> bind_param("s",$news_limit);                                                                                       
		echo $conn -> error;                                                                                                        
		$stmt -> bind_result($news_title_db,$news_content_db,$news_author_db,$news_added_db, $news_id);                                    
		$stmt -> execute();

		$raw_news_html = null;

		while ($stmt -> fetch()) {
			$raw_news_html .= "\n <div class='item'> <H3 class='uudis_pealkiri'>".stripslashes($news_title_db)."</H3>";
			$date_of_news = new DateTime($news_added_db);                                                            
			$raw_news_html .= "\n <H4 class='uudis_date'>Lisatud: ".$date_of_news->format('d-m-Y H:i:s')."</H4>";    
			

	
	
			$stmt1 = $conn1 ->prepare("SELECT vr21_news_photo_id, vr21_news_photo_filename, vr21_news_photo_alt_text FROM vr21_news_photo WHERE vr21_news_photo_news_id =  ? ");    
			$stmt1 -> bind_param("i",$news_id);                                                                                      
			echo $conn1 -> error; 
			$stmt1 -> bind_result($photo_id, $news_photo_filename,$news_photo_alt_text);                                       
			$stmt1 -> execute();
	
			while ($stmt1 -> fetch()) {
				$raw_news_html .= '<div class="news_photo_frame">';
				$raw_news_html .=  '<img class="news_photo" src='.$path_news .$news_photo_filename.' alt='.$news_photo_alt_text.' data-fn='.$news_photo_filename.' data-id='.$photo_id.'>';
				$raw_news_html .= '</div>'."\n";
			}

			$raw_news_html .= "\n <P class='uudis'>".nl2br(stripslashes($news_content_db))."</P>";
	
			$raw_news_html .= "\n <P> Edastas: ";
			if(!empty($news_author_db)){
				$raw_news_html .= $news_author_db;
			} else { 
				$raw_news_html .= "Tundmatu reporter";
			}

			$raw_news_html .= "\n ";
			$raw_news_html .='<form action="add_news.php"> <button type="submit" class="btn btn-outline-primary">Muuda  </button>';
			$raw_news_html .='<input type="hidden" id="change_news_id" name="change_news_id" value="'.$news_id.'">';

			$raw_news_html .= '</form>';
			$raw_news_html .= "</P><BR><hr></div>";
		
		}
		$stmt -> close();
		$conn -> close();
		return $raw_news_html;
	}


	function edit_news($news_id){
		$mysqli=  new mysqli ($GLOBALS["server_host"],$GLOBALS["server_user_name"],$GLOBALS["server_password"],$GLOBALS["database"]); 
		$query = "SELECT vr21_news_title, vr21_news_content, vr21_news_author FROM vr21_news WHERE vr21_news_id =".$news_id;
		$result = $mysqli->query($query);
		$news = $result->fetch_assoc();
		return $news;
	}
	function edit_photo($news_id){
		$mysqli=  new mysqli ($GLOBALS["server_host"],$GLOBALS["server_user_name"],$GLOBALS["server_password"],$GLOBALS["database"]); 
		$query = "SELECT vr21_news_photo_id, vr21_news_photo_filename, vr21_news_photo_alt_text FROM vr21_news_photo WHERE vr21_news_photo_news_id =".$news_id;
		$result = $mysqli->query($query);
		$photo = $result->fetch_assoc();
		return $photo;
	}



	function store_edited_news($row_num,$news_title,$news_content,$news_author,$news_picture,$news_alt_text){

		if(trim($news_title) == "!KUSTUTA!"){
			delete_news_pics_files($row_num);
			header("Location: show_news.php");
			return;
		}

		$conn =  new mysqli ($GLOBALS["server_host"],$GLOBALS["server_user_name"],$GLOBALS["server_password"],$GLOBALS["database"]);
		$conn -> set_charset("utf-8");
		$stmt = $conn ->prepare("UPDATE vr21_news SET vr21_news_title = ?, vr21_news_content = ?, vr21_news_author = ?  WHERE vr21_news_id = ?");	
		echo $conn -> error;
		$stmt -> bind_param("sssi",$news_title,$news_content,$news_author,$row_num);

		if(!$stmt->execute()){
			echo $stmt->error;
			$stmt -> close();
			$conn -> close();
			return false;
		}

		if (!empty($news_picture)){
		$stmt = $conn ->prepare("UPDATE vr21_news_photo SET vr21_news_photo_filename = ?, vr21_news_photo_alt_text = ?, vr21_news_photo_owner_id = ?  WHERE vr21_news_photo_news_id = ?");	
			echo $conn -> error;
			$stmt -> bind_param("ssii", $news_picture, $news_alt_text, $_SESSION["user_id"],$row_num);

			if(!$stmt->execute()){
				echo $stmt->error;
				$stmt -> close();
				$conn -> close();
				return false;
			}
		}

		if ($conn->affected_rows == 0 and !empty($news_picture)){
			$stmt = $conn ->prepare("INSERT INTO vr21_news_photo (vr21_news_photo_news_id, vr21_news_photo_filename, vr21_news_photo_alt_text, vr21_news_photo_owner_id ) VALUES (?,?,?,?)");
			echo $conn -> error;
			$stmt -> bind_param("issi", $row_num, $news_picture, $news_alt_text, $_SESSION["user_id"]);
			
			if(!$stmt->execute()){
				echo $stmt->error;
				$stmt -> close();
				$conn -> close();
				return false;
			}
		}
		$stmt -> close();
		$conn -> close();
		return true;
	}

	function delete_news_pics_files($row_num){
		$mysqli=  new mysqli ($GLOBALS["server_host"],$GLOBALS["server_user_name"],$GLOBALS["server_password"],$GLOBALS["database"]); 
		$query = "SELECT vr21_news_photo_id, vr21_news_photo_filename, vr21_news_photo_alt_text FROM vr21_news_photo WHERE vr21_news_photo_news_id =".$row_num;
		$result = $mysqli->query($query);
		
		while ($photo = $result->fetch_assoc()){
			$delete_file = "../upload_photos_news/".$photo['vr21_news_photo_filename'];

			if (!unlink($delete_file)) {
				echo ("Pildifaili ei õnnestunud kustutada");
			}
		}
		$query = "DELETE FROM vr21_news_photo WHERE vr21_news_photo_news_id =".$row_num;
		$result = $mysqli->query($query);
		$query = "DELETE  FROM vr21_news WHERE vr21_news_id =".$row_num;
		$result = $mysqli->query($query);

	}

	function mysql_escape($inp) {
		if(is_array($inp))
			return array_map(__METHOD__, $inp);
	
		if(!empty($inp) && is_string($inp)) {
			return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
		}
	
		return $inp;
	}

	
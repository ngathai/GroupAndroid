<?php

	/* Dang ki bang username (khong trung) va pass */
	function storeUser($conn, $username, $pass) {
		$uuid = uniqid ( '', true );
		$hash = hashSSHA ( $pass );
		$encrypted_pass = $hash ['encrypted'];
		$salt = $hash ['salt'];
		$sql = "INSERT INTO users(unique_id, username, pass, salt) VALUES('$uuid','$username','$encrypted_pass','$salt')";
		$result = mysqli_query ($conn, $sql);
		if ($result) {
			$uid = mysqli_insert_id ($conn);
			$result = mysqli_query ($conn, "SELECT * FROM users WHERE id_user = $uid" );
			return mysqli_fetch_array ( $result, MYSQLI_ASSOC );
		} else {
			return false;
		}
	}
	
	/* Dang ki bai hat bang idUser va idSong*/
	function storeSongBooking($conn, $idUser, $idSong){
		$sql = "INSERT INTO bookinglist(id_song, id_user) VALUES ($idSong, $idUser)";
		$result = mysqli_query($conn, $sql);
		if($result){
			$mysql1 = "UPDATE bookinglist SET counting = counting + 1 WHERE id_order = 1";
			mysqli_query($conn, $mysql1);
			return true;
		}else{
			return false;
		}
	}
	
	/* Kiem tra bai hat da ton tai trong bookinglist hay chua bang idUser va idSong*/
	function isCheckBookingList($conn, $idSong){
		$sql = "SELECT id_order, id_song, id_user FROM bookinglist WHERE id_song = '$idSong'";
		$result = mysqli_query($conn, $sql);
		$no_of_row = mysqli_num_rows($result);
		if($no_of_row>0){
			return true;
		}else{
			return false;
		}
	}
	function isCheckLimit($conn, $idUser){
		$sql = "SELECT id_user FROM bookinglist ORDER BY id_order DESC LIMIT 2";
		$result = mysqli_query($conn, $sql);
		$count=0;

		while ( $currUser = mysqli_fetch_array ( $result, MYSQLI_ASSOC ) ){
			$currId = $currUser["id_user"];
			if($idUser == $currId){
				$count++;
				}
		}
		if($count==2){
			return true;
			echo $count;
		}else{
			return false;
			echo $count;
		}
	}
	/* Kiem tra bai hat da ton tai trong favorite hay chua */
	function isCheckFavorite($conn, $idUser, $idSong){
		$sql = "SELECT id_song, id_user FROM favorite WHERE id_song = '$idSong' AND id_user = '$idUser'";
		$result = mysqli_query($conn, $sql);
		$no_of_row = mysqli_num_rows($result);
		if($no_of_row>0){
			return true;
		}else{
			return false;
		}
	}
	
	
	/* Luu bai hat vao favorite bang idUser va idSong*/
	function storeSongFavorite($conn, $idUser, $idSong){
		$sql = "INSERT INTO favorite(id_song, id_user) VALUES ($idSong, $idUser)";
		$result = mysqli_query($conn, $sql);
		if($result){
			return true;
		}else{
			return false;
		}
	}
	
	/* Lay toan bo thong tin bang username va pass dua vao mang */
	function getUserByUsernameAndPassword($conn, $username, $pass) {
		$sql = "SELECT * FROM users WHERE username = '$username'";
		$result = mysqli_query ($conn, $sql);
		$no_of_rows = mysqli_num_rows ( $result );
		if ($no_of_rows > 0) {
				
			$result = mysqli_fetch_array ( $result, MYSQLI_ASSOC );
			$salt = $result ['salt'];
			$encrypted_pass = $result ['pass'];
			$hash = checkhashSSHA ( $salt, $pass );
				
			if ($encrypted_pass == $hash) {
				return $result;
			}
		} else {
			return false;
		}
	}
	
	function updateUserByUsernameAndPassword($conn, $username, $pass, $newPass){
		$sql = "SELECT * FROM users WHERE username = '$username'";
		$result = mysqli_query ($conn, $sql);
		$no_of_rows = mysqli_num_rows ( $result );
		
		if($no_of_rows>0){
			$result = mysqli_fetch_array ( $result, MYSQLI_ASSOC );
			$salt = $result ['salt'];
			$encrypted_pass = $result ['pass'];
			$hash = checkhashSSHA ( $salt, $pass );
			if($encrypted_pass == $hash){
				$newHash = checkhashSSHA ( $salt, $newPass );
				$sql = "UPDATE users SET pass = '$newHash' WHERE username = '$username'";
				$result = mysqli_query ($conn, $sql);
				if($result){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	
	function updateSongByIdAndUser($conn, $idUserOne, $idSongOne, $idOrderOne, 
			$idUserTwo, $idSongTwo, $idOrderTwo){
		$sql = "UPDATE bookinglist SET id_song = '$idSongOne', id_user = '$idUserOne' WHERE id_order = '$idOrderOne';";
		$result = mysqli_query($conn, $sql);
		if($result){
			$sql2 = $sql = "UPDATE bookinglist SET id_song = '$idSongTwo', id_user = '$idUserTwo' WHERE id_order = '$idOrderTwo';";
			$result2 = mysqli_query($conn, $sql2);
			return true;
		}else{
			return false;
		}
	}
	
	/* Kiem tra username da ton tai hay chua */
	function isUserExisted($conn, $username) {
		$sql = "SELECT * FROM users WHERE username = '$username'";
		$result = mysqli_query ($conn, $sql);
		$no_of_rows = mysqli_num_rows ( $result );
		if ($no_of_rows > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/* Ma hoa mat khau dua vao mang key: salt, encrypted */
	function hashSSHA($password) {
		$salt = sha1 ( rand () );
		$salt = substr ( $salt, 0, 10 );
		$encrypted = base64_encode ( sha1 ( $password . $salt, true ) . $salt );
		$hash = array (
				"salt" => $salt,
				"encrypted" => $encrypted 
		);
		return $hash;
	}
	
	/* Kiem tra mat khau nhap vao co dung khong 
	 * input: salt, password
	 */
	function checkhashSSHA($salt, $password) {
		$hash = base64_encode ( sha1 ( $password . $salt, true ) . $salt );
		return $hash;
	}
	
?>

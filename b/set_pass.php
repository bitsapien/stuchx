<?php

//initializing database
require("db.php");
session_start();
// for forgot password 
if($_POST['submitResetPage'] == 'Change Password'){
	$new = $mysqli->real_escape_string(sha1($_POST['new_pass']));
	$cnf = $mysqli->real_escape_string(sha1($_POST['cnf_pass']));
	$token = $mysqli->real_escape_string($_POST['tok']);
	if($new == $cnf){
		$putpass = $mysqli->query('UPDATE people SET people_pass="'.$new.'" WHERE people_forget_key="'.$token.'";') or die($mysqli->error.__LINE__);
		$token_n = sha1(uniqid(mt_rand(), true));  // generate token

		$removetoken = $mysqli->query('UPDATE people SET people_forget_key="'.$token_n.'" WHERE people_forget_key="'.$token.'";') or die($mysqli->error.__LINE__);
		header('Location:login.php?alert=forget');
	}
	else {
		header('Location:reset.php?token='.$token.'&alert=nomatch');
	}


	
}

$user_id = $_SESSION['id'];
$old = sha1($_POST['old_pass']);
$new = sha1($_POST['new_pass']);
$cnf = sha1($_POST['cnf_pass']);
	// check if change password request is from a new user 
	$getMem = $mysqli->prepare('SELECT people_pass FROM people WHERE people_id=?') or die('Couldn\'t check the email');
	$getMem->bind_param('s', $user_id);
	$getMem->execute();
	$getMem->store_result();
	$getMem->bind_result($old_pass); // build cookie
	while($getMem->fetch()) {
		if($old == $old_pass){
			if($new == $cnf){
			$insert_row = $mysqli->query('UPDATE people SET people_pass="'.$new.'" WHERE people_id="'.$user_id.'";');
			echo 1;
			}
			else
			echo 0;

		}
		else{
			echo 0;
		}

	}

?>

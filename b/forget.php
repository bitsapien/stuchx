<?php
require("db.php");
require('modules.php');
$email = $mysqli->real_escape_string($_POST['ufemail']);


$getEmail = $mysqli->prepare('SELECT people_name FROM people WHERE people_email=?') or die('Couldn\'t check the email');
$getEmail->bind_param('s', $email);
$getEmail->execute();
$getEmail->store_result();
$countRows = $getEmail->num_rows; 

if($countRows == 1){
	$getEmail->bind_result($name_user);
	while($getEmail->fetch()) {
		$usr = $name_user;
	}
	$token = md5(uniqid(mt_rand(), true).$usr);  // generate token
	$insert_row = $mysqli->query('UPDATE people SET people_forget_key="'.$token.'" WHERE people_email="'.$email.'";') or die($mysqli->error.__LINE__); // store token

	// sending email

	
	$message = '<p> Hi '.$usr.',
<br><br>
You requested a password change , please click on the link below or paste it onto your address bar.
<br><br><center><a href="'.S_PATH.'reset.php?token='.$token.'">'.S_PATH.'reset.php?token='.$token.'</a>
<br><br><br><br>
<a href="'.S_PATH.'reset.php?token='.$token.'" style="text-align:center;text-decoration:none; padding:10px;font-size:1.2em;
	color:#FFF;
	background: linear-gradient(#830C0C, #830C0C) repeat scroll 0% 0% #1B5898;
	
	box-shadow: 0px -2px 0px rgba(0, 0, 0, 0.5) inset, 0px 2px 0px rgba(0, 0, 0, 0.1);">Click here to reset your password </a></center>

<br><br>If this was not you,  please notify us, by clicking <a href="'.S_PATH.'reset.php?token='.substr($token, -1).substr($token, 0, -1).'">here</a>.';

$mail_flag = stuch_mail($email,'Reset Password Request - STUCH Web-Console (no-reply)',$message);
}	
echo $mail_flag;


?>

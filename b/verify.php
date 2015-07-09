<?php
/*	Login Verification
*	by C Rahul,
*	website: http://crahul.eu.cr
*	email:   c.rahulx@gmail.com
*	twitter: CRahul92
*	facebook: http://www.facebook.com/rahul.wozniak
*
*	note : If you need a site built ,you can contact me ,or if 
*	you are a developer and you want to suggest changes ,feel 
*	free to contact me. :)
*/
ob_start();
require("db.php");

// Define $myusername and $mypassword
$email = $_POST['email'];
$pass = sha1($_POST['pass']);



$getEmail = $mysqli->prepare('SELECT people_id, people_name, people_dp, people_email, people_topCode,people_desc,people_oneLiner FROM people WHERE people_email=? AND people_pass=?') or die('Couldn\'t check the email');
$getEmail->bind_param('ss', $email, $pass);
$getEmail->execute();
$getEmail->store_result();
$countRows = $getEmail->num_rows;

if($countRows == 1){
	session_start();
	$getEmail->bind_result($id, $name, $dp, $emails,$topCode,$desc,$ol); // build cookie
	 while($getEmail->fetch()) {
	  $getPos = $mysqli->prepare('SELECT position_code,position_name FROM position WHERE position_people_id=?') or die('Couldn\'t check for position.');
	  $getPos->bind_param('s', $id);
	  $getPos->execute();
	  $getPos->store_result();
	  $getPos->bind_result($code, $pos_name);
	  $i = 0;
	  $_SESSION['score'] = $score[$topCode];
	  $_SESSION['topCode'] = $topCode;
	  while($getPos->fetch()) {
	   
	   $_SESSION['code'.$i] = $code;
	   $_SESSION['pos_name'.$i] =$pos_name;
	   $i++;
	  }
	  $_SESSION['id'] = $id;
	  $_SESSION['name'] = $name;
	  $_SESSION['dp'] = $dp;
	  $_SESSION['email'] = $emails;
	if(($desc == "")||($ol == "")||($dp =='')) // checking if incomplete profile
	  $_SESSION['incomplete'] = 'true';


	 }
// Updating last login
	 $now = date("Y-m-d H:i:s");
	 $sql="UPDATE people SET people_lastLogin='".$now."' WHERE people_id='".$id."'";

	 if($mysqli->query($sql) === false) {
	 trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
	 } else {
	 $last_inserted_id = $conn->insert_id;
	 $affected_rows = $conn->affected_rows;
	 }

	header("location:dash.php");

}
else {
	session_start();
	session_destroy();
	header("location:login.php?alert=wrong&email=".$email);exit;


}

ob_end_flush();
?>

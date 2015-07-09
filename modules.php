<?php

/* ==================================================================================================== 
*  Checks Votes for a position and returns who the candidate is and whether there has been a vote
*/

function check_vote($app_pos_id,$mysqli) {


$getVote = $mysqli->prepare('SELECT app_people_id, app_position_id FROM approval WHERE app_people_id=? AND app_position_id=?') or die('Couldn\'t check the vote.');
$getVote->bind_param('ss', $_SESSION['id'], $app_pos_id); // getting whether this person as voted for the position or not
$getVote->execute();
$getVote->store_result();
$countRows = $getVote->num_rows;

// get the person for whom this position as meant to be
$getVoteCandidate = $mysqli->prepare('SELECT position_people_id, position_addedBy FROM position WHERE position_id=?') or die('Couldn\'t check the vote.');
$getVoteCandidate->bind_param('s', $app_pos_id); // getting whether this person as voted for the position or not
$getVoteCandidate->execute();
$getVoteCandidate->store_result();
$getVoteCandidate->bind_result($candidate_id,$addedBy);
while($getVoteCandidate->fetch()) {
 $result['candid_id'] = $candidate_id; // Candidate ID
}



// Vote done or not

if($countRows == 1){
	$result['voted'] = 1;
}
else
	$result['voted'] = 0;

// Check if vote is initiated by source or candidate 
if(($candidate_id == $_SESSION['id'])||($addedBy == $_SESSION['id']))
	$result['voted'] = 2;
return $result;
}

/* ==================================================================================================== */


/* ==================================================================================================== 
*  Check if position active/waiting
*/

function is_role_active($app_pos_id,$mysqli) {

include('db-config.php');

// get the person for whom this position as meant to be
$getPos = $mysqli->prepare('SELECT position_code,position_approvalScore FROM position WHERE position_id=?') or die('Couldn\'t check the vote.');
$getPos->bind_param('s', $app_pos_id); // getting whether this person as voted for the position or not
$getPos->execute();
$getPos->store_result();
$getPos->bind_result($pos_code,$app_score);
while($getPos->fetch()) {
	if($app_score >= $min_score[$pos_code])
		return 1;

	else
		return 0;
 
}

}

/* ==================================================================================================== */
/* ==================================================================================================== 
*  Get Top Role for user
*/

function get_top_role($u_id,$mysqli) {

include('db-config.php');

// get the person for whom this position as meant to be
$getPosName = $mysqli->prepare('SELECT position_code,position_id FROM position WHERE position_people_id=?') or die('Couldn\'t check the vote.');

$getPosName->bind_param('s', $u_id); // getting whether this person as voted for the position or not
$getPosName->execute();
$getPosName->store_result();
$getPosName->bind_result($pos_code,$pos_id);
$base = 0;
while($getPosName->fetch()) {
	if(is_role_active($pos_id,$mysqli)){
		if($base < $score[$pos_code])
			$base = $score[$pos_code];
	
	}
$code = array_search($base, $score);

return $code;

 
}

}

/* ==================================================================================================== */
/* ==================================================================================================== 
 *  Deletion of roles/people
 * ====================================================================================================
 */



/*
 * Role Expiry : Deletes role when (last_updated was more than 7 days ago,and is not active ) or (deletionScore > 22)
 */
function role_expiry($mysqli) {

include('db-config.php');

// get the person for whom this position as meant to be
$getPosName = $mysqli->prepare('SELECT position_lastUpdate,position_deletionScore,position_id FROM position WHERE 1') or die('Couldn\'t check the vote.');

$getPosName->execute();
$getPosName->store_result();
$getPosName->bind_result($pos_last_updates,$pos_deletion_score,$pos_id);
$base = 0;
while($getPosName->fetch()) {
	$then =  strtotime($pos_last_updates);
	$now = time();
	$diff = $now - $then;
	$year_diff = date('Y',$diff);
	$mon_diff = date('n',$diff);
	$day_diff = date('j',$diff);
	// checking if 7 days have expired since last update
	if(($year_diff > 1970)||($mon_diff > 1))
		$day_diff = 8;

	if ((($day_diff > $expiry_days) && (is_role_active($pos_id,$mysqli)))||($pos_deletion_score > $position_deletion_ceil)) { // check if he is active or his deletion status is high
		$sql="DELETE FROM position WHERE position_id='".$pos_id."'"; // delete from positions
 
		if($mysqli->query($sql) === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
		} else {
		  $affected_rows = $mysqli->affected_rows;
		}
		$sql="DELETE FROM approval WHERE app_position_id='".$pos_id."'"; // delete from approvals
 
		if($mysqli->query($sql) === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
		} else {
		  $affected_rows = $mysqli->affected_rows;
		}
	}
	
}

}

/* ==================================================================================================== */
/*
 * People Expiry : Deletes user when (no role in more than 7 days ) or (deletionScore > 28)
 */
function people_expiry($mysqli) {

include('db-config.php');

// get the person for whom this position as meant to be
$getPplName = $mysqli->prepare('SELECT people_lastUpdate,people_deletionScore,people_id FROM people WHERE 1') or die('Couldn\'t check the vote.');

$getPplName->execute();
$getPplName->store_result();
$getPplName->bind_result($last_updates,$deletion_score,$user_id);
$base = 0;
while($getPplName->fetch()) {
	$then =  strtotime($last_updates);
	$now = time();
	$diff = $now - $then;
	$year_diff = date('Y',$diff);
	$mon_diff = date('n',$diff);
	$day_diff = date('j',$diff);
	// checking if 7 days have expired since last update
	if(($year_diff > 1970)||($mon_diff > 1))
		$day_diff = 8;
	// checking for roles
	$getRole = $mysqli->prepare('SELECT position_id FROM position WHERE position_people_id=?') or die('Couldn\'t check the email');
	$getRole->bind_param('s', $user_id);
	$getRole->execute();
	$getRole->store_result();
	$countRows = $getRole->num_rows;




	if ((($day_diff > $expiry_days) && ($countRows == 0))||($deletion_score > $people_deletion_ceil)) { // check if he has roles or his deletion status is high
		$sql="DELETE FROM position WHERE position_people_id='".$user_id."'"; // delete from positions
 
		if($mysqli->query($sql) === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
		} else {
		  $affected_rows = $mysqli->affected_rows;
		}
		$sql="DELETE FROM approval WHERE app_position_id='".$user_id."'"; // delete from approvals
 
		if($mysqli->query($sql) === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
		} else {
		  $affected_rows = $mysqli->affected_rows;
		}
		$sql="UPDATE people SET people_archive=1 WHERE people_id='".$user_id."'"; // archive person
 
		if($mysqli->query($sql) === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
		} else {
		  $affected_rows = $mysqli->affected_rows;
		}
	}
	
}

}

/* ==================================================================================================== */
/* ==================================================================================================== */
/*
 * Mail-Sender : Sends an email when $to,$subject,$message are given
 */
function stuch_mail($to,$subject,$message) {

include('db-config.php');
	$headers = "From: ".$send_address."\r\n";
	$headers .= "BCC: ".$send_address_bcc."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$top = '<html>
<body style=" display: block;
    margin:  20px auto;
    padding: 12px;
    -webkit-box-shadow: 0px 0px 5px rgba(0,0,0,.8);
    -moz-box-shadow: 0px 0px 5px rgba(0,0,0,.8);
    box-shadow: 0px 0px 5px rgba(0,0,0,.8);
    position: relative; 
    background-color:#fff ;font-family:\'Gotham Narrow Light\',\'Gotham Book\', Gotham-Book,\'Lato\', sans-serif;">

<img src="'.S_PATH.'img/logo-stuch.png"/><img style="float:right;" src="'.S_PATH.'img/aditya-big.png"/>';
	$bottom = '<br><br>
Regards,
<br><em>STUCHx-WebTeam.</em><br><br>
			<center>Copyright 2014, STUCHx <img src="'.S_PATH.'img/aditya.png" class="footer-img" /> | For any queries or suggestions ,mail to : '.$send_email.'</center>
</body>
</html>';
	$msg = $top.$message.$bottom;
	if (@mail($to, $subject, $msg, $headers)) {
		return 1;header("Location:mam.php");
	} else {
		return 0;
	}


}
/* ==================================================================================================== */
?>

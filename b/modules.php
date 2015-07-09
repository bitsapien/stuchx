<?php
/* ==================================================================================================== 
*  Checks whether input string is contact or phone or link and format it accordingly
*/
function format_contact($string,$name) {
			// Script to find out whether the number is a phone number , an email or a link ?
			if ( preg_match( '/^[+]?([\d]{0,3})?[\(\.\-\s]?([\d]{3})[\)\.\-\s]*([\d]{3})[\.\-\s]?([\d]{4})$/', $string ) ) 
        			$res = '<i class="fa fa-phone-square"></i><a href="tel:'.$string.'" > '.$string.'</a>'; // phone -number
    			else if(preg_match_all('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', $string,$op)) {
				if ( (strpos(  $string, 'http://' ) == 0) || (strpos(  $string , 'https://') == 0) ) 
        				$link = $string;// without http
				else 
        				$link = 'http://'.$string; // with http
				$i = '<i class="fa fa-external-link-square"></i>';
				$n = $link;
				if(strpos($link,'facebook.com')){
					$i = '<i class="fa fa-facebook-square"></i>';$n = $op[6][0];
				}
				if(strpos($link,'twitter.com')) {
					$i = '<i class="fa fa-twitter-square"></i>';$n = $op[6][0]; }
				if(strpos($link,'linkedin.com')) {
					$i = '<i class="fa fa-linkedin-square"></i>';$n = $name; }
				if(strpos($link,'pinterest.com')) {
					$i = '<i class="fa fa-pinterest-square"></i>';$n = $op[6][0]; }
				if(strpos($link,'tumblr.com')) {
					$i = '<i class="fa fa-tumblr-square"></i>';$n = preg_replace("/(http\:\/\/|https\:\/\/)/", "", $link); }
				if(strpos($link,'reddit.com')) {
					$i = '<i class="fa fa-reddit-square"></i>';$n = substr($op[6][0], 5); }
				if(strpos($link,'youtube.com')) {
					$i = '<i class="fa fa-youtube-square"></i>';$n = $op[6][0]; }
				if(strpos($link,'wordpress.com')) {
					$i = '<i class="fa fa-wordpress"></i>';$n = preg_replace("/(http\:\/\/|https\:\/\/)/", "", $link); }
				if(strpos($link,'github.com')) {
					$i = '<i class="fa fa-github-square"></i>';$n = $op[6][0]; }
				if(strpos($link,'instagram')) {
					$i = '<i class="fa fa-instagram-square"></i>';$n = $op[6][0]; }
				if(strpos($link,'soundcloud')) {
					$i = '<i class="fa fa-soundcloud"></i>';$n = $op[6][0]; }
				$res = $i.'<a href="'.$link.'" target="_blank" title="'.$link.'"> '.$n.'</a>';

    			}
    			else if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i', $string )) {
        				$res = '<i class="fa fa-envelope-square"></i><a href="mailto:'.$string.'" >'.$name.'</a>'; // email
    			}
			else {
				$res = '<i class="fa fa-skype"></i>'.$string; // skype
			}
return $res;
}
/* ==================================================================================================== */
	
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

function is_role_active($app_pos_id,$mysqli,$ppl_id) {

include('db-config.php');

// get the person for whom this position as meant to be
$getPos = $mysqli->prepare('SELECT position_code FROM position WHERE position_id=?') or die('Couldn\'t check the vote.');
$getPos->bind_param('s', $app_pos_id); // getting whether this person as voted for the position or not
$getPos->execute();
$getPos->store_result();
$getPos->bind_result($pos_code);
$app_score = get_percent_on_position($app_pos_id,$mysqli,$ppl_id);

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
	if(is_role_active($pos_id,$mysqli,$u_id)){
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
 * Role Expiry : Deletes role when (last_updated was more than 7 days ago,and is not active ) or (deletionScore > 0.88)
 */
function role_update($mysqli) {

include('db-config.php');

// get the person for whom this position as meant to be
$getPosName = $mysqli->prepare('SELECT position_lastUpdate,position_id,position_code,position_people_id FROM position WHERE 1') or die('Couldn\'t check the vote.');

$getPosName->execute();
$getPosName->store_result();
$getPosName->bind_result($pos_last_updates,$pos_id,$pos_code,$pos_ppl);
$base = 0;
while($getPosName->fetch()) {
	$pos_deletion_score = get_deletion_on_position($pos_id,$mysqli);
	
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
	// Updating the topCode
$getPplName = $mysqli->prepare('SELECT people_id FROM people WHERE people_archive=0') or die('Couldn\'t check the ppl.');

$getPplName->execute();
$getPplName->store_result();
$getPplName->bind_result($ppl_id);

while($getPplName->fetch()) {
		$where['people_id'] = $ppl_id;
		$upd['people_topCode'] = get_top_role($ppl_id,$mysqli);
		do_sql('people',$upd,'update',$mysqli,$where);
	

}

}

/* ==================================================================================================== */
/*
 * People Expiry : Deletes user when (no role in more than 7 days ) or (deletionScore > 28)
 */
function people_expiry($mysqli) {

include('db-config.php');

// get the person for whom this position as meant to be
$getPplName = $mysqli->prepare('SELECT people_lastUpdate,people_id FROM people WHERE 1') or die('Couldn\'t check the vote.');

$getPplName->execute();
$getPplName->store_result();
$getPplName->bind_result($last_updates,$user_id);
$base = 0;
while($getPplName->fetch()) {
	$deletion_score = get_deletion_on_people($user_id,$mysqli);
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
/* ==================================================================================================== 
*  Check if vote has been cast on a post
*/

function check_vote_on_post($blog_id,$mysqli) {

include('db-config.php');

// get the person for whom this position as meant to be
$getVote = $mysqli->prepare('SELECT votes_blog_id FROM votes WHERE votes_people_id=? AND votes_blog_id=?') or die('Couldn\'t check the vote.');
$getVote->bind_param('ss', $_SESSION['id'],$blog_id); // getting whether this person as voted for the position or not
$getVote->execute();
$getVote->store_result();
$getVote->bind_result($id);
$countRows = $getVote->num_rows;

return $countRows;

}

/* ==================================================================================================== */

/* ==================================================================================================== 
*  Get votes that have been cast on a post
*/

function get_votes_on_post($blog_id,$mysqli) {

include('db-config.php');

// get the person for whom this position as meant to be
$getVote = $mysqli->prepare('SELECT ppl.people_topCode FROM votes v INNER JOIN people ppl ON ppl.people_id = v.votes_people_id  WHERE v.votes_blog_id=?') or die('Couldn\'t check the sum of votes.');
$getVote->bind_param('s',$blog_id); // getting whether this person as voted for the position or not
$getVote->execute();
$getVote->store_result();
$getVote->bind_result($code);
$sum = 0;
while($getVote->fetch()) {
	$sum = $sum +$score[$code];
}

return $sum;

}

/* ==================================================================================================== */
/* ==================================================================================================== 
*  Get approval votes that have been cast on a position
*/

function get_percent_on_position($pos_id,$mysqli,$ppl_id) {

include('db-config.php');

// get the person for whom this position as meant to be
$getVote = $mysqli->prepare('SELECT ppl.people_topCode FROM approval v INNER JOIN people ppl ON ppl.people_id = v.app_people_id  WHERE v.app_position_id=? AND ppl.people_id!=?') or die('Couldn\'t check the sum of votes for pos.');
$getVote->bind_param('ss',$pos_id,$ppl_id); // getting whether this person as voted for the position or not
$getVote->execute();
$getVote->store_result();
$getVote->bind_result($code);
$sum = 2;
while($getVote->fetch()) {
	$sum = $sum +$score[$code];
}
$tot = get_strength($ppl_id,$mysqli);
$strength = $sum/$tot;

return $strength;

}

/* ==================================================================================================== */
/* ==================================================================================================== 
*  Get deletion votes that have been cast on a position
*/

function get_deletion_on_position($pos_id,$mysqli) {

include('db-config.php');

// get the person for whom this position as meant to be
$getVote = $mysqli->prepare('SELECT ppl.people_topCode FROM deletion v INNER JOIN people ppl ON ppl.people_id = v.deletion_people_id  WHERE v.deletion_tbl_id=? AND deletion_tbl=? ') or die('Couldn\'t check the sum of deletion votes for pos.');
$tbl = 'position';
$getVote->bind_param('ss',$pos_id, $tbl); // getting whether this person as voted for the position or not
$getVote->execute();
$getVote->store_result();
$getVote->bind_result($code);
$sum = 2;
while($getVote->fetch()) {
	$sum = $sum +$score[$code];
}
$tot = get_strength($ppl_id,$mysqli);
$strength = $sum/$tot;

return $strength;

}

/* ==================================================================================================== */
/* ==================================================================================================== 
*  Get deletion votes that have been cast on a position
*/

function get_deletion_on_people($ppl_id,$mysqli) {

include('db-config.php');

// get the person for whom this position as meant to be
$getVote = $mysqli->prepare('SELECT ppl.people_topCode FROM deletion v INNER JOIN people ppl ON ppl.people_id = v.deletion_people_id  WHERE v.deletion_tbl_id=? AND deletion_tbl=? ') or die('Couldn\'t check the sum of deletion votes for pos.');
$tbl = 'people';
$getVote->bind_param('ss',$ppl_id, $tbl); // getting whether this person as voted for the position or not
$getVote->execute();
$getVote->store_result();
$getVote->bind_result($code);
$sum = 2;
while($getVote->fetch()) {
	$sum = $sum +$score[$code];
}
$tot = get_strength($ppl_id,$mysqli);
$strength = $sum/$tot;

return $strength;

}

/* ==================================================================================================== */
/* ==================================================================================================== 
*  Get total votes strength
*/

function get_strength($ppl_id,$mysqli) {

include('db-config.php');

// get the person for whom this position as meant to be
$getVote = $mysqli->prepare('SELECT people_topCode FROM people WHERE people_topCode=? OR people_topCode=? OR people_topCode=? AND people_id!=?') or die('Couldn\'t check the sum of votes fot strength.');

$getVote->bind_param('ssss',$roles['Editor-in-Chief'],$roles['Editor'],$roles['Heads of Groups / Directors'],$ppl_id); // getting whether this person as voted for the position or not
$getVote->execute();
$getVote->store_result();
$getVote->bind_result($code);
$sum = 0;
while($getVote->fetch()) {
	$sum = $sum +$score[$code];
}

return $sum;

}

/* ==================================================================================================== */
/* ==================================================================================================== 
*  Get number of notifs
*/

function get_notif_count($blog_id,$mysqli) {

	// code for responses
	$getResponseC = $mysqli->prepare('SELECT response_id FROM response WHERE response_blog_id=? AND response_seen=? ') or die('Couldn\'t check the responses');
	$seen = '0';
	$getResponseC->bind_param('ss', $blog_id,$seen);
	$getResponseC->execute();
	$getResponseC->store_result();
	$countRows = $getResponseC->num_rows;
	$num = $countRows;
	return $num;

}
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
			<center>Copyright 2014, STUCHx <img src="'.S_PATH.'img/aditya.png" class="footer-img" /> | Contact us on : '.$send_email.'</center>
</body>
</html>';
	$msg = $top.$message.$bottom;
	if (@mail($to, $subject, $msg, $headers)) {
		return 1;
	} else {
		return 0;
	}


}
/* ==================================================================================================== */
?>

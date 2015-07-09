<?php
session_start();
include('db.php');
include('modules.php');


$id  = $mysqli->real_escape_string($_GET['id']);
$getResponse = $mysqli->prepare('SELECT ppl.people_id,res.response_text, ppl.people_name, res.response_lastUpdated FROM response res
INNER JOIN people ppl ON res.response_people_id = ppl.people_id WHERE res.response_blog_id=?') or die('Couldn\'t check the responses');
$getResponse->bind_param('s', $id);
$getResponse->execute();
$getResponse->store_result();
$getResponse->bind_result($pid, $text, $pname, $time);
while($getResponse->fetch()) {
	$res .= '
			
				<h4><a href="profile.php?id='.$pid.'" title="Visit Profile">'.$pname.'</a></h4> <small> '.$time.' </small>
				<div class="well">
					'.$text.'
				</div>
			
		
 ';
}
// updating seen-state
$send = '';
$where = '';
$send['response_seen'] = 1;
$where['response_blog_id'] = $id ;
do_sql('response',$send,'update',$mysqli,$where);
echo $res;
?>

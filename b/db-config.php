<?php 
/* PROCEED WITH CAUTION */

/* =========================================== DATABASE AND URL CONFIGURATION ======================================================*/
define('SQL_HOST','localhost');define('SQL_USER','stuchx');define('SQL_PASS','dUXwB4xBQDqfbWsR');define('SQL_DB','stuchx');define('S_PATH','http://ecourses.aec.edu.in/aditya/stuchx/b/');define('SERVER_PATH','/aditya/stuchx/b');
/* ================================================================================================================================ */
/* ======================================= SCORES AND HIERARCHY CONFIGURATION ======================================================*/
$score = array(	"EDC" => 6,
		"EDT" => 4,
		"DIR" => 2,
		"MOD" => 1,
		"OTH" => 0);
$roles = array(	"Editor-in-Chief"=>"EDC",
		"Editor"=>"EDT",
		"Heads of Groups / Directors"=>"DIR",
		"Moderator"=>"MOD",
		"Other"=>"OTH");
/* minimum scores for activation
 * EDT - 12
 * EDC - 16
 * DIR - 12
 * MOD - 8
 */

$min_score['EDT'] = 0.5;
$min_score['EDC'] = 0.6;
$min_score['DIR'] = 0.5;
$min_score['MOD'] = 0.2;
$min_score['OTH'] = 0.0;
/* ================================================================================================================================ */
/* ======================================= EXPIRY RULES CONFIGURATION ============================================================*/
$expiry_days = 7; // Expiry of user-role if no one votes within 7 days
$position_deletion_ceil = 22; // Min score needed to delete a position
$people_deletion_ceil = 28; // Min score needed to delete a person
$blog_approval_ceil = 0; // Min score needed for blog to be published
/* ================================================================================================================================ */
/* ======================================= LIST OF PAGES CONFIGURATION =============================================================*/
$common_pages = array('header.php','footer.php');
$front_pages = array('about.php','browse.php','dash.php','edit_profile.php','post.php','profile.php','users.php','login.php','forget.php','reset.php','write.php');
$ajax_pages = array('editProfile_all','position_all.php','responses.php');
$bg_pages = array('db.php','db-config.php','filehandler.php','index.php','logout.php','submit.php','verify.php','set_pass.php');
/* ================================================================================================================================ */
/* ===================================== PERMISSIONS FOR PAGE ACCESS CONFIGURATION =================================================*/
// EDC
$perm['EDC'] = array_merge($common_pages,$front_pages,$ajax_pages,$bg_pages);
// EDT
$front_pages_copy = $front_pages;

$perm['EDT'] =  array_merge($common_pages,$front_pages_copy,$ajax_pages,$bg_pages);
// DIR

$perm['DIR'] = $perm['EDT'];

// MOD
if (($key = array_search('users.php', $front_pages_copy)) !== false) {
    unset($front_pages_copy[$key]);
}

$perm['MOD'] = array_merge($common_pages,$front_pages_copy,$bg_pages);
// OTH
$perm['OTH'] = array_merge($common_pages,$front_pages_copy,$bg_pages);
// Non-users
if (($key = array_search('edit_profile.php', $front_pages_copy)) !== false) {
    unset($front_pages_copy[$key]);
}
if (($key = array_search('write.php', $front_pages_copy)) !== false) {
    unset($front_pages_copy[$key]);
}
if (($key = array_search('dash.php', $front_pages_copy)) !== false) {
    unset($front_pages_copy[$key]);
}
$bg_pages_copy = $bg_pages;
if (($key = array_search('logout.php', $bg_pages_copy)) !== false) {
    unset($bg_pages_copy[$key]);
}
if (($key = array_search('filehandler.php', $bg_pages_copy)) !== false) {
    unset($bg_pages_copy[$key]);
}


$perm['non'] = array_merge($common_pages,$front_pages_copy,$bg_pages_copy);


//echo SERVER_PATH.$perm['OTH'][0];
//echo '<code>';
//print_r($perm);
//echo '</code><br>';
//print_r($_SERVER);
session_start();
$server = SERVER_PATH;
$file_name = substr($_SERVER['PHP_SELF'],strlen($server));

//if($_SESSION['id']!='') {
	//if(!in_array($file_name,$perm[$_SESSION['topCode']])){
		//echo '<script>window.location.assign("'.S_PATH.'error/403.html");</script>';exit;}
//}
//else { 
	//if(!in_array($file_name,$perm['non'])){
		//echo '<script>window.location.assign("'.S_PATH.'error/403.html");</script>';exit;}

//}
/* ================================================================================================================================ */
/* ============================================== MAIL CONFIGURATION ===============================================================*/

$send_address = 'stuchx.web@aec.edu.in';
$send_address_bcc = 'c.rahulx@gmail.com';
$send_email = 'stuchx.data@gmail.com';
/* ================================================================================================================================ */
/* ============================================== SOCIAL LINKS CONFIGURATION ===============================================================*/

$social['facebook'] = 'http://www.facebook.com/STUCHx';
$social['twitter'] = 'http://www.twitter.com/STUCHx';

/* ================================================================================================================================ */

?>

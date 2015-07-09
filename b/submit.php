<?php
/* Submit Module */

// Recieves POST data

// session begin
session_start();
echo 11;
// DB
include('db.php');
include('filehandler.php');
include('modules.php');

switch($_POST['flag']) {
	case 'oneLiner' :
		if($_POST['oL']!="") {
			$where['people_id'] = $_SESSION['id'];
			$send['people_oneLiner'] =   htmlspecialchars($_POST['oL']);
			echo do_sql('people',$send,'update', $mysqli, $where);
		}
	break;
	case 'Desc' :
		if($_POST['desc']!="") {
			$where['people_id'] = $_SESSION['id'];
			$send['people_desc'] =   htmlspecialchars(nl2br($_POST['desc']));
			echo do_sql('people',$send,'update', $mysqli,$where);
		}
	break;
	case 'addContact' :
		if(($_POST['cLink']!="")) {
			$where['contact_link'] = $_POST['cLink'];			
			$send['contact_link'] = $_POST['cLink'];
			$send['contact_people_id'] = $_SESSION['id'];
			echo do_sql('contact',$send,'insert', $mysqli,$where);
		}
	break;
	case 'addUser' :
			$where['people_name'] = $_POST['uname'];
			$where['people_roll'] = $_POST['urefno'];
			$where['people_email'] = $_POST['uemail'];
			$send['people_name'] = $_POST['uname'];
			$send['people_roll'] = $_POST['urefno'];
			$send['people_email'] = $_POST['uemail'];
			$pass = $_POST['urefno'].rand(1,99999);
			$send['people_pass'] = sha1($pass);			

			// Auto-detecting whether student or faculty
			if((ctype_alnum( $send['people_roll']))&&(strlen( $send['people_roll']) == 10))
				$send['people_sf'] = 'S';
			else if(ctype_digit( $send['people_roll']))
				$send['people_sf'] = 'F';
			else {
				echo 'Error in Roll Number/ Employee ID. Go back and try again.';exit; }
			$send['people_addedBy'] = $_SESSION['id'];
			$send['people_topCode'] = 'OTH';
			$send['people_created'] = date("Y-m-d H:i:s");
			$send['people_forget_key'] = sha1(uniqid(mt_rand(), true));
			$msg = '<p> Hi '.$send['people_name'].',
				<br><br>
				A user account was just created for you on STUCHx. You may login using the link below. We suggest you change your password, on your first login.
				<br><br>
				Your Login Credentials 
				<br>
				<br>Username : <b>'.$send['people_email'].'</b>
				<br>Password : <b>'.$pass.'</b>
				<center><a href="'.S_PATH.'login.php">'.S_PATH.'login.php</a>
				<br><br><br><br>
				<a href="'.S_PATH.'" style="text-align:center;text-decoration:none; padding:10px;font-size:1.2em;
					color:#FFF;
					background: linear-gradient(#830C0C, #830C0C) repeat scroll 0% 0% #1B5898;

					box-shadow: 0px -2px 0px rgba(0, 0, 0, 0.5) inset, 0px 2px 0px rgba(0, 0, 0, 0.1);">Login to STUCHx</a></center>
				';


		if(($_POST['uname']!="")&&($_POST['urefno']!="")&&($_POST['uemail']!="") && ($_FILES['udp']['name'] != "")&&(($_SESSION['topCode']=='EDC')||($_SESSION['topCode']=='EDT')||($_SESSION['topCode']=='DIR'))) {
			// File Handling
 			$link = fileHandler($_FILES['udp'], "img");
			if(($link !='File Dimensions incorrect.')&&($link !='There was an error processing the file.')&&($link !='File Type Incorrect!')&&($link !='Conversion to grayscale failed.')) { 
				$send['people_dp'] = 'img/profiles/'.$link;
				$re = do_sql('people', $send, 'insert', $mysqli,$where);
				$mail_flag = stuch_mail($send['people_email'],'You have been registered at STUCHx',$msg);		
				if($re == 'Duplicate Entry')
				header('Location:users.php?error=dup');
				else
				header('Location:users.php?done=1');
			}
			else
				header('Location:users.php?error=img');
		}
		else {
			if(($_POST['uname']!="")&&($_POST['urefno']!="")&&($_POST['uemail']!="")&&($_POST['external'] == 'true' )) {

				$send['people_addedBy'] = '-1';

				$re = do_sql('people', $send, 'insert', $mysqli,$where);
				$mail_flag = stuch_mail($send['people_email'],'You have been registered at STUCHx',$msg);
				// Position
				$send['position_name'] = 'Contributor';
				$send['position_code'] = 'OTH';
				$send['position_people_id'] = $re;
				$send['position_addedBy'] = '-1';

				echo do_sql('position',$send,'insert', $mysqli);

		
				if($re == 'Duplicate Entry')
					header('Location:login.php?alert=dup');
				else
					header('Location:login.php?alert=reg');
			}
			else	{
			echo '<script>alert("There was an error.Retry.");window.location.assign("'.S_PATH.'users.php");</script>';exit; }
		
		}
		break;
	case 'editUser' :
		if(($_POST['uname']!="")&&($_POST['urefno']!="")&&($_POST['uemail']!="")&&($_POST['usf']!="") && ($_FILES['udp']['name'] != "")&&(($_SESSION['topCode']=='EDC')||($_SESSION['topCode']=='EDT')||($_SESSION['topCode']=='DIR'))) {
			$where['people_id'] = $_POST['uid'];
			$send['people_name'] = $_POST['uname'];
			$send['people_roll'] = $_POST['urefno'];
			$send['people_email'] = $_POST['uemail'];
			$send['people_sf'] = $_POST['usf'][0];

			// File Handling
 			$link = fileHandler($_FILES['udp'], "img");
			if(($link !='File Dimensions incorrect.')||($link !='There was an error processing the file.')||($link !='File Type Incorrect!')) {
				$send['people_dp'] = 'img/profiles/'.$link;
				echo do_sql('people', $send, 'update', $mysqli,$where);
				header('Location:users.php?done=1');
			}
			else
				echo $link;		
		}
		else if(($_POST['uname']!="")&&($_POST['urefno']!="")&&($_POST['uemail']!="")&&(($_SESSION['topCode']=='EDC')||($_SESSION['topCode']=='EDT')||($_SESSION['topCode']=='DIR'))){
			$where['people_id'] = $_POST['uid'];
			$send['people_name'] = $_POST['uname'];
			$send['people_roll'] = $_POST['urefno'];
			$send['people_email'] = $_POST['uemail'];
			echo do_sql('people', $send, 'update', $mysqli,$where);
			header('Location:users.php?done=1');
		
		}
		else {
			echo '<script>alert("There was an error.Retry.");window.location.assign("'.S_PATH.'users.php");</script>';
		
		}
		break;
	case 'addPos' :
		if(($_POST['pos_name']!="")&&($_POST['pos_code']!="")&&($_POST['pos_person']!="")&&($_POST['pos_by']!="")&&(($_SESSION['topCode']=='EDC')||($_SESSION['topCode']=='EDT')||($_SESSION['topCode']=='DIR'))) {
			$send['position_name'] = $_POST['pos_name'];
			$send['position_code'] = $roles[$_POST['pos_code']];
			$send['position_people_id'] = $_POST['pos_person'];
			$send['position_addedBy'] = $_POST['pos_by'];
			echo do_sql('position',$send,'insert', $mysqli,$where);
			header('Location:users.php?done=1');
		}
			
	break;
	case 'removeContact' :
		if(($_POST['cid']!="")) {
			$where['contact_id'] = $_POST['cid'];
			$where['contact_people_id'] = $_SESSION['id'];
			$send = '';
			echo do_sql('contact',$send,'delete', $mysqli, $where);
		}
	break;
	case 'delRecPeople' :
		if(($_POST['did']!="")&&(($_SESSION['topCode']=='EDC')||($_SESSION['topCode']=='EDT')||($_SESSION['topCode']=='DIR'))) {
			// checking whether there exists a deletion vote
			$getDel = $mysqli->prepare('SELECT deletion_tbl_id, deletion_people_id FROM deletion WHERE deletion_tbl=? AND deletion_tbl_id=? AND deletion_people_id=?') or die('Couldn\'t check the email');
			$table = "people";
			$getDel->bind_param('sss', $table, $_POST['did'], $_SESSION['id']);
			$getDel->execute();
			$getDel->store_result();
			$countRows = $getDel->num_rows;

			if($countRows == 1){

			$where['deletion_tbl_id'] = $_POST['did'];
			$where['deletion_people_id'] = $_SESSION['id'];
			$where['deletion_tbl'] = $table;
			$send = '';
			echo do_sql('deletion',$send,'delete', $mysqli, $where); // deleted entry

			}
			else{

			$send['deletion_tbl_id'] = $_POST['did'];
			$send['deletion_people_id'] = $_SESSION['id'];
			$send['deletion_tbl'] = $table;
			$where = '';
			echo do_sql('deletion',$send,'insert', $mysqli); // inserted entry
			}
		}
	break;
	case 'upDnVote' :
		if(($_POST['pos']!="")&&(($_SESSION['topCode']=='EDC')||($_SESSION['topCode']=='EDT')||($_SESSION['topCode']=='DIR'))) {
			$pos_id  = $mysqli->real_escape_string($_POST['pos']);
			$vote_valid = check_vote($pos_id,$mysqli); // vote check



			if($vote_valid['voted']) {
				if($vote_valid['voted'] == 2){
					echo 0;exit;
				}
				// deleting a vote 
				$send = '';
				$where['app_position_id'] = $pos_id;
				$where['app_people_id'] = $_SESSION['id'];
				do_sql('approval',$send,'delete',$mysqli,$where);

	
	
			}
			else {
	
				// inserting a vote 
				$send['app_position_id'] = $pos_id;
				$send['app_people_id'] = $_SESSION['id'];
				do_sql('approval',$send,'insert',$mysqli);


			}

				// update top role
				// fetching user id
				$getUID = $mysqli->prepare('SELECT position_people_id FROM position WHERE position_id=? ') or die('Couldn\'t check the userid');
				$getUID->bind_param('s', $pos_id);
				$getUID->execute();
				$getUID->store_result();
				$getUID->bind_result($user_id); 
				while($getUID->fetch()) {
					$top_role = get_top_role($user_id,$mysqli);
				}		

		}
	break;
	case 'blog_add' :
		
		if($_POST['submit'] == 'Save & Publish')
			$send['blog_final'] = '1';
		if($_POST['submit'] == 'Save')
			$send['blog_final'] = '0';

		$send['blog_by'] = $_SESSION['id'];
		$send['blog_title'] = htmlentities($_POST['blog_title']);
		$send['blog_created'] = date("Y-m-d H:i:s");

		/** ------------------------------------------image digestion starts---------------------------------------- **/
		// separating out all image tags

/**
		$doc = new DOMDocument();
		@$doc->loadHTML($_POST['blog_post']);

		$tags = $doc->getElementsByTagName('img');
		$i = 0;
		foreach ($tags as $tag) {

		       $each_one[$i] = $tag->getAttribute('src');
			$i++;
		}
		$num_of_images = $i;
		$i = 0;
		for(;$i<$num_of_images;$i++){
			$each = $each_one[$i];
			define('UPLOAD_DIR', 'blog_images/');
			$img = $each;
			$img = str_replace('data:;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$data = base64_decode($img);
			$file[$i] = UPLOAD_DIR . uniqid() . '.png';echo $data;
			$success = file_put_contents($file[$i], $data);
			print $success ? $file : 'Unable to save the file.'; 
			$i++;
			// now embedding path to src
			str_replace($each, $file[$i],$_POST['blog_post']);
		}
		/** ------------------------------------------image digestion ends---------------------------------------- **/

			$send['blog_post'] = htmlentities($_POST['blog_post']);

//echo $send['blog_post'];
exit();

		if(($_POST['blog_id'] == '')||($_POST['blog_id'] == '0')) {
			if(($send['blog_by']!='')&&($send['blog_title']!='')&&($send['blog_post']!='')&&($send['blog_created']!='')&&($send['blog_final']!=''))
			echo do_sql('blog',$send,'insert',$mysqli);

		}
		else  {
			if(($send['blog_by']!='')&&($send['blog_title']!='')&&($send['blog_post']!='')&&($send['blog_created']!='')&&($send['blog_final']!='')) {
			$where['blog_id'] = $_POST['blog_id'];
			echo do_sql('blog',$send,'update',$mysqli,$where);}

		}
		header('Location:write.php?done=1');			
	break;
	case 'delBlog':
		if(($_POST['bid']!="")&&($_SESSION['id']!='')) {
				// deleting a blog 
				$send = '';
				$where['blog_id'] = $_POST['bid'];
				$where['blog_by'] = $_SESSION['id'];
				do_sql('blog',$send,'delete',$mysqli,$where);
		}	
	break;
	case 'voteBlog':
		if(($_POST['bid']!="")&&($_SESSION['id']!='')) {
				// deleting a blog 
				$send = '';
				$res = check_vote_on_post($_POST['bid'],$mysqli);
				$where['votes_blog_id'] = $_POST['bid'];
				$where['votes_people_id'] = $_SESSION['id'];
				if($res)					
					do_sql('votes',$send,'delete',$mysqli,$where);
				else
					do_sql('votes',$where,'insert',$mysqli,$where['votes_blog_id'].' AND '.$where['votes_people_id']);
		}	
	break;
	case 'send_response':
		if(($_POST['bid']!="")&&($_SESSION['id']!='')&&($_POST['sendResponse']!="")) {
				// deleting a blog 
				$send['response_blog_id'] = $_POST['bid'];
				$send['response_people_id'] = $_SESSION['id'];
				$send['response_text'] = htmlentities($_POST['sendResponse']);
				do_sql('response',$send,'insert',$mysqli,$send);
		}	
	break;
	case 'approvePeople' :
		if($_POST['pid']!="") {
			$where['people_id'] = $_POST['pid'];
			$send['people_addedBy'] = '0';
			echo do_sql('people',$send,'update', $mysqli,$where);
		}
	break;


}
echo 1;
 

?>

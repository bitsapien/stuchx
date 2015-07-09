<?php
/* Submit Module */

// Recieves POST data

// session begin
session_start();

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
		if(($_POST['cName']!="")&&($_POST['cName']!="")) {
			$send['contact_name'] = $_POST['cName'];
			$send['contact_link'] = $_POST['cLink'];
			$send['contact_people_id'] = $_SESSION['id'];
			echo do_sql('contact',$send,'insert', $mysqli);
		}
	break;
	case 'editContact' :
		if(($_POST['cName']!="")&&($_POST['cName']!="")) {
			$where['contact_id'] = $_POST['id'];
			$send['contact_name'] = $_POST['cName'];
			$send['contact_link'] = $_POST['cLink'];
			echo do_sql('contact',$send,'update', $mysqli,$where);
		}
	break;
	case 'addUser' :
		if(($_POST['uname']!="")&&($_POST['urefno']!="")&&($_POST['uemail']!="") && ($_FILES['udp']['name'] != "")) {
			$send['people_name'] = $_POST['uname'];
			$send['people_roll'] = $_POST['urefno'];
			$send['people_email'] = $_POST['uemail'];
			$send['people_pass'] = sha1($_POST['urefno'].date("Yd"));
			$send['people_sf'] = $_POST['usf'][0];
			$send['people_addedBy'] = $_SESSION['id'];
			$send['people_created'] = date("Y-m-d H:i:s");
			$send['people_forget_key'] = sha1(uniqid(mt_rand(), true));
			// File Handling
 			$link = fileHandler($_FILES['udp'], "img");
			if(($link !='File Dimensions incorrect.')||($link !='There was an error processing the file.')||($link !='File Type Incorrect!')) {
				$send['people_dp'] = 'img/profiles/'.$link;
				echo do_sql('people', $send, 'insert', $mysqli);
				header('Location:users.php?done=1');
			}
			else
				echo $link;
			$msg = '<p> Hi '.$send['people_name'].',
			<br><br>
			A user account was just created for you on STUCHx. You may login using the below link.
			<br><br>
			Your Login Credetials 
			<br>
			<br>Username : <b>'.$send['people_email'].'</b>
			<br>Password : <b>'.$_POST['urefno'].date("Yd").'</b>
			<center><a href="'.S_PATH.'login.php">'.S_PATH.'</a>
			<br><br><br><br>
			<a href="'.S_PATH.'" style="text-align:center;text-decoration:none; padding:10px;font-size:1.2em;
				color:#FFF;
				background: linear-gradient(#830C0C, #830C0C) repeat scroll 0% 0% #1B5898;

				box-shadow: 0px -2px 0px rgba(0, 0, 0, 0.5) inset, 0px 2px 0px rgba(0, 0, 0, 0.1);">Login to STUCHx</a></center>
			';
			$mail_flag = stuch_mail($send['people_email'],'You have been registered at STUCHx',$msg);		
		}
		else {
			echo '<script>alert("There was an error.Retry.");window.location.assign("'.S_PATH.'users.php");</script>';
		
		}
		break;
	case 'editUser' :
		if(($_POST['uname']!="")&&($_POST['urefno']!="")&&($_POST['uemail']!="")&&($_POST['usf']!="") && ($_FILES['udp']['name'] != "")) {
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
		else if(($_POST['uname']!="")&&($_POST['urefno']!="")&&($_POST['uemail']!="")){
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
		if(($_POST['pos_name']!="")&&($_POST['pos_code']!="")&&($_POST['pos_person']!="")&&($_POST['pos_by']!="")) {
			$send['position_name'] = $_POST['pos_name'];
			$send['position_code'] = $roles[$_POST['pos_code']];
			$send['position_people_id'] = $_POST['pos_person'];
			$send['position_addedBy'] = $_POST['pos_by'];
			$send['position_expires'] = date("Y-m-d+7 H:i:s");
			echo do_sql('position',$send,'insert', $mysqli);
			header('Location:users.php?done=1');
		}
			
	break;
	case 'addED' :
		if(($_POST['blog_post']!="")) {
			$where['blog_approvalScore'] = $_POST['blog_app'];
			$where['blog_by'] = $_SESSION['id'];
			$send['blog_post'] = htmlentities($_POST['blog_post']);
			echo do_sql('blog',$send,'update', $mysqli, $where);
			header('Location:editorial_edit.php?done=1');
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
		if(($_POST['did']!="")) {
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
			// subtracting 
			$sql="UPDATE people SET people_deletionScore=people_deletionScore-".$_SESSION['score']." WHERE people_id='".$_POST['did']."';";

			if($mysqli->query($sql) === false) {
				trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
			} else {
				$affected_rows = $mysqli->affected_rows;
			}

			}
			else{

			$send['deletion_tbl_id'] = $_POST['did'];
			$send['deletion_people_id'] = $_SESSION['id'];
			$send['deletion_tbl'] = $table;
			$where = '';
			echo do_sql('deletion',$send,'insert', $mysqli,''); // inserted entry
			// adding 
			$sql="UPDATE people SET people_deletionScore=people_deletionScore+".$_SESSION['score']." WHERE people_id='".$_POST['did']."';";

			if($mysqli->query($sql) === false) {
				trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
			} else {
				$affected_rows = $mysqli->affected_rows;
			}

			}
		}
	break;
	case 'upDnVote' :
		if(($_POST['pos']!="")) {
			$pos_id  = $mysqli->real_escape_string($_POST['pos']);
			$vote_valid = check_vote($pos_id,$mysqli); // vote check



			if($vote_valid['voted']) {
				if($vote_valid['voted'] == 2){
					echo 0;exit;
				}
				// subtract_score
				$sql="UPDATE position SET position_approvalScore=position_approvalScore-".$_SESSION['score']." WHERE position_id='".$pos_id."';";

				if($mysqli->query($sql) === false) {
					trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
				} else {
					$affected_rows = $mysqli->affected_rows;
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
				// add_score
				$sql="UPDATE position SET position_approvalScore=position_approvalScore+".$_SESSION['score']." WHERE position_id='".$pos_id."';";

				if($mysqli->query($sql) === false) {
					trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
				} else {
					$affected_rows = $mysqli->affected_rows;
				}echo '<p>score added';
	


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
				}echo '<p>got top_role : '.$top_role. " and user id : ".$user_id;
				// now updating table with top_role
				$upd['people_id'] = $user_id;
				$upd['people_topCode'] = $top_role;
				do_sql('people',$upd,'update',$mysqli);echo '<p>update added';
		

		}
	break;
	


}
echo 1;
 

?>

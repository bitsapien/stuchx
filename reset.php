<?php $page="Password Reset"; include('header.php');
$tokenKEY = $mysqli->real_escape_string($_GET["token"]);
$invalid_characters = array("$", "%", "#", "<", ">", "|", ";", "INSERT", "UPDATE", "DELETE", ",");
$tok = str_replace($invalid_characters, "", $tokenKEY);

$alert = '';
if($_GET['alert']=='nomatch')
	$alert = '	<div class="row-fluid">
	<div class="col-sm-12"><br><br><p class="alert alert-danger">Paswords do not match. Re-enter the password.</p></div></div>
';

$reset_form='
	<div class="row-fluid">
	<div class="col-sm-12"><br><br>
	    <form role="form" class="form-horizontal" id="pass_form" data-toggle="validator" action="set_pass.php" method="POST">
	    <div class="form-group">
		<label class="control-label" for="exampleInputEmail2">New Password </label>
		<input type="password" name="new_pass" data-toggle="validator" data-minlength="6" class="form-control" id="inputPassword" placeholder="Password" required>
		<span class="help-block" style="margin-left:20px;">Minimum of 6 characters</span>

	    </div>
	    <div class="form-group">
		<label class="control-label" for="exampleInputEmail2">Confirm new Password </label>
		<input type="password" class="form-control" id="inputPasswordConfirm" data-match="#inputPassword" data-match-error="Whoops, these don\'t match" placeholder="Confirm" required name="cnf_pass"><div class="help-block with-errors"></div>
	    </div><input type="hidden" name="tok" value="'.$tok.'"><div class="form-group">
		<input type="submit" class="btn btn-primary" name="submitResetPage" value="Change Password" ></div>
	  </form>
       </div>
     </div>
     
';






$getTok = $mysqli->prepare("SELECT people_name FROM people WHERE people_forget_key=?") or die("Couldn\"t check the Tok");
$getTok->bind_param("s", $tok);
$getTok->execute();
$getTok->store_result();
$countRows = $getTok->num_rows;
if($countRows == 1){
	echo $alert.$reset_form;	
}
else{ // checking if the reset request needs to be taken away incase it were fraudulent 
	$toku = substr($tok, 1).$tok[0];
	$getToku = $mysqli->prepare("SELECT people_name FROM people WHERE people_forget_key=?") or die("Couldn\"t check the Tok");
	$getToku->bind_param("s", $toku);
	$getToku->execute();
	$getToku->store_result();
	$countRows = $getToku->num_rows; 
	if($countRows == 1){
		$insert_row = $mysqli->query('UPDATE people SET people_forget_key="" WHERE people_forget_key="'.$toku.'";') or die($mysqli->error.__LINE__); // remove token
		echo '<div class="row-fluid">
	<div class="col-sm-12"><br><br><p class="alert alert-warning">Thank you for informing us, suspicious activity on your account has been reported. We suggest you change your password.</p></div></div>';
		
	}
	else {
		echo '<script>window.location.href="'.S_PATH.'"</script>';
}

}	
?>
<?php include('footer.php'); ?>


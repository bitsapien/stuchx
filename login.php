<?php $page="Login"; include('header.php'); ?>
<br>
<?php 

switch($_GET['alert']) {
	case 'forget':
		$alert = '	<div class="row-fluid">
	<div class="col-sm-12 center"><br><p class="alert alert-success"> Please use your new password to login.</p>
	</div>
	</div>
';
	break;
	case 'wrong':
	$alert = '	<div class="row-fluid">
	<div class="col-sm-12 center"><br><p class="alert alert-danger"> The email/password you entered do not match, please try again.</p>
	</div>
	</div>
';
}

echo $alert.$login_form; ?>	

	

<?php include('footer.php'); ?>

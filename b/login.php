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
	break;
	case 'reg':
	$alert = '	<div class="row-fluid">
	<div class="col-sm-12 center"><br><p class="alert alert-success"> You have been registered. Check your mail (please also check your spam folder) for your username and password.</p>
	</div>
	</div>
';
	break;
	case 'dup':
	$alert = '	<div class="row-fluid">
	<div class="col-sm-12 center"><br><p class="alert alert-danger"> You already have been registered. Please check your email for username and password , or notify us by email on <a href="mailto:stuch.data@gmail.com">stuch.data@gmail.com</a></p>
	</div>
	</div>
';
	break;
}

echo $alert.'<br>'.$login_form; ?>	

	

<?php include('footer.php'); ?>

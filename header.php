<?php
session_start();
require("db.php");
include('modules.php');
people_expiry($mysqli); // people deletions
role_expiry($mysqli); // role deletions







if($_SESSION['id']!="") {
	switch($_SESSION['topCode']){

	case 'EDC':
	$nav = '<!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav sidebar-header">
                <li class="sidebar-brand">
		    <a href="edit_profile.php" title="'.$_SESSION['name'].'" ><img class="dp" src="'.$_SESSION['dp'].'" ></a>
                </li>
                <li>
                    <a href="users.php" title="Manage People" ><span class="fa fa-users"></span></a>
                </li>
                <li>
                    <a href="editorial_edit.php" title="Update Editorial" > <span class="fa fa-cube"></span></a>
                </li>

		<hr>
                <li>
                    <a href="about.php" title="About" ><span class="glyphicon glyphicon-info-sign"></span></a>
                </li>
<!--                <li>
                    <a href="browse.php" title="Browse" ><span class="glyphicon glyphicon-list"></span></a>
                </li>
-->            </ul>
	    <ul class="sidebar-footer sidebar-nav">
                <li>
		    <a href="logout.php" title="Logout" ><span class="fa fa-sign-out"></span></a>
                </li>

            </ul>

        </div>
        <!-- /#sidebar-wrapper -->
	';
	break;
	case 'EDT':
	case 'DIR':
	$nav = '<!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav sidebar-header">
                <li class="sidebar-brand">
		    <a href="edit_profile.php" title="'.$_SESSION['name'].'" ><img class="dp" src="'.$_SESSION['dp'].'" ></a>
                </li>
                <li>
                    <a href="users.php" title="Manage People" ><span class="fa fa-users"></span></a>
                </li>
		<hr>
                <li>
                    <a href="about.php" title="About" ><span class="glyphicon glyphicon-info-sign"></span></a>
                </li>
<!--                <li>
                    <a href="browse.php" title="Browse" ><span class="glyphicon glyphicon-list"></span></a>
                </li>
-->            </ul>
	    <ul class="sidebar-footer sidebar-nav">
                <li>
		    <a href="logout.php" title="Logout" ><span class="fa fa-sign-out"></span></a>
                </li>

            </ul>

        </div>
        <!-- /#sidebar-wrapper -->
	';
	break;
	default:
	$nav = '<!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav sidebar-header">
                <li class="sidebar-brand">
		    <a href="edit_profile.php" title="'.$_SESSION['name'].'" ><img class="dp" src="'.$_SESSION['dp'].'" ></a>
                </li>
		<hr>
                <li>
                    <a href="about.php" title="About" ><span class="glyphicon glyphicon-info-sign"></span></a>
                </li>
<!--                <li>
                    <a href="browse.php" title="Browse" ><span class="glyphicon glyphicon-list"></span></a>
                </li>
-->            </ul>
	    <ul class="sidebar-footer sidebar-nav">
                <li>
		    <a href="logout.php" title="Logout" ><span class="fa fa-sign-out"></span></a>
                </li>

            </ul>

        </div>
        <!-- /#sidebar-wrapper -->
	';
	break;

	}
}

else {
	$nav = '<!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav sidebar-header">
                <li class="sidebar-brand">

                </li>
                <li>
                    <a href="about.php" title="About" ><span class="glyphicon glyphicon-info-sign"></span></a>
                </li>
<!--                <li>
                    <a href="browse.php" title="Browse" ><span class="glyphicon glyphicon-list"></span></a>
                </li>
-->                <li>
                    <a href="#" data-toggle="modal" data-target="#login" title="Login" ><span class="glyphicon glyphicon-user"></span></a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->
';
}


// Cooking a login-form

$login_form = ' <form role="form" action="verify.php" method="POST">
		  <div class="form-group">
		    <label for="InputEmail">Email address</label>
		    <input type="email" class="form-control" id="InputEmail" name="email" placeholder="Enter email" value="'.$_GET['email'].'">
		  </div>
		  <div class="form-group">
		    <label for="InputPassword">Password</label>
		    <input type="password" class="form-control" id="InputPassword" name="pass" placeholder="Password">
		  </div>
		  <button type="submit" class="btn btn-primary">Submit</button>
		  <br><a href="#foo" data-toggle="modal" data-target="#passForget">Forgot your password ? </a>
		</form>';

// Cooking a success modal

$success_modal = '<!-- SUCCESS Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Successful!</h4>
      </div>
      <div class="modal-body">
	A link has been sent to your email. Follow instruction in mail.<br><br><p style="border:1px solid #FBEED5;;border-radius:5px;text-align:center;background:#F2DEDE;color:#B94A48;padding:5px;">Please check your spam folder incase you are unable to find it.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

';
// cooking a failure modal

$failure_modal = '<!-- FAIL Modal -->
<div class="modal fade" id="failModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Failed :( </h4>
      </div>
      <div class="modal-body">
	There seems to be something wrong, try re-entering your correct email.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

';


?>
<!DOCTYPE html>
<html lang="en"><Head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
        <meta charset="utf-8">
        <title>STUCHx - <?php echo $page; ?></title>
        <title></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   
        
        <!--[if lt IE 9]>
          <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

	<link href="css/bootstrap.css" type="text/css" rel="stylesHeet">

	<!-- Custom CSS -->
	<link href='http://fonts.googleapis.com/css?family=Lato:300' rel='stylesheet' type='text/css'>
	<link href="css/simple-sidebar.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="css/jquery-te-1.4.0.css">
       




    </head>
    
    <!-- HTML code from Bootply.com editor -->
    
    <body>
<!-- Login Modal -->
	<div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<h4 class="modal-title" id="myModalLabel">Login</h4>
	      </div>
	      <div class="modal-body">
		<?php echo $login_form;//echo sha1('abcdef'); ?>
	      </div>
	    </div>
	  </div>
	</div>
<!-- Forget Password Modal -->
<div class="modal fade" id="passForget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="validator">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Give us your email, so we can send you a reset link</h4>
      </div>
      <div class="modal-body">
	    <form role="form" class="form-horizontal" id="forget_form" data-toggle="validator">
	    <div class="form-group">
		<label class="col-sm-2 control-label" for="exampleInputEmail2">Your Email</label>
		<div class="col-sm-10"><input type="email" class="form-control" id="InputEmail" placeholder="Enter Email" name="ufemail"></div>

	    </div>

	  </form>
         
      </div>
      <div class="modal-footer">
	<button type="submit" class="btn btn-primary" id = "submitEmail">Send me a link</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Delete Confirm -->
<?php echo $success_modal;echo $failure_modal; ?>
 
            <div id="wrapper">

	     <?php echo $nav; ?>
        <!-- Page Content -->
        <div id="page-content-wrapper">


        <div class="container-fluid"><a href="#menu-toggle" class="btn btn-default" id="menu-toggle">  <span class="fa fa-navicon"></span></a>


<script>


</script>

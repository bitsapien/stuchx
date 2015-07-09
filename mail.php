<?php
error_reporting(-1);
ini_set('display_errors', 'On');
	$to = 'rahul.wozniak@gmail.com';
	$subject = 'Reset Password Request - FantasiaFootball.com (no-reply)';

	$headers = "From: stuch@aec.edu.in\r\n";
	$headers .= "BCC: c.rahulx@gmail.com\r\n";
	$headers .= "MIME-Version: 1.0\r\n";echo 'hh';
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$message = '<html>
<body style=" display: block;
    margin:  20px auto;
    padding: 12px;
    -webkit-box-shadow: 0px 0px 5px rgba(0,0,0,.8);
    -moz-box-shadow: 0px 0px 5px rgba(0,0,0,.8);
    box-shadow: 0px 0px 5px rgba(0,0,0,.8);
    position: relative; 
    background-color:#fff ;font-family:Tahoma, Geneva, sans-serif; ">

<img src="http://www.fantasiafootball.com/img/fantasia.png"/><hr>
<p> Hi '.$usr.',
<br><br>
You requested a password change , please click on the link below or paste it onto your address bar.
<br><br><center><a href="http://www.fantasiafootball.com/reset.php?token='.$token.'">http://www.fantasiafootball.com/reset.php?token='.$token.'</a>
<br><br><br><br>
<a href="http://www.fantasiafootball.com/reset.php?token='.$token.'" style="text-align:center;text-decoration:none; padding:10px;font-size:1.2em;
	color:#FFF;
	background: linear-gradient(#2E89CA, #1B5898) repeat scroll 0% 0% #1B5898;
	border-radius: 5px;
	box-shadow: 0px -2px 0px rgba(0, 0, 0, 0.5) inset, 0px 2px 0px rgba(0, 0, 0, 0.1);">Click here to reset your password </a></center>

<br><br>If this was not you,  please notify us, by clicking <a href="http://www.fantasiafootball.com/reset.php?token='.substr($token, -1).substr($token, 0, -1).'">here</a>.<br><br>
Regards,
<br><em>FantasiaFootball-Team.</em><br><br><hr>
<center><p>Copyright &copy; Fantasia 2014 &#9642; <a href="http://fantasiafootball.com/policy.html">Policy</a></center>
</body>
</html>';

	if (@mail($to, $subject, $message, $headers)) {
		echo('1');
	} else {
		echo('0');
	}

	
?>

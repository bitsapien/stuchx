<?php $page="Dashboard"; include('header.php'); ?>
<br><br>
<?php //print_r($_SESSION); 
$welcome = '<h2>Hi, '.$_SESSION['name'].' !!</h2>	Use the links on the left to navigate to specific parts of the site.';
echo $welcome;
?>



	

<?php include('footer.php'); ?>

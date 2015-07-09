<?php $page="Edit Editor's Note"; include('header.php');

$getBlog = $mysqli->prepare('SELECT blog_post FROM blog WHERE blog_by=? AND blog_approvalScore=? ') or die('Couldn\'t check the profile');
$appscore = "100";
$getBlog->bind_param('ss', $_SESSION['id'], $appscore);
$getBlog->execute();
$getBlog->store_result();

$getBlog->bind_result($blog);


while($getBlog->fetch()) {
	// extract blog
	$blog_old = $blog;

}

if($_GET['done'] == "1")
	$alert = '  <div class="alert alert-info">
		    <button type="button" class="close" data-dismiss="alert">&times;</button>
		    Changes you made have been updated.
		    </div>
';

 ?>

<h2> Update Editor's Note </h2>
<?php echo $alert; ?>
<!------------------------------------------------------------ jQUERY TEXT EDITOR ------------------------------------------------------------>
<form action="submit.php" method="POST" id="addED_form">
<textarea name="blog_post" class="blog_post" placeholder="Enter Post here"><?php echo $blog_old;?></textarea>
<input type="hidden" name="flag" value="addED">
<input type="hidden" name="blog_app" value="<?php echo $appscore; ?>">
<input type="submit" class="btn btn-primary pull-right" id="submitAddED" value="Publish Post"></input>


</form>

<?php include('footer.php'); ?>

<?php $page = "Browse"; include('header.php'); 
$getBlogList = $mysqli->prepare('SELECT b.blog_id,b.blog_title,b.blog_lastUpdated,ppl.people_name FROM blog b INNER JOIN people ppl ON ppl.people_id = b.blog_by WHERE b.blog_final=1') or die($mysqli->error);
$getBlogList->execute();
$getBlogList->store_result();

$getBlogList->bind_result($blog_id,$blog_title,$blog_u,$blog_by);

while($getBlogList->fetch()) {
	$op.= '			 <a href="post.php?id='.$blog_id.'" class="list-group-item no-justify">
				    <h4 class="list-group-item-heading">'.$blog_title.'
					<span class="badge" style="vertical-align:middle;">'.get_votes_on_post($blog_id,$mysqli).'</span>
				    </h4>
				    <p class="list-group-item-text">By '.$blog_by.' on '.$blog_u.'</p>
				    
				  </a>';
}


?>

   		<br><br><br>

	  	<br><br>
<!-- Browse main -->
		<div class="row-fluid">
	      		

				<div class="col-xs-6 ">

					<h2><span class="glyphicon glyphicon-list"></span> Browse</h2>
				</div>

				<div class="col-xs-6 ">

					<img src="img/logo-stuchx.png" class="img-responsive pull-right" alt="STUCHx"><br><br>
				</div>

		</div>
		<div class="row-fluid">
			<div class="col-sm-12"><br><hr><br><br><br>
				<div class="list-group">
					<?php echo $op; ?>
				</div>			
			</div>

		</div>
<?php include('footer.php'); ?> 	


<?php $page = "Browse"; include('header.php'); ?>

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
		      		<a id="toggler" href="#" data-toggle="collapse" class="active" data-target="#jan14">
				<i class="icon-folder-open"></i>
				<i class="icon-folder-close"></i>
				January 2014
				</a>

				<div id="jan14" class="collapse out">
					<ul class="nav nav-list">
					  <li class="active"><a href="post.php">Home</a></li>
					  <li><a href="post.php">Library</a></li>
					</ul>
				</div>
				<br>
		      		<a id="toggler" href="#" data-toggle="collapse" class="active" data-target="#feb14">
				<i class="icon-folder-open"></i>
				<i class="icon-folder-close"></i>
				February 2014
				</a>

				<div id="feb14" class="collapse out">
					<ul class="nav nav-list">
					  <li class="active"><a href="post.php">Home</a></li>
					  <li><a href="post.php">Library</a></li>
					</ul>
				</div>
			</div>

		</div>
<?php include('footer.php'); ?> 	


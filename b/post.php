<?php $page = "Post Title"; include('header.php');



// Getting the GET

$id = $mysqli->real_escape_string($_GET['id']);

if($_SESSION['id'] != '') {
	// Send Response Modal

	$modal_sr = '<div class="modal fade" id="sendResponseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="validator">
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Send a Response</h4>
	      </div>
	      <div class="modal-body">
		    <form role="form" class="form-horizontal" id="sendReponse_form" data-toggle="validator">
		    <div class="form-group">


		    </div>

		  </form>
		 
	      </div>
	      <div class="modal-footer"><div id="loading"><img src="img/loading.gif"></img></div>
		<button type="submit" class="btn btn-primary" id = "submitSRespone">Send</button>
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>
	';


	echo $modal_sr;



	// Showing love for the post and making a love button 


	$res = check_vote_on_post($id,$mysqli);
	if($res)
		$vote = '<a href="#" onclick="post_vote_up_dn(\''.$id.'\');" ><span class="fa fa-arrow-down"></span></a>';
	else
		$vote = '<a href="#" onclick="post_vote_up_dn(\''.$id.'\');" ><span class="fa fa-arrow-up"></span></a>';


	$love = '<div class="panel panel-default">
	  <div class="panel-body">
	    <i class="fa fa-star"></i> '.get_votes_on_post($id,$mysqli).' '.$vote.' <a href="#" class="btn-default" data-id="'.$id.'" data-toggle="modal" data-target="#sendResponseModal" title="Send Response" onclick="add_id('.$id.');">Send Response</a>
	  </div>
	</div>';
}
else {
	$love = '<div class="alert alert-warning alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  To like or respond, login.
</div>';
	

}
// Building a post
$sql = "SELECT ppl.people_name,ppl.people_id,b.blog_title,b.blog_post,b.blog_lastUpdated,b.blog_id FROM blog b INNER JOIN people ppl ON b.blog_by = ppl.people_id WHERE blog_id='".$id."' AND blog_final=1";
$getUSR = query_exec($sql,$mysqli);
$getUSR->bind_result($blog_usr_name,$blog_usr_id,$blog_title,$blog_content,$blog_date,$blog_id);

while($getUSR->fetch()) {
	$sc = get_votes_on_post($blog_id,$mysqli);
	if(($sc < $blog_approval_ceil)){
		include('error/404.html');exit;} // Score check
	$blog_usr_name = '<a href="profile.php?id='.$blog_usr_id.'" title="View Profile" class="pull-right" >'.$blog_usr_name.'</a>';
	$blog_title = $blog_title;
	$blog_content = $blog_content;
	$blog_date = $blog_date;
}
$sql = "SELECT contact_link FROM contact WHERE contact_people_id='".$id."'";
$getUSR = query_exec($sql,$mysqli);
$getUSR->bind_result($con_link);
$con_name = "ytest";
while($getUSR->fetch()) {
	$con_o .= '<p><a href="'.$con_link.'">'.$con_name.'</a></p>';
}

if($blog_id == '') {
include('error/404.html');exit;} // Availability Check

?>



   		<br><br><br>

	  	<br><br>
<!-- Profile main -->
		<div class="row-fluid">
	      		

				<div class="col-xs-6 no-justify">

					<h1> <?php echo $blog_title; ?> </h1>
				</div>

				<div class="col-xs-6 ">

					<img src="img/logo-stuchx.png" class="img-responsive pull-right" alt="Responsive image">
				</div>

		</div>
		<div class="row-fluid">
	      		

				<div class="col-xs-12 ">

					<small><?php echo $blog_date; ?></small>
				</div>

		</div>
		<div class="row-fluid">
	      		

				<div class="col-xs-12 no-justify"><br>
					<?php echo $love; ?> 
				</div>

		</div>

		<div class="row-fluid">
	      		

				<div class="col-xs-12 ">
					
					<p><?php echo htmlspecialchars_decode($blog_content); ?></p>
				</div>

		</div>
		<div class="row-fluid">
	      		

				<div class="col-xs-12 ">

					<br><br><?php echo $blog_usr_name; ?><br><br><br>
				</div>


		</div>
<?php include('footer.php'); ?> 	
<script>
function post_vote_up_dn(id){
				$.ajax({
					type: "POST",
					cache: false,
					url: "submit.php",
					data: "bid="+id+"&flag=voteBlog",
					success: function(data) {
						location.reload();
					}
				});

}
function add_id(id){
	$( "#sendReponse_form" ).empty();
	$( "#sendReponse_form" ).append( '<div class="col-sm-12"><textarea name ="sendResponse" class="form-control" rows="2"></textarea><input type="hidden" name="flag" value="send_response"><input type="hidden" name="bid" value="'+id+'"><br></div>');
}
$(document).ready(function() {
			$('#submitSRespone').click(function() {
				$.ajax({
					type: "POST",
					cache: false,
					url: "submit.php",
					data: $("#sendReponse_form").serialize(),
					success: function(data) {
							$('#sendResponseModal').modal('hide');
							$('.body-edit').html("Your response has been saved.");
							$('#successModal').modal('show');
					}
				});
					

			});

		});

</script>

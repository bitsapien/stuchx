<?php $page="Dashboard"; include('header.php'); ?>
<br><br>
<?php
$welcome = '<h2>Hi, '.$_SESSION['name'].' !!</h2>	Use the links on the left to navigate to specific parts of the site.';
echo $welcome;
echo '<br><br>'.$notice;
//print_r($_SESSION); 
// Send Response Modal

/*$modal_sr = '<div class="modal fade" id="sendResponseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="validator">
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



$post_table = '<thead>
			<tr>
			<th> Title</th>
			<th> Author </th>
			<th> Score</th>
			<th> Vote </th>
			<th> Last Edited</th>
			<th></th>
			</tr>
	      </thead>';

$getBlogList = $mysqli->prepare('SELECT b.blog_id,b.blog_title,b.blog_lastUpdated,ppl.people_name FROM blog b INNER JOIN people ppl ON ppl.people_id = b.blog_by WHERE b.blog_final=1') or die($mysqli->error);
$getBlogList->execute();
$getBlogList->store_result();

$getBlogList->bind_result($blog_id,$blog_title,$blog_u,$blog_by);

while($getBlogList->fetch()) {
	// votes
	$res = check_vote_on_post($blog_id,$mysqli);
	if($res)
		$vote_td = '<a href="#" onclick="post_vote_up_dn(\''.$blog_id.'\');" ><span class="fa fa-arrow-down"></span></a>';
	else
		$vote_td = '<a href="#" onclick="post_vote_up_dn(\''.$blog_id.'\');" ><span class="fa fa-arrow-up"></span></a>';

		

	$post_table .= '<tr>'.'<td>'.$blog_title.'</td>'.'<td>'.$blog_by.'</td>'.'<td>'.get_votes_on_post($blog_id,$mysqli).'</td>'.'<td>'.$vote_td.'</td>'.'<td>'.$blog_u.'</td>'.'<td>'.'<a href="#" class="btn-default" data-id="'.$blog_id.'" data-toggle="modal" data-target="#sendResponseModal" title="Send Response" onclick="add_id('.$blog_id.');">Send Response</a>'.'</td>'.'</tr>';

}

$list_of_blogs = '<div class="row-fluid">
		      		

					<div class="col-sm-12 no-justify"><br><br>
						<table class="table table-hover well">
							'.$post_table.'
						</table>
	
					</div>
		</div>
';





echo $list_of_blogs;*/



?>



	

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

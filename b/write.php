<?php $page="Write a Post"; include('header.php');

$modal_response = '<div class="modal fade" id="response_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="validator">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="response_modal_title">Response</h4>
      </div>
      <div class="modal-body no-justify" id="response_modal_body" >
		
	
         
      </div>
      <div class="modal-footer"><div id="loading"><img src="img/loading.gif"></img></div>
	
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
';


echo $modal_response;


$blog_edit_id = $mysqli->real_escape_string($_GET['id']);

$getBlog = $mysqli->prepare('SELECT blog_post,blog_id,blog_title FROM blog WHERE blog_id=? AND blog_by=? AND blog_final=?') or die('Couldn\'t check the blog');
$f = '0';
$getBlog->bind_param('sss',$blog_edit_id, $_SESSION['id'],$f);
$getBlog->execute();
$getBlog->store_result();

$getBlog->bind_result($blog,$blog_id,$blog_ttl);


while($getBlog->fetch()) {
	// extract blog
	$blog_old = $blog;
	$blog_id = $blog_id;
	$blog_ttl = $blog_ttl;
}



$alert = '';
if($_GET['done'] == "1")
	$alert = '  <div class="alert alert-info">
		    <button type="button" class="close" data-dismiss="alert">&times;</button>
		    Changes you made have been updated.
		    </div>
';

 ?>
<div class="row-fluid">
<div class="col-sm-12 no-justify">
<h2> Write a Post </h2>
<?php echo $alert; ?>
<!-------------------------------------------------------- jQUERY TEXT EDITOR ------------------------------------------------------------>
<form role="form" action="submit.php" method="POST" id="blog_form">
  <div class="form-group">
    <label for="InputTitle">Title</label>
    <input type="text" class="form-control" id="InputTitle" placeholder="Enter title" name="blog_title" value="<?php echo $blog_ttl;?>">
  </div><div class="form-group">
<textarea name="blog_post" class="blog_post" placeholder="Enter Post here"><?php echo $blog_old;?></textarea>
</div>
<input type="hidden" name="flag" value="blog_add">
<input type="hidden" name="blog_id" value="<?php echo $blog_id; ?>">
<input type="submit" class="btn btn-primary pull-right" style="margin:0 10px 0 10px;" name="submit" id="submitBlog" value="Save & Publish"></input> 
<input type="submit" class="btn btn-info pull-right" name="submit" id="submitBlog" value="Save"></input>
</form>
</div>
</div>
<?php 


// Get Blog listing

$post_table = '<thead>
			<tr>
			<th> Title</th>
			<th> Published? </th>
			<th> Created</th>
			<th> Last Edited</th>
			<th>  </th>
			<th>  </th>
			<th>  </th>
			</tr>
	      </thead>';

$getBlogList = $mysqli->prepare('SELECT blog_id,blog_title,blog_final,blog_created,blog_lastUpdated FROM blog WHERE blog_by=?') or die($mysqli->error);
$getBlogList->bind_param('s',$_SESSION['id']);
$getBlogList->execute();
$getBlogList->store_result();

$getBlogList->bind_result($blog_id,$blog_title,$blog_final,$blog_c,$blog_u);

while($getBlogList->fetch()) {




	$num = '<span class="badge" style="vertical-align:middle;">'.get_notif_count($blog_id,$mysqli).'</span>';
	$blog_title_link = ''.$blog_title.'';



	if($blog_final == '0'){ // checking if published 
		$final = '<span class="fa fa-times"></span>';
		$action = '<a href="write.php?id='.$blog_id.'" class= "" data-toggle="modal" title="Edit" "><span class="fa fa-pencil-square-o"></span></a>';
	}
	else if($blog_final == '1'){
		$final = '<span class="fa fa-check"></span>';
		$action = '<b>'.get_votes_on_post($blog_id,$mysqli).'</b>';
		$sc = get_votes_on_post($blog_id,$mysqli);
		if($sc >= $blog_approval_ceil)
			$blog_title_link = '<a target="_blank" href="post.php?id='.$blog_id.'">'.$blog_title.'</a>';
		

	}
	if($blog_final != '2') {
		$post_table .= '<tr>'.'<td>'.$blog_title_link.'</td>'.'<td>'.$final.'</td>'.'<td>'.$blog_c.'</td>'.'<td>'.$blog_u.'</td>'.'<td>'.$action.'</td>'.'<td>'.'<a href="#" class="confirm-delete" data-id="'.$blog_id.'" data-toggle="modal" data-target="#delModal" title="Delete" onclick="del_blog('.$blog_id.');"><span class="fa fa-trash-o"></span></a>'.'</td>'.'<td>'.'<a href="#" class="btn-default" data-id="'.$blog_id.'" data-toggle="modal" data-target="#response_modal" title="Show Response" onclick="getResponseCs('.$blog_id.',\''.$blog_title.'\');" >'.$num.' View Responses</a>'.'</tr>';
	}
}

$list_of_blogs = '<div class="row-fluid">
		      		

					<div class="col-sm-12 no-justify"><br><br>
						<table class="table table-hover well">
							'.$post_table.'
						</table>
	
					</div>
		</div>
';





echo $list_of_blogs;









include('footer.php'); ?>

<script>
// Delete Confirm
     
function del_blog(id) {
			$( ".del-footer" ).empty();
			$( ".del-footer" ).append( '<button type="button" class="btn btn-danger" onclick="del_id('+id+');">Yes</button><button type="button" class="btn btn-default" data-dismiss="modal">No</button>');
}
function del_id(id){
				$.ajax({
					type: "POST",
					cache: false,
					url: "submit.php",
					data: "bid="+id+"&flag=delBlog",
					success: function(data) {
						location.reload();
					}
				});
}
function getResponseCs(id, name) {console.log(id+name);
				$.ajax({

					cache: false,
					url: "responses.php?id="+id,

					success: function(data) {
							$('#response_modal_body').html(data);
							$('#response_modal_title').html("Response for '"+name+"'");
							$('#response_modal').modal('show');

					}
				});
}

</script>

<?php $page="Manage Users"; include('header.php');

if($_GET['done'] == "1")
	$alert = '  <p><div class="alert alert-info">
		    <button type="button" class="close" data-dismiss="alert">&times;</button>
		    Changes you made have been successfully updated.
		    </div>
';
if($_GET['error'] == "dup")
	$alert = '  <p><div class="alert alert-danger">
		    <button type="button" class="close" data-dismiss="alert">&times;</button>
		    A duplicate entry was found.
		    </div>
';
if($_GET['error'] == "img")
	$alert = '  <p><div class="alert alert-danger">
		    <button type="button" class="close" data-dismiss="alert">&times;</button>
		    There was an error with the image you\'ve uploaded. 
		    <ul>
			<li>The dimensions must be 150x150.</li> 
			<li>Accepted Image formats : JPG or PNG </li> 
		    </div>
';
/* ============================================ Users in general =======================================================================*/
$decider = "AND 1";
if($_SESSION['code'] == 'DIR') 
	$decider = "people_addedBy='".$_SESSION['id']."'"; // for DIRs who will only be shown the users they've added

$getInfo = $mysqli->prepare("SELECT people_id, people_name, people_dp, people_roll, people_lastLogin FROM people WHERE people_archive=0 AND people_addedBy!='-1' ".$decider) or die('Couldn\'t check the profile');
$getInfo->execute();
$getInfo->store_result();
// Collecting name, picture link, roll number, description, one liner
$getInfo->bind_result($id, $name, $dp, $roll, $ll);
$user_rows = '<thead>
			<tr>
			<th> </th>
			<th> Name </th>
			<th> Edit Profile</th>
			<th> Roles </th>
			<th> Delete </th>
			<th> Last Login </th>
			</tr>
	      </thead>
	      ';



$i = 0;
while($getInfo->fetch()) { 
	$roles_edit = '<a href="#" class= "" data-toggle="modal" data-target="#positions_modal" title="Edit" onclick="getPosition('.$id.',\''.$name.'\');" ><span class="fa fa-star"></span></a>';
	// remove add role for user himself
	if($id == $_SESSION['id'])
		$roles_edit = '<span class="fa fa-times"></span>';
	$deletionScore = get_deletion_on_position($id,$mysqli);
	// check if deletion flag has been put
	$exclaim = '<tr>';$alert_del = '';
	if($deletionScore > ($people_deletion_ceil-0.3)) {
		$exclaim = '<tr class="alert alert-danger">';$alert_del = '<p><small style="color:red;">Marked for deletion with a high vote.</small>';}
	// check if the person has made a delete request
	$getDel = $mysqli->prepare('SELECT deletion_tbl_id FROM deletion WHERE deletion_people_id=? AND deletion_tbl=? AND deletion_tbl_id=?') or die('Couldn\'t check the deletion');
	$tbl = 'people';
	$getDel->bind_param('sss', $_SESSION['id'],$tbl,$id);
	$getDel->execute();
	$getDel->store_result();
	$countRows = $getDel->num_rows;
	if($countRows == 0)
		$tr_del = '<a href="#" class= "" data-toggle="modal" id="#DeleteProfile" title="Delete" onclick="del_recycle(\''.$id.'\');" ><span class="fa fa-trash-o"></span></a>';
	else
		$tr_del = '<a href="#" class= "" data-toggle="modal" id="#ResetProfile" title="Recycle" onclick="del_recycle(\''.$id.'\');"><span class="fa fa-recycle"></span></a>';


	// extract users
$i++;

		$user_rows .= $exclaim.'<td><img class="responsive small-img" src="'.$dp.'"/></td>'.'<td><a title="'.$roll.'" href="profile.php?id='.$id.'"target="_blank" >'.$name.$alert_del.'</td>'.'<td>'.'<a href="#" class= "" data-toggle="modal" data-target="#EditProfile" title="Edit" onclick="getProfile('.$id.',\''.$name.'\');"><span class="fa fa-pencil-square-o"></span></a>'.'</td>'.'<td>'.$roles_edit.'</td>'.'<td>'.$tr_del.'</td>'.'<td>'.$ll.'</td>'.'</tr>';

}
$user_table = '<br><br><div class="row-fluid">
		      		

					<div class="col-sm-12 no-justify">
							<table class="table table-hover well">
 								'.$user_rows.'
							</table>
							
					</div>
		</div>
';
/* ============================================ Users registered externally ================================================================*/

$getGInfo = $mysqli->prepare('SELECT people_id, people_name, people_dp, people_roll, people_created, people_oneLiner, people_desc,people_email, people_lastLogin FROM people WHERE people_archive=0 AND people_addedBy=?') or die('Couldn\'t check the profile');
$ab = '-1';
$getGInfo->bind_param('s', $ab);
$getGInfo->execute();
$getGInfo->store_result();
// Collecting name, picture link, roll number, description, one liner
$getGInfo->bind_result($id, $name, $dp, $roll, $created, $ol, $desc, $email, $ll);
$guser_rows = '<thead>
			<tr>
			<th> </th>
			<th> Details </th>
			<th> Approve ?</th>
			</tr>
	      </thead>
	      ';



while($getGInfo->fetch()) { 
		if(($ol!='')&&($desc!=''))
		$guser_rows .= '<tr class="'.$id.'">'.'<td><img class="responsive small-img" src="'.$dp.'"/></td>'.'<td><a title="'.$roll.'" href="profile.php?id='.$id.'"target="_blank" >'.$name.'</a><p>'.$roll.'<p>'.$email.'<p>'.$created.'</td>'.'<p>Last Login: '.$ll.'</td>'.'<td>'.'<a href="#" class= "" data-toggle="modal" data-target="#EditProfile" title="Edit" onclick="appProfile('.$id.');"><span class="fa fa-check-square-o"></span></a>'.'</td>'.'</tr>';

}
$guser_table = '<br><br><div class="row-fluid">
		      		

					<div class="col-sm-12 no-justify">
							<table class="table table-hover well">
 								'.$guser_rows.'
							</table>
							
					</div>
		</div>
';



$add_user_form = '<br><br><div class="row-fluid">
		      		

					<div class="col-sm-12 no-justify">
						<div class="panel panel-default">
						  <div class="panel-heading">
						    <h3 class="panel-title">Add a matey</h3>
						  </div>
						  <div class="panel-body">
							<form role="form" method="POST" enctype="multipart/form-data" id="addUser_form" action="submit.php" >
							  <div class="form-group">
							    <label for="InputName">Name</label>
							    <input type="text" class="form-control" id="InputName" name="uname" placeholder="Enter name">
							  </div>
							  <div class="form-group">
							    <label for="InputRefNo">Roll Number/Emp Code</label>
							    <input type="text" class="form-control" id="InputRefNo" name="urefno" placeholder="Enter RollNo/EmpNo">
							  </div>

							  <div class="form-group">
							    <label for="InputEmail">Email address</label>
							    <input type="email" class="form-control" id="InputEmail" name="uemail" placeholder="Enter email">
<input type="hidden" name="flag" value="addUser">
							  </div>


							  <div class="form-group">
							    <label for="InputDP">Upload Image</label>
							    <input type="file" id="InputDP" name="udp">
							    <p class="help-block">Image must be of 150x150 dimensions.</p>
							  </div>
							  <button type="submit" class="btn btn-primary" id="submitAddUser">Submit</button>
							  <div id="loading"><img src="img/loading.gif"></img></div>
							</form>
						  </div>
						</div>	
					</div>
		</div>
';



// modals

$modal_editProfile = '<div class="modal fade" id="editProfile_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="validator">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="editProfile_modal_title">Edit Profile</h4>
      </div>
      <div class="modal-body">
		<div id="editProfile_modal_body"></div>

	
         
      </div>
      <div class="modal-footer"><div id="loading"><img src="img/loading.gif"></img></div>
	
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
';
$modal_positions = '<div class="modal fade" id="position_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="validator">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="position_modal_title">Positions</h4>
      </div>
      <div class="modal-body">
		<div id="position_modal_body"></div>

	
         
      </div>
      <div class="modal-footer"><div id="loading"><img src="img/loading.gif"></img></div>
	
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
';

echo $alert;
echo $modal_editProfile;
echo $modal_positions;

echo $add_user_form;
echo $user_table;
echo $guser_table;
?>



<!-- Ajax -->
<script src="js/jquery.js"></script>
<script type="text/javascript">


$(document).ready(function(){

// add Contact

			$('#submitAddContact').click(function() {
				$("#loading").css("display","inline");
				$.ajax({
					type: "POST",
					cache: false,
					url: "submit.php",
					data: $("#addContact_form").serialize(),
					success: function(data) {$('#addContact').modal('hide');
						if(data == "1"){

							
							$('#successModal').find('.modal-body').text('Changes made succesfully!');
							$('#successModal').modal('show');}
						else{
							$('#failModal').find('.modal-body').text('Changes could not be saved.');
							$('#failModal').modal('show');}
						$("#loading").css("display","none");

					}
				});
					

			});

<?php echo $modal_submit_js; ?>


});


function getProfile(id, name) {
				$.ajax({

					cache: false,
					url: "editProfile_all.php?id="+id,

					success: function(data) {
							$('#editProfile_modal_body').html(data);
							$('#editProfile_modal_title').html("Profile of "+name);

							$('#editProfile_modal').modal('show');

					}
				});
}
function getPosition(id, name) {
				$.ajax({

					cache: false,
					url: "position_all.php?id="+id,

					success: function(data) {
							$('#position_modal_body').html(data);
							$('#position_modal_title').html("Roles for "+name);
							$('#position_modal').modal('show');

					}
				});
}
function del_recycle(id) {
				$.ajax({
					type: "POST",
					cache: false,
					url: "submit.php",
					data: "did="+id+"&flag=delRecPeople",
					success: function(data) {
						location.reload();
					}
				});
}
function vote_up_dn(id) {
				$.ajax({
					type: "POST",
					cache: false,
					url: "submit.php",
					data: "pos="+id+"&flag=upDnVote",
					success: function(data) {
						location.reload();
					}
				});
}
function appProfile(id) {
				$.ajax({
					type: "POST",
					cache: false,
					url: "submit.php",
					data: "pid="+id+"&flag=approvePeople",
					success: function(data) {
						$( "."+id+"" ).remove();
					}
				});
}

</script>


	

<?php include('footer.php'); ?>

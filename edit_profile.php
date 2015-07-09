<?php $page="Edit Profile"; include('header.php');

$getInfo = $mysqli->prepare('SELECT people_roll, people_desc, people_oneLiner FROM people WHERE people_id=?') or die('Couldn\'t check the profile');
$getInfo->bind_param('s', $_SESSION['id']);
$getInfo->execute();
$getInfo->store_result();
// Collecting name, picture link, roll number, description, one liner
$getInfo->bind_result($roll, $desc, $oneLiner);
while($getInfo->fetch()) {
	// extract positions
	$i = 0;
	$pos_name = '';
	while($_SESSION['pos_name'.$i] != "") {
		$pos_name .= ' /'.$_SESSION['pos_name'.$i];
		$i++;
	}
	$roll = $roll;
	$desc = htmlspecialchars_decode($desc);
	$oneLiner = htmlspecialchars_decode($oneLiner);
	if($desc == "")
		$desc = "<script>alert('Please update your description block');</script> No description. Write a paragraph on you using the blue icon above this section.";
	if($oneLiner == "")
		$oneLiner = "<script>alert('Please update your a-line-about-you block');</script> No description. Write a line about you using the blue icon above this section.";
}
$profile_info = '<br><br><div class="row-fluid">
		      		

					<div class="col-sm-2 ">

							<img src="'.$_SESSION['dp'].'" class="img-responsive" /><br>

					</div>
					<div class="col-sm-10 no-justify">
							<span class="large-font">'.$_SESSION['name'].'</span><br>
							<span class="med-font">'.$pos_name.'</span><br>
							<p>'.$roll.'</p>
							
					</div>
		</div>
';

$profile_desc = '<div class="row-fluid">
				<div class="col-xs-12"><br>
					<h3>A line about you ...<a href="#" class= "pull-right" data-toggle="modal" data-target="#oneLiner" title="Edit" ><span class="fa fa-pencil-square-o"></span></a></h3>
					
				</div>
	      		

				<div class="col-xs-12 well">
					
					 '.$oneLiner.'
				</div>

		</div>
		<div class="row-fluid">
	      		

				<div class="col-xs-12"><br>
					<h3>Description<a href="#" class= "pull-right" data-toggle="modal" data-target="#Desc" title="Edit" ><span class="fa fa-pencil-square-o"></span></a></h3>
					
				</div>

		</div>
		<div class="row-fluid">
	      		

				<div class="col-xs-12 well">
					
					 '.$desc.'
				</div>

		</div>
';

$getContact = $mysqli->prepare('SELECT contact_id, contact_name, contact_link FROM contact WHERE contact_people_id=?') or die('Couldn\'t check the contact');
$getContact->bind_param('s', $_SESSION['id']);
$getContact->execute();
$getContact->store_result();
// Collecting cname, clink
$getContact->bind_result($cid, $cname, $clink);
$profile_contacts = '		<div class="row-fluid"><br>
	      		

				<div class="col-xs-12 ">
					<h3>Links and Contact Information<a href="#" class= "pull-right" data-toggle="modal" data-target="#addContact" title="Add contact" ><span class="fa fa-plus-square"></span></a></h3><br>';

$modal_editContact = '';
while($getContact->fetch()) {
	$js_func = "fetch_rm('".$cid."','".$cname."');";
	$profile_contacts .= '<p><a href="'.$clink.'" target="_blank">'.$cname.'</a><a href="#" class= "" data-toggle="modal" data-target="#editContact'.$cid.'" title="Edit" > <span class="fa fa-pencil-square-o"></span></a>|<a href="#" class= "" data-toggle="modal" data-target="#remove" title="Delete" onclick="'.$js_func.'"> <span class="fa fa-trash-o"></span></a></p>';
	
	$modal_editContact .= '<div class="modal fade" id="editContact'.$cid.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="validator">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Make Changes</h4>
      </div>
      <div class="modal-body">
	    <form role="form" class="form-horizontal" id="editContact_form'.$cid.'" data-toggle="validator">
	    <div class="form-group">
		<label class="col-sm-2 control-label" for="cName">Name</label>
		<div class="col-sm-10"><input type="text" class="form-control" id="cName" placeholder="eg. Facebook or Twitter" name="cName" value="'.$cname.'"></div>

	    </div>
	    <div class="form-group">
		<label class="col-sm-2 control-label" for="cLink">Link</label>
		<div class="col-sm-10"><input type="text" class="form-control" id="cLink" placeholder="Enter URL" name="cLink" value="'.$clink.'"><input type="hidden" name="flag" value="editContact"><input type="hidden" name="id" value="'.$cid.'"></div>

	    </div>

	  </form>
         
      </div>
      <div class="modal-footer"><div id="loading"><img src="img/loading.gif"></img></div>
	<button type="submit" class="btn btn-primary" id = "submitEditContact'.$cid.'">Save Changes</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>';

$modal_submit_js .= '// add Contact

			$("#submitEditContact'.$cid.'").click(function() {console.log("in");
				$("#loading").css("display","inline");
				$.ajax({
					type: "POST",
					cache: false,
					url: "submit.php",
					data: $("#editContact_form'.$cid.'").serialize(),
					success: function(data) {$("#editContact'.$cid.'").modal("hide");
						if(data == "1"){

							
							$("#successModal").find(".modal-body").text("Changes made succesfully!");
							$("#successModal").modal("show");}
						else{
							$("#failModal").find(".modal-body").text("Changes could not be saved.");
							$("#failModal").modal("show");}
						$("#loading").css("display","none");

					}
				});
					

			});
';

}

$profile_contacts .= '		</div>
		</div>';
// modals

$modal_oneLiner = '<div class="modal fade" id="oneLiner" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="validator">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Describe yourself in a line (displayed on about-page)</h4>
      </div>
      <div class="modal-body">
	    <form role="form" class="form-horizontal" id="ol_form" data-toggle="validator">
	    <div class="form-group">
		<div class="col-sm-12"><input type="text" class="form-control" id="Ol" placeholder="Enter One Liner to be displayed on about-page" name="oL" value="'.$oneLiner.'"><input type="hidden" name="flag" value="oneLiner"></div>

	    </div>

	  </form>
         
      </div>
      <div class="modal-footer"><div id="loading"><img src="img/loading.gif"></img></div>
	<button type="submit" class="btn btn-primary" id = "submitOL">Update</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
';
$modal_desc = '<div class="modal fade" id="Desc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="validator">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Talk about yourself in detail (displayed on profile-page)</h4>
      </div>
      <div class="modal-body">
	    <form role="form" class="form-horizontal" id="desc_form" data-toggle="validator">
	    <div class="form-group">
		<div class="col-sm-12"><textarea name ="desc" class="form-control" rows="4">'.br2nl($desc).'</textarea><input type="hidden" name="flag" value="Desc"></div>

	    </div>

	  </form>
         
      </div>
      <div class="modal-footer"><div id="loading"><img src="img/loading.gif"></img></div>
	<button type="submit" class="btn btn-primary" id = "submitDesc">Update</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
';

$modal_addContact ='<div class="modal fade" id="addContact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="validator">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Add a Contact or Link</h4>
      </div>
      <div class="modal-body">
	    <form role="form" class="form-horizontal" id="addContact_form" data-toggle="validator">
	    <div class="form-group">
		<label class="col-sm-2 control-label" for="cName">Name</label>
		<div class="col-sm-10"><input type="text" class="form-control" id="cName" placeholder="eg. Facebook or Twitter" name="cName"></div>

	    </div>
	    <div class="form-group">
		<label class="col-sm-2 control-label" for="cLink">Link</label>
		<div class="col-sm-10"><input type="text" class="form-control" id="cLink" placeholder="Enter URL" name="cLink"></div>
		<input type="hidden" name="flag" value="addContact">
	    </div>

	  </form>
         
      </div>
      <div class="modal-footer"><div id="loading"><img src="img/loading.gif"></img></div>
	<button type="submit" class="btn btn-primary" id = "submitAddContact">Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>';
$modal_remove = '<div class="modal fade" id="remove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="validator">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Are you sure ?</h4>
      </div>
      <div class="modal-body">
	    <form role="form" class="form-horizontal" id="rm_form" data-toggle="validator" action="submit.php" method="POST">

	  </form>
         
      </div>
      <div class="modal-footer"><div id="loading"><img src="img/loading.gif"></img></div>
	<button type="submit" class="btn btn-primary" id = "submitRM">Yes</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>
';

echo $modal_remove;
echo $modal_editContact;
echo $modal_addContact;
echo $modal_oneLiner;
echo $modal_desc;
echo $profile_info;
echo $profile_desc;
echo $profile_contacts;

?>



<!-- Ajax -->
<script src="js/jquery.js"></script>
<script type="text/javascript">


$(document).ready(function(){
// One Liner Update

			$('#submitOL').click(function() {
				$("#loading").css("display","inline");
				$.ajax({
					type: "POST",
					cache: false,
					url: "submit.php",
					data: $("#ol_form").serialize(),
					success: function(data) {$('#oneLiner').modal('hide');
						if(data == "1"){

							
							$('#successModal').find('.modal-body').text('Changes made succesfully!');
							$('#successModal').modal('show');}
						else{
							$('#failModal').find('.modal-body').text('Changes could not be saved.');
							$('#failModal').modal('show');}
						$("#loading").css("display","none");location.reload();

					}
				});
					

			});
// Desc Update

			$('#submitDesc').click(function() {
				$("#loading").css("display","inline");
				$.ajax({
					type: "POST",
					cache: false,
					url: "submit.php",
					data: $("#desc_form").serialize(),
					success: function(data) {$('#Desc').modal('hide');
						if(data == "1"){

							
							$('#successModal').find('.modal-body').text('Changes made succesfully!');
							$('#successModal').modal('show');}
						else{
							$('#failModal').find('.modal-body').text('Changes could not be saved.');
							$('#failModal').modal('show');}
						$("#loading").css("display","none");location.reload();

					}
				});
					

			});
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
						$("#loading").css("display","none");location.reload();

					}
				});
					

			});

<?php echo $modal_submit_js; ?>


});

function fetch_rm(cid,cname) {
	var msg = 'Are you sure you want to delete "'+cname+'" ?<input type="hidden" name="flag" value="removeContact"><input type="hidden" name="cid" value="'+cid+'">';
	$('#rm_form').html(msg);
			$('#submitRM').click(function() {
				$("#loading").css("display","inline");
				$.ajax({
					type: "POST",
					cache: false,
					url: "submit.php",
					data: $("#rm_form").serialize(),
					success: function(data) {$('#remove').modal('hide');
						if(data == "1"){

							
							$('#successModal').find('.modal-body').text('Changes made succesfully!');
							$('#successModal').modal('show');}
						else{
							$('#failModal').find('.modal-body').text('Changes could not be saved.');
							$('#failModal').modal('show');}
						$("#loading").css("display","none");location.reload();

					}
				});
					

			});

}

</script>


	

<?php include('footer.php'); ?>

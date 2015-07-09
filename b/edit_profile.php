<?php $page="Edit Profile"; include('header.php');
$bootstrap_js_enable = '';

echo '<link rel="stylesheet" type="text/css" href="css/imgareaselect-default.css" />';
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
	if(($_SESSION['dp']==''))
		$alert_user[0] = '<span class="badge danger">Upload your Display Picture below.</span>';
	if(($oneLiner == ""))
		$alert_user[1] = '<span class="badge danger">Add a one-liner using the icon on the right.</span>';
	if(($desc == ""))
		$alert_user[2] = '<span class="badge danger">Add a description using the icon on the right.</span>';
	if(($_SESSION['dp']!='')&&($oneLiner != "")&&($desc != ""))
		$_SESSION['incomplete'] = 'false';

}
$profile_info = '<br><br><div class="row-fluid">
		      		

					<div class="col-sm-2 ">

							'.$alert_user[0].'<img src="'.$_SESSION['dp'].'" class="img-responsive" /><br>

					</div>
					<div class="col-sm-10 no-justify">
							<span class="large-font">'.$_SESSION['name'].'</span><br>
							<span class="med-font">'.$pos_name.'</span><br>
							<p>'.$roll.'</p>
							<p class="pull-right"><a href="#foo" data-toggle="modal" data-target="#passModal" title="Change Password"  ><i class="fa fa-asterisk"></i> Change Password</a></p>
							
					</div>
		</div>
';

$profile_desc = '<div class="row-fluid">
				<div class="col-xs-12"><br>
					<h3>A line about you ... '.$alert_user[1].'<a href="#" class= "pull-right" data-toggle="modal" data-target="#oneLiner" title="Edit" ><span class="fa fa-pencil-square-o"></span></a></h3>
					
				</div>
	      		
				
				<div class="col-xs-12 well">
					
					 '.$oneLiner.'
				</div>

		</div>
		<div class="row-fluid">
	      		
				
				<div class="col-xs-12"><br>
					<h3>Description '.$alert_user[2].'<a href="#" class= "pull-right" data-toggle="modal" data-target="#Desc" title="Edit" ><span class="fa fa-pencil-square-o"></span></a></h3>
					
				</div>

		</div>
		<div class="row-fluid">
	      		

				<div class="col-xs-12 well">
					
					 '.$desc.'
				</div>

		</div>
';

$getContact = $mysqli->prepare('SELECT contact_id, contact_link FROM contact WHERE contact_people_id=?') or die('Couldn\'t check the contact');
$getContact->bind_param('s', $_SESSION['id']);
$getContact->execute();
$getContact->store_result();
// Collecting cname, clink
$getContact->bind_result($cid, $clink);
$profile_contacts = '		<div class="row-fluid"><br>
	      		

				<div class="col-xs-12 ">
					<h3>Links and Contact Information<a href="#" class= "pull-right" data-toggle="modal" data-target="#addContact" title="Add contact" ><span class="fa fa-plus-square"></span></a></h3><br>';

$modal_editContact = '';
while($getContact->fetch()) {
	$js_func = "fetch_rm('".$cid."','".$cname."');";
	$profile_contacts .= '<p>'.format_contact($clink, $_SESSION['name']).'<a href="#" class= "" data-toggle="modal" data-target="#remove" title="Delete" onclick="'.$js_func.'"> <span class="fa fa-trash-o"></span></a></p>';
	

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
		<div class="col-sm-12"><input type="text" class="form-control" id="Ol" placeholder="Enter One Liner to be displayed on about-page" name="oL" value="'.$oneLiner.'"  onKeyDown="limitText(this.form.oL,this.form.countdown,75);" 
onKeyUp="limitText(this.form.oL,this.form.countdown,75);" maxlength="75"><input type="hidden" name="flag" value="oneLiner"><br>
<input readonly type="text" name="countdown" size="1" value="0"> characters left.</font></div>

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
		<label class="col-sm-2 control-label" for="cLink">Link</label>
		<div class="col-sm-10"><input type="text" class="form-control" id="cLink" placeholder="Enter URL or Email or Phone Number or Usernames(Skype)" name="cLink"></div>
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
$change_password = '<!-- Password Modal -->
<div class="modal fade" id="passModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-toggle="validator">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Change Password</h4>
      </div>
      <div class="modal-body">
	    <form role="form" class="form-horizontal" id="pass_form" data-toggle="validator">
	    <div class="form-group">
		<label class="col-sm-2 control-label" for="exampleInputEmail2">Old Password </label>
		<div class="col-sm-10"><input type="password" class="form-control" id="exampleInputEmail2" placeholder="Enter Password" name="old_pass"></div>

	    </div>
	    <div class="form-group">
		<label class="col-sm-2 control-label" for="exampleInputEmail2">New Password </label>
		<div class="col-sm-10"><input type="password" name="new_pass" data-toggle="validator" data-minlength="6" class="form-control" id="inputPassword" placeholder="Password" required></div>
		<span class="help-block" style="margin-left:20px;">Minimum of 6 characters</span>

	    </div>
	    <div class="form-group">
		<label class="col-sm-2 control-label" for="exampleInputEmail2">Confirm new Password </label>
		<div class="col-sm-10"><input type="password" class="form-control" id="inputPasswordConfirm" data-match="#inputPassword" data-match-error="Whoops, these don\'t match" placeholder="Confirm" required name="cnf_pass"><div class="help-block with-errors"></div></div>
	    </div>

	  </form>
         
      </div>
      <div class="modal-footer">
	<button type="submit" class="btn btn-primary" id = "submitPass">Save changes</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
'; 
$profile_dp = ' <div class="row-fluid">
		<div class="col-sm-12">  <br>
			<h3>Change Your Display Image</h3><br>
			<div class="panel panel-default">
			  <div class="panel-body"> 
				<form class="form" id="dp_form" enctype="multipart/form-data" method="post" action="dp_upload.php">  
				     <div class="form-group"><input name="image" size="30" type="file" id="fileInput"> <p class="help-block">Only jpg and png allowed.</p></div>  
				    <input type="submit" value="Upload" class="btn btn-default">
				</form>  <br>
				<div class="progress">
					  <div class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
					    <span class="sr-only percent">45% Complete</span>
					  </div>
				</div>
				<div id="status"> </div>
			   </div>
			</div>
		 </div>
		</div>
';





echo $modal_uploadDP;
echo $modal_remove;
echo $modal_editContact;
echo $modal_addContact;
echo $modal_oneLiner;
echo $modal_desc;
echo $profile_info;
echo $profile_desc;
echo $profile_contacts;
echo $alert_user[0].$profile_dp;

echo $change_password;


?>



<!-- Ajax -->
<script src="js/jquery.min.js"></script>
<script src="http://malsup.github.com/jquery.form.js"></script>
<script src="js/jquery.imgareaselect.pack.js"></script>
<script type="text/javascript">
function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} else {
		limitCount.value = limitNum - limitField.value.length;
	}
}
    function preview(img, selection) {  
        var scaleX = 100 / selection.width;  
        var scaleY = 100 / selection.height;   
      
        $("#thumbnail + div > img").css({  
            width: Math.round(scaleX * 200) + "px",  
            height: Math.round(scaleY * 300) + "px",  
            marginLeft: "-" + Math.round(scaleX * selection.x1) + "px",  
            marginTop: "-" + Math.round(scaleY * selection.y1) + "px"  
        });  
        $("#x1").val(selection.x1);  
        $("#y1").val(selection.y1);  
        $("#x2").val(selection.x2);  
        $("#y2").val(selection.y2);  
        $("#w").val(selection.width);  
        $("#h").val(selection.height);  
    }  
    $(document).ready(function () {  
        $("#save_thumb").click(function() {  
            var x1 = $("#x1").val();  
            var y1 = $("#y1").val();  
            var x2 = $("#x2").val();  
            var y2 = $("#y2").val();  
            var w = $("#w").val();  
            var h = $("#h").val();  
            if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){  
                alert("You must make a selection first");  
                return false;  
            }else{  
                return true;  
            }  
        });  
    });   



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
	$(document).ready(function() {
			$('#submitPass').click(function() {
				$.ajax({
					type: "POST",
					cache: false,
					url: "set_pass.php",
					data: $("#pass_form").serialize(),
					success: function(data) {
						if(data == "1"){

							$('#passModal').modal('hide');
							$('.body-edit').html("Password Changed.");

							$('#successModal').modal('show');}
						else{
							
							$('#failModal').modal('show');}

					}
				});
					

			});

		});
(function() {
 
            var bar = $('.progress-bar');
            var percent = $('.percent');
            var status = $('#status');
 
            $('form').ajaxForm({
                beforeSend: function() {
console.log('in');
		    $("#thumbnail").imgAreaSelect({ remove:true });
                    status.empty();
                    var percentVal = '0%';
                    bar.width(percentVal)
                    percent.html(percentVal);
		    
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percentVal = percentComplete + '%';
                    bar.width(percentVal)
                    percent.html(percentVal);
                },
                success: function() {
                    var percentVal = '100%';
                    bar.width(percentVal)
                    percent.html(percentVal);
                },
                complete: function(xhr) {
                    status.html(xhr.responseText);
		    $("#thumbnail").imgAreaSelect({ aspectRatio: "1:1", onSelectChange: preview,  onSelectStart: function () {console.log('f');
        $('.dp').append('<input type="submit" class="btn btn-primary" name="upload_dp" value="Save" id="save_thumb" />');
   			} });  

                }
            });
        })();



</script>


	

<?php include('footer.php'); ?>

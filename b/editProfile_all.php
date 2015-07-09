<?php
include('db.php');
$id  = $mysqli->real_escape_string($_GET['id']);
$getInfo = $mysqli->prepare('SELECT people_id, people_name, people_roll, people_email FROM people WHERE people_id=?') or die('Couldn\'t check the profile');
$getInfo->bind_param('s', $id);
$getInfo->execute();
$getInfo->store_result();
// Collecting name, picture link, roll number, description, one liner
$getInfo->bind_result($id, $name, $roll, $email);

while($getInfo->fetch()) {
	// extract users
echo'


<form role="form" method="POST" enctype="multipart/form-data" id="addUser_form" action="submit.php" >
  <div class="form-group">
    <label for="InputName">Name</label>
    <input type="text" class="form-control" id="InputName" name="uname" placeholder="Enter name" value="'.$name.'">
  </div>
  <div class="form-group">
    <label for="InputRefNo">Roll Number/Emp Code</label>
    <input type="text" class="form-control" id="InputRefNo" name="urefno" placeholder="Enter RollNo/EmpNo" value="'.$roll.'">
  </div>
	  <div class="form-group">
    <label for="InputEmail">Email address</label>
			    <input type="email" class="form-control" id="InputEmail" name="uemail" placeholder="Enter email" value="'.$email.'"><input type="hidden" name="flag" value="editUser"><input type="hidden" name="uid" value="'.$id.'">
  </div>
  <div class="form-group">
    <label for="InputDP">Upload Image</label>
    <input type="file" id="InputDP" name="udp">
    <p class="help-block">Image must be of 150x150 dimensions.</p>
  </div>
  <button type="submit" class="btn btn-primary" id="submitAddUser">Submit</button>
  <div id="loading"><img src="img/loading.gif"></img></div>
</form>';
}
?>

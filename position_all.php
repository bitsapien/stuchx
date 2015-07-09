<?php
session_start();
include('db.php');
include('modules.php');


$id  = $mysqli->real_escape_string($_GET['id']);
$getInfo = $mysqli->prepare('SELECT pos.position_id, pos.position_name, pos.position_code,pos.position_approvalScore,ppl.people_name FROM position pos
INNER JOIN people ppl ON pos.position_addedBy = ppl.people_id WHERE pos.position_people_id=?') or die('Couldn\'t check the profile');
$getInfo->bind_param('s', $id);
$getInfo->execute();
$getInfo->store_result();
$getInfo->bind_result($pid, $pname, $pcode, $pscore, $padded);
$pos_rows = '<table class="table"><thead>
			<tr>
			<th> Name </th>
			<th> Added By</th>
			<th> Vote </th>
			</tr>
	      </thead>
	      ';

while($getInfo->fetch()) {
	// votes
	$res = check_vote($pid,$mysqli);
	if(!$res['voted'])
		$vote_td = '<td>'.'<a href="#" onclick="vote_up_dn(\''.$pid.'\');" ><span class="fa fa-arrow-up"></span></a>'.'</td>';
	else if($res['voted'] == 1)
		$vote_td = '<td>'.'<a href="#" onclick="vote_up_dn(\''.$pid.'\');" ><span class="fa fa-arrow-down"></span></a>'.'</td>';
	else
		$vote_td = '<td>'.'<span class="fa fa-times"></span>'.'</td>';
	// check whether eligible 
	$active = is_role_active($pid,$mysqli);
	if($active) 
		$tr = '<tr>';
	else
		$tr = '<tr class="alert alert-warning">';
	if($pscore == "0")
		$tr = '<tr class="alert alert-danger">';


	// extract positions
		$pos_rows .= $tr.'<td>'.$pname.'</td>'.'<td>'.$padded.'</td>'.$vote_td.'</tr>';
}
$pos_rows .="</table>";

foreach($roles as $key=>$val){
	if($key != "")
		$list.="<option>".$key."</option>";
}
$pos_form .='	
<form class="form-inline" role="form" method="POST" id="addPos_form" action="submit.php" >
  <div class="form-group">
    <label for="InputName">Role Name</label>
    <input type="text" class="form-control" id="InputName" name="pos_name" placeholder="Enter name" value="'.$name.'">
  </div>
  <div class="form-group">
    <label for="InputCode"> Type of Role</label>
    <select id="InputCode" class="form-control" name="pos_code" >
	'.$list.'  
   </select>
  </div><input type="hidden" name="flag" value="addPos"><input type="hidden" name="pos_person" value="'.$id.'"><input type="hidden" name="pos_by" value="'.$_SESSION['id'].'">
  <button type="submit" class="btn btn-primary" id="submitPos">Submit</button>
  <div id="loading"><img src="img/loading.gif"></img></div>
</form>';


echo $pos_rows;
echo $pos_form;

?>

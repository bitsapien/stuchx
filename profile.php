<?php $page = "Profile "; include('header.php');
// Getting the GET

$id = $mysqli->real_escape_string($_GET['id']);
$sql = "SELECT ppl.people_name,ppl.people_oneLiner,ppl.people_dp,ppl.people_sf,ppl.people_id,ppl.people_desc,pos.position_name,ppl.people_roll FROM people ppl INNER JOIN position pos ON ppl.people_id = pos.position_people_id WHERE people_id='".$id."'";
$getUSR = query_exec($sql,$mysqli);
$getUSR->bind_result($usr_name,$usr_ol,$usr_dp,$usr_sf,$usr_id,$usr_desc,$pos_name,$usr_roll);

while($getUSR->fetch()) {
	$name_o = $usr_name;
	$ol_o = $usr_ol;
	$dp_o = $usr_dp;
	$sf_o = $usr_sf;
	$id_o = $usr_id;
	$desc_o = $usr_desc;
	$roll_o = $usr_roll;
	$pos_o .= "/".$pos_name." ";

}
$sql = "SELECT contact_name,contact_link FROM contact WHERE contact_people_id='".$id."'";
$getUSR = query_exec($sql,$mysqli);
$getUSR->bind_result($con_name,$con_link);

while($getUSR->fetch()) {
	$con_o .= '<p><a href="'.$con_link.'">'.$con_name.'</a></p>';
}

 ?>

   		<br><br><br>

	  	<br><br>
<!-- Profile main -->
		<div class="row-fluid">
	      		

				<div class="col-sm-2 ">

						<img src="<?php echo $dp_o; ?>" class="img-responsive" />

				</div>
				<div class="col-sm-10 no-justify">
						<span class="large-font"><?php echo $name_o; ?></span><br>
						<span class="med-font"><?php echo $pos_o; ?></span><br>
						<p><?php echo $roll_o; ?></p>
						
				</div>
		</div>
		<div class="row-fluid">
	      		

				<div class="col-xs-12 "><br>
					<h3>Description</h3>
					<p><?php echo html_entity_decode($desc_o); ?></p>
				</div>

		</div>
		<div class="row-fluid">
	      		

				<div class="col-xs-12 ">
					<h3>Get in touch</h3>
					<?php echo $con_o; ?>
				</div>

		</div>
<?php include('footer.php'); ?> 	


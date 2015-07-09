<?php $page = "About"; include('header.php'); ?>

<?php
/* List of pieces that need to be extracted 
 * =========================================
 * 3 > Editoial Board Members (EDC,EDT)
 * 4 > Directors/Heads of Groups (DIR)
 * 5 > Contributors (People who have posted articles) // For now people under specific units
 */



// 3 >

$sql = "SELECT people_name,people_oneLiner,people_dp,people_sf,people_id,people_topCode FROM people WHERE people_topCode='EDT' OR people_topCode='EDC'";
$getEDT = query_exec($sql,$mysqli);
$getEDT->bind_result($edt_name,$edt_ol,$edt_dp,$edt_sf,$edt_id,$edt_tc);
$EDT_counter = 1;
while($getEDT->fetch()) {
//echo $edt_name;
	$well ='';
	if($edt_tc == 'EDC') {
		$tmp = $EDT_counter;
		$EDT_counter = 0;
		$well = 'well';
	}

	if($edt_sf == 'S') {

		$edt_card['student'][$EDT_counter] = '<div class="col-sm-6 '.$well.'"><div class="col-sm-2" >
						<a href="profile.php?id='.$edt_id.'"><img src="'.$edt_dp.'" class="img-responsive" /></a>
					</div>
					<div class="col-sm-10 ">
						<h3>'.$edt_name.'</h3>
						<small> '.array_search($edt_tc,$roles).' (Student) </small>
						<p>'.$edt_ol.'</p><br>
					</div></div>';
	}
	else if($edc_sf == 'F')  {
		$edt_card['faculty'][$EDT_counter] = '<div class="col-sm-6 '.$well.'"><div class="col-sm-2" >
						<a href="profile.php?id='.$edt_id.'"><img src="'.$edc_dp.'" class="img-responsive" /></a>
					</div>
					<div class="col-sm-10 ">
						<h3>'.$edc_name.'</h3>
						<small> '.array_search($edt_tc,$roles).' (Faculty) </small>
						<p>'.$edc_ol.'</p><br>
					</div></div>';		

	}
	if($edt_tc == 'EDC') {
		$EDT_counter = $tmp;
	}

$EDT_counter++;
}
// cards extracted ,now organize
$EDT_c = 0;
while($EDT_c < $EDT_counter){
	$edt_op .= '<div class="row-fluid">'.$edt_card['student'][$EDT_c].$edt_card['faculty'][$EDT_c].'</div><br>';
$EDT_c++;
}
// EDT in $edt_op

// 4 >

$sql = "SELECT ppl.people_name,ppl.people_oneLiner,ppl.people_dp,ppl.people_sf,ppl.people_id,pos.position_id,pos.position_name FROM people ppl INNER JOIN position pos ON ppl.people_id = pos.position_people_id WHERE pos.position_code = 'DIR'";
$getDIR = query_exec($sql,$mysqli);
$getDIR->bind_result($dir_name,$dir_ol,$dir_dp,$dir_sf,$dir_id,$dir_pos_id,$dir_pos_name);
$DIR_counter = 0;
while($getDIR->fetch()) {
	if($dir_sf == 'S') {
		if(is_role_active($dir_pos_id,$mysqli,$dir_id))
		$dir_card['student'][$DIR_counter] = '<div class="col-sm-6"><div class="col-sm-2" >
						<a href="profile.php?id='.$dir_id.'"><img src="'.$dir_dp.'" class="img-responsive" /></a>
					</div>
					<div class="col-sm-10">
						<h3>'.$dir_name.'</h3>
						<small> '.$dir_pos_name.' (Student) </small>
						<br>
					</div></div>';
		else
			$dir_card['student'][$DIR_counter] = '';
	}
	else if($edc_sf == 'F')  {
		if(is_role_active($dir_id,$mysqli,$dir_id))
		$dir_card['faculty'][$DIR_counter] = '<div class="col-sm-6"><div class="col-sm-2" >
						<a href="profile.php?id='.$dir_id.'"><img src="'.$dir_dp.'" class="img-responsive" /></a>
					</div>
					<div class="col-sm-10">
						<h3>'.$dir_name.'</h3>
						<small> '.$dir_pos_name.' (Faculty) </small>
						<br>
					</div></div>';		
		else
			$dir_card['faculty'][$DIR_counter] = '';

	}
$DIR_counter++;
}
// cards extracted ,now organize
$DIR_c = 0;
while($DIR_c <= $DIR_counter){
	$dir_op .= '<div class="row-fluid">'.$dir_card['student'][$DIR_c].$dir_card['faculty'][$DIR_c].'</div><br>';
$DIR_c++;
}
// DIR in $dir_op
// 5 >

$sql = "SELECT ppl.people_name,ppl.people_dp,ppl.people_sf,ppl.people_id FROM people ppl INNER JOIN position pos ON ppl.people_id = pos.position_people_id WHERE pos.position_code = 'MOD'";
$getMOD = query_exec($sql,$mysqli);
$getMOD->bind_result($mod_name,$mod_dp,$mod_sf,$mod_id);
$MOD_counter = 0;
while($getMOD->fetch()) {
	$mod_op.= '<a href="profile.php?id='.$mod_id.'" title="'.$mod_name.'"><img src="'.$mod_dp.'" class="contrib-img" /></a><';
}

// MOD in $mod_op
?>




   		<br><br><br>
		<div class="row-fluid">

				<div class="col-xs-6 ">

					<img src="img/logo-stuch.png" class="img-responsive" alt="STUCH">

				</div>

				<div class="col-xs-6 ">

					<img src="img/aditya-big.png" class="img-responsive pull-right" alt="AEC">
				</div>

			<div class="col-sm-12 ">
	      			<br>
			</div>
		</div>	
		
		<div class="row-fluid">
			<div class="col-sm-12 "><br><br><br><br>
		      		<p class="med-font center">STUCHx is the web version of the college magazine. Read uncensored articles and catch all behind-the-scenes action here.</p><br><br>
			</div>
		</div>
	  	<br><br>
	<!-- Editorial Board -->
		<div class="row-fluid">

			
				<div class="col-sm-12 ">
					<h2> Editorial Board </h2><br>
				</div>

		</div>	
	<br>
		<?php echo $edt_op; ?>
	<!-- Directors -->
		<div class="row-fluid">

			
				<div class="col-sm-12 ">
					<h2> Directors </h2><br>
				</div>

		</div>	
	<br>
		<?php echo $dir_op; ?>
	<!-- Volunteers -->
		<div class="row-fluid contib">

			
				<div class="col-sm-12 ">
					<h2> Volunteers </h2>
				</div>

		</div>	
	<br>

		<div class="row-fluid">
	      		
			<div class="col-sm-12 contrib">
				<?php echo $mod_op; ?>
			</div>
		</div>
	<!-- Contact Info -->
		<div class="row-fluid contib">

			
				<div class="col-sm-12 "><br><hr>
					<h2> Contact Information </h2>
				</div>

		</div>	
	<br>

		<div class="row-fluid">
	      		
			<div class="col-sm-12 contrib">
			<?php
				/* SOCIAL */
				foreach($social as $name=>$link)
					echo '<h4> '.format_contact($link, $name).'</h4>';
			?>
				<h4><span class="fa fa-envelope-square"> </span> <a href="mailto:<?php echo $send_email; ?>"><?php echo $send_email; ?></a> </h4>
			</div>

		</div>
<?php include('footer.php'); ?> 	


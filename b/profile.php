<?php $page = "Profile "; include('header.php');
// Getting the GET

$id = $mysqli->real_escape_string($_GET['id']);
$sql = "SELECT ppl.people_name,ppl.people_oneLiner,ppl.people_dp,ppl.people_sf,ppl.people_id,ppl.people_desc,ppl.people_roll FROM people ppl WHERE people_id='".$id."'";
$getUSR = query_exec($sql,$mysqli);
$getUSR->bind_result($usr_name,$usr_ol,$usr_dp,$usr_sf,$usr_id,$usr_desc,$usr_roll);

while($getUSR->fetch()) {
	$name_o = $usr_name;
	$ol_o = $usr_ol;
	$dp_o = $usr_dp;
	$sf_o = $usr_sf;
	$id_o = $usr_id;
	$desc_o = $usr_desc;
	$roll_o = $usr_roll;


}
// Listing Positions
$id = $mysqli->real_escape_string($_GET['id']);
$sql = "SELECT position_id, position_name,position_code FROM position WHERE position_people_id='".$id."'";
$getPOS = query_exec($sql,$mysqli);
$getPOS->bind_result($pos_id,$pos_name,$pos_code);

while($getPOS->fetch()) {
	if(is_role_active($pos_id,$mysqli)){

	$pos_o .= "/".$pos_name." ";
	}

}
if ($pos_o == '')
$pos_o = 'Contributor';
// Listing contacts
$sql = "SELECT contact_link FROM contact WHERE contact_people_id='".$id."'";
$getUSR = query_exec($sql,$mysqli);
$getUSR->bind_result($con_link);

while($getUSR->fetch()) {
	$con_o .= '<p>'.format_contact($con_link, $name_o).'</p>';
}

// Listing blogs
$getBlogList = $mysqli->prepare('SELECT blog_id,blog_title FROM blog WHERE blog_final=1 AND blog_by="'.$id.'" ;') or die($mysqli->error);
$getBlogList->execute();
$getBlogList->store_result();

$getBlogList->bind_result($blog_id,$blog_title);

$list = '<ul>';
$f = 0;
while($getBlogList->fetch()) {
	$list.='<li><a href="post.php?id='.$blog_id.'" title="View" >'.$blog_title.'</a></li>';$f = 1;
}
$list.='</ul>';
if($f == '0')
$list = '<em> No posts yet. </em>';
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
					<h3>Description</h3><br>
					<p><?php echo html_entity_decode($desc_o); ?></p><br>
				</div>

		</div>
		<div class="row-fluid">
	      		

				<div class="col-xs-12 ">
					<h3>Get in touch</h3><br>
					<?php echo $con_o; ?>
				</div>

		</div>
		<div class="row-fluid">
	      		

				<div class="col-xs-12 "><br>
					<div class="panel panel-default">
					  <div class="panel-heading"><h3>Posts</h3></div>
					  <div class="panel-body"> 
					<p><?php echo $list; ?></p><br>
					  </div>
					</div>
				</div>

		</div>

<?php include('footer.php'); ?> 	


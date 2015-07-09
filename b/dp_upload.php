<?php

include('db.php');
        if (isset($_FILES["image"])) {  
        //Get the file information  
        $userfile_name = $_FILES["image"]["name"];  
        $userfile_tmp = $_FILES["image"]["tmp_name"];  
        $userfile_size = $_FILES["image"]["size"];  
        $filename = basename($_FILES["image"]["name"]);  
        $file_ext = substr($filename, strrpos($filename, ".") + 1);  
        $max_file = 6000000;
	$pic_width = 300;
        //Only process if the file is a JPG and below the allowed limit  
        if((!empty($_FILES["image"])) && ($_FILES["image"]["error"] == 0)) {  
            if (($file_ext!="jpg") && ($userfile_size > $max_file)) {  
                $error= "ONLY jpeg images under 5MB are accepted for upload";  
            }  
        }else{  
            $error= "Select a jpeg image for upload";  
        }  
        //Everything is ok, so we can upload the image.  
        if (strlen($error)==0){  
      
            if (isset($_FILES["image"]["name"])){  
      		$large_image_location = 'temp/'.md5(time()).'.jpg';
                move_uploaded_file($userfile_tmp, $large_image_location) or die('file error');  
                chmod ($large_image_location, 0777); 
      		list($width, $height, $type, $attr) = getimagesize($large_image_location);
		$height_new = ($pic_width/$width)*$height;
            }  
    		echo '<center><p class="text-info"><i class="fa fa-crop"></i> Click and drag on the picture to make a selection. If it doesn\'t work, wait for 10-15 seconds and retry dragging. </p><img id="thumbnail" class="pull-center" src="'.$large_image_location.'" width="'.$pic_width.'" height="'.$height_new.'">	<form name="thumbnail" action="dp_upload.php" method="post" class="dp">
				

				<input type="hidden" name="loc" value="'.$large_image_location.'" id="loc" />

				<input type="hidden" name="sm_width" value="'.$pic_width.'" id="sm_width" />

				<input type="hidden" name="x1" value="" id="x1" />

				<input type="hidden" name="y1" value="" id="y1" />

				<input type="hidden" name="x2" value="" id="x2" />

				<input type="hidden" name="y2" value="" id="y2" />

				<input type="hidden" name="w" value="" id="w" />

				<input type="hidden" name="h" value="" id="h" /> <br>

				

			</form></center>';
            exit();  
        }  
    }

// Uploading Pic 

if(($_POST['upload_dp'] == 'Save')&&($_POST['x1']!='')) { 
    //Get the new coordinates to crop the image. 
echo '14';
    $x1 = (int) $_POST["x1"];
    $y1 = (int) $_POST["y1"];
    $x2 = (int) $_POST["x2"]; // not really required  
    $y2 = (int) $_POST["y2"]; // not really required  
    $w = (int) $_POST["w"]; 
    $h = (int) $_POST["h"];  
    $sm_width = (int)$_POST['sm_width'];

    $large_image_location = $_POST["loc"];
    $thumb_image_location = "img/profiles/" .  md5(time()).".png";

    // Image Preperation 
    list($bg_width, $bg_height, $type, $attr) = getimagesize($large_image_location);
    $sm_height = ($sm_width/$bg_width)*$bg_height;
    $x1_n = round($x1 * ($bg_width/$sm_width));
    $y1_n = round($y1 * ($bg_height/$sm_height));
    $w_n = round($w * ($bg_width/$sm_width));
    $h_n = $w_n;
	
    //Scale the image to the 150px by 150px  
    $scale = 150/$w_n;  
    $cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w_n,$h_n,$x1_n,$y1_n,$scale);  
    //Reload the page again to view the thumbnail  
    $send['people_dp'] = $cropped;echo '<p>'.$cropped.'<p>';
    $where['people_id'] = $_SESSION['id'];
    do_sql('people',$send,'update',$mysqli,$where);
    // deleting the temporary and old file
    $old = getcwd(); // Save the current directory
    chdir('temp/') or die('ch');echo getcwd();
    unlink(substr($large_image_location,5));
    chdir($old); 
    $old = getcwd(); // Save the current directory
    //echo chdir('img/profiles/');
    unlink(substr($_SESSION['dp'],13));
    chdir($old); 

    $_SESSION['dp'] = $cropped;

    header('Location:edit_profile.php?done=1');
}  
function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($image); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image); 
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image); 
			break;
  	}
	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
	    if($newImage && imagefilter($newImage, IMG_FILTER_GRAYSCALE))
	    {
	      echo 'Image converted to grayscale.<img src="'.$thumb_image_name.'">';

	      imagepng($newImage, $thumb_image_name) or die('gerror');
	    }
	    else
	    {
	      return 'Conversion to grayscale failed.';
	    }
	// checking bit _depth

	
	chmod($thumb_image_name, 0777);
	return $thumb_image_name;
}

?>

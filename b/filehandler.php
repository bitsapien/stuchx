<?php
session_start();

function fileHandler($file, $type, $max_width, $max_height)
{
    // filesize check
	function return_bytes($val) {
	    $val = trim($val);
	    $last = strtolower($val[strlen($val)-1]);
	    switch($last) {
		// The 'G' modifier is available since PHP 5.1.0
		case 'g':
		    $val *= 1024;
		case 'm':
		    $val *= 1024;
		case 'k':
		    $val *= 1024;
	    }

    	return $val;
	}

	if($file['size']>=return_bytes(ini_get('post_max_size')))
		{header('location:dash.php?3');exit;}

    $upload_flag = 0;
    if ($type != "img") {
        //$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        //echo "Extension : ".$ext;
        $abs_path = $type . "_" . md5(time());
        $path = "../files/" . $abs_path;
        if (($file['type'] == "application/pdf") || ($file['type'] == "application/rtf") || ($file['type'] == "application/msword") || ($file['type'] == "message/rfc822") || ($file['type'] == "message/rfc822") || ($file['type'] == "text/html") || ($file['type'] == "text/plain") || ($file['type'] == "image/gif") || ($file['type'] == "image/jpeg") || ($file['type'] == "image/x-png") || ($file['type'] == "image/pjpeg") || ($file['type'] == "image/png") || ($file['type'] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document")) {
            if ($file["name"] != "") {
                if ($file["error"] > 0) {
                    $debug = "Return Code: " . $file["error"] . "<br>";
                    echo "new";
                } else {
                    $debug.= "Upload: " . $file["name"] . ",";
                    $debug.= "Type: " . $file["type"] . ",";
                    $debug.= "Size: " . ($file["size"] / 1024) . " kB,";
                    $debug.= "Temp media: " . $file["tmp_name"] . ",";
                    if (file_exists($path)) {
                        $debug.= $file["name"] . " already exists. ";
                    } else { 
                        move_uploaded_file($file["tmp_name"], $path) or die("file error");
                        $debug.= "Stored in: " . $path;
                        $upload_flag = 1;
                    }
                }return $abs_path;
            }
	    else{return 0;}
            
        }
    } else {




        //$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $abs_path = $type . "_" . md5(time()).".png";
        $path = "img/profiles/" . $abs_path;
        if (($file['type'] == "image/jpeg") || ($file['type'] == "image/x-png") || ($file['type'] == "image/png")) {

	    $arr_image_details = getimagesize($file["tmp_name"]);
            $width = $arr_image_details[0];print_r($arr_image_details);
            $height = $arr_image_details[1];echo $width;echo $height;
	    if(($width!=150)&&($height!=150))
		return 'File Dimensions incorrect.';

	    // converting to grayscale
	    switch($file['type']) {
		case 'image/jpeg':
			$im = imagecreatefromjpeg($file["tmp_name"]);
		break;
		case 'image/png':
		case 'image/x-png':
			$im = imagecreatefrompng($file["tmp_name"]);
		break;
	    }
	    if($im && imagefilter($im, IMG_FILTER_GRAYSCALE))
	    {
	      echo 'Image converted to grayscale.';

	      imagepng($im, $file["tmp_name"]);
	    }
	    else
	    {
	      return 'Conversion to grayscale failed.';
	    }

	    imagedestroy($im);

            if ($file["name"] != "") {
                if ($file["error"] > 0) {
                    $debug = "Return Code: " . $file["error"] . "<br>";
                    return 'There was an error processing the file.';
                } else {
                    $debug.= "Upload: " . $file["name"] . ",";
                    $debug.= "Type: " . $file["type"] . ",";
                    $debug.= "Size: " . ($file["size"] / 1024) . " kB,";
                    $debug.= "Temp media: " . $file["tmp_name"] . ",";
                    if (file_exists($path)) {
                        $debug.= $file["name"] . " already exists. ";
                    } else {
                        /*if (makeThumbnails('../img/thumbnails/', $file["tmp_name"], $abs_path, $max_width, $max_height)) {
                        }*/
                        move_uploaded_file($file["tmp_name"], $path) or die("file error");
                        $debug.= "Stored in: " . $path;
                        $upload_flag = 1;
                    }
                }
            }return $abs_path;
        }
	else{return 'File Type Incorrect!';}
        
    }
    //handling done
    //echo $upload_flag;
    
}
?>


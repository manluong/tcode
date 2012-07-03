<?php
class Imagel {
    public $max_width = "500";							// Max width allowed for the large image
    private $thumb_width = "100";						// Width of thumbnail image
    private $thumb_height = "100";						// Height of thumbnail image
    private $allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
    private $allowed_image_ext = array();
    private $image_ext = "";


    function __construct(){
	$this->allowed_image_ext = array_unique($allowed_image_types); // do not change this
	foreach($this->allowed_image_ext as $mime_type => $ext) {
	$this->image_ext.= strtoupper($ext)." ";
	}
    }

    function resizeImage($image,$width,$height,$scale) {
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
	imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);

	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$image);
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$image,90);
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$image);
			break;
    }

	chmod($image, 0777);
	return $image;
    }
    //You do not need to alter these functions
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
	    switch($imageType) {
		    case "image/gif":
			    imagegif($newImage,$thumb_image_name);
			    break;
	    case "image/pjpeg":
		    case "image/jpeg":
		    case "image/jpg":
			    imagejpeg($newImage,$thumb_image_name,90);
			    break;
		    case "image/png":
		    case "image/x-png":
			    imagepng($newImage,$thumb_image_name);
			    break;
	}
	    chmod($thumb_image_name, 0777);
	    return $thumb_image_name;
    }
    //You do not need to alter these functions
    function getHeight($image) {
	    $size = getimagesize($image);
	    $height = $size[1];
	    return $height;
    }
    //You do not need to alter these functions
    function getWidth($image) {
	    $size = getimagesize($image);
	    $width = $size[0];
	    return $width;
    }

}
?>

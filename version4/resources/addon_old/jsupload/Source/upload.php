<?php
$target_path = "../Demo/uploads/";
$allowedExts = array();
$maxFileSize = 0;

function ByteSize($bytes) { 
    $size = $bytes / 1024; 
    if($size < 1024) 
        { 
        $size = number_format($size, 2);
        $size .= ' KB'; 
        }  
    else  
        { 
        if($size / 1024 < 1024)  
            { 
            $size = number_format($size / 1024, 2); 
            $size .= ' MB'; 
            }  
        else if ($size / 1024 / 1024 < 1024)   
            { 
            $size = number_format($size / 1024 / 1024, 2); 
            $size .= ' GB'; 
            }  
        } 
    return $size; 
    } 

function getHeaders() {
    $headers = array();
    foreach ($_SERVER as $k => $v)
	{
        if (substr($k, 0, 5) == "HTTP_")
		{
            $k = str_replace('_', ' ', substr($k, 5));
            $k = str_replace(' ', '-', ucwords(strtolower($k)));
            $headers[$k] = $v;
		}
	}
    return $headers;
}  

$headers = getHeaders();

if ($headers['X-Requested-With']=='XMLHttpRequest') { 
	$fileName = $headers['X-File-Name'];
	$fileSize = $headers['X-File-Size'];
	$ext = substr($fileName, strrpos($fileName, '.') + 1);
	if (in_array($ext,$allowedExts) or empty($allowedExts)) {
		if ($fileSize<$maxFileSize or empty($maxFileSize)) {
		$content = file_get_contents("php://input");
		file_put_contents($target_path.$fileName,$content);
		echo '{"success":true, "file": "'.$target_path.$fileName.'"}';
	} else { echo('{"success":false, "details": "Maximum file size: '.ByteSize($maxFileSize).'."}'); };
	} else {
		echo('{"success":false, "details": "File type '.$ext.' not allowed."}');
		}
} else {
	if ($_FILES['file']['name']!='') {
	$fileName= $_FILES['file']['name'];
	$fileSize = $_FILES['file']['size'];
	$ext = substr($fileName, strrpos($fileName, '.') + 1);
	if (in_array($ext,$allowedExts) or empty($allowedExts)) {
		if ($fileSize<$maxFileSize or empty($maxFileSize)) {
	$target_path = $target_path . basename( $_FILES['file']['name']);
	if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
		echo '{"success":true, "file": "'.$target_path.'"}';
	} else{
		echo '{"success":false, "details": "move_uploaded_file failed"}';
	}
} else { echo('{"success":false, "details": "Maximum file size: '.ByteSize($maxFileSize).'."}'); };
} else echo('{"success":false, "details": "File type '.$ext.' not allowed."}');
} else echo '{"success":false, "details": "No file received."}';

	
	}

?>

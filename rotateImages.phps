<?php
error_reporting(E_ALL);
$dir = "/var/www/vhosts/worldskills.org/cake/wiw/webroot/img/wiw/resized/";
echo "Starting to rotate images in folder {$dir}<hr />";
$dh = opendir($dir);

while(($file = readdir($dh)) !== false){
    if($file == "." || $file == "..") continue;
    
    //check filetype
    $ext = explode(".", $file);
    $ext = array_pop($ext);
    if($ext != 'jpg' && $ext != 'jpeg') continue;

    //set file permissions
  //  chmod($dir."/".$file, 0777);

    //check image rotation from EXIF data
    $exif = @exif_read_data("{$dir}" . $file);

    //check orientation
    if (!empty($exif['Orientation'])) {

        $image = imagecreatefromjpeg("{$dir}" . $file);
        $needsRotation = false;

        switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 0, 0);
                $needsRotation = true;
                break;

            case 6:
                $image = imagerotate($image, 0, 0);
                $needsRotation = true;
                break;

            case 8:
                $image = imagerotate($image, 0, 0);
                $needsRotation = true;
                break;
        }

        if($needsRotation){
            //save image
            echo "Rotated image {$file}<br />";
            //header('Content-type: image/jpeg');
            imagejpeg($image, "{$dir}".$file, 100); 
        }//needsRotation

        imagedestroy($image); //free from memory
    }   


}//while readdir

?>
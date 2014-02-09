<?php

$DEBUG = false;

if($DEBUG){
	echo "<html><body>";
	}

$res_array = array();
$res_object = array();

$pic_query = 'https://api.500px.com/v1/photos?';
$pic_query .= 'consumer_key=c7yohCeIPeEPwG52IUqGotl7kFD8tLzLQkBHWt6B';
$pic_query .= '&feature=popular';
$pics_object = json_decode(file_get_contents($pic_query));

foreach ($pics_object->photos as $photo_object) {
	$res_object = array();
	//$res_object['id'] = $photo_object->id;

	$exif_query = 'https://api.500px.com/v1/photos/';
	$exif_query .= $photo_object->id;
	$exif_query .= '?consumer_key=c7yohCeIPeEPwG52IUqGotl7kFD8tLzLQkBHWt6B';
	$exif_json = file_get_contents($exif_query);
	$exif_object = json_decode($exif_json);
	$exif = $exif_object->photo;
	
	$res_object['url'] = $exif->image_url;
	$res_object['aperture'] = str_replace(',','.',$exif->aperture);
	$res_object['shutter_speed'] = $exif->shutter_speed;
	$res_object['iso'] = $exif->iso;
	$res_object['focal_length'] = $exif->focal_length;
	$res_object['camera'] = $exif->camera;
	
	if($DEBUG){
		echo("<p><img src=\"".$res_object['url']."\"></img></p>");
	}
	
	if($exif->height == 0){ 
		$ratio = 0;
	}else
		$ratio = $exif->width / $exif->height;
	
	//echo $ratio;
	
	//echo $exif->image_url;
	//print_r($exif);
	
	if( ($ratio > 1.2) && ($ratio < 1.55) && isset($res_object['url']) && isset($res_object['aperture']) && isset($res_object['shutter_speed']) && isset($res_object['iso']) && isset($res_object['focal_length'])){ 
		$res_array[] = $res_object;
	}
}
//print_r($res_array);
//echo json_encode($res_array);

$fp = fopen('cache.json', 'w');
fwrite($fp, json_encode($res_array));
fclose($fp);

if($DEBUG){
	echo "</body></html>";
}

echo "Done: " . count($res_array) . " photos pulled.";

?>
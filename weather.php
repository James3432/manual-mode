<?php
$key = '006787398cd71685';
$query = 'http://api.wunderground.com/api/' . $key . '/conditions/q/UK/London.json';
$obj = json_decode(file_get_contents($query), true);
$conds = $obj['current_observation'];
//print_r($conds);
$weather = $conds['weather'];
$time = new DateTime($conds['local_time_rfc822']);
//echo $weather;
$hour = $time->format('H');
if($hour > 6 && $hour < 9){
	$timeofday = 'sunrise ';
}elseif($hour >= 9 && $hour < 18){
	$timeofday = 'day ';
}elseif($hour >= 18 && $hour < 21){
	$timeofday = 'sunset ';
}else{
	$timeofday = 'night ';
}

$weatherstring = $timeofday . $weather;

$res_array = array();
$res_object = array();

$pic_query = 'https://api.500px.com/v1/photos/search?';
$pic_query .= 'consumer_key=c7yohCeIPeEPwG52IUqGotl7kFD8tLzLQkBHWt6B';
$pic_query .= '&term=';
$pic_query .= urlencode($weatherstring);
$pic_query .= '&sort=times_viewed';//votes_count or favorites_count are alternatives. rating didn't work so well
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
	$res_object['shutter_speed'] = str_replace(' ','',str_replace('.','',preg_replace('/[a-zA-Z]/', ' ', $exif->shutter_speed)));
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
		$weatherphoto = $res_object;
		break;
	}
}

	$int_aperture = (float)(end(explode('/', $weatherphoto['aperture'])));
	if ($int_aperture >= 9) {
		$weatherphoto['aperture_description'] = "Small aperture\nLess light\nEverything in focus";
	} elseif ($int_aperture >= 4) {
		$weatherphoto['aperture_description'] = "Medium aperture\nAverage amount of light\nSlight background blur";
	} else {
		$weatherphoto['aperture_description'] = "Wide aperture\nLots of light\nBlurred background";
	}

	$sh_array = explode('/', $weatherphoto['shutter_speed']);
	if (count($sh_array) == 1) {
		$num_shutter = $sh_array[0];
	} else {
		$num_shutter = $sh_array[0]/$sh_array[1];
	}
	if ($num_shutter <= 0.001) {
		$weatherphoto['shutter_speed_description'] = "Fast shutter\nLess light\nFreezes motion";
	} elseif ($num_shutter <= 0.017) {
		$weatherphoto['shutter_speed_description'] = "Medium shutter\nAverage amount of light\nSlight motion blur";
	} elseif ($num_shutter <= 0.07) {
		$weatherphoto['shutter_speed_description'] = "Slow shutter\nLots of light\nBlurred motion";
	} else {
		$weatherphoto['shutter_speed_description'] = "Very slow shutter\nLots of light\nUse a tripod";
	}

	$int_iso = (int)($weatherphoto['iso']);
	if ($int_iso >= 1600) {
		$weatherphoto['iso_description'] = "High sensitivity";
	} elseif ($int_iso >= 400) {
		$weatherphoto['iso_description'] = "Medium sensitivity";
	} else {
		$weatherphoto['iso_description'] = "Low sensitivity";
	}

	$int_focal_length = (int)($weatherphoto['focal_length']);
	if ($int_focal_length >= 100) {
		$weatherphoto['focal_length_description'] = "High zoom";
	} elseif ($int_focal_length >= 20) {
		$weatherphoto['focal_length_description'] = "Medium zoom";
	} else {
		$weatherphoto['focal_length_description'] = "Zoomed out";
	}


?>
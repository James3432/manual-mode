<?php

$DEBUG = false;

if($DEBUG){
	echo "<html><body>";
	}

if(isset($_GET['q'])){
	$search = $_GET['q'];
	
	$res_array = array();
	$res_object = array();

	$pic_query = 'https://api.500px.com/v1/photos/search?';
	$pic_query .= 'consumer_key=c7yohCeIPeEPwG52IUqGotl7kFD8tLzLQkBHWt6B';
	$pic_query .= '&term=';
	$pic_query .= urlencode($search);
	$pic_query .= '&sort=rating';
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
		
		$int_aperture = (float)(end(explode('/', $res_object['aperture'])));
	if ($int_aperture >= 9) {
		$res_object['aperture_description'] = "Small aperture\nLess light\nEverything in focus";
	} elseif ($int_aperture >= 4) {
		$res_object['aperture_description'] = "Medium aperture\nAverage amount of light\nSlight background blur";
	} else {
		$res_object['aperture_description'] = "Wide aperture\nLots of light\nBlurred background";
	}

	$sh_array = explode('/', $res_object['shutter_speed']);
	if (count($sh_array) == 1) {
		$num_shutter = $sh_array[0];
	} else {
		$num_shutter = $sh_array[0]/$sh_array[1];
	}
	if ($num_shutter <= 0.001) {
		$res_object['shutter_speed_description'] = "Fast shutter\nLess light\nFreezes motion";
	} elseif ($num_shutter <= 0.017) {
		$res_object['shutter_speed_description'] = "Medium shutter\nAverage amount of light\nSlight motion blur";
	} elseif ($num_shutter <= 0.07) {
		$res_object['shutter_speed_description'] = "Slow shutter\nLots of light\nBlurred motion";
	} else {
		$res_object['shutter_speed_description'] = "Very slow shutter\nLots of light\nUse a tripod";
	}

	$int_iso = (int)($res_object['iso']);
	if ($int_iso >= 1600) {
		$res_object['iso_description'] = "High sensitivity";
	} elseif ($int_iso >= 400) {
		$res_object['iso_description'] = "Medium sensitivity";
	} else {
		$res_object['iso_description'] = "Low sensitivity";
	}

	$int_focal_length = (int)($res_object['focal_length']);
	if ($int_focal_length >= 100) {
		$res_object['focal_length_description'] = "High zoom";
	} elseif ($int_focal_length >= 20) {
		$res_object['focal_length_description'] = "Medium zoom";
	} else {
		$res_object['focal_length_description'] = "Zoomed out";
	}
		
		if( ($ratio > 1.2) && ($ratio < 1.55) && isset($res_object['url']) && isset($res_object['aperture']) && isset($res_object['shutter_speed']) && isset($res_object['iso']) && isset($res_object['focal_length']) &&!empty($res_object['url']) &&!empty($res_object['aperture']) &&!empty($res_object['shutter_speed']) &&!empty($res_object['iso']) &&!empty($res_object['focal_length']) ){ 
			$res_array[] = $res_object;
		}
	}
	//print_r($res_array);
	echo json_encode($res_array);

	//$fp = fopen('cache.json', 'w');
	//fwrite($fp, json_encode($res_array));
	//fclose($fp);

	if($DEBUG){
		echo "</body></html>";
	}

	//echo "Done: " . count($res_array) . " photos pulled.";
	
}else{
	//echo "null";
}

?>
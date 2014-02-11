<?php

$DEBUG = false;

if($DEBUG){
	echo "<html><body>";
	}

$res_array = array();
$res_object = array();

/*
$flickr_query = 'http://api.flickr.com/services/rest/?';
$flickr_query .= '&method=flickr.photos.search';
$flickr_query .= '&format=json&nojsoncallback=1';
$flickr_query .= '&api_key=91ffacfe5d47d2dc09282c43f9fe1477';
$flickr_query .= '&sort=relevance';
$flickr_query .= '&license=1';
$flickr_query .= '&text=' . $reference;
$flickr_query .= '&per_page=';
$flickr_query .= '3';
*/
$flickr_query = 'http://api.flickr.com/services/rest/?';
$flickr_query .= '&method=flickr.interestingness.getList';
$flickr_query .= '&format=json&nojsoncallback=1';
$flickr_query .= '&api_key=91ffacfe5d47d2dc09282c43f9fe1477';
$flickr_query .= '&sort=views';
$flickr_query .= '&per_page=10';
$flickr_query .= '&license=1';
$flickr_object = json_decode(file_get_contents($flickr_query));

foreach ($flickr_object->photos->photo as $photo_object) {
	$res_object = array();
	//$res_object['id'] = $photo_object->id;

	$exif_query = 'http://api.flickr.com/services/rest/?';
	$exif_query .= '&method=flickr.photos.getExif';
	$exif_query .= '&format=json&nojsoncallback=1';
	$exif_query .= '&photo_id=';
	$exif_query .= $photo_object->id;
	$exif_query .= '&api_key=91ffacfe5d47d2dc09282c43f9fe1477';

	$exif_json = file_get_contents($exif_query);
	$exif_object = json_decode($exif_json);

	$res_object['url'] = 'http://farm' . $photo_object->farm . '.staticflickr.com/' . $photo_object->server . '/' . $photo_object->id . '_' . $photo_object->secret . '_b.jpg';
	
	if($DEBUG){
		echo("<p><img src=\"".$res_object['url']."\"></img></p>");
	}

	if ($exif_object->stat == 'ok') {
		foreach ($exif_object->photo->exif as $exif) {
			
			if ($exif->tag == 'FNumber') {
				$res_object['aperture'] = $exif->clean->_content;
			}
			elseif ($exif->tag == 'ExposureTime') {
				$res_object['shutter_speed'] = "1/"+$exif->clean->_content;
			}
			elseif ($exif->tag == 'CameraISO' || $exif->tag == 'ISO') {
				$res_object['iso'] = $exif->raw->_content;
			}
			elseif ($exif->tag == 'FocalLength') {
				$res_object['focal_length'] = $exif->raw->_content;
			}
			elseif ($exif->tag == 'Model') {
				$res_object['camera'] = $exif->raw->_content;
			}
		}
	}
	
	$size = getimagesize($res_object['url']);
	$ratio = $size[0] / $size[1];
	//echo $ratio;
	
	if( ($ratio > 1.2) && ($ratio < 1.55) && isset($res_object['url']) && isset($res_object['aperture']) && isset($res_object['shutter_speed']) && isset($res_object['iso']) && isset($res_object['focal_length'])){ 
		$res_array[] = $res_object;
	}
}

//echo json_encode($res_array);

$fp = fopen('cache.json', 'w');
fwrite($fp, json_encode($res_array));
fclose($fp);

if($DEBUG){
	echo "</body></html>";
}

?>

Done.


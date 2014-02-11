<?php

$DEBUG = false;

if($DEBUG){
	echo "<html><body>";
	}

$res_array = array();



$date = getdate();

// Responses for specific dates
if ($date['mon'] == '11' && $date['mday'] == '5') $res_array[] = 'fireworks';
if ($date['mon'] == '2' && $date['mday'] >= '7' && $date['mday'] <= '9' && $date['year'] == '2014') $res_array[] = 'hackathon';

// Responses for specific times
if ($date['hour'] == '11' && $date['mday'] == '5') $res_array[] = 'fireworks';

// Sunrise and sunset
$sunset = date_sunset(time(), SUNFUNCS_RET_TIMESTAMP);
$sunrise = date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP);
if (time() > ($sunset - 2000) && time() < ($sunset + 1500)) $res_array[] = 'sunset';
if (time() > ($sunrise - 2000) && time() < ($sunrise + 1500)) $res_array[] = 'sunrise';

// Some default terms
$res_array[] = 'concert';
$res_array[] = 'sports';
$res_array[] = 'portrait';

// Popular flickr tags
$flickr_query = 'http://api.flickr.com/services/rest/?';
$flickr_query .= '&method=flickr.tags.getHotList';
$flickr_query .= '&format=json&nojsoncallback=1';
$flickr_query .= '&api_key=91ffacfe5d47d2dc09282c43f9fe1477';
$flickr_query .= '&count=3';
$flickr_query .= '&period=week';
$flickr_object = json_decode(file_get_contents($flickr_query));
foreach($flickr_object->hottags->tag as $tag) {
	$res_array[] = $tag->_content;
}

// Responses for specific seasons
if ($date['mon'] == '1' || $date['mon'] == '2' || $date['mon'] == '12') $res_array[] = 'winter';
if ($date['mon'] == '3' || $date['mon'] == '4' || $date['mon'] == '5') $res_array[] = 'spring';
if ($date['mon'] == '6' || $date['mon'] == '7' || $date['mon'] == '8') $res_array[] = 'summer';
if ($date['mon'] == '9' || $date['mon'] == '10' || $date['mon'] == '11') $res_array[] = 'autumn';

echo json_encode($res_array);


?>


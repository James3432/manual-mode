<?php

$res_array = array();
$res_object = array();

for($i = 1; $i <=100; $i++) {
		
	$res_object['url'] = "test url";
	$res_object['shutter_speed'] = "2000";
	$res_object['aperture'] = "f11";
	$res_object['iso'] = "200";
	$res_object['focal_length'] = "200";
	$res_object['camera'] = "200";

	$res_array[] = $res_object;
}

echo json_encode($res_array);


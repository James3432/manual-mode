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
	$timeofday = 'day time ';
}elseif($hour >= 18 && $hour < 21){
	$timeofday = 'sunset ';
}else{
	$timeofday = 'night ';
}

echo $timeofday . $weather;

?>
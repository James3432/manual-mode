<?php
$key = '006787398cd71685';
$query = 'http://api.wunderground.com/api/' . $key . '/conditions/q/CA/San_Francisco.json';
$obj = file_get_contents($query);
echo $obj;

?>
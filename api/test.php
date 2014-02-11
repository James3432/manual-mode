<?php

$str = "160 Sek";
$patterns = array( '[a-zA-Z]');
$rep = array ('');
echo preg_replace('/[a-zA-Z]/', ' ', $str);

?>
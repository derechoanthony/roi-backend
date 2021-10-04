<?php
$str = "O'Reilly?";
// eval("echo '" . addslashes($str) . "';");


$data = "Gareth Mullin's Bistro Steak &amp; Eggs Brunch";
$data = str_replace('&amp;',' %26', $data );
echo $data;

?>
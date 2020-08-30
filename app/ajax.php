<?php
include "../view/site.php";

if (isset($_GET["value"])) {
	$ajax_value = $_GET["value"];
} else {
	echo "Error receiving ajax data: " . $ajax_value;
	exit();
}

$ajax_array = (array) json_decode($ajax_value);

extract($ajax_array);

switch ($ajax) {
	case "db":
	$class = new $class();
	$class->$function($input);
	break;
}

?>

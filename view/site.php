<?php
/*
* Routing page
*/

date_default_timezone_set('Europe/London');

$localhost = array(
    '127.0.0.1',
    '::1',
	'localhost'
);

if (!in_array($_SERVER['REMOTE_ADDR'], $localhost)) {
    define("STATE", "foreign");
    $root="https://theheadlines.org.uk";
    ini_set("log_errors", 1);
    ini_set("error_log", "/var/www/the_headlines.org.uk/public_html/errors/php-error-2020.log");
} else  {
  	define("STATE", "local");
	$root="http://localhost:8888/headlines";
	//date_default_timezone_set('UTC');
  	error_reporting(-1);
	ini_set('error_reporting', E_ALL);
}

require "../app/model.php";
require "../app/controller/sort_data.php";
//require "../app/controller/test_suite.php";
require "../app/controller/sort_headlines/run_python.php";
require "../app/controller/sort_headlines/analyse_string.php";
require "../app/controller/sort_headlines/sort_headlines.php";
require "../app/controller/sort_headlines/weigh_matches.php";
require "../app/controller/search_db_model.php";

$app = $root . "/app";

$controller = $app . "/controller";

$view = $root . "/view";

$css = $view . "/css";

$js = $view . "/js";

if (STATE !== "local" && !isset($_SESSION['id']) && isset($app_page)) {
	header("Location: ../index");
}

//used for security on local development
if (STATE == "local" && !isset($_SESSION['id']) && !isset($app_page)) {
	//header("Location: ../login");
}

//commented out to save db space
/*
if (!isset($_SESSION['visitor']) && STATE !== "local") {
	$user = new users();
	$user->visitor();
}*/
?>

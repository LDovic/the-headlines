<?php
include "../view/site.php";

$users = new users();

if (!empty($_POST['uid']) && !empty($_POST['pwd'])) {
	$uid = $_POST['uid'];	
	$pwd = $_POST['pwd'];	
	$login = $users->login($uid, $pwd);
} elseif(isset($_POST['logout'])) {
	header("refresh:1;url=../view/app.php");
	echo "Logging out...";
	session_destroy();
} else {
	$users->failure_redirect();
}
	
?>
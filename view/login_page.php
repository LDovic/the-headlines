<?php
$app_page = 1;

require 'header.php';

if (!isset($_SESSION['id'])) {
echo "<form action='../app/login.php' method='POST'>
	<input type='text' name='xxxx' placeholder='Username'>
	<input type='password' name='xxxx' placeholder='Password'>
	<button type='submit'>Log in</button>
</form>";
}
require 'footer.php';
?>

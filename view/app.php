<?php
$app_page = 1;

require 'header.php';
require 'footer.php';

if (isset($_SESSION['id'])) {
	echo "<div id='ajax_response'>
</div>
<button id='get_headlines_button' type='button'>
	Get headlines button
</button>
<button type='button' id='sort_headlines_id' onclick=\"ajax(array = [
							'sort_headlines',
							'subtopics',
							'topics'
							])\">
	Sort headlines button
</button>";
} else {
	echo "<form action='../app/login.php' method='POST'>
						 	<input type='text' name='uid' placeholder='Username'>
							<input type='password' name='pwd' placeholder='Password'>
							<button type='submit'>Log in</button>
					</form>";
}
?>
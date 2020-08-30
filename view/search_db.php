<?php
require 'header.php';
$search_db_user = new search_db_user();
?>
<section>
<?php
$search_db_user->display_search_box();
$search_db_user->display_options('topics');
$search_db_user->display_options('regions');
$search_db_user->display_options('sources');
?>
<input class="search_db_select" id="date_from" type="date" min="2018-11-19">Date From</select>
<input class="search_db_select" id="date_to" type="date" min="2018-11-19">Date To</select>
<input class="search_db_select" id="return_unique" type="checkbox">Return unique</input>
<button id="search_db_confirm_button">
	Search
</button>
<?php
include 'ajax_response.php';
?>
</section>
<?php
include '../view/footer.php';
?>

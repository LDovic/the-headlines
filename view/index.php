<?php
require 'header.php';
include 'about_text.php';
$charts = new charts();
$charts->display_sources();
include 'ajax_response.php';
include 'footer.php';
?>

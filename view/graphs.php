<?php
require 'header.php';
$search_db_user = new search_db_user();
$charts = new charts();
$charts->line_graphs_recursive('month', 'Y-m-d');
include 'footer.php';
?>

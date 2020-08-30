<?php
require 'header.php';

/*
Pie Charts
*/

$charts = new charts();

$sources = array(
	'All sources' => null,
	'BBC' => 'http://www.bbc.co.uk/news',
	'Guardian' => 'https://www.theguardian.com/uk',
	'Daily Mail' => 'http://www.dailymail.co.uk',
	'Independent' => 'https://www.independent.co.uk',
	'The Times' => 'https://www.thetimes.co.uk'
);

$time_periods = array(
	'today',
	'week',
	'month'
);

$charts->display_charts($sources, $time_periods, 'horizontal', 3);

include 'footer.php';
?>
<?php
include "../view/site.php";
$sort_headlines_from_python = new sort_headlines_from_python();

$sort_headlines_from_python->insert(json_encode(array(
  'table' => 'headlines',
  'unset' => array(
  'body',
  'matched_subtopics',
  'matched_topics',
  'matched_regions',
  'keywords',
  'pass'
))));
?>

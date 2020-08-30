<?php
require 'header.php';
include 'about_text.php';
$charts = new charts();

echo "<h3 style='margin-left:20px;'><a target='_blank' href='https://en.wikipedia.org/wiki/Propaganda:_The_Formation_of_Men%27s_Attitudes'>Propaganda: The Formation of Men's Attitudes</a></h3>";
echo "<h4 style='margin-left:20px;'><em><a target='_blank' href='https://en.wikipedia.org/wiki/Jacques_Ellul'>Jacques Ellul</a></em></h4><br>";

function stringposition($haystack, $needles) {
  foreach ($needles as $needle) {
	  if (strpos($haystack, $needle)) {
    		return True;
		}
  	}		
}

//displays quotes from propaganda quotes page with highlighted selections

$propaganda = file_get_contents('prop.txt');
$conts = explode("\n", $propaganda);
foreach ($conts as $k => $val) {
	if (!empty($val)) {
		if ((int)$val) {
			echo "<em>p. ".$val."</em><br>";
		}
		if (strlen($val) > 30) {
          if (stringposition($val, array('technological', 'technology', 'mass')) == True) {
         	 echo "<h3>".$val."</h3><br>";
          } else {
            echo "<p>".$val."</p><br>";
         }
       }
	}
}
?>

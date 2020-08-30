<?php

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

WEIGH MATCHES

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

class weigh_matches {
	public function top_match($headline_data, $which_data, $top_matches) {
		$headline_data[$which_data] = json_encode(array(array_search(max($top_matches), $top_matches)));
		return $headline_data;
	}
	
	public function top_matches($matched_data_set) {
		arsort($matched_data_set);
		$top_matches = array_slice($matched_data_set, 0, 2);
		return $top_matches;
	}
	
	public function weakest_match($top_matches) {
		if (array_sum($top_matches) <= 1) {
			return true;
		}
	}
	
	public function equal_match($top_matches) {
		foreach ($top_matches as $val) {
			if (array_sum($top_matches)/count($top_matches) !== $val) {
				return false;
			}
		}
	}
	
	public function strong_match($top_matches, $pass, $headlines) {
		$max = max($top_matches);
		count($top_matches) > 1 ? $min = min($top_matches) : $min = 0;
		
		$pass4 = (($pass == 4) && ($max > 3));
		$pass4 ? $sum = 0 : $sum = $min/2;

		if ($max <= $min + $sum) {
			return false;
		}	
		
		return true;
	}
	
	public function assign_top_match($headline_data, $which_data, $pass = NULL) {		
		$sort_headlines = new sort_headlines_from_python();
		extract($headline_data);
		$matched_data_set = array_count_values(${'matched_'.$which_data});

		$top_matches = $this->top_matches($matched_data_set);
		
		if ($this->weakest_match($top_matches)) {
			return $headline_data;
		}
		
		if ($this->equal_match($top_matches)) {
			return $headline_data;
		}
				
		if ($this->strong_match($top_matches, $pass, $headlines)) {
			$headline_data = $this->top_match($headline_data, $which_data, $top_matches);
			if ($which_data == 'topics') {
				$headline_data['pass'] = $pass;
			}
		}
		
		return $headline_data;
	}
}
?>

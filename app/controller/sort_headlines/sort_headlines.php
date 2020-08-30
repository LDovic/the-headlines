<?php

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

SORT HEADLINES FROM PYTHON

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

class sort_headlines_from_python {
	public function append_source_data($headline_data) {
		$db = new db();		
		$sources_array = $db->search(array('table' => 'sources'));
		foreach($sources_array as $source_data) {
			extract($source_data);
			if ($sources == $headline_data['source']) {
				if ($headline_data['regions'] == "[]") {
				$headline_data['regions'] = $default_region;
				}
			}
		}
		return $headline_data;
	}
	
	public function politics_demonym_assignment($headline_data) {
		if(array_key_exists('topics', $headline_data) && array_key_exists('regions', $headline_data) && strpos($headline_data['topics'], 'Politics') !== false) {
			$db = new db();
			$region_data = $db->search(array('table' => 'regions', 'regions' => json_decode($headline_data['regions'])[0]));
			$demonym = json_decode($region_data[0]['demonym'])[0];
			$headline_data['topics'] = str_replace('Politics', $demonym . ' Politics', $headline_data['topics']);
		}
		return $headline_data;
	}
	
	public function arrange_matched_values($headline_data) {
		$matches = array('matched_subtopics', 'matched_topics', 'matched_regions', 'keywords');
		foreach($matches as $match) {
			if (!is_string($headline_data[$match])) {
				$headline_data[$match] = json_encode(array_count_values($headline_data[$match]));
			}
		}
		return $headline_data;
	}
	
	public function pass($headline_data, $which_data, $which_keywords, $strings) {
		$find_matches = new find_matches();
				
		extract($headline_data);
		
		foreach($which_data as $val1) {
			foreach($which_keywords as $val2) {
				$headline_data = $find_matches->find_matches_procedural($headline_data, $val1, $val2, $strings);
			}
		}
		
		return $headline_data;
	}
	
	public function no_match($headline_data) {		
		if (!array_key_exists('pass', $headline_data)) {
			return true;
		}
	}
	
	public function sort_topics($headline_data) {
		$find_matches = new find_matches();
		$weigh_matches = new weigh_matches();
		
		extract($headline_data);
		$link_keywords = $find_matches->return_link_keywords($link);

		/*
			Topics and Subtopics sort

		*/

		/*
		First Pass
		*/

		$headline_data = $this->pass($headline_data, array('subtopics', 'topics'), array('lists', 'keywords'), array($headlines, $link_keywords));
		$headline_data = $weigh_matches->assign_top_match($headline_data, 'topics', 1);
		
		if (!$this->no_match($headline_data)) {
			return $headline_data;
		}
		
		/*
		Second Pass
		*/
		
		$headline_data = $this->pass($headline_data, array('subtopics', 'topics'), array('lists', 'keywords'), array($body));
		$headline_data = $weigh_matches->assign_top_match($headline_data, 'topics', 2);
		
		if (!$this->no_match($headline_data)) {
			return $headline_data;
		}
		
		/*
		Third Pass
		*/
		
		$headline_data = $this->pass($headline_data, array('subtopics', 'topics'), array('keywords_ii'), array($headlines, $body));
		$headline_data = $weigh_matches->assign_top_match($headline_data, 'topics', 3);
		
		if (!$this->no_match($headline_data)) {
			return $headline_data;
		}
		
		/*
		Fourth Pass
		*/
		
		$headline_data = $this->pass($headline_data, array('subtopics', 'topics'), array('lists', 'keywords', 'keywords_ii'), array($headlines));
		$headline_data = $weigh_matches->assign_top_match($headline_data, 'topics', 4);
		
		if (!$this->no_match($headline_data)) {
			return $headline_data;
		}
		
		/*
		Fifth Pass
		*/

		$headline_data = $weigh_matches->assign_top_match($headline_data, 'topics', 5);
		
		if (!$this->no_match($headline_data)) {
			return $headline_data;
		}
		
		/*
		Sixth Pass
		*/
		
		$headline_data['pass'] = 6;
		return $headline_data;
	}
	
	public function sort_regions($headline_data) {
		$weigh_matches = new weigh_matches();
		
		extract($headline_data);
		
		$headline_data = $this->pass($headline_data, array('regions'), array('lists', 'keywords', 'demonym', 'cities'), array($headlines, $body));
		
		$headline_data = $weigh_matches->assign_top_match($headline_data, 'regions');
			
		if (!array_key_exists('regions', $headline_data)) {
			$headline_data['regions'] = json_encode(array('United Kingdom'));
		}
		if (array_key_exists('regions', $headline_data) && empty(json_decode($headline_data['regions']))) {
			$headline_data['regions'] = json_encode(array('United Kingdom'));
		}
		
		return $headline_data;
	}
	
	public function start() {
      	$db = new db();
		$python = new python();
		$weigh_matches = new weigh_matches();
 
		$results = $python->return_headlines();

		foreach($results as $source => $result) {
			if (!empty($result)) {
				foreach ($result as $key => $headline_data) {
					$headline_data = $this->sort_topics($headline_data);
					$headline_data = $weigh_matches->assign_top_match($headline_data, 'subtopics');
					$headline_data = $this->sort_regions($headline_data);
					$headline_data = $this->arrange_matched_values($headline_data);
					$headline_data = $this->politics_demonym_assignment($headline_data);					
					$result[$key] = $headline_data;
				}
				$results[$source] = $result;
			}
		}
		return $results;
	}
	
	public function insert($input) {
		if (STATE == 'local') {
			$time_pre = microtime(true);
		}
		
		$db = new db();
		$input = (array) json_decode($input);
      extract($input);

      $results = $this->start();

      foreach($results as $result) {
			if (!empty($result)) {
				foreach($result as $headline) {
					foreach($unset as $val) {
						unset($headline[$val]);
					}
					$headline['table'] = $table;
					$db->insert($headline);
				}
			}
		}
		
		if (STATE == 'local') {
			$time_post = microtime(true);
			$exec_time = round($time_post - $time_pre);
			echo $exec_time . " seconds<br>";
		}
	}
}
?>

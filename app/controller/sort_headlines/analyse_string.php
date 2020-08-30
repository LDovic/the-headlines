<?php

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

Analyse Strings (Headlines or Body)

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

class find_matches {	
	public function subtopics_extra_data($data, $headline_data) {
		if (!empty($data['regions'])) {
			$headline_data = $this->create_array_key($headline_data, 'matched_regions');
			array_push($headline_data['matched_regions'], $data['regions']);
		}
		$headline_data = $this->create_array_key($headline_data, 'matched_topics');
		array_push($headline_data['matched_topics'], $data['topics']);
		return $headline_data;
	}
	
	public function return_link_keywords($string) {
		$db = new db();
		$topics = $db->search(array('table' => 'topics'));
		$path = parse_url($string)['path'];
		$keywords = explode('/', $path);
		$keywords = implode(' ', $keywords);
		return $keywords;
	}
	
	public function analyse_string($keyword, $string, $headline_data, $matched_key, $data, $which_data) {
		set_time_limit(300);
		preg_match_all("^\b$keyword\b^i", $string, $match);
		if (!empty($match[0])) {
			foreach($match[0] as $val) {
				array_push($headline_data['keywords'], $val);
				array_push($headline_data[$matched_key], $data[$which_data]);
				if ($which_data == 'subtopics') {
					$headline_data = $this->subtopics_extra_data($data, $headline_data);
				}
			}
		}
		return $headline_data;
	}
	
	public function return_matches($headline_data, $data_set_keywords, $strings, $which_data, $matched_key) {
		foreach($data_set_keywords as $key => $data) {
			extract($data);
			foreach($keywords as $keyword) {
				foreach($strings as $string) {
					$headline_data = $this->analyse_string($keyword, $string, $headline_data, $matched_key, $data, $which_data);
				}
			}
		}
		return $headline_data;
	}
	
	public function create_array_key($headline_data, $key) {
		!array_key_exists($key, $headline_data) ? $headline_data[$key] = array() : $headline_data;
		return $headline_data;
	}
	
	public function compile_data_set($which_data, $which_keywords) {
		$db = new db();
		$data_set = $db->search(array('table' => $which_data));
		
		$keywords_array = array();
				
		foreach($data_set as $key => $data) {
			$data_set[$key]['keywords'] = json_decode($data[$which_keywords]);
			if (empty($data[$which_keywords])) {
				unset($data_set[$key]);
			}
		}
		
		return $data_set;
	}

	public function find_matches_procedural($headline_data, $which_data, $which_keywords, $strings) {
		$weigh_matches = new weigh_matches();

		extract($headline_data);
				
		$matched_key = 'matched_'.$which_data;
		$headline_data = $this->create_array_key($headline_data, $matched_key);
		$headline_data = $this->create_array_key($headline_data, 'keywords');
		
		$data_set = $this->compile_data_set($which_data, $which_keywords);
				
		return $this->return_matches($headline_data, $data_set, $strings, $which_data, $matched_key);
	}
}
?>

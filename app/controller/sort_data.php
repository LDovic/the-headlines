<?php

/*
*
* These sort data classes are for organising data for the view, after it has been sorted by the sort_headlines/ directory.
*
*/

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

SORT DATA

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

class sort_data {	
	public function count_values($data_array, $which_data) {
		$return_array = array();
		
		foreach($data_array as $data) {
			foreach($data as $datum) {
				array_push($return_array, $datum[$which_data]);
			}
		}
		return array_count_values($return_array);

	}
	
	public function return_top_x($counted_data_array, $number) {
		arsort($counted_data_array);
		return(array_slice($counted_data_array, 0, $number));
	}
	
	public function calculate_percentage($counted_data_array) {
		$return_array = array();
		$sum = array_sum($counted_data_array);
		foreach($counted_data_array as $key => $value) {
			$return_array[$key] = round(($value/$sum)*100) . "%";
		}
		return $return_array;
	}
	
	public function json_decode_values($array, $key_or_value) {
		$return_array = array();
		foreach($array as $key => $value) {
			$key_or_value == 'key' ? $return_array[ucwords(json_decode($key)[0])] = $value : $return_array[$key] = [ucwords(json_decode($value)[0])];
		}
		return $return_array;
	}
	
	public function capitalise_values($value) {
		return ucwords($value);
	}
}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

LISTS

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

class lists {
  public function display_topics($time_period, $source) {
		$db = new db();
		
		$search = array(
			'table' => 'headlines',
			'time_period' => $time_period
		);
		
		if ($source !== 'All sources') {
			$search['sources'] = $source;
		}
		
		$results = $db->search($search);
		
		if (empty($results)) {
			echo 'No data<br>';
			exit();
		}
		
		echo "<br><h4>".ucfirst($time_period).":</h4>";
		$x = 0;
	
		foreach($results as $headline) {
			if (!empty(json_decode($headline['topics']))) {
				$topics[] = json_decode($headline['topics'])[0];
			}
		}
		
		$values = array_count_values($topics);
		$size = array_sum($values);
		$pols = 0;

		foreach($values as $key => $vals) {  
			if (strpos($key, 'Politics')) {
				$pols += $vals;
			} else {
				$output[$key] = round(($vals/$size)*100);
			}
		}
		
		$output['Politics'] = round(($pols/$size)*100);

		if (!function_exists('u_a_sort')) {
          function u_a_sort($a, $b) {
				if ($a == $b) {
					return 0;
				}
				return ($a > $b) ? -1 : 1;
			}
        }

		uasort($output, 'u_a_sort');
		
		foreach($output as $key => $val) {
			echo $key . ": " . $val . "%<br>";
		}
	}
  
	public function find_headlines_by_source($sources, $time_period) {
		$db = new db;
		$return_array = array();
		foreach($sources as $source) {
			array_push($return_array, $db->search(array('table' => 'headlines', 'source' => $source['sources'], 'time_period' => $time_period)));
		}
		return $return_array;
	}
	
	public function display_source_data($bias, $percentage_data, $time_period) {
		echo $bias . " Wing Newspapers Topic Breakdown: <br>";
		echo "This " . $time_period . "<br>";
		foreach($percentage_data as $key => $value) {
			echo $key . ": " . $value . "<br>";
		}
	}
	
	public function run_sources($time_period) {
		$db = new db;
		$sort_data = new sort_data;
		$sources = $db->search(array('table' => 'sources'));	
		$left_sources = array();
		$right_sources = array();
		foreach($sources as $source) {
			$source['bias'] == 'left' ? array_push($left_sources, $source) : array_push($right_sources, $source);
		}
		$left_headlines = $this->find_headlines_by_source($left_sources, $time_period);
		$right_headlines = $this->find_headlines_by_source($right_sources, $time_period);
		$left_topics_count = $sort_data->count_values($left_headlines, 'topics');
		$right_topics_count = $sort_data->count_values($right_headlines, 'topics');
		$left_percentage = $sort_data->calculate_percentage($left_topics_count);
		$right_percentage = $sort_data->calculate_percentage($right_topics_count);
		$this->display_source_data('Left', $left_percentage, $time_period);
		$this->display_source_data('Right', $right_percentage, $time_period);
	}
}


/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

SEARCH DB (USER)

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

class search_db_user {
	public function __construct() {
		$search_by = array('topics', 'subtopics', 'regions', 'super_regions', 'continents', 'sources');	
		$this->search_by = $search_by;
	}
		
	public function display_search_box() {
		echo "<input class='search_db_select' id='text' type='text' placeholder='Search'>";
	}
	
	public function display_options($which_data) {	
		$db = new db();
		$sort_data = new sort_data();
		$options_array = $db->search(array('table' => $which_data));

		$sort_array = array();
		foreach($options_array as $options) {
			$which_data == 'sources' ? $opt = $options[$which_data] : $opt = $sort_data->capitalise_values($options[$which_data]);
			array_push($sort_array, $opt);
		}
		
		echo "<select id='$which_data'";
		if ($which_data == 'subtopics') {
			echo 'multiple';
		}
		
		echo " class='search_db_select'>
		<option disabled selected='selected'>";
		echo $sort_data->capitalise_values($which_data);
		echo "</option>";
		asort($sort_array);
		foreach($sort_array as $option) {
		echo "<option value='$option' name='option'>$option</option>";
		}
		echo "<option>--none--</option>
			</select>";
	}
	
	public function unset_null_values($input) {
		foreach($input as $key => $value) {
			if ($value == '--none--' || !isset($value) || $value == "") {
				unset($input[$key]);
			}
		}
		return $input;
	}
	
	public function suggest_subtopic($subtopic) {
		$input = array('table' => 'headlines', 'subtopics' => $subtopic);
		$unset = array('id', 'topics', 'subtopics', 'regions', 'sorted', 'body', 'matched_topics', 'matched_subtopics', 'matched_regions', 'keywords');
		$search_db_json = htmlentities(json_encode(array($input, $unset)));
		echo "Suggested subtopic: <button style='margin:5px;padding:5px;' onclick=\"ajax(array = [
			'db',
			'display_tables',
			'search',
			encodeURIComponent('^'+'$search_db_json'+'^')
			])\">
			$subtopic</button>";
	}
	
	public function search_bar($input) {
		$db = new db();
		$subtopics = $db->search(array('table' => 'subtopics'));
		extract($input);

		foreach($subtopics as $subtopic_array) {
			$subtopic = $subtopic_array['subtopics'];
			$keywords_array = json_decode($subtopic_array['keywords']);
			$lists_array = json_decode($subtopic_array['lists']);
			$keywords_ii_array = json_decode($subtopic_array['keywords_ii']);
			if ($text == $subtopic || in_array($text, $keywords) || in_array($text, $lists_array) || in_array($text, $keywords_ii_array)) {
				$this->suggest_subtopic($subtopic, $text);
			}
		}
	}
	
	public function unset_search_results($results, $unset_array) {
		$return_array = array();
		foreach($results as $result) {
			foreach($unset_array as $unset) {
				unset($result[$unset]);
			}
		array_push($return_array, $result);
		}
		return $return_array;
	}
	
	public function search($input) {
		$db = new db();
		$sort_data = new sort_data();
		$tables = new tables();
		$input = (array) json_decode($input);

		extract($input);

		if (is_object($input)) {
			$input = (array) $input;
		}

		$input = $this->unset_null_values($input);

		if (count($input) > 2 || count($unset) == 0) {
			if (array_key_exists('date_from', $input) && !array_key_exists('date_to', $input)) {
				$input['date_to'] = date("Y-m-d", strtotime("tomorrow", time()));
			}
			if (array_key_exists('date_to', $input) && !array_key_exists('date_from', $input)) {
				$input['date_from'] = "2018-01-01";
			}
			if (array_key_exists('text', $input)) {
				$this->search_bar($input);
			}
			if (!$results = $db->search($input)) {
				echo "Search results returned empty";
				return;
			}
			
			$results = $this->unset_search_results($results, $unset);
			return $tables->display_table($results, $key);				
		}
		echo "Please select input";
	}
	
}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

DISPLAY TABLE

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

class tables {
	public function display_keywords($value) {
		$sort_data = new sort_data();
		$array = json_decode($value);
		$keywords = "<option>" . implode("</option><option>", $array) . "</option>";
		return $keywords;
	}
	
	public function tablerow($array) {
		$tablerow = "<tr>";
		foreach($array as $key => $value) {
			if ($key == 'link') {
				$value = "<a href='$value' target='_blank'>" . $value . "</a>";
			}
			/*
			if ($key == 'id') {
				$value = "<p id='non_user_table_row_id'>".$value."</p>";
			}
			*/
			if ($key == 'topics') {
				$value = "<p id='$value' class='topics_link'>" . json_decode($value)[0] . "</a>";
			}
			if ($key == 'keywords') {
				$value = "<select>" . $this->display_keywords($value) . "</select>";
			}
			if ($key == 'keywords_ii') {
				$value = "<select>" . $this->display_keywords($value) . "</select>";
			}
			$row = "<td class='$key'>" . $value . "</td>";
			$tablerow .= $row;
		}
		$tablerow .= "</tr>";
		return $tablerow;
	}
	
	public function tablehead($array) {
		$tablehead = "<tr><th>" . implode("</th><th>", $array) . "</th></tr>";
		return $tablehead;
	}
	
	public function get_heads($keys) {
		$return_array = array();
		foreach($keys as $value) {
			$value == 'insertion_datetime' ? $rvalue = 'Date' : $rvalue = ucwords($value);
			array_push($return_array, $rvalue);
		}
		return $return_array;
	}
	
	public function results_sets($results_set) {
		echo "<div style='text-align:center;margin-top:20px;'>";
		for ($x = 1; $x <= $results_set; $x++) {
			$y = $x-1;
			echo "<p id='iteration_$x' class='results_iterations' data-iteration='$y' style='text-decoration:underline;display:inline;margin-right:10px;'>$x</p>";
		}
		echo "</div>";
	}
	
	public function slice_results($results) {		
		foreach($results as $key => $result) {
			$new[] = $result;
			if ($key%50 == 0) {
				$holding[] = $new;
				unset($new);
			}
		}		
		return array_reverse($holding);
	}
	
	public function display_table($results, $key) {
		$sort_data = new sort_data();
		$db = new db();
		//$results_array = $this->slice_results($results);

		//foreach ($results_array[$key] as $k => $result) {		
		foreach ($results as $k => $result) {
			$rows[] = $this->tablerow($result);
			$keys = array_keys($result);
		}
		
		$heads = $this->get_heads($keys);
		$rows = implode("", $rows);
		$headers = $this->tablehead($heads);
		
		echo "<div style='overflow-x: auto;'>";
		echo "<table>" . $headers . $rows . "</table>";
		echo "</div>";
		//$this->results_sets($arr_length);
	}
}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

CHARTS

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

class charts {
  	public function assign_colors($set_topics) {
		$db = new db();
		$topics_arr = $db->search(array('table' => 'topics'));
		
		foreach($set_topics as $key => $val) {
			foreach($topics_arr as $topics) {
				$topic = $topics['topics'];
				$color = $topics['colors'];
				if ((strpos($key, $topic) || $key == $topic)) {
					$set_topics[$key]['color'] = $color; 
				}
			}
		}
		return $set_topics;
	}
	
	public function display_sources() {
		$db = new db();
		$sources_list = $db->search(array('table' => 'sources'));
		array_unshift($sources_list, array('sources' => 'All sources'));
		
		echo "<select class='select_index'>";
		foreach($sources_list as $source) {
			$source = $source['sources'];
			echo "<option>$source</option>";
		}
		echo "</select>";
	}

	public function display_calandar() {
		$db = new db();
		echo "<input class='charts_time_period' id='date_from' type='date' min='2018-11-19'>Date From</select>
<input class='search_db_select' id='date_to' type='date' min='2018-11-19'>Date To</select>";
	}

	public function pie_chart($chart, $chart_container) {
		$db = new db();
		$sort_data = new sort_data();
		extract($chart);
		extract($search);

		$results = $db->search($search);

		$count = $sort_data->count_values(array($results), $which_data);
		
		//unset null values so data is pretty
		foreach($count as $key => $val) {
			if ($key == "[]") {
				unset($count[$key]);
			}
		}
		
		$top = $sort_data->return_top_x($count, $top_num);
		
		$json_decode = $sort_data->json_decode_values($top, 'key');

		foreach($json_decode as $key => $val) {
			$json_decode[$key] = array('value' => $val);
		}
		
		$json_decode = $this->assign_colors($json_decode);

		$json = json_encode($json_decode);
		echo "<div class='$chart_container'>
			<canvas class='pie_charts' data-labels='$json' data-title='$title' width='1000px' height='1000px'></canvas>
		</div>";
	}
  
  	public function display_charts($sources, $time_periods, $type, $num) {
      	$chart_container = 'chart_container_' . $type;
      	$charts = 'charts_container_' . $type;
	
		foreach($sources as $key => $source) {
			$chart = array(
				'search' => array(
					'table' => 'headlines',
				),
				'which_data' => 'topics',
				'top_num' => $num
				);
		  	
			if ($type !== 'vertical') {
				$title = "<h2 style='color:#d3d3d3;'><em>$key</em></h2>";
			}
			
		  	if ($key !== 'All sources') {
				$chart['search']['sources'] = $source;
		    }

			echo "<div class='$charts'>";
				if (isset($title)) {
					echo $title;
				}
	
				foreach($time_periods as $time_period) {
					$chart['title'] = $time_period;
					$chart['search']['time_period'] = $time_period;
					$this->pie_chart($chart, $chart_container);
				}
			echo "</div>";
        }
    }

	public function index_charts($input) {		
		$lists = new lists();
		$input = (array) json_decode($input);
		extract($input);

		$sources = array(
			$source => $url
		);

		if ($time_period == NULL) {
			$time_periods = array(
				'month',
				'week',
				'today'
			);
		}

		echo "<div style='overflow:hidden; text-align:center;'>";
			echo "<div style='float:left;width:60%;min-height:3000px;'>";
				$this->display_charts($sources, $time_periods, 'vertical', 20);
			echo "</div>";
			echo "<div style='overflow:hidden;'>"; 
					$lists->display_topics('month', $source);
					$lists->display_topics('week', $source);
					$lists->display_topics('today', $source);
			echo "</div>";
		echo "</div>";
	}
	  
  	public function bar_charts() {
		$db = new db();
		$sort_data = new sort_data();
		
		$censorship_acts = $db->search(array('table' => 'censorship_acts'));

		foreach ($censorship_acts as $act) {
			$dates[] = $act['date'];
		}
		
		$min = min($dates);
		$max = max($dates);
		
		$acv = array_count_values($dates);
		
		for ($x = $min; $x < $max; $x++) {
			if (!in_array($x, $dates)) {
				$acv[$x] = 0;
			}
		}
		
		ksort($acv);
		
		$json = json_encode($acv);
		
		if (STATE == "local") {
			echo "<div id='bar_chart_container'>
				<canvas id='bar_chart' data-labels='$json'></canvas>
				</div>";
		}
	}

	public function line_graphs_recursive($time_period, $date_format, $recursive = NULL, $topic_array = NULL, $colors = array()) {
		$db = new db();
		$sort_data = new sort_data();
		//get topics list
		$topics = $db->search(
		array(
			'table' => 'topics',
			'select' => array(
				'topics',
				'colors'
			)
		));

		//unset blank topics
		//blank topics shouldnt exist
		foreach($topics as $key => $topic) {
			if ($topics[$key]['topics'] == '[]') {
				unset($topics[$key]);
			}
		}

		//get topics array actual size and set num to highest unsorted topic
		$num = 0;
		if ($recursive !== NULL) {
			$num = $recursive + 1;
		}      
      
		$current_topic = $topics[$num]['topics'];

		//search headlines by topic
		$headlines = $db->search(
		array(
			'table' => 'headlines',
			'select' => array(
				'insertion_datetime'
			),
			'time_period' => $time_period,
			'topics' => $current_topic
		));
	
		//create array of topics to receive headline count
		$topic_array[$current_topic] = array();

      	array_push($colors, $topics[$num]['colors']);

		//count dates
		foreach ($headlines as $headline) {
			//convert insertion_datetime into PHP datetime format
			$datetime = new DateTime($headline['insertion_datetime']);
			//format datetime to function variable date format
			$date = $datetime->format($date_format);
			array_push($topic_array[$current_topic], $date);
		}
      
		$topic_array[$current_topic] = array_count_values($topic_array[$current_topic]);
		if ($num == (count($topics))) {
			$json_topics = json_encode($topic_array);
			$json_colors = json_encode($colors);
			echo "<div id='graph_container'>
				<canvas id='line_graph' data-colors='$json_colors' data-headlines='$json_topics'></canvas>
			</div>";
			return;
		}
		$this->line_graphs_recursive($time_period, $date_format, $num, $topic_array, $colors);
	}
	
	public function line_graphs($time_period, $date_format, $topic) {
		$db = new db();
		$sort_data = new sort_data();
		
		//get topics list
		$topics = $db->search(
		array(
			'table' => 'topics',
			'select' => array(
				'topics'
			)
		));
		
		//search headlines by topic
		$headlines = $db->search(
		array(
			'table' => 'headlines',
			'select' => array(
				'insertion_datetime'
			),
			'time_period' => $time_period,
			'topics' => $topic
		));
				
		//create array of topics to receive headline count
		$topic_array[$topic] = array();

		//count dates
		foreach ($headlines as $headline) {
			//convert insertion_datetime into PHP datetime format
			$datetime = new DateTime($headline['insertion_datetime']);
			//format datetime to function variable date format
			$date = $datetime->format($date_format);
			array_push($topic_array[$topic], $date);
		}
				
		$topic_array[$topic] = array_count_values($topic_array[$topic]);

		$json = json_encode($topic_array);

		echo "<div id='graph_container'>
			<canvas id='line_graph' data-topic_count_by_month='$json'></canvas>
		</div>";
		return;
	}
}

?>

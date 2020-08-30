<?php
/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

All interactions with model.php from the controller go through this class.

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

class db {
	public function time_period($time_period) {
		$midnight1 = date("Y-m-d", strtotime("tomorrow", time()));
		switch ($time_period) {
			case "today":
			$date_from = date("Y-m-d", strtotime("midnight", time()));
			$date_to = $midnight1;
			break;
			case "week":
			$date_from =  date('Y-m-d', strtotime("last monday", time()));			
			$date_to = $midnight1;
			break;
			case "month":
			$date_from =  date('Y-m-01', strtotime("midnight", time()));
			$date_to = date('Y-m-t', strtotime("tomorrow", time()));
			break;
			case "year":
			$date_from = date('Y-m-01', strtotime("first day of January this year", time()));
			$date_to = date('Y-m-31', strtotime("last day of December this year", time()));
			break;
			default:
			$month = (int) $time_period[0];
			$date_from =  date('Y-'.$month.'-01', strtotime("midnight", time()));
			$date_to = date('Y-'.$month.'-t', strtotime("tomorrow", time()));
			break;
		}
		return array('date_from' => $date_from, 'date_to' => $date_to);
	}
	
	public function search($input) {		
		$sql_class = new sql_get_data();
		extract($input);
		
		if (isset($time_period)) {
			$period = $this->time_period($time_period);
			unset($input['time_period']);
			foreach($period as $key => $val) {
				$input[$key] = $val;
			}
		}
		
		$get_data = $sql_class->build_sql("search", $input);
		return $get_data;
	}
	
	public function insert($input) {
		$sql_class = new sql_insert_data();
		$sql_class->build_sql("insert", $input);
	}
	
	public function update($input) {
		$sql_class = new sql_insert_data();
		$sql_class->build_sql("update", $input);
	}
	
	public function update_subtopic_count($subtopic) {
		$sql_class = new sql_insert_data();
		$sql_class->build_sql('update_subtopic_count', $subtopic);
	}
	
	public function delete($input) {
		$sql_class = new sql_insert_data();
		$sql_class->build_sql("delete", $input);
	}
}
?>

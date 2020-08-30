<?php
if(!isset($_SESSION)) {
	session_start();
}

class sql {
	public function __construct() {
		include_once("../view/site.php");
		if (STATE !== "local") {
			$servername = "xxxxxxxx";
			$username = "xxxxxxxx";
			$password = "xxxxxxxx";
			$dbname = "xxxxxxxx";
		} else {
			$servername = "xxxxxxxx";
			$username = "xxxxxxxx";
			$password = "xxxxxxxx";
			$dbname = "xxxxxxxx";
		}
		
		$conn = new mysqli($servername, $username, $password, $dbname);

		if ($conn->connect_error) {
			if (STATE == "local") {
      			echo "<Connection Failed> " . $conn->connect_error;
			}
			$file = fopen(__DIR__ . "/../../errors/php_error.txt", "a");
			$write = date("Y-m-d H:i:s") . ": " . " Connection Failed\n";
		}
		
		$sql = "SET SESSION time_zone = '+00:00'";
		if (!$conn->query($sql)) {
			if (STATE == "local") {
      			echo "<TimeZone Not Set> " . $conn->connect_error;
			}
			$file = fopen(__DIR__ . "/../../errors/php_error.txt", "a");
			$write = date("Y-m-d H:i:s") . ": " . " Timezone not set\n";
		}
		
		$this->conn = $conn;
	}
	
	public function error($message) {
		if (STATE == "local") {
			echo $message;
		}
	}
}

class sql_get_data extends sql {
	public function execute_sql($sql) {
		$fetched_data_array = array();
		
		if (!$result = $this->conn->query($sql)) {
			$this->error("Error: " . $sql . $this->conn->error);
			return;
		}
		
		while($row = $result->fetch_assoc()) {
			array_push($fetched_data_array, $row);
		}

		return $fetched_data_array;
	}
	
	public function build_sql($function, $input = NULL) {
		switch($function) {
			case "search_distinct":
			$sql = "SELECT DISTINCT($input) FROM $table";
			break;
			case "search":
			extract($input);

			$sql = "SELECT ";

			if (!isset($select)) {
				$select = array('*');
			}

			foreach($select as $s) {
				$sql .= $s . ", ";
			}

			$sql = rtrim($sql, ', ');

			$sql .= " FROM $table";

			$sql_array = array();
			foreach ($input as $key => $val) {
				if ($key == 'id') {
					$sql_id = "id = '$id'";
					array_push($sql_array, $sql_id);
				}
				if ($key == 'text') {
					$text = $this->conn->real_escape_string($text);
					$sql_subtopics = "headlines COLLATE UTF8_GENERAL_CI LIKE '%$text%'";
					array_push($sql_array, $sql_subtopics);
				}
				if ($key == 'headlines') {
					$headlines = $this->conn->real_escape_string($headlines);
					$sql_headline = "headlines = '$headlines'";
					array_push($sql_array, $sql_headline);
				}
				if ($key == 'topics') {
					$sql_topics = "topics LIKE '%$topics%'";
					array_push($sql_array, $sql_topics);
				}
				if ($key == 'subtopics') {
					$sql_subtopics = "subtopics LIKE '%$subtopics%'";
					array_push($sql_array, $sql_subtopics);
				}
				if ($key == 'regions') {
					$sql_subtopics = "regions LIKE '%$regions%'";
					array_push($sql_array, $sql_subtopics);
				}
				if ($key == 'super_regions') {
					$sql_subtopics = "super_regions LIKE '%$super_regions%'";
					array_push($sql_array, $sql_subtopics);
				}
				if ($key == 'continents') {
					$sql_subtopics = "continents LIKE '%$continents%'";
					array_push($sql_array, $sql_subtopics);
				}
				if ($key == 'sources') {
					$sql_source = "source = '$sources'";
					array_push($sql_array, $sql_source);
				}
				if ($key == 'date_from') {
					$sql_timeframe = "insertion_datetime>='$date_from' AND insertion_datetime<'$date_to'";
					array_push($sql_array, $sql_timeframe);
				}
				if ($key == 'events') {
					$sql_events = "events = '$events'";
					array_push($sql_array, $sql_events);
				}
              	if ($key == 'word') {
                	$sql_word = "word = '$word'";
					array_push($sql_array, $sql_word);
                }
				if ($key == 'checked') {
					$sql_checked = "checked = '$checked'";
					array_push($sql_array, $sql_checked);
				}
			}
			
			$count = sizeof($sql_array);
			if ($count > 0) {
				$sql .= " WHERE " . $sql_array[0];
			//	if ($count > 0) {
				for ($x = 1; $x < $count; $x++) {
					$sql .= " AND " . $sql_array[$x];
				}
			//	}
			}
			
			if (isset($group_by)) {
				$sql .= " GROUP BY $group_by";
			}
			
			if (isset($order_by)) {
				$sql .= " ORDER BY insertion_datetime DESC";
			}

			break;
		}
		return $this->execute_sql($sql);
	}
}

class sql_insert_data extends sql {
	public function insert_sql($sql) {
		if (!$this->conn->query($sql) === TRUE) {
			$this->error("<br>Error: " . $sql . "<br>" . $this->conn->error . "<br>");
		}
	}
	
	public function build_sql($function, $input) {
		extract($input);
		switch($function) {
			case "insert":
			$sql = "INSERT INTO $table (";
			foreach($input as $key => $value) {
				if ($key !== 'table') {
					$sql .= $key . ", ";
				}
			}
			$sql = rtrim($sql, ", ");
			$sql .= ") VALUES (";
			foreach($input as $key => $value) {
				if ($key !== 'table') {
					$value = $this->conn->real_escape_string($value);
					$sql .= "'" . $value . "'" . ", ";
				}
			}
			$sql = rtrim($sql, ", ");
			$sql .= ")";
			break;
			case "update":
			$sql = "UPDATE $table SET ";
			$set = array_diff_key($input, ['table' => 'x', 'where' => 'x']);
			foreach($set as $key => $value) {
				$sql .= "$key = '$value' ";
			}
			foreach($input as $key => $value) {
				$value = $this->conn->real_escape_string($value);
				if ($key == 'where') {
					$sql .= "WHERE id = '$value'";
				}
			}
			break;
		}
		$this->insert_sql($sql);
	}
}

class users extends sql {
	/*
	* IP logging no longer in use
	*/
	public function get_ip($table) {
		$ip = $_SERVER["REMOTE_ADDR"] ?? '127.0.0.1';
		$page = json_encode(array($_SERVER["PHP_SELF"] => 1));
		//https://stackoverflow.com/questions/409999/getting-the-location-from-an-ip-address
		$location_details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
		$location = $location_details->city;
		//$region = $location_details->region;
		//$company = $location_details->company['name'];
		$return_array = array('table' => $table, 'ip' => $ip, 'location' => $location, 'page' => $page);
		return $return_array;
	}
	
	public function log_ip($table, $login) {
		$insert = new sql_insert_data();
		$ip_arr = $this->get_ip($table);
		extract($ip_arr);
		$sql = "SELECT * FROM $table WHERE ip = '$ip'";
		$result = $this->conn->query($sql);
		if (!$row = $result->fetch_assoc()) {
			$sql = $insert->build_sql('insert', $ip_arr);
			//$sql = "INSERT INTO `$table`(`ip`, `location`, `$login`) VALUES ('$ip', '$location', '$login' + 1)";
			$result = $this->conn->query($sql);			
		} else {
			$vpages = (array)json_decode($row['page']);
			$page = key((array)json_decode($ip_arr['page']));
			foreach($vpages as $key => $val) {
				if ($page == $key) {
					$vpages[$key] = $val += 1;
				}
			}
			$vpages = json_encode($vpages);
			$sql = "UPDATE $table SET `page` = '$vpages', `$login` = $login + 1 WHERE ip = '$ip'";
			$result = $this->conn->query($sql);
		}
	}
	
	public function failure_redirect() {
		$sql = "UPDATE users SET incorrect_login_count = incorrect_login_count + 1";
		$result = $this->conn->query($sql);
		$this->log_ip('attempted_logins', 'incorrect_login_count');
		
		header("refresh:1;url=../view/app.php");
		echo "Password or username incorrect";
	}
	
	public function login($uid, $pwd) {
		$sql = "SELECT * FROM users WHERE uid='$uid'";
		$result = $this->conn->query($sql);
			
		if (!$result) {
			echo "Error: " . $sql . $this->conn->error;
		} else {
			if (!$row = $result->fetch_assoc()) {
				$this->failure_redirect();
			} else {
				$hashed = $row['pwd'];
				$hash = password_verify($pwd, $hashed);
				if ($hash == 0) {
					$this->failure_redirect();
				} else {
					$sql = "UPDATE users SET login_count = login_count + 1";
					$result = $this->conn->query($sql);
					$this->log_ip('attempted_logins', 'login_count');
					
					$_SESSION['id'] = $row['id'];
					header("Location: ../view/app.php");	
				}
			}
		}
	}
	
	public function visitor() {
		$this->log_ip('visitors', 'visit_count');
		$_SESSION['visitor'] = $_SERVER["REMOTE_ADDR"];		
	}
}

?>

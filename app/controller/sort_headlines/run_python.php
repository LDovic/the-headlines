<?php
/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

RUN PYTHON SCRIPT

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

class python {
	public function test_crontab() {
		$file = fopen(__DIR__ . "/../../errors/crontabtest.txt", "w");
		fwrite($file, "works");		
	}
	
	public function python_failure($python_result, $source) {
		include_once("../view/site.php");
		
		$file = fopen(__DIR__ . "/../../errors/php_error.txt", "a");
		$write = date("Y-m-d H:i:s") . ": " . strtoupper($source) . " python module failed\n";
		fwrite($file, $write);
	}
	
	public function execute_python() {
		include_once("../view/site.php");
		if (STATE !== "local") {
			return json_decode(shell_exec("xxxxxxxx xxxxxxxx"), true);
		} else {
			return json_decode(shell_exec("xxxxxxxx xxxxxxxx"));
		}
	}
	
	public function return_headlines() {
		$sources = array('bbc','guardian','telegraph','mail','independent','times');
		$result_array = [];
		foreach($sources as $source) {
			$file = fopen("sources.txt", "w");
			fwrite($file, $source);
			if (!$python_result = $this->execute_python()) {
				$this->python_failure($python_result, $source);
			}
			$results_array[$source] = json_decode(json_encode($python_result), true);
		}
		return $results_array;
	}
}
?>

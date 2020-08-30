function ajax(value, callback) {
	//used for debugging
	/*
  	var len = value.length;

	for (var x = 0; x < len; x++) {		
		console.log(value[x]);
	}
	*/

	var loader = document.getElementsByClassName("loader")[0];
	loader.style.display = "block";
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("ajax_response").innerHTML = this.responseText;
			loader.style.display = "none";
			if (typeof callback !== 'undefined') {
				callback();	
			}
		}
	};
	
	xhttp.open("GET", "https://www.theheadlines.org.uk/app/ajax.php?value=" + value, true);
	xhttp.send();
}

var sPath = window.location.pathname;
var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

AJAX Dict

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

var ajax_dict_func = function adf(ajax_, class_, function_, input_, callback) {
	ajax_dict = {
		'ajax': ajax_,
		'class': class_,
		'function': function_,
		'input': input_
	};
	arr = encodeURIComponent(JSON.stringify(ajax_dict));
	ajax(arr, callback);
}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

App

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

var get_headlines_button = document.getElementById('get_headlines_button');
if (sPage == 'app.php' && get_headlines_button) {	
	get_headlines_button.addEventListener("click", function() {
		var input = {};
		var unset = ['body', 'matched_subtopics', 'matched_topics', 'matched_regions', 'keywords', 'pass'];
		input['table'] = 'headlines';
		input['unset'] = unset;
		input_json = JSON.stringify(input);
		ajax_dict_func('db', 'sort_headlines_from_python','insert', input_json);
	});
}

var test_headlines_button = document.getElementById('test_headlines_id');
if (test_headlines_button) {
	test_headlines_button.addEventListener("click", function() {
		var input = {};
		var unset = ['body'];
		input['table'] = 'test_headlines';
		input['unset'] = unset;
		input_json = JSON.stringify(input);
		ajax_dict_func('db', 'test_suite', 'initialize', input_json);
	});
}

var test_results_button = document.getElementById('test_results_id');
if (test_results_button) {
	test_results_button.addEventListener("click", function() {
		var input = {};
		input['table'] = 'test_results';
		input['positive'] = 'result';
		input_json = JSON.stringify(input);
		ajax_dict_func('db', 'test_suite', 'test_results', input_json);
	});
}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

Click if you agree

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

var box = document.getElementById("pop-up");

if (box) {
	var agree = [document.getElementById("agree_button"), document.getElementById("disagree_button")];
	agree.forEach(function(elem) {
		elem.addEventListener("click", function() {
		box.style = "display: none";
		});
	});
}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

DISPLAY TABLES (NON-USER)

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

var topics_link = function() {
	var topics = document.getElementsByClassName('topics_link');
	for (let n = 0; n < topics.length; n++) {
		topic = topics[n];
		if (topic) {
			topic.addEventListener("click", function() {
				topic = topics[n].id;
				var display_table = {};
				unset_array = [];
				display_table.table = 'subtopics';
				display_table.topics = topic;
				ajax_array = [display_table,unset_array];
				display_table_json = encodeURIComponent('^'+JSON.stringify(ajax_array)+'^');
				ajax(['db', 'display_tables', 'search', display_table_json]);
			});
		}
	}
}

var table_array = ["subtopics", "topics", "regions"];

table_array_length = table_array.length;

for (let n = 0; n < table_array_length; n++) {
	var table = document.getElementById(table_array[n] + "_table");
	if (table) {
		table.addEventListener("click", function() {
			var display_table = {};
			display_table.table = table_array[n];
			unset_array = [];
			ajax_array = [display_table,unset_array];
			display_table_json = '^'+JSON.stringify(ajax_array)+'^';
			ajax(['db', 'display_tables', 'search', display_table_json]);
			window.setTimeout(topics_link, 50);
		});
	}
}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

EVENTS

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

function add_to_event(headlines) {
	add_event_dict = {};
	var events = document.getElementsByClassName('search_db_select')[1].value;
	add_event_dict.event = events;
	add_event_dict.headline = headlines;
	add_event_dict_json = encodeURIComponent('^'+JSON.stringify(add_event_dict)+'^');
	ajax(['db', 'sort_topics', 'add_to_event', add_event_dict_json]);
}

function get_select(select) {
	var result = [];
	var options = select && select.options;
	
	for (var i = 0; i < options.length; i++) {
		var opt = options[i];
		if (opt.selected) {
			result.push(opt.value || opt.text);
		}
	}
	return result;
}

function add_new_event() {
	var new_event_dict = {}
	new_event_dict.table = 'events';
	new_event_dict.events = document.getElementById("new_event").value;
	new_event_dict.topics = JSON.stringify([document.getElementsByClassName('search_db_select')[3].value]);
	new_event_dict.regions = JSON.stringify([document.getElementsByClassName('search_db_select')[4].value]);
	
	var event_subtopics = document.getElementsByClassName('search_db_select')[2];
	new_event_dict.subtopics = JSON.stringify(get_select(event_subtopics));
	
	new_event_dict_json = encodeURIComponent('^'+JSON.stringify(new_event_dict)+'^');
	
	ajax(['db', 'db', 'insert', new_event_dict_json]);
}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

BUTTON CLICK - SORT HEADLINES
1. DELETE
2. ADD KEYWORD
3. UPDATE/REMOVE DATA

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

function add_new_keyword() {
	add_keyword = function() {
		console.log(document.getElementById("table_id"));
		var keyword = prompt("Enter Additional Keyword: ");
		if (keyword != null) {
			if (confirm("Add " + keyword + " to " + button + "?")) {
				var add_new_keyword_dict = {};
				add_new_keyword_dict.table = 'subtopics';
				add_new_keyword_dict.where = button;
				add_new_keyword_dict.keywords = keyword;
				var add_new_keyword_dict_json = JSON.stringify(add_new_keyword_dict);
				ajax(['db', 'sort_topics', 'add_new_keyword', encodeURIComponent('^'+add_new_keyword_dict_json+'^')]);
			} else {
				add_keyword();
			}
		}
	}
	add_keyword();
}

function button_click(data) {
	_function = data[1];
	button = data[4];
	if (event.shiftKey && _function == 'subtopics') {
		if (confirm("Are you sure you want to delete the " + button + " subtopic?")) {
			var delete_dict = {};
			delete_dict.subtopic = button;
			var delete_dict_json = JSON.stringify(delete_dict);
			ajax(['db', 'db', 'delete', delete_dict_json]);	
		}
	} else if (event.altKey) {
		add_new_keyword();
	} else {
		new_data = [];
		for (x = 0; x < data.length; x++) {
			new_data.push(encodeURIComponent('^'+data[x]+'^'));
		}
		ajax(new_data);
	}
}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

ADD SUBTOPIC

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

function get_new_subtopic() {
	var new_subtopic_dict = {};

	var new_subtopic_search = document.getElementById('new_subtopic_search');
	var new_keyword_search = document.getElementById('new_keyword_search');
	var new_topic_search = document.getElementsByClassName('search_db_select')[0];

	new_subtopic_dict.table = "subtopics";
	new_subtopic_dict.subtopics = new_subtopic_search.value;
	new_subtopic_dict.keywords = JSON.stringify([new_keyword_search.value]);
	new_subtopic_dict.topics = new_topic_search.value;
		
	var new_subtopic_dict_json = JSON.stringify(new_subtopic_dict);
	ajax(['db', 'db', 'insert', encodeURIComponent('^'+new_subtopic_dict_json+'^')]);
}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

SEARCH DATABASE (USER)

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

var search_db_select = document.getElementsByClassName('search_db_select');
var search_db_confirm_button = document.getElementById('search_db_confirm_button');

var search_db_dict = {};
search_db_dict.table = 'headlines';
search_db_dict.order_by = 1;
//search_db_dict.checked = 1;

for (let x = 0; x < search_db_select.length; x++) {
	modulo = 0;
	search_db_select[x].addEventListener("change", function() {
		var id = search_db_select[x].id;
		var value = this.value;
		search_db_dict[id] = value;
		/*
		if (id == 'date_to' || id == 'date_from') {
			d = Number(new Date(value));
			t = Number(new Date('2018-11-19'));
			if (d < t) {
				alert('Please select date after 19th November 2018');
			}
		}
		*/
		if (id == 'return_unique') {
			modulo += 1;
			if (modulo%2 == 1) {
				search_db_dict['group_by'] = 'headlines';
			}
		}
	});
}

function create_search_db_json(key) {
	ajax_dict = {};
	unset_array = ['id', 'subtopics', 'topics', 'regions', 'super_regions', 'continents', 'checked', 'source', 'event', 'body', 'sorted', 'matched_subtopics', 'matched_topics', 'matched_regions', 'keywords'];
	ajax_dict['input'] = search_db_dict;
	ajax_dict['unset'] = unset_array;
	ajax_dict['key'] = key;
	var search_db_dict_json = JSON.stringify(ajax_dict);	
	return search_db_dict_json;
}

/*
var results_iterations = function results_iterations() {
	var results_its = document.getElementsByClassName('results_iterations');
	if (results_its) {
		for (let n = 0; n < results_its.length; n++) {
			results_its[n].addEventListener("click", function() {
				it = results_its[n];
				key = it.getAttribute('data-iteration');
				search_db_dict_json = create_search_db_json(key);
				ajax_dict_func('db', 'search_db_user', 'search', search_db_dict_json, results_its);
				window.setTimeout(results_iterations, 2000);
			});
		}
	}
}
*/

if (search_db_confirm_button) {
	search_db_confirm_button.addEventListener("click", function() {
		search_db_dict_json = create_search_db_json(0);
		ajax_dict_func('db', 'search_db_user', 'search', search_db_dict_json);
		//window.setTimeout(results_iterations, 2000);
	});

}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

PIE CHARTS

CHARTS.PHP

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

var load_pie_charts = function() {
	var pie_charts = document.getElementsByClassName("pie_charts");
	if (pie_charts) {
		for (var x = 0; x < pie_charts.length; x++) {
			var values = [];
			var colors = [];

			var id = pie_charts[x];
			var title = pie_charts[x].dataset.title;

			var data = pie_charts[x].dataset.labels;
			var json = JSON.parse(data);
			var labels = Object.keys(json);
			var val = Object.values(json);
			
			for (var z = 0; z < val.length; z++) {
				values.push(val[z].value);
				colors.push('rgba(' + val[z].color + ')');
			}
			
			let chart = new Chart(id, {
				type: 'doughnut',
				data: {
					labels: labels,
					datasets: [{
						data: values,
					    backgroundColor:
						colors
						   }]
					   },
				options: {
					cutoutPercentage: 10,
					title: {
						display: true,
						text: title.toUpperCase()
					}
				}
			});
		}
	};
}

var pie_charts_container = document.getElementsByClassName("charts_container_horizontal");

if (pie_charts_container) {
	for (var x = 0; x < pie_charts_container.length; x++) {
		if (x%2 == 0) {
			pie_charts_container[x].style.background = '#e6e6e6';
		}
	}
}

if (sPage == 'charts') {
	window.setTimeout(load_pie_charts, 500);
}


/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

PIE CHARTS AND LISTS

INDEX.PHP

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

var index_charts_get_data = function index_charts_get_data(source) {
	var input_dict = {};
	input_dict['source'] = source;
	input_dict['time_period'] = null;
	input_dict['url'] = source;
	var input = JSON.stringify(input_dict);
	ajax_dict_func('db', 'charts', 'index_charts', input, load_pie_charts);
	//window.setTimeout(select_charts, 200);
}

var select_charts_index = document.getElementsByClassName("select_index");
if (select_charts_index) {
	for (let x = 0; x < select_charts_index.length; x++) {
		select_charts_index[x].addEventListener("change", function() {
			index_charts_get_data(this.value);
		});
	}
}

if (sPage == 'index' || sPage == '') {
	window.addEventListener("load", index_charts_get_data('All sources'));
}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

Bar Chart

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

var bar_chart_container = document.getElementById("bar_chart_container");

if (bar_chart_container) {
	var chart = document.getElementById("bar_chart");
	var id = chart.id;
	var data = chart.dataset.labels
	var json = JSON.parse(data);
	var keys = Object.keys(json);
	var values = Object.values(json);
	var bar_chart = new Chart(id, {
		type: 'bar',
		data: {
			labels: keys,
			datasets: [{
				label: "Acts of Censorship In The UK Since 1795",
				data: values,
				backgroundColor: [
					//'rgba(68, 79, 100, 1)'
				]
			}]			
		},
		options: {
			title: {
				//display: true,
				//text: "Acts of Censorship In The UK Since 1795"
			}
		}
	});
}

/*

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

LINE GRAPH

MOST POPULAR TOPICS - MONTH

$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$<*>$

*/

var line_graph = document.getElementById("line_graph");

if (line_graph) {
	window.addEventListener("load", function() {
		var data = line_graph.dataset.headlines;
		var colors = JSON.parse(line_graph.dataset.colors);
	
		var id = line_graph.id;
		var json = JSON.parse(data);	
		var labels = Object.keys(json);
		var datasets = Object.values(json);
		var months = Object.keys(datasets[0]);
	
		var values = new Array();
		for (var y = 0; y < datasets.length; y++) {
			values.push(Object.values(datasets[y]));
		}
		
		for (var z = 0; z < colors.length; z++) {
			colors[z] = 'rgba(' + colors[z] + ')';
		}
      
		let chart = new Chart(id, {
			type: 'line',
			data: {
					labels: months,
					datasets: [
						{
							label: labels[0],
							data: values[0],
							borderColor: colors[0],
							backgroundColor: colors[0],
							lineTension: 0,
							fill: false
						},
						{
							label: labels[1],
							data: values[1],
							borderColor: colors[1],
							backgroundColor: colors[1],
							lineTension: 0,
							fill: false
						},
						{
						label: labels[2],
							data: values[2],
							borderColor: colors[2],
							backgroundColor: colors[2],
							lineTension: 0,
							fill: false
						},
						{
							label: labels[3],
							data: values[3],
							borderColor: colors[3],
							backgroundColor: colors[3],
							lineTension: 0,
							fill: false
						},
						{
							label: labels[4],
							data: values[4],
							borderColor: colors[4],
							backgroundColor: colors[4],
							lineTension: 0,
							fill: false
						},
						{
							label: labels[5],
							data: values[5],
							borderColor: colors[5],
							backgroundColor: colors[5],
							lineTension: 0,
							fill: false
						},
						{
							label: labels[6],
							data: values[6],
							borderColor: colors[6],
							backgroundColor: colors[6],
							lineTension: 0,
							fill: false
						},
						{
							label: labels[7],
							data: values[7],
							borderColor: colors[7],
							backgroundColor: colors[7],
							lineTension: 0,
							fill: false
						},
						{
							label: labels[8],
							data: values[8],
							borderColor: colors[8],
							backgroundColor: colors[8],
							lineTension: 0,
							fill: false
						},
						{
							label: labels[9],
							data: values[9],
							borderColor: colors[9],
							backgroundColor: colors[9],
							lineTension: 0,
							fill: false
						},
						{
					label: labels[10],
					data: values[10],
					borderColor: colors[10],
					backgroundColor: colors[10],
					lineTension: 0,
					fill: false
					},
					{
					label: labels[11],
					data: values[11],
					borderColor: colors[11],
					backgroundColor: colors[11],
					lineTension: 0,
					fill: false
					},
					{
					label: labels[12],
					data: values[12],
					borderColor: colors[12],
					backgroundColor: colors[12],
					lineTension: 0,
					fill: false
					},
					{
					label: labels[13],
					data: values[13],
					borderColor: colors[13],
					backgroundColor: colors[13],
					lineTension: 0,
					fill: false
					},
					{
					label: labels[14],
					data: values[14],
					borderColor: colors[14],
					backgroundColor: colors[14],
					lineTension: 0,
					fill: false
					},
					{
					label: labels[15],
					data: values[15],
					borderColor: colors[15],
					backgroundColor: colors[15],
					lineTension: 0,
					fill: false
					},
					{
					label: labels[16],
					data: values[16],
					borderColor: colors[16],
					backgroundColor: colors[16],
					lineTension: 0,
					fill: false
					},
					{
					label: labels[17],
					data: values[17],
					borderColor: colors[17],
					backgroundColor: colors[17],
					lineTension: 0,
					fill: false
					},
					{
					label: labels[18],
					data: values[18],
					borderColor: colors[18],
					backgroundColor: colors[18],
					lineTension: 0,
					fill: false
					},
					{
					label: labels[19],
					data: values[19],
					borderColor: colors[19],
					backgroundColor: colors[19],
					lineTension: 0,
					fill: false
					},
                    {
					label: labels[20],
					data: values[20],
					borderColor: colors[20],
					backgroundColor: colors[20],
					lineTension: 0,
					fill: false
					}
					]
				},
				options: {
					animation: {
						duration: 0
					},
					hover: {
						animationDuration: 0
					},
					responsiveAnimationDuration: 0,
					cutoutPercentage: 10,
					title: {
						display: true,
						text: "Topic Trends This Month"
					}
				}
			});
	});
}

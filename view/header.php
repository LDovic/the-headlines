<?php
session_start();
require "site.php";
?>
<!DOCTYPE html>
<html lang="en-US">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<head>
	<link rel="stylesheet" type="text/css" href="<?=$css . "/style.css"?>">
<title>
		Search the UK Headlines | UK Headlines Database
</title>
<meta name="description" content="UK headlines database | Researchers or anyone interested can use this tool to view online reporting trends in the UK | Keeping an eye on the public eye">
</head>
<body>
	<div id="wrapper">
<!--		<div id="pop-up">
		<p>
			Click here if you agree:
		</p>
		<button id="agree_button">Agree</button>
		<button id="disagree_button">Disagree</button>
		</div> -->
		<div id="nav-wrapper">
			<ul id="nav">
	        		<li><a href="index">Home</a></li>
        	        <li><a href="about">About</a></li>
                    <!--<li><a href="charts">Charts</a></li>-->
                    <li><a href="graphs">Graphs</a></li>
        			<li><a href="search">Search Headlines</a></li>
					<?php
					if (isset($_SESSION['id'])) {
						echo  "<li><a href='happ'>App</a></li>
		        				<li><a href='tables'>Tables</a></li>
								<li><form action='app/login.php' method='POST'>
								<input type='hidden' name='logout' value='1'>
								<button type='submit'>Log Out</button>
								</form></li>";
					}
					?>
					<li>
						<a href="mailto:info@theheadlines.org.uk">
							info@theheadlines.org.uk
						</a>
					</li>
			</ul>
	</div>
	<div class="loader"></div>
	

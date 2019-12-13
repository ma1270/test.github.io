<?php
	$conn = new mysqli('localhost', 'root','', 'id11874361_whatfityou');
	$conn->set_charset("utf8");
	if ($conn->connect_error) {
	    die("Connection failed with DataBase");
	}
?>
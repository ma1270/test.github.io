<?php
	session_start();
	if(!(isset($_SESSION['user']['id']))){
		header('Location:index.php');
		exit();
	}
	$_SESSION['bookmarks']['show'] = "hidden";
	$_SESSION['search']['show'] = 'hidden';
	$_SESSION['all'] = '';
	$_SESSION['bookmarks']['bttn'] = 'Show Bookmarks';
	header('Location:profile.php');
	exit();
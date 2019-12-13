<?php
	session_start();
	if(isset($_SESSION['user']['id'])){
		if($_SESSION['bookmarks']['bttn'] == 'Show Bookmarks'){
			$_SESSION['bookmarks']['show'] = "";
			$_SESSION['search']['show'] = 'hidden';
			$_SESSION['all'] = 'hidden';
			$_SESSION['bookmarks']['bttn'] = 'Hidde Bookmarks';
		}else{
			$_SESSION['bookmarks']['show'] = "hidden";
			$_SESSION['search']['show'] = 'hidden';
			$_SESSION['all'] = '';
			$_SESSION['bookmarks']['bttn'] = 'Show Bookmarks';
		}
	}
	
	header('Location:profile.php');
	exit();
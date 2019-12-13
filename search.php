<?php
	session_start();
	if(!(isset($_SESSION['user']['id']))){
		header('Location:index.php');
		exit();
	}
	if($_COOKIE['searchValue'] != ''){
		include 'connect.php';
		$sql = "SELECT `id` FROM `charities` WHERE `name` LIKE '%" . $_COOKIE['searchValue'] ."%' OR `name` LIKE '" . $_COOKIE['searchValue'] ."%' OR `name` LIKE '%" . $_COOKIE['searchValue'] ."';";
			$result = $conn->query($sql);
			$_SESSION['bookmarks']['show'] = "hidden";
			$_SESSION['search']['show'] = '';
			$_SESSION['all'] = 'hidden';
			if ($result->num_rows > 0) {
			    while ($row = $result->fetch_assoc()) {
					$_SESSION['search']['id'][$row['id']] = $row['id'];
				}
			} else {
				$error = TRUE;
			}
		$conn->close();
	}
	header('Location:profile.php');
	exit();
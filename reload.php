<?php
	session_start();
	if(isset($_SESSION['flag'])){
		$_SESSION['flag'] = 1;
		header('Location:question.php');
		exit();
	}else{
		header('Location:profile.php');
		exit();
	}
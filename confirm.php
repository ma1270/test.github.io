<?php 
	session_start();
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if(isset($_POST['id'])){
			include 'connect.php';
			$sql = "UPDATE `users` SET `verification` = 1 WHERE `id` = ". $_POST['id'] .";";
			if (!($conn->query($sql) === TRUE)) {
				echo "<script> alert('No Connection with database now try at another time'); </script>";
			}else{
				$_SESSION['user']['id'] = $_POST['id'];
				header('Location:profile.php');
				exit();
			}
		}
	}

?>
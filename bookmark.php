<?php
	session_start();
	if(!(isset($_SESSION['user']['id']))){
		header('Location:index.php');
		exit();
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		include 'connect.php';
		if(isset($_SESSION['bookmarks']['id'][$_POST['id']])){
			$sql = "DELETE FROM `bookmarks` WHERE `userId` = ".$_SESSION['user']['id']." AND `charityId` = ". $_POST['id'] .";";
			if ($conn->query($sql) === TRUE) {
			    unset($_SESSION['bookmarks']['id'][$_POST['id']]);
			}
		}else{
			$sql = "INSERT INTO `bookmarks`(`userId`, `charityId`) VALUES (".$_SESSION['user']['id'].",". $_POST['id'] .");";
			if ($conn->query($sql) === TRUE) {
				$_SESSION['bookmarks']['id'][$_POST['id']] = $_POST['id'];
			}
		}
		$conn->close();
	}
?>
<script type="text/javascript">
	window.close();
</script>
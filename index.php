<?php 
	session_start();
	if(isset($_SESSION['user']['id'])){
		header('Location:profile.php');
		exit();
	}
	$page = 'login'	;
	$login_error = NULL;
	$register_error = NULL;
	$register_succeed = NULL;
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		include 'connect.php';
		$to = $_POST['email'];
		$subject = 'Email verification';
	    $headers = 'From: ma1270056@gmail.com' . "\r\n";
	    $headers .= "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		if(isset($_POST['phone'])){
			/***************************************register************************************************/
			$page = 'register';
			$sql = "SELECT `id` FROM `users` WHERE `email` = '". $_POST['email'] . "';";
			$result = $conn->query($sql);
			if ($result->num_rows > 0){
				$register_error = 'There is user with that email you can`t register with it';
			}else{
				if($_POST['password'] != $_POST['confirm']){
					$register_error = 'password and it`s confirm must be equal';
				}else{
					$sql = "INSERT INTO `users`(`name`, `email`, `phone`, `password`, `location`) VALUES ('".
					$_POST['name'] ."','". $_POST['email'] ."','". $_POST['phone'] ."','". $_POST['password'] 
					."',". $_POST['location'] .");";
					echo $_POST['location'];
					if (!($conn->query($sql) === TRUE)) {
					   $register_error = "No Connection with database now try at another time";
					}else{
						$register_succeed = 'Succeeded cofirm your registration from email';
						$message = '<form action = "http://whatfityou.000webhostapp.com/confirm.php" method = "POST">
			    					<input type="hidden" name="id" value="'. $conn->insert_id .'">
			    					<input type="submit" value="Confirm account"> </form>';	
			    		$message = wordwrap($message,70);
			    		mail($to,$subject,$message,$headers);
					}
				}
			}
		}else{
			/***************************************Login************************************************/
			$page = 'login';
			$sql = "SELECT `id` , `verification` , `password` FROM `users` WHERE `email` = '". $_POST['email'] . "';";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
			    $row = $result->fetch_assoc();
			    if($row['verification'] == 1){
			    	if($row['password'] != $_POST['password']){
			    		$login_error = "Wrong Password, check Your Email we just sent it";
			    		$subject = 'Remember Password';
			    		$message = 'Your password is : ' . $row['password'];
			    		mail($to,$subject,$message,$headers);
			    	}else{
			    		$_SESSION['user']['id'] = $row['id'];
			    		header('Location:profile.php');
			    		exit();
			    	}
			    }else{
			    	$message = '<form action = "http://whatfityou.000webhostapp.com/confirm.php" method = "POST">
			    				<input type="hidden" name="id" value="'. $row['id'] .'">
			    				<input type="submit" value="Confirm account"> </form>';
			    	$message = wordwrap($message,70);
			    	mail($to,$subject,$message,$headers);
			    	$login_error = "You must confirm your Email first";
			    }
			    
			} else {
			    $login_error = "Wrong Email";
			}
		}
		$conn->close();
	}
	include 'header.php';
?>

<section class="index">
	<img src="images/logo.jpg">
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>" autocomplete="on">
			<label><?php echo $login_error; ?></label>
			<input type="email" name="email" placeholder="Enter Your Email" required autocomplete="on">
			<input type="password" name="password" placeholder="Enter Your Password" required autocomplete="new-password">
			<input type="submit" value="Login">
			<a id="goToRegister">Create new account</a>
		</form>
</section>
<section id="register" action="<?php echo $_SERVER['PHP_SELF'] ?>" 
	class="index <?php if($page != 'register'){ echo 'hidden'; } ?>">
	<img src="images/logo.jpg">
	<form method="POST" autocomplete="on">
		<label style="color: <?php if($login_error != NULL){echo "red";}
			elseif ($register_succeed != NULL) { echo "green";  } ?>" >
			<?php 
			if($login_error != NULL){echo $login_error;}
			elseif ($register_succeed != NULL) { echo $register_succeed;  } ?>
		</label>
		<input type="text" name="name" placeholder="Enter Your Name" maxlength="15" required>
		<input type="email" name="email" placeholder="Enter Your Email" required>
		<input type="tel" name="phone" placeholder="Enter Your phone" required>
		<input type="password" name="password" placeholder="Enter Your Password" required>
		<input type="password" name="confirm" placeholder="Confirm Your Password" required>
		<label> 
			<span>Location</span> 
			<select name="location">
				<?php 
					include 'connect.php';
					$sql = "SELECT * FROM `locations`;";
					$result = $conn->query($sql);
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
						    echo '<option value = "'. $row['id'] .'" >'. $row['location'] .'</option>';
						}
					}
					$conn->close();
				?>
			</select>
		</label>
		<input type="submit" value="Register">
		<a id="goToLogin">Login instead</a>
	</form>
</section>
<script src="js/jquery-3.4.1.min.js"></script>
<?php include 'js/plugin.php' ?>
<?php
	include 'footer.php';
?>

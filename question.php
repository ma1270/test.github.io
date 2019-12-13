<?php 
	session_start();
	if(!(isset($_SESSION['user']['id']))){
		header('Location:index.php');
		exit();
	}

	include 'connect.php';	
	if(!(isset($_SESSION['question']))){
		$sql = "SELECT * FROM `questions`;";
		$result = $conn->query($sql);
		$_SESSION['index'] = 0;
		$_SESSION['count'] = $result->num_rows;
		$_SESSION['flag'] = 0;
		$_SESSION['answer'] = '';
		if ($_SESSION['count'] > 0) {
	    	while ($row = $result->fetch_assoc()) {
	    		$_SESSION['question'][] = $row;
	    	}
		}
	}else{
		if($_SESSION['flag'] == 1){
			if($_SESSION['index'] == 0){
				$_SESSION['answer'] = '';
			}
			$_SESSION['answer'] .= $_COOKIE['answer'];
			$_SESSION['index']++;
			if($_SESSION['index'] == $_SESSION['count']){
				$_SESSION['index'] = 0;
				$_SESSION['flag'] = 0;
				$sql = "SELECT `id`, `answer` FROM `categories`;";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					$user_answer = explode(",", $_SESSION['answer']);
					$cat = array();
	    			while ($row = $result->fetch_assoc()) {
	    				$correct_answer = explode(",", $row['answer']);
	    				$counter = 0;
	    				$n = count($correct_answer);
	    				for ($i=0; $i < $n; $i++) { 
	    					if($user_answer[$i] == $correct_answer[$i]){
	    						$counter++;
	    					}
	    				}
	    				$cat[] = $row['id']. ':' .$counter;

	    			}

	    			$res = implode(",", $cat);
	    			$sql = "UPDATE `users` SET `answers`= '". $res ."' WHERE `id` = ". $_SESSION['user']['id'] .";";
	    			$_SESSION['user']['answers'] = $res;
					if (!($conn->query($sql) === TRUE)) {
					   echo "<script> alert('No Connection eith database now try at another time'); </script>";
					}
				}

				unset($_SESSION['index']);
				unset($_SESSION['count']);
				unset($_SESSION['flag']);
				unset($_SESSION['answer']);
				unset($_SESSION['question']);
				unset($_SESSION['sort']);
				header('Location:profile.php');
				exit();
				
			}else{
				$_SESSION['answer'] .= ',';
			}
		}else if($_SESSION['index'] > $_SESSION['count']){
			$_SESSION['index'] = 0;
		}
		$_SESSION['flag'] = 0;
	}
	$conn->close();
	
	include 'header.php';
?>
	<section class="question">
		<img src="images/logo.jpg">
		<form>
			<p id="q" ><?php echo $_SESSION['question'][$_SESSION['index']]['question']; ?>?</p>
			<label><input type="radio" name="answer" value="A" checked>
				<p id="A"><?php echo $_SESSION['question'][$_SESSION['index']]['answerA']; ?></p>
			</label>
			<label><input type="radio" name="answer" value="B">
				<p id="B"><?php echo $_SESSION['question'][$_SESSION['index']]['answerB']; ?></p>
			</label>
			<input type="button" value="<?php if($_SESSION['index'] == $_SESSION['count']-1){ echo 'Finish'; } else { echo 'Next'; } ?>" id="answer"/>
		</form>
	</section>
	<script src="js/jquery-3.4.1.min.js"></script>
	<script>
		/*global $ document window*/
		$(document).ready(function (){
			$(".question").animate({left:'15%'},1000);
		    $("#answer").click(function (){
		    	document.cookie = "answer=" + $("input[name='answer']:checked").val();
			    $(".question").animate({left:'-100%'},1000,function(){window.location.href = 'reload.php';});
			    
			})
		});
	</script>
<?php
	include 'footer.php';
?>

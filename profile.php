<?php
	session_start();
	if (isset($_SESSION['question'])) {
		unset($_SESSION['index']);
		unset($_SESSION['count']);
		unset($_SESSION['flag']);
		unset($_SESSION['answer']);
		unset($_SESSION['question']);
	}
	if(isset($_SESSION['user']['id'])){
		if(!(isset($_SESSION['user']['name']))){
			include 'connect.php';
			$error = FALSE;
			$sql = "SELECT * FROM `users` WHERE `id` = " . $_SESSION['user']['id'] . " ;";
			$result = $conn->query($sql);
			if ($result->num_rows == 1) {
			    $row = $result->fetch_assoc();
			    $_SESSION['user'] = $row;
			} else {
				$error = TRUE;
			}
			$sql = "SELECT `location` FROM `locations` WHERE `id` = " . $_SESSION['user']['location'] . " ;";
			$result = $conn->query($sql);
			if ($result->num_rows == 1) {
			    $row = $result->fetch_assoc();
			    $_SESSION['user']['location'] = $row['location'];
			} else {
				$error = TRUE;
			}
			
			$sql = "SELECT  `id`,`name` FROM `categories` ;";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
			    while ($row = $result->fetch_assoc()) {
					$_SESSION['categories'][$row['id']]['name'] = $row['name'];
					$_SESSION['sort'][$row['id']] = $row['id'];
				}
			} else {
				$error = TRUE;
			}
			$sql = "SELECT * FROM `charities` WHERE `Addres` LIKE '%" . $_SESSION['user']['location'] ."%' OR `Addres` LIKE '" . $_SESSION['user']['location'] ."%' OR `Addres` LIKE '%" . $_SESSION['user']['location'] ."';";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
			    while ($row = $result->fetch_assoc()) {
					$_SESSION['charities']['myLocation'][$row['id']] = array($row['name'],$row['Addres'],$row['phone'],
						$row['Website'],$row['activity']);
					$_SESSION['categories'][$row['Category']]['charities'][] = $row['id'];
				}
			} else {
				$error = TRUE;
			}
			$sql = "SELECT * FROM `charities` WHERE `Addres` NOT LIKE '%" . $_SESSION['user']['location'] ."%' AND `Addres` NOT LIKE '" . $_SESSION['user']['location'] ."%' AND `Addres` NOT LIKE '%" . $_SESSION['user']['location'] ."';";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
			    while ($row = $result->fetch_assoc()) {
					$_SESSION['charities']['notMyLocation'][$row['id']] = array($row['name'],$row['Addres'],$row['phone'],
						$row['Website'],$row['activity']);
					$_SESSION['categories'][$row['Category']]['charities'][] = $row['id'];
				}
			} else {
				$error = TRUE;
			}

			$sql = "SELECT `charityId` FROM `bookmarks` WHERE `userId` = " . $_SESSION['user']['id'] . " ;";
			$result = $conn->query($sql);
			$_SESSION['all'] = '';
			$_SESSION['search']['value'] = '';
			$_SESSION['search']['show'] = 'hidden';
			$_SESSION['search']['id'] = array();
			$_SESSION['bookmarks']['show'] = 'hidden';
			$_SESSION['bookmarks']['bttn'] = 'Show Bookmarks';
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$_SESSION['bookmarks']['id'][$row['charityId']] = $row['charityId'];
				}    
			} else {
				$_SESSION['bookmarks']['id'] = array();
			}
			if($error === TRUE){
				echo "<script> alert('No Connection with database now try at another time'); </script>";
			    header('Location:index.php');
				exit();
			}
			$conn->close();
		}

	}else{
		header('Location:index.php');
		exit();
	}
	if (!(isset($_SESSION['sort']))) {
		include 'connect.php';
		$error = FALSE;
		if(count($_SESSION['user']['answers']) != 0){
			$answer = explode(",", $_SESSION['user']['answers']);
			$_SESSION['sort'] = array();
			foreach ($answer as $value) {
				$val = explode(":",$value);
				$_SESSION['sort'][$val[0]] = $val[1]; 
			}
			arsort($_SESSION['sort']);
		}else{
			foreach ($_SESSION['categories'] as $key=>$value) {
				$_SESSION['sort'][$key] = $key; 
			}
		}
		
		if($error === TRUE){
			echo "<script> alert('No Connection with database now try at another time'); </script>";
			header('Location:index.php');
			exit();
		}
		$conn->close();
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		include 'connect.php';
		if(isset($_SESSION['bookmarks']['id'][$_POST['id']]) && isset($_POST['id'])){
			$sql = "DELETE FROM `bookmarks` WHERE `userId` = ".$_SESSION['user']['id']." AND `charityId` = ". $_POST['id'] .";";
			if ($conn->query($sql) === TRUE) {
			    unset($_SESSION['bookmarks']['id'][$_POST['id']]);
			}
		}
		$conn->close();
	}
	include 'header.php';
?>
<header>

	<hgroup>
		<h1><?php echo $_SESSION['user']['name']; ?></h1>
		<h4><?php echo $_SESSION['user']['email']; ?></h4>
	</hgroup>
	<div class="search">
		<input type="search" name="search" id="searchValue">
		<input type="image" src="images/search.jpg" width="30px" id="searchButton">
	</div>
	<div class="bttns">
		<form action="question.php">
			<input type="submit" value="Test your skills">
		</form>
		<form action="logOut.php">
			<input type="submit" value="log out">
		</form>
	</div>
</header>
<div class="profile">
	<section>
		<div class="clear"></div>
		<div class="<?php echo($_SESSION['all']); ?>" id="all">
		<?php
			foreach ($_SESSION['sort'] as $key => $value) {
				echo "<div class='type' id='d". $key ."'>
					<span><img src='images/right.png' width='30px' id='i". $key ."'></span> 
					<span>". $_SESSION['categories'][$key]['name'] ."</span> 
					<span class='right'> ". count($_SESSION['categories'][$key]['charities']) ." item(s)</span>
				</div>
				<article id='a". $key ."' class = 'hidden'>";
					foreach ($_SESSION['categories'][$key]['charities'] as $id) {
					if (isset($_SESSION['charities']['myLocation'][$id])) {
						$btt = "Add to bookmarks";
						if(isset($_SESSION['bookmarks']['id'][$id])){
							$btt = "Remove from bookmarks";
						}
						echo "<div class='charity'>
							 <fieldset><legend>" . $_SESSION['charities']['myLocation'][$id][0] . "</legend><table>
							 <tr><td>Address: </td> <td>" . $_SESSION['charities']['myLocation'][$id][1] ."</td></tr>
							 <tr><td>Phone: </td> <td>" . $_SESSION['charities']['myLocation'][$id][2] ."</td></tr>
							 <tr><td>Website: </td> <td> <a href='". $_SESSION['charities']['myLocation'][$id][3] ."' >" . $_SESSION['charities']['myLocation'][$id][3] ."</td></tr>
							 <tr><td>Activites: </td> <td>" . $_SESSION['charities']['myLocation'][$id][4] ."</td></tr>
							 </table>
							 <form action='bookmark.php' method='POST' target='_blank'>
							 <input type='hidden' name='id' value='". $id ."' >
							 <button class='bookmark' id='c". $id ."' >" . $btt . "</button>
							 </form>
							 </fieldset>
							 </div>";
					}
				}
				foreach ($_SESSION['categories'][$key]['charities'] as $id) {
					if (isset($_SESSION['charities']['notMyLocation'][$id])) {
						$btt = "Add to bookmarks";
						if(isset($_SESSION['bookmarks']['id'][$id])){
						$btt = "Remove from bookmarks";
						}
						echo "<div class='charity'>
							 <fieldset><legend>" . $_SESSION['charities']['notMyLocation'][$id][0] . "</legend><table>
							 <tr><td>Address: </td> <td>" . $_SESSION['charities']['notMyLocation'][$id][1] ."</td></tr>
							 <tr><td>Phone: </td> <td>" . $_SESSION['charities']['notMyLocation'][$id][2] ."</td></tr>
							 <tr><td>Website: </td> <td> <a href='". $_SESSION['charities']['notMyLocation'][$id][3] ."' >" . $_SESSION['charities']['notMyLocation'][$id][3] ."</td></tr>
							 <tr><td>Activites: </td> <td>" . $_SESSION['charities']['notMyLocation'][$id][4] ."</td></tr>
							 </table>
							 <form action='bookmark.php' method='POST' target='_blank'>
							 <input type='hidden' name='id' value='". $id ."' >
							 <button class='bookmark' id='c". $id ."' >" . $btt . "</button>
							 </form>
							 </fieldset>
							 </div>";
					}
					
				}
				echo "</article>";
			}
			
		?>
	</div>
		<article id='bookmarks' class = '<?php echo($_SESSION['bookmarks']['show']); ?>'>
			<?php
				foreach ($_SESSION['bookmarks']['id'] as $id) {
					if (isset($_SESSION['charities']['myLocation'][$id])) {
						echo "<div class='charity' id='db". $id ."'>
							 <fieldset><legend>" . $_SESSION['charities']['myLocation'][$id][0] . "</legend><table>
							 <tr><td>Address: </td> <td>" . $_SESSION['charities']['myLocation'][$id][1] ."</td></tr>
							 <tr><td>Phone: </td> <td>" . $_SESSION['charities']['myLocation'][$id][2] ."</td></tr>
							 <tr><td>Website: </td> <td> <a href='". $_SESSION['charities']['myLocation'][$id][3] ."' >" . $_SESSION['charities']['myLocation'][$id][3] ."</td></tr>
							 <tr><td>Activites: </td> <td>" . $_SESSION['charities']['myLocation'][$id][4] ."</td></tr>
							 </table>
							 <form action='". $_SERVER['PHP_SELF'] ."' method='POST'>
							 <input type='hidden' name='id' value='". $id ."' >
							 <button class='bookmark' id='b". $id ."' >Remove from bookmarks</button>
							 </form>
							 </fieldset>
							 </div>";
					}
				}
				foreach ($_SESSION['bookmarks']['id'] as $id) {
					if (isset($_SESSION['charities']['notMyLocation'][$id])) {
						echo "<div class='charity'>
							 <fieldset><legend>" . $_SESSION['charities']['notMyLocation'][$id][0] . "</legend><table>
							 <tr><td>Address: </td> <td>" . $_SESSION['charities']['notMyLocation'][$id][1] ."</td></tr>
							 <tr><td>Phone: </td> <td>" . $_SESSION['charities']['notMyLocation'][$id][2] ."</td></tr>
							 <tr><td>Website: </td> <td> <a href='". $_SESSION['charities']['notMyLocation'][$id][3] ."' >" . $_SESSION['charities']['notMyLocation'][$id][3] ."</td></tr>
							 <tr><td>Activites: </td> <td>" . $_SESSION['charities']['notMyLocation'][$id][4] ."</td></tr>
							 </table>
							 <form action='". $_SERVER['PHP_SELF'] ."' method='POST'>
							 <input type='hidden' name='id' value='". $id ."' >
							 <button class='bookmark' id='b". $id ."' >Remove from bookmarks</button>
							 </form>
							 </fieldset>
							 </div>";
					}
				}
			?>
		</article>
		<article id='search' class = '<?php echo($_SESSION['search']['show']); ?>'>
			<div class="cancel" id="x">X</div>
			<div class="clear"></div>
			<?php
				foreach ($_SESSION['search']['id'] as $id) {
					if (isset($_SESSION['charities']['myLocation'][$id])) {
						$btt = "Add to bookmarks";
						if(isset($_SESSION['bookmarks']['id'][$id])){
							$btt = "Remove from bookmarks";
						}
						echo "<div class='charity' id='db". $id ."'>
							 <fieldset><legend>" . $_SESSION['charities']['myLocation'][$id][0] . "</legend><table>
							 <tr><td>Address: </td> <td>" . $_SESSION['charities']['myLocation'][$id][1] ."</td></tr>
							 <tr><td>Phone: </td> <td>" . $_SESSION['charities']['myLocation'][$id][2] ."</td></tr>
							 <tr><td>Website: </td> <td> <a href='". $_SESSION['charities']['myLocation'][$id][3] ."' >" . $_SESSION['charities']['myLocation'][$id][3] ."</td></tr>
							 <tr><td>Activites: </td> <td>" . $_SESSION['charities']['myLocation'][$id][4] ."</td></tr>
							 </table>
							 <form action='". $_SERVER['PHP_SELF'] ."' method='POST'>
							 <input type='hidden' name='id' value='". $id ."' >
							 <button class='bookmark' id='b". $id ."' >". $btt . "</button>
							 </form>
							 </fieldset>
							 </div>";
					}
				}
				foreach ($_SESSION['search']['id'] as $id) {
					if (isset($_SESSION['charities']['notMyLocation'][$id])) {
						$btt = "Add to bookmarks";
						if(isset($_SESSION['bookmarks']['id'][$id])){
							$btt = "Remove from bookmarks";
						}
						echo "<div class='charity'>
							 <fieldset><legend>" . $_SESSION['charities']['notMyLocation'][$id][0] . "</legend><table>
							 <tr><td>Address: </td> <td>" . $_SESSION['charities']['notMyLocation'][$id][1] ."</td></tr>
							 <tr><td>Phone: </td> <td>" . $_SESSION['charities']['notMyLocation'][$id][2] ."</td></tr>
							 <tr><td>Website: </td> <td> <a href='". $_SESSION['charities']['notMyLocation'][$id][3] ."' >" . $_SESSION['charities']['notMyLocation'][$id][3] ."</td></tr>
							 <tr><td>Activites: </td> <td>" . $_SESSION['charities']['notMyLocation'][$id][4] ."</td></tr>
							 </table>
							 <form action='". $_SERVER['PHP_SELF'] ."' method='POST'>
							 <input type='hidden' name='id' value='". $id ."' >
							 <button class='bookmark' id='b". $id ."' >". $btt ."</button>
							 </form>
							 </fieldset>
							 </div>";
					}
				}
			?>
		</article>
	</section>
</div>
<div class="end">
	<button class='showBookmarks' id='getBookmarks' ><?php echo $_SESSION['bookmarks']['bttn']; ?></button>
</div>

<script src="js/jquery-3.4.1.min.js"></script>
	<script>
		/*global $ document window*/
		$(document).ready(function (){
			$("#getBookmarks").click(function (){
				window.location.href = 'show.php';
			})
			$("#searchButton").click(function (){
				document.cookie = "searchValue=" + $("input[name='search']").val();
				window.location.href = 'search.php';
			})
			$("#x").click(function (){
				window.location.href = 'cancel.php';
			})
			var arr = new Array();
			<?php 
				foreach ($_SESSION['sort'] as $key => $value){
					echo "
						arr[". $key ."] = 0;
						$('#d". $key ."').click(function (){
						$('#a". $key ."').slideToggle(1000);
						if(arr[". $key ."] == 0){
							$('#i". $key ."').attr('src','images/down.png');
							arr[". $key ."] = 1;
						}else{
							$('#i". $key ."').attr('src','images/right.png');
							arr[". $key ."] = 0;
						}
					})";
				}
				foreach ($_SESSION['charities']['myLocation'] as $key => $value){
					echo "
						$('#c". $key ."').click(function (){
						if($('#c". $key ."').text() == 'Add to bookmarks'){
							$('#c". $key ."').text('Remove from bookmarks');
						}else{
							$('#c". $key ."').text('Add to bookmarks');
						}
					})";
				}
				foreach ($_SESSION['charities']['notMyLocation'] as $key => $value){
					echo "
						$('#c". $key ."').click(function (){
						if($('#c". $key ."').text() == 'Add to bookmarks'){
							$('#c". $key ."').text('Remove from bookmarks');
						}else{
							$('#c". $key ."').text('Add to bookmarks');
						}
					})";
				}
				foreach ($_SESSION['bookmarks']['id'] as $id){
					echo "
						$('#b". $key ."').click(function (){
							$('#bookmarks').remove('#db". $key ."');
					})";
				}
			?>
		    
		});
		
	</script>

<?php
	include 'footer.php';
?>
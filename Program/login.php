<?php
	require_once "config.php";
	
	$_SESSION['webpage'] = "login";

	//Kijelentkezés
	if(isset($_POST['buttonlogout'])) 
	{
		session_destroy();
	}
			
	$message = "";
	
	//Bejelentkezés
	if(isset($_POST['subbutton']))
	{
		$username = mysqli_real_escape_string($con, $_POST['username']);
		$password = mysqli_real_escape_string($con, $_POST['password']);
		$query = mysqli_query($con, "select * from users where username = '{$username}' and password = md5('{$password}')");
		if(mysqli_num_rows($query) == 0)
		{
			$message = "Hibás felhasználónév vagy jelszó.";
		}
		else
		{
			$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
			$_SESSION["LoggedIn"] = true;
			$_SESSION['UserID'] = $row['ID'];
			
		}
		mysqli_free_result($query);
	}
	if(isset($_SESSION['LoggedIn'])) Header("Location: index.php");
?>

<html>
	
	<body>		
		<div class="divlogin">
			<form class="formlogin" method="post" action="login.php">
				<input name="username" class="textfh" type="text" required="required" placeholder="Felhasználónév@"/></br>
				<input name="password" class="textpass" type="password" required="required" placeholder="Jelszó***"/></br>
				<input type="submit" name="subbutton" class="buttonlogin" value="Belépés"/></br>
				<label class="labeluzenet"><?php echo $message; ?></label>
			</form>
		</div>
	</body>
	
</html>
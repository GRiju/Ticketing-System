<?php
	if(isset($_SESSION['LoggedIn']))
	{
		$query = mysqli_query($con, "select * from users where ID = '{$_SESSION['UserID']}'");
		if(mysqli_num_rows($query) != 0)
		{
			$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
			$_SESSION['UserGroup'] = $row['usergroup'];
		}
		else
		{
			unset($_SESSION['LoggedIn']);
		}
		mysqli_free_result($query);
	}
	
	if(!isset($_SESSION['LoggedIn'])) Header("Location: login.php");
?>

<html>
	
	<body>
		<header>
			<nav>
				<form class="menuform" method="post" action="login.php">
					<button class="menubutton" name="buttonlogout">Kijelentkezés</button>
				</form>
				<form class="menuform" method="post" action="index.php">
					<button class="menubutton">Főoldal</button>
				</form>
				<form class="menuform" method="post" action="tickets.php?show=own">
					<button class="menubutton">Hibajegyek</button>
				</form>
				<form class="menuform" method="post" action="rooms.php">
					<button class="menubutton">Termek</button>
				</form>
				
				<?php
					if($_SESSION['UserGroup'] >= 2)
					{
				?>
						<form class="menuform" method="post" action="admin.php">
							<button class="menubutton" name="buttonusers">Admin felület</button>
						</form>
				<?php
					}
				?>
			</nav>
		</header>
		

	</body>
</html>
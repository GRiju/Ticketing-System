<?php
	require_once "config.php";
	require_once "staticelements.php";
	
	if($_SESSION['webpage'] != "users")
	{
		unset($_SESSION['sortby']);
		unset($_SESSION['order']);
	}
	
	$_SESSION['webpage'] = "users";
	
	if(!isset($_SESSION['sortby'])) 
	{
		$_POST['sortby'] = "ID";
		$_POST['order'] = "asc";
	}
	
	if(isset($_POST['sortby']))
	{
		$_SESSION['sortby'] = $_POST['sortby'];
		$_SESSION['order'] = $_POST['order'];
	}
	
	$message = "";
	
	$username = "";
	$passw = "";
	$veznev = "";
	$kernev = "";
	
	if($_SESSION['UserGroup'] < 2) die;
	
	if(!isset($_POST['page']))
	{
		$_POST['page'] = 1;
	}
	
	if(isset($_POST['nextpagebtn']))
	{
		$_POST['page']++;
	}
	
	if(isset($_POST['prevpagebtn']))
	{
		$_POST['page']--;
	}
	
	//Hozzáadás
	if(isset($_SESSION['suseraddbutton']) && isset($_POST["useraddbutton"]))
	{
		$username = mysqli_real_escape_string($con, $_POST["fhtext"]);
		$passw = mysqli_real_escape_string($con, $_POST["pwtext"]);
		$veznev = mysqli_real_escape_string($con, $_POST["veznevtext"]);
		$kernev = mysqli_real_escape_string($con, $_POST["kernevtext"]);
		$usergroup = $_POST['groupselect'];
		
		$query = mysqli_query($con, "select * from users where username = '{$username}'");
		if(mysqli_num_rows($query) != 0)
		{
			$message = "Ilyen felhasználónév már létezik!";
		}
		else
		{
			mysqli_query($con, "insert into users values (0, '{$username}', MD5('{$passw}'), '{$kernev}', '{$veznev}', '{$usergroup}')");
			
			$username = "";
			$passw = "";
			$veznev = "";
			$kernev = "";
		}
		unset($_SESSION['suseraddbutton']);
		mysqli_free_result($query);
	}
	
	//Szerkesztés
	if(isset($_POST["usereditbutton"]))
	{
		$username = mysqli_real_escape_string($con, $_POST["fhtextedit"]);
		$passw = mysqli_real_escape_string($con, $_POST["pwtextedit"]);
		$veznev = mysqli_real_escape_string($con, $_POST["veznevtextedit"]);
		$kernev = mysqli_real_escape_string($con, $_POST["kernevtextedit"]);
		$usergroup = $_POST['groupselectedit'];
		
		$query = mysqli_query($con, "select * from users where username = '{$username}' AND ID != {$_GET['userid']}");
		if(mysqli_num_rows($query) != 0)
		{
			$message = "Ilyen felhasználónév már létezik!";
		}
		else
		{
			mysqli_query($con, "update users set username = '{$username}', firstname = '{$kernev}', lastname = '{$veznev}', usergroup = '{$usergroup}'
			where ID = {$_GET['userid']}");
			
			if(!empty($passw))
			{
				mysqli_query($con, "update users set password = MD5('{$passw}')where ID = {$_GET['userid']}");
			}
			$message = "Sikeres mentés";
		}
	}
	
	//Törlés
	if(isset($_POST["deleteuserbutton"]))
	{
		if($_POST['fhid'] == $_SESSION['UserID']) alert("A saját felhasználódat nem törölheted.");
		else mysqli_query($con, "delete from users where ID = '{$_POST['fhid']}'");
	}
?>

<html>
	<head>
		
		
	</head>
	
	<body>
		<header>
			<nav class="submenunav">
				<form class="menuform" method="get" action="users.php">
					<button class="menubutton" >Új felhasználó hozzáadása</button>
					<input type="hidden" name="show" value="adduser">
				</form>
			</nav>
		</header>
		
		<main>
		
			<?php
				if(isset($_GET['show']))
				{
					if($_GET['show'] == "adduser")
					{
						?>
						<div class="infodiv">
							<form method="post" action="users.php?show=adduser">
								<input type="text" class="cmntext" name="fhtext" required="required" value="<?php print $username;?>" autofocus placeholder="Felhasználónév"/>
								<input type="text" class="cmntext" name="pwtext" required="required" value="<?php print $passw;?>" placeholder="Jelszó"/></br>
								<input type="text" class="cmntext" name="veznevtext" required="required" value="<?php print $veznev;?>" placeholder="Vezetéknév">
								<input type="text" class="cmntext" name="kernevtext" required="required" value="<?php print $kernev;?>" placeholder="Keresztnév"></br>
								<select name="groupselect" class="cmntext">
									<option value="1">Dolgozó</option>
									<option value="2">Adminisztrátor</option>
								</select></br>
								<input type="submit" name="useraddbutton" value="Létrehoz"></br>
								<label><?php echo $message; ?></label>
							</form>
							<form method="post" action="users.php">
								<input type="submit" value="Vissza">
							</form>
						</div>
						<?php
						$_SESSION['suseraddbutton'] = 1;
					}
					else if($_GET['show'] == "edituser")
					{
						$query = mysqli_query($con, "select * from users where ID = {$_GET['userid']}");
						$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
						
						?>
						<div class="infodiv">
							<form method="post" action="users.php?show=edituser&userid=<?php print $row['ID']; ?>">
								<input type="text" class="cmntext" name="fhtextedit" required="required" value="<?php print $row['username'];?>" autofocus placeholder="Felhasználónév"/>
								<input type="text" class="cmntext" name="pwtextedit" value="" placeholder="Jelszó"/></br>
								<input type="text" class="cmntext" name="veznevtextedit" required="required" value="<?php print $row['lastname'];?>" placeholder="Vezetéknév">
								<input type="text" class="cmntext" name="kernevtextedit" required="required" value="<?php print $row['firstname'];?>" placeholder="Keresztnév"></br>
								<select name="groupselectedit" class="cmntext">
									<option value="1" <?php if($row['usergroup'] == 1) { ?> selected="selected" <?php } ?>>Dolgozó</option>
									<option value="2" <?php if($row['usergroup'] == 2) { ?> selected="selected" <?php } ?>>Adminisztrátor</option>
								</select></br>
								<input type="submit" name="usereditbutton" value="Mentés"></br>
								<label><?php echo $message; ?></label>
							</form>
							<form method="post" action="users.php">
								<input type="submit" value="Vissza">
							</form>
						</div>
						<?php
						mysqli_free_result($query);
					}
				}
				else
				{
					$fhgroup = array("Dolgozó", "Adminisztrátor");
					?>
					<form method="get" action="users.php">
						<select name="searchby">
							<option value="username">Felhasználónév</option>
							<option value="lastname">Vezetéknév</option>
							<option value="firstname">Keresztnév</option>
						</select>
						<input type="text" name="searchfh" placeholder="Felhasználó keresése">
						<input type="submit" value="Keresés">
					</form>
					
					<form method="post" action="<?php print $_SERVER['REQUEST_URI']; ?>">
						
						<label>Rendezés</label>
						<select name="sortby">
						
							<option value="username" <?php if($_SESSION['sortby'] == "username") { ?>selected="selected" <?php } ?>>Felhasználónév</option>
							<option value="lastname" <?php if($_SESSION['sortby'] == "lastname") { ?>selected="selected" <?php } ?>>Vezetéknév</option>
							<option value="firstname" <?php if($_SESSION['sortby'] == "firstname") { ?>selected="selected" <?php } ?>>Keresztnév</option>
						
						</select>
						
						<select name="order">
						
							<option value="asc" <?php if($_SESSION['order'] == "asc") { ?>selected="selected" <?php } ?>>Növekvő</option>
							<option value="desc" <?php if($_SESSION['order'] == "desc") { ?>selected="selected" <?php } ?>>Csökkenő</option>
						
						</select>
						<input type="submit" name="sortbybtn" value="Rendez">
					
					</form>
					
					<div class="divlist">
						<table class="datatable">
							<tr class="borderbottom">
								<th>ID</a></th>
								<th>Felhasználónév</th>
								<th>Vezetéknév</th>
								<th>Keresztnév</th>
								<th>Felhasználói csoport</th>
							</tr>
							<?php
							$where = "";
							
							$minpg = $_POST['page']*ROWPERPAGEUSERS-ROWPERPAGEUSERS;
							$maxpg = $_POST['page']*ROWPERPAGEUSERS;
							
							$limit = " LIMIT {$minpg}, {$maxpg}";
							if(!isset($_GET['searchfh']))
							{
								$query = mysqli_query($con, "select * from users order by {$_SESSION['sortby']} {$_SESSION['order']}".$limit);
							}
							else
							{
								$where = " where {$_GET['searchby']} LIKE '%{$_GET['searchfh']}%'";
								$query = mysqli_query($con, "select * from users ".$where." order by {$_SESSION['sortby']} {$_SESSION['order']}".$limit);
							}
							if(mysqli_num_rows($query) != 0)
							{
								while($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
								{										
								?>
									<tr class="borderbottom">
										<td><?php print $row['ID']; ?></td>
										<td width="20%"><?php print $row['username']; ?></td>
										<td><?php print $row['lastname']; ?></td>
										<td><?php print $row['firstname']; ?></td>
										<td width="15%"><?php print $fhgroup[intval($row['usergroup'])-1]; ?></td>
										<td width="25%">
										
											<form class="fhmenuform" method="post" action="users.php" onsubmit="return DeleteWarning(this)">
												<button class="smlbutton" name="deleteuserbutton">Törlés</button>
												<input type="hidden" name="fhid" value="<?php print $row['ID']; ?>">
												<input type="hidden" name="page" value="<?php print $_POST['page']; ?>">
											</form>
											
											<form class="fhmenuform" method="post" action="users.php?show=edituser&userid=<?php print $row['ID']; ?>">
												<button class="smlbutton" name="edituserbtn">Szerkesztés</button>
											</form>
										</td>
									</tr>
									
								<?php
								}
							}
							?>
						</table>
						<div class="pageselectdiv">
							<?php 
							$query = mysqli_query($con, "select * from users ".$where);
							$maxpage = ceil(mysqli_num_rows($query)/ROWPERPAGEUSERS);
							
							print "Oldal: ".$_POST['page']."/".$maxpage."<br>"; 
							
							if($_POST['page'] > 1)
							{
							?>
							<form class="navpgbtns" method="post" action="<?php print $_SERVER['REQUEST_URI'];?>">
								
								<button name="prevpagebtn">Előző oldal</button>
								<input type="hidden" name="page" value="<?php print $_POST['page']; ?>">
								
							</form>
							<?php
							}
							
							if($_POST['page'] < $maxpage)
							{
							?>
								<form class="navpgbtns" method="post" action="<?php print $_SERVER['REQUEST_URI'];?>">
									<button name="nextpagebtn">Következő oldal</button>
									<input type="hidden" name="page" value="<?php print $_POST['page']; ?>">
									
								</form>
							<?php
							}
							?>
						</div>
					</div>
					<?php
				}
			?>
		
		</main>
		
	</body>
</html>
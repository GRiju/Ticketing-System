<?php
	require_once "config.php";
	require_once "staticelements.php";
	
	$_SESSION['webpage'] = "rooms";
		
	//Terem létrehozás
	if(isset($_SESSION['snewroombutton']) && isset($_POST['newroombutton']))
	{
		$roomname = mysqli_real_escape_string($con, $_POST['newroombuttonname']);
		$roomdesc = mysqli_real_escape_string($con, $_POST['newroombuttondesc']);
		mysqli_query($con, "insert into rooms values (0, '{$roomname}', '{$roomdesc}')");
		unset($_SESSION['snewroombutton']);
	}

	//Terem szerkesztés
	if(isset($_POST['roomeditbutton']))
	{
		mysqli_query($con, "update rooms set Roomname = '{$_POST['editroomname']}', description = '{$_POST['editroomdesc']}' where ID = '{$_POST['roomid']}'");
	}
	
	//Terem törlés
	if(isset($_POST['roomiddelete']))
	{
		mysqli_query($con, "delete from rooms where ID={$_POST['roomiddelete']}");
	}
	
	//Eszköz létrehozás
	if(isset($_POST['newdevicebutton']) && isset($_SESSION['snewdevicebutton']))
	{
		mysqli_query($con, "insert into devices values (0, {$_POST['roomid']}, '{$_POST['newdevicename']}', '{$_POST['newdevicedesc']}', curdate())");
		unset($_SESSION['snewdevicebutton']);
	}
	
	//Eszköz törlés
	if(isset($_POST['devicedelete']))
	{
		mysqli_query($con, "delete from devices where ID = '{$_POST['deviceid']}'");
	}
	
	//Eszköz szerkesztés
	if(isset($_POST['editdevicebutton']))
	{
		print "update devices set DeviceName = {$_POST['editdevicename']}, Description = {$_POST['editdevicedesc']} where ID = {$_POST['deviceid']}";
		mysqli_query($con, "update devices set DeviceName = '{$_POST['editdevicename']}', Description = '{$_POST['editdevicedesc']}' where ID = {$_POST['deviceid']}");
	}
?>

<html>
	<head>
	</head>
	
	<body>
		<header>
			<?php
			if($_SESSION['UserGroup'] >= 2)
			{
			?>
			<nav class="submenunav">
				<form class="menuform" method="get" action="rooms.php">
					<button class="menubutton">Új terem hozzáadása</button>
					<input type="hidden" name="show" value="new">
				</form>
				<?php
				if(isset($_GET['show']))
				{
					if($_GET['show'] == 'roomdevices')
					{
						?>
						<form class="menuform" method="get" action="rooms.php">
							<button class="menubutton">Új eszköz hozzáadása</button>
							<input type="hidden" name="roomid" value='<?php print $_GET['roomid']?>'>
							<input type="hidden" name="show" value="newroombuttondevice">
						</form>
						<?php
					}
				}
			}
			?>
			</nav>
		</header>
		
		<main>
		<?php
			if(!isset($_GET['show']))
			{
				?>
				<form method="get" action="rooms.php">
					<input type="text" name="searchr" placeholder="Terem keresése">
					<input type="submit" value="Keresés">
				</form>
				
				<?php
				if(!isset($_GET['searchr'])) $query = mysqli_query($con, "select * from rooms order by Roomname asc");
				else $query = mysqli_query($con, "select * from rooms where Roomname LIKE '%{$_GET['searchr']}%' order by Roomname asc");
				if(mysqli_num_rows($query) != 0)
				{
					while($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
					{
						?>
							
							<div class="roomsdiv">
								<div class="roomsdivcontent">
									<h1 class="roomsname"><?php print $row['Roomname'] ?></h1>
									<p class="roomsdesc">
										<?php
											$query2 = mysqli_query($con, "select * from devices where RoomID = {$row['ID']}");
											print "Eszközök száma: ".mysqli_num_rows($query2)."<br>";
											
											$query2 = mysqli_query($con, "select * from tickets 
																			inner join devices on tickets.deviceID = devices.ID
																			where tickets.status != 4 AND devices.RoomID = {$row['ID']}");
											print "Hibára jelentett eszközök száma: ".mysqli_num_rows($query2)."<br>";
											print "<br>";
											print nl2br($row['description']) ?>
									</p>
								</div>
								
								<div class="roomsdivset">
									<form method="post" action="rooms.php?show=roomdevices&roomid=<?php print $row['ID'] ?>">
										<button class="smlbutton">Eszközök</button>
										<input type="hidden" name="roomnamehidden" value="<?php print nl2br($row['Roomname']) ?>">
									</form>
									
									<?php
									if($_SESSION['UserGroup'] >= 2)
									{
										?>
										<form method="post" action="rooms.php?show=roomedit&roomid=<?php print $row['ID'] ?>">
											<button class="smlbutton" name='roomid' value='<?php print $row['ID'] ?>'>Szerkesztés</button>
											<input type="hidden" name="roomnamehidden" value="<?php print nl2br($row['Roomname']) ?>">
											<input type="hidden" name="roomdeschidden" value="<?php print nl2br($row['description']) ?>">
										</form>
									<?php
									}
									?>
										
									<?php
									if($_SESSION['UserGroup'] >= 2)
									{
										?>
										<form method="post" action="rooms.php"  onsubmit="return DeleteWarning(this)">
											<button class="smlbutton roomdeletebutton" name='roomiddelete' value='<?php print $row['ID'] ?>'>Törlés</button>
										</form>
									<?php
									}
									?>
								</div>
								
							</div>
						<?php
					}
				}
				mysqli_free_result($query);
			}
			else if($_GET['show'] == 'new')
			{
				if($_SESSION['UserGroup'] < 2) die;
				?>
				<div class="infodiv">
					<form method="post" action="rooms.php">
						<input type="text" class="cmntext" name="newroombuttonname" required="required" placeholder="Szoba neve" autofocus>
						<input type="submit" name="newroombutton" value="Létrehoz"></br>
						<textarea placeholder="Szoba rövid leírása (Nem kötezelő)" class="cmnarea" name="newroombuttondesc" rows="2"></textarea></br>
						
					</form>
				</div>
				<?php
				$_SESSION['snewroombutton'] = 1;
			}
			else if($_GET['show'] == 'roomdevices')
			{
				$query = mysqli_query($con, "select Roomname from rooms where ID = '{$_GET['roomid']}'");
				$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
				?>
				<div class='deviceroomdiv'>
					<h1 class="deviceroomname"><?php print $row['Roomname']; ?></h1>
					<?php
					$query = mysqli_query($con, "select * from devices where RoomID = '{$_GET['roomid']}'");
					if(mysqli_num_rows($query) != 0)
					{
						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
						{
							$query2 = mysqli_query($con, "select * from tickets where deviceID = {$row['ID']} AND status != 4");
							
							?>
								<div class="devicesdiv">
									<h1 class="devicename"><?php print $row['DeviceName'] ?></h1>
									<h2 class="devicedesc">Jelentett hibák száma: <?php print mysqli_num_rows($query2); ?><h2>
									<h2 class="devicedesc"><?php print $row['Description'] ?></h2>
									
										<form class="devicesform" method="post" action="tickets.php?show=new">
											<button class="smlbutton devicesbuttons">Hiba jelentése</button>
											<input type="hidden" name="deviceid" value="<?php print $row['ID'] ?>">
										</form>
									
									<form class="devicesform" method="get" action="tickets.php">
										<button class="smlbutton devicesbuttons">Eszköz hibajegyei</button>
										<input type="hidden" name="show" value="device">
										<input type="hidden" name="deviceid" value="<?php print $row['ID'] ?>">
									</form>
									
									<?php
									if($_SESSION['UserGroup'] >= 2)
									{
										?>
										
										<form class="devicesform" method="post" action="rooms.php?show=editroomdevice&deviceid=<?php print $row['ID'] ?>">
										
											<button class="smlbutton devicesbuttons">Szerkesztés</button>
											
											<input type="hidden" name="devicenamehidden" value="<?php print $row['DeviceName'] ?>">
											<input type="hidden" name="devicedeschidden" value="<?php print $row['Description'] ?>">
											<input type="hidden" name="roomid" value="<?php print $_GET['roomid'] ?>">
											
										</form>
										
										<form class="devicesform" method="post" action="rooms.php?show=roomdevices&roomid=<?php print $_GET['roomid']; ?>" onsubmit="return DeleteWarning(this)">
											<button class="smlbutton devicesbuttons" name="devicedelete">Törlés</button>
											<input type="hidden" name="deviceid" value="<?php print $row['ID'] ?>">
										</form>
									<?php
									}
									?>
									
								</div>
							
							
							<?php
						}
					}
					else
					{
						print("Ebben a teremben nincsen semmilyen eszköz.");
					}
					?>
				</div>
				
				<?php
			}
			else if($_GET['show'] == 'roomedit')
			{
				if($_SESSION['UserGroup'] < 2) die;
				?>
				<div class="infodiv">
					<form method="post" action="rooms.php">
						<input type="text" name="editroomname" class="cmntext" required="required" placeholder="Szoba neve" value="<?php print $_POST['roomnamehidden'] ?>">
						<input type="submit" name="roomeditbutton" value="Módosít"><br>
						<textarea placeholder="Szoba rövid leírása (Nem kötezelő)" class="cmnarea" name="editroomdesc" rows="2"><?php print $_POST['roomdeschidden'] ?></textarea></br>
						<input type="hidden" name="roomid" value='<?php print $_POST['roomid'] ?>'>
					</form>
				</div>
				<?php
			}
			else if($_GET['show'] == 'newroombuttondevice')
			{
				if($_SESSION['UserGroup'] < 2) die;
				?>
				<div class="infodiv">
					<form method="post" action="rooms.php?roomid=<?php print $_GET['roomid'] ?>&show=roomdevices">
						<input type="text" class="cmntext" name="newdevicename" required="required" placeholder="Eszköz neve" autofocus>
						<input type="submit" name="newdevicebutton" value="Létrehoz"><br>
						<textarea placeholder="Eszköz rövid leírása (Nem kötezelő)" class="cmnarea" name="newdevicedesc" rows="2"></textarea></br>
						<input type="hidden" name="roomid" value='<?php print $_GET['roomid'] ?>'>
					</form>
				</div>
				<?php
				$_SESSION['snewdevicebutton'] = 1;
			}
			
			else if($_GET['show'] == 'editroomdevice')
			{
				if($_SESSION['UserGroup'] < 2) die;
				?>
				<div class="infodiv">
					<form method="post" action="rooms.php?roomid=<?php print $_POST['roomid'] ?>&show=roomdevices">
						<input type="text" class="cmntext" name="editdevicename" required="required" placeholder="Eszköz neve" value="<?php print $_POST['devicenamehidden']; ?>" autofocus>
						<input type="submit" name="editdevicebutton" value="Módosít"><br>
						<textarea placeholder="Eszköz rövid leírása (Nem kötezelő)" class="cmnarea" name="editdevicedesc" rows="2"><?php print $_POST['devicedeschidden']; ?></textarea></br>
						<input type="hidden" name="deviceid" value='<?php print $_GET['deviceid'] ?>'>
					</form>
				</div>
				<?php
				$_SESSION['snewdevicebutton'] = 1;
			}
		?>
		</main>
	</body>

</html>
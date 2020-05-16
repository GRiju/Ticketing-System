<?php
	require_once "config.php";
	require_once "staticelements.php";
	
	if($_SESSION['webpage'] != "tickets")
	{
		unset($_SESSION['sortby']);
		unset($_SESSION['order']);
	}
	
	$_SESSION['webpage'] = "tickets";
	
	if(empty($_GET)) header("Location: tickets.php?show=own");
	
	//Rendezés
	if(!isset($_SESSION['sortby'])) 
	{
		$_POST['sortby'] = "Date";
		$_POST['order'] = "desc";
	}
	
	if(isset($_POST['sortby']))
	{
		$_SESSION['sortby'] = $_POST['sortby'];
		$_SESSION['order'] = $_POST['order'];
	}
	
	//Lapozás
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
	
	//Új hibajegy
	if(isset($_SESSION['snewticketbutton']) && isset($_POST['newticketbutton']))
	{		
		$desc = mysqli_real_escape_string($con, $_POST['ticketdesc']);
		
		$abc = range('A', 'Z');

		$tid = "";
		
		do
		{
			$tid = "";
			for($i = 0; $i < 6; $i++)
			{
				if($i <= 2) $tid = $tid.$abc[rand(0, sizeof($abc))];
				else
				{
					$tid = $tid.rand(0, 9);
				}
			}
			$query = mysqli_query($con, "select * from tickets where ticketID = '{$tid}'");
		}
		while(mysqli_num_rows($query) != 0);
			
		mysqli_query($con, "insert into tickets values (0, '{$tid}', '{$_POST['deviceid']}', '{$_SESSION['UserID']}', now(), '{$desc}', 1, 1)");
	}
	
	//Törlés
	if(isset($_POST['delticketbtn']))
	{
		mysqli_query($con, "delete from tickets where ticketID = '{$_POST['tcktid']}'");
		header("Location: tickets.php?".$_SESSION['lastpage']);
		
	}
	
	//Módosítás
	if(isset($_POST['ticketeditsubmit']))
	{
		mysqli_query($con, "update tickets set status = '{$_POST['selstat']}', priority = '{$_POST['selprior']}' where ticketID = '{$_POST['tcktid']}'");
		header("Location: tickets.php?".$_SESSION['lastpage']);
	}
?>

<html>
	<head>

	</head>
	
	<body>
		<header>
			<nav class="submenunav">
			<?php
			if(isset($_GET['deviceid']))
			{
			?>
				
					<form class="menuform" method="post" action="tickets.php?show=new">
						<button class="menubutton" name="newticketmenubutton">Új hibajegy</button>
						<input type="hidden" name="deviceid" value="<?php print $_GET['deviceid']; ?>">
					</form>
			<?php
			}
			if($_SESSION['UserGroup'] >= 2)
			{
			?>
			
					<form class="menuform" method="post" action="tickets.php?show=all">
						<button class="menubutton">Összes hibajegy</button>
					</form>
					
					<form class="menuform" method="post" action="tickets.php?show=half">
						<button class="menubutton">Kezeletlen hibajegyek</button>
					</form>
							
			<?php
			}
			?>
			</nav>
			
		</header>
	
		<main>
			<?php
			
				$prequery = "select tickets.*, users.username, users.firstname, users.lastname, rooms.ID as RoomID, devices.ID as DeviceID, devices.DeviceName, rooms.Roomname from tickets 
						inner join devices on tickets.deviceID = devices.ID 
						left join users on tickets.userID = users.ID
						inner join rooms on devices.RoomID = rooms.ID ";
			
				if(isset($_GET['show']))
				{
					if($_GET['show'] == "new")
					{
						?>
						
							<div class="infodiv">
								<form method="post" action="tickets.php">
								
									<textarea type="textarea" name="ticketdesc" class="cmnarea" required="required" placeholder="Hiba leírása..." autofocus></textarea><br>
									<input type="submit" name="newticketbutton" value="Küldés">
									
									<input type="hidden" name="deviceid" value="<?php print $_POST['deviceid'] ?>">
								
								</form>
							</div>
						<?php
						$_SESSION['snewticketbutton'] = 1;
					}
					else if($_GET['show'] == "edit")
					{
						$query = mysqli_query($con, "select status, priority from tickets where ticketID = '{$_POST['tcktid']}'");
						$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
						?>
						
						<div class="infodiv">
							<form method="post" action="tickets.php">
								<input type="hidden" name="tcktid" value="<?php print $_POST['tcktid']; ?>">
								Prioritás: 
								<select name="selprior" class="cmntext">
								
									<?php
									for($i = 0; $i < sizeof($ticketprior); $i++)
									{
										?>
										<option value="<?php print $i+1; ?>" <?php if($i+1 == $row['priority']) { ?> selected="selected"><?php } print $ticketprior[$i]; ?> </option>
										<?php
									}
									?>
								</select>
								<br>
								Státusz:
								<select name="selstat" class="cmntext">
								
									<?php
									for($i = 0; $i < sizeof($ticketstatus); $i++)
									{
										?>
										<option value="<?php print $i+1; ?>" <?php if($i+1 == $row['status']) { ?> selected="selected"><?php } print $ticketstatus[$i]; ?> </option>
										<?php
									}
									?>
									
								</select> 
								<br>
								<input type="submit" name="ticketeditsubmit" value="Módosít">
								
							
							</form>
						</div>
						<?php
					}
					else
					{
						$where = "";
						if($_GET['show'] == "own")
						{
							$where = "where tickets.userID = '{$_SESSION['UserID']}'";
							
							$_SESSION['lastpage'] = "show=own";
						}
						
						else if($_GET['show'] == "all")
						{
							if($_SESSION['UserGroup'] < 2) die;
							$_SESSION['lastpage'] = "show=all";
						}
						
						else if($_GET['show'] == "half")
						{
							if($_SESSION['UserGroup'] < 2) die;
							$where = "where tickets.status != 4";
							$_SESSION['lastpage'] = "show=half";
						}
						
						else if($_GET['show'] == "device")
						{
							$where = "where deviceID = '{$_GET['deviceid']}'";
						}
						
						$order2 = "";
						if($_GET['show'] == "half") $order2 = "tickets.priority desc, ";
						
						$minpg = $_POST['page']*ROWPERPAGETICKET-ROWPERPAGETICKET;
						
						$order = " order by ".$order2."tickets.{$_SESSION['sortby']} {$_SESSION['order']} LIMIT {$minpg}, ".ROWPERPAGETICKET;
						
						$query = mysqli_query($con, $prequery.$where.$order);
						?>
					
					
					<form method="post" action="<?php print $_SERVER['REQUEST_URI']; ?>">
						
						<label>Rendezés</label>
						<select name="sortby">
						
							<option value="Date" <?php if($_SESSION['sortby'] == "Date") { ?>selected="selected" <?php } ?>>Dátum</option>
							<option value="status" <?php if($_SESSION['sortby'] == "status") { ?>selected="selected" <?php } ?>>Státusz</option>
							<option value="priority" <?php if($_SESSION['sortby'] == "priority") { ?>selected="selected" <?php } ?>>Prioritás</option>
						
						</select>
						
						<select name="order">
						
							<option value="asc" <?php if($_SESSION['order'] == "asc") { ?>selected="selected" <?php } ?>>Növekvő</option>
							<option value="desc" <?php if($_SESSION['order'] == "desc") { ?>selected="selected" <?php } ?>>Csökkenő</option>
						
						</select>
						<input type="submit" name="sortbybtn" value="Rendez">
					
					</form>
					<div class="divlist">
					
					<?php
					if(mysqli_num_rows($query) != 0)
					{
						?>
						
						<table class="datatable">
						
						<tr class="borderbottom">
							<th>ticketID</th>
							<th>Feladó</th>
							<th>Eszköz neve</th>
							<th>Hiba leírása</th>
							<th>Terem neve</th>
							<th>Dátum</th>
							<th>Státusz</th>
							<th>Prioritás</th>
						</tr>
						
						<?php
						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
						{
							if(empty($row['username']))
							{	
								$row['username'] = "Törölt felhasználó";
							}
						?>
							<tr>
								<td><?php print $row['ticketID']; ?></td>
								<td width="15%"><?php print $row['username']." (".$row['lastname']." ".$row['firstname'].")"; ?></td>
								<td><?php print "<a href=tickets.php?show=device&deviceid={$row['DeviceID']}>".$row['DeviceName']."</a>"; ?></td>
								<td><?php print $row['description']; ?></td>
								<td><?php print "<a href=rooms.php?show=roomdevices&roomid={$row['RoomID']}>".$row['Roomname']."</a>"; ?></td>
								<td width="15%"><?php print $row['Date']; ?></td>
								<td width="15%"><?php print $ticketstatus[intval($row['status'])-1]; ?></td>
								<td><?php print $ticketprior[intval($row['priority'])-1]; ?></td>
								<td>
								<?php
									
									if($_SESSION["UserGroup"] >= 2)
									{											
									?>
										<form method="post" action="tickets.php" onsubmit="return DeleteWarning(this)">
											<button class="smlbutton tcktbuttons" name="delticketbtn">Törlés</button>
											<input type="hidden" name="tcktid" value="<?php print $row['ticketID']; ?>">
											<input type="hidden" name="page" value="<?php print $_POST['page']; ?>">
										</form>
									
									
										<form method="post" action="tickets.php?show=edit">
										
											<button class="smlbutton tcktbuttons" name="editticketbtn">Szerkesztés</button>
											<input type="hidden" name="tcktid" value="<?php print $row['ticketID']; ?>">
											
										</form>
									<?php
									}
									?>
								
									
								</td>
							</tr>
						<?php
						}								
						print_r($row);
						
						?>
						
						</table>
						<div class="pageselectdiv">
							<?php 
							$query = mysqli_query($con, "select * from tickets ".$where);
							$maxpage = ceil(mysqli_num_rows($query)/ROWPERPAGETICKET);
							
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
						<?php
					}
					else
					{
						if(!isset($_GET['deviceid'])) print "Nincsenek feladott hibajegyeid.";
						else print "Ehhez az eszközhöz nincsenek feladott hibajegyek.";
					}
					?>
					</div>
					<?php
					}
				}	
			?>
		</main>
		
	</body>	
</html>
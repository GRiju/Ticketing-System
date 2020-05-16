<?php
	require_once "config.php";
	require_once "staticelements.php";
	
	$_SESSION['webpage'] = "admin";
	
	if($_SESSION['UserGroup'] < 2) die;
?>

<html>
	<head>
		
	</head>
	
	<body>
		<header>
			<nav class="submenunav">
				<form class="menuform" method="post" action="users.php">
					<button class="menubutton" name=>Felhasználók</button>
				</form>
			</nav>
		</header>
		
		<main>
			<?php
				$text = "";
				//-
				$query = mysqli_query($con, "select deviceid, count(deviceid) dc, devices.DeviceName as dname
											from tickets, devices where devices.ID = tickets.deviceID
											group by deviceID order by dc DESC limit 1");
						
				$text .= "<p>A legtöbb hibajeggyel rendelkező eszköz: ";
				if(mysqli_num_rows($query) != 0)
				{
					$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
					$text .= "<a href='tickets.php?show=device&deviceid={$row['deviceid']}'>{$row['dname']} ({$row['dc']} darab)</a><br>";
				}
				else $text .= "Nincs adat";
				$text .= "</p>";
				
				//-
				$query = mysqli_query($con, "select deviceid, count(deviceid) dc, devices.DeviceName as dname
											from tickets, devices
											where devices.ID = tickets.deviceID and status != 4
											group by deviceID order by dc DESC limit 1");
				
				$text .= "<p>A legtöbb lezáratlan hibajeggyel rendelkező eszköz: ";
				if(mysqli_num_rows($query) != 0)
				{
					$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
					$text .= "<a href='tickets.php?show=device&deviceid={$row['deviceid']}'>{$row['dname']} ({$row['dc']} darab)</a><br>";
				}
				else $text .= "Nincs adat";
				$text .= "</p>";
				
				//-
				$query = mysqli_query($con, "select * from tickets");
				$all = mysqli_num_rows($query);
				
				$query = mysqli_query($con, "select * from tickets where status != 4");
				$half = mysqli_num_rows($query);
				
				if($all == 0)
				{
					$all = 1;
					$half = 1;
				}
				if($half == 0) $half = $all;
				
				$percent = abs(round(($half/$all)*100, 2)-100);
				
				$text .= "<p>A hibajegyek {$percent}%-a van <font color='green'><b>kész</b></font> státuszban. ";
				$text .= "</p>";
				
				//-
				$query = mysqli_query($con, "select roomid, count(roomid) rc, rooms.Roomname as rname
											from devices, rooms
											where rooms.ID = devices.RoomID
											group by roomid order by rc DESC limit 1");
				
				$text .= "<p>A legtöbb eszközzel rendelkező terem: ";
				if(mysqli_num_rows($query) != 0)
				{
					$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
					$text .= "<a href='rooms.php?show=roomdevices&roomid={$row['roomid']}'>{$row['rname']} ({$row['rc']} darab)</a><br>";
				}
				else $text .= "Nincs adat";
				$text .= "</p>";
				
				//-
				$query = mysqli_query($con, "select users.username, users.firstname, users.lastname, count(tickets.ticketID) as cticket
                                            from tickets, users
                                            where users.ID = tickets.userID
                                            group by userID order by cticket desc limit 1
											");
				
				$text .= "<p>A legtöbb hibajegyet feladó felhasználó: ";
				if(mysqli_num_rows($query) != 0)
				{
					$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
					$text .= $row['username']." (".$row['lastname']." ".$row['firstname'].") (".$row['cticket']." darab)";
				}
				else $text .= "Nincs adat";
				$text .= "</p>";
				
				
				
				?>
				
					<div class="statsdiv">
						<?php print $text; ?>
					</div>
				
				<?php
			
			?>
		</main>
		
	</body>
</html>
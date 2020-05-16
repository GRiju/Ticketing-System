<?php
	require_once "config.php";
	require_once "staticelements.php";
	
	$_SESSION['webpage'] = "index";
			
	//Hozzáadás
	if(isset($_SESSION['snewnewsbutton']) && isset($_POST['newnewsbutton']))
	{
		$query = mysqli_query($con, "select firstname, username from users where ID = '{$_SESSION['UserID']}'");
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		$hircim = mysqli_real_escape_string($con, $_POST['newnewsbuttontitle']);
		$hirszoveg = mysqli_real_escape_string($con, $_POST['newnewsbuttontext']);
		mysqli_query($con, "insert into news values (0, '{$row['firstname']} ({$row['username']})', now(), '{$hircim}', '{$hirszoveg}')");
		unset($_SESSION['snewnewsbutton']);
	}
	
	//Szerkesztés
	if(isset($_POST['editnewsbutton']))
	{
		$hircim = mysqli_real_escape_string($con, $_POST['editnewstitle']);
		$hirszoveg = mysqli_real_escape_string($con, $_POST['editnewstext']);
		mysqli_query($con, "update news set title = '{$hircim}', text = '{$hirszoveg}' where ID = {$_POST['newseditid']}");
	}
	
	//Törlés
	if(isset($_POST['newsdeletebtn']))
	{
		mysqli_query($con, "delete from news where ID={$_POST['newsdeletebtn']}");
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
					<form class="menuform" method="get" action="index.php">
						<button class="menubutton" id="newarticlebutton">Új hír írása</button>
						<input type="hidden" name="show" value="new">
					</form>
				</nav>
			<?php
			}
			?>
		</header>
		
		<main>
		
			<?php
			if(!isset($_GET['show']))
			{
				$query = mysqli_query($con, "select * from news order by date desc");
				if(mysqli_num_rows($query) != 0)
				{
					while($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
					{
						?>
						<div class="indexnewsdiv" id='newsdeletebtnq<?php print $row['ID'] ?>'>
							<p class="newsdate"><?php print $row['date']."<br>"."Írta: ".$row['author'] ?></p>
							<div class="indexnewscontentdiv">
								<h1 class="newsh1"><?php print $row['title']?></h1>
								<p class="newsp"><?php print nl2br($row['text']) ?></p>
							</div>
							
							<?php
							if($_SESSION['UserGroup'] >= 2)
							{
								?>
								 <form method="post" action="index.php"  onsubmit="return DeleteWarning(this)">
								
									<button class="smlbutton newsbuttons" name='newsdeletebtn' value='<?php print $row['ID'] ?>' id="">Törlés</button>
									
								 </form>
								 
								<form method="post" action="index.php?show=edit">
									<button class="smlbutton newsbuttons" name="newseditbtn" value='<?php print $row['ID'] ?>'>Szerkesztés</button>
									<input type="hidden" name="titleedit" value="<?php print $row['title']; ?>">
									<input type="hidden" name="textedit" value="<?php print $row['text']; ?>">
								</form>
							 <?php
							}
							?>
							
						</div>
						<?php
					}
				}
				mysqli_free_result($query);
			}
			else if($_GET['show'] == "new")
			{
				if($_SESSION['UserGroup'] < 2) die;
				?>
				<div class="infodiv">
					<form method="post" action="index.php">
						<input type="text" name="newnewsbuttontitle" required="required" class="newnewstitle" placeholder="Hír címe..." autofocus>
						<input type="submit" name="newnewsbutton" class="newnewsbutton" value="Hír kiírása"></br>
						<textarea placeholder="Hír szövege..." required="required" name="newnewsbuttontext" class="newnewstext" rows="15"></textarea></br>
						
					</form>
				</div>
				<?php
				$_SESSION['snewnewsbutton'] = 1;
			}
			else if($_GET['show'] == "edit")
			{
				if($_SESSION['UserGroup'] < 2) die;
				?>
				<div class="infodiv">
					<form method="post" action="index.php">
						<input type="text" name="editnewstitle" required="required" class="newnewstitle" placeholder="Hír címe..." value="<?php print $_POST['titleedit']; ?>" autofocus>
						<input type="submit" name="editnewsbutton" class="newnewsbutton" value="Módosít"><br>
						<textarea placeholder="Hír szövege..." required="required" name="editnewstext" class="newnewstext" rows="15"><?php print $_POST['textedit']; ?></textarea></br>
						<input type="hidden" name="newseditid" value="<?php print $_POST['newseditbtn']; ?>">
					</form>
				</div>
				<?php
			}
			?>
		</main>
	</body>
	
</html>
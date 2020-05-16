<?php
	session_start();
	
	define("SQLHOST", "localhost");
	define("SQLUSER", "root");
	define("SQLPASS", "");
	define("SQLDB", "ticketsys");
	
	define("ROWPERPAGETICKET", 5); //hibajegyek lapozásonkénti oldalszám
	define("ROWPERPAGEUSERS", 10); //felhasználók lapozásonkénti oldalszám
	
	$ticketstatus = array(
					"<font color='red'><b>Új hibajegy</b></a>",
					"<font color='yellow'><b>Feldolgozás alatt...</b></a>",
					"<font color='orange'><b>Ellenőrzés alatt...</b></a>",
					"<font color='green'><b>Kész</b></a>");
	$ticketprior = array(
					"<font color='green'><b>Általános</b></a>",
					"<font color='red'><b>Fontos</b></a>");
	
	
	$con = mysqli_connect(SQLHOST, SQLUSER, SQLPASS);
	if(!$con) die("Nem sikerült csatlakozni: ".mysqli_connect_error());
	mysqli_select_db($con, SQLDB);
	$con->set_charset('utf8');
	
	function alert($str)
	{
		echo "<script>alert('$str');</script>";
	}
?>

<html>
	<head>
		<link charset="utf-8" rel="stylesheet" href="css/mystyles.css" type="text/css"/>
		<title>Ticketing system</title>
		
		<script>
			function DeleteWarning()
			{
				return confirm("Biztos törölni akarod?");
			}
		
		</script>
	</head>
	
	<body>
		<footer>
			<p class="foot">Ticketing system By Oszvald Gergő @ 2018-2019</p>
		</footer>
	</body>
</html>
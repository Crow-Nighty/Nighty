<?php

error_reporting (E_ALL ^ E_NOTICE); /* 1st line (recommended) */

$allowedDomains = array("www.domain.com", "domain.com", "localhost", "localhost/TEST");

if (in_array($_SERVER['HTTP_HOST'], $allowedDomains)) {
} else {
	die("Domain Error");
}

ob_start();

if(file_exists("install.php") == "1"){
	header('Location: install.php');
	exit();
}

$uid = "";

include 'inc/database.php';

if($_GET['action'] == "key"){
	echo '
		<script>
			window.alert("Key: '.$key.'\nUID: '.$uid.'");
		</script>
	';
}

if (!isset($_SESSION)) { 
	session_start(); 
}

if (!isset($_SESSION['username'])) {
	header('Location: login.php?error=not-logged-in');
	exit();
}

$username = $_SESSION['username'];
$date = date("Y-m-d");
$datetime = date("Y-m-d H:i:s");

$result = mysqli_query($con, "SELECT * FROM `settings` LIMIT 1") or die(mysqli_error($con));
while($row = mysqli_fetch_assoc($result)){
	$website = $row['website'];
	$paypal = $row['paypal'];
	$footer = $row['footer'];
	$favicon = $row['favicon'];
}

?>
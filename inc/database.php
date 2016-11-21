<?php

$myhost = "localhost";
$myuser = "root2";
$mypass = "root2";
$mydb = "website";
$key = "";

$con = mysqli_connect($myhost, $myuser, $mypass, $mydb);

if (mysqli_connect_errno())
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>
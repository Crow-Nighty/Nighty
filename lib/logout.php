<?php

error_reporting (E_ALL ^ E_NOTICE); /* 1st line (recommended) */

if (!isset($_SESSION)) { session_start(); }

$_SESSION = array(); 

session_destroy(); 

header("Location: ../login.php");

?>
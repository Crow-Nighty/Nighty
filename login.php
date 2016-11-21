<?php

error_reporting (E_ALL ^ E_NOTICE); /* 1st line (recommended) */

ob_start();

if(file_exists("install.php") == "1"){
	header('Location: install.php');
	exit();
}

include 'inc/database.php';

$result = mysqli_query($con, "SELECT * FROM `settings` LIMIT 1") or die(mysqli_error($con));
while($row = mysqli_fetch_assoc($result)){
	$website = $row['website'];
	$favicon = $row['favicon'];
}

if (!isset($_SESSION)) { 
	session_start(); 
}

if (isset($_SESSION['username'])) {
	header('Location: index.php');
	exit();
}

if(isset($_POST['username']) && isset($_POST['password'])){

	$username = mysqli_real_escape_string($con, $_POST['username']);
	$password = mysqli_real_escape_string($con, md5($_POST['password']));
	
	$result = mysqli_query($con, "SELECT * FROM `users` WHERE `username` = '$username'") or die(mysqli_error($con));
	if(mysqli_num_rows($result) < 1){
		header("Location: login.php?error=incorrect-password");
	}
	while($row = mysqli_fetch_array($result)){
		if($password != $row['password']){
			header("Location: login.php?error=incorrect-password");
		}elseif($row['status'] == "0"){
			header("Location: login.php?error=banned");
		}else{
			$_SESSION['id'] = $row['id'];
			$_SESSION['username'] = $username;
			$_SESSION['email'] = $row['email'];
			$_SESSION['rank'] = $row['rank'];
			header("Location: index.php");
		}
	}
	
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="24/7">
    <meta name="keyword" content="">
    <link rel="shortcut icon" href="<?php echo $favicon;?>">

    <title><?php echo $website;?> - Login</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet">
	<!--external css-->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
	
</head>

  <body class="login-body">

    <div class="container">

      <form class="form-signin" action="login.php" method="POST">
        <h2 class="form-signin-heading"><?php echo $website;?></h2>
        <div class="login-wrap">
            <input type="text" id="username" name="username" class="form-control" placeholder="Username" autofocus>
            <input type="password" id="password" name="password" class="form-control" placeholder="Password">
            <button class="btn btn-lg btn-login btn-block" type="submit">Sign in</button>
	  </form>
            <div class="registration">
                Don't have an account yet?
                <a class="" href="register.php">
                    Create an account
                </a>
            </div>

        </div>

    </div>
	
	<?php 
	if($_GET['error'] == "banned"){
		echo '
			<div class="modal fade" id="error" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top: 15%; overflow-y: visible; display: none;">
				<div class="modal-dialog modal-sm">
					<div class="modal-content panel-danger">
						<div class="modal-header panel-heading">
							<center><h3 style="margin:0;"><i class="icon-warning-sign"></i> Error!</h3></center>
						</div>
						<div class="modal-body">
							<center>
								<strong>Your account has been banned.</strong>
							</center>
						</div>
					</div>
				</div>
			</div>
		';
	}

	if($_GET['error'] == "incorrect-password"){
		echo '
			<div class="modal fade" id="error" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top: 15%; overflow-y: visible; display: none;">
				<div class="modal-dialog modal-sm">
					<div class="modal-content panel-danger">
						<div class="modal-header panel-heading">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<center><h3 style="margin:0;"><i class="icon-warning-sign"></i> Error!</h3></center>
						</div>
						<div class="modal-body">
							<center>
								<strong>The password you entered was not correct.</strong>
							</center>
						</div>
					</div>
				</div>
			</div>
		';
	}
	
	if($_GET['error'] == "not-logged-in"){
		echo '
			<div class="modal fade" id="error" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top: 15%; overflow-y: visible; display: none;">
				<div class="modal-dialog modal-sm">
					<div class="modal-content panel-warning">
						<div class="modal-header panel-heading">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<center><h3 style="margin:0;"><i class="icon-warning-sign"></i> Error!</h3></center>
						</div>
						<div class="modal-body">
							<center>
								<strong>You must be logged in to do that.</strong>
							</center>
						</div>
					</div>
				</div>
			</div>
		';
	}
	?>

    <!-- js placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
	
	<?php
	if(isset($_GET['error'])){
		echo "<script type='text/javascript'>
				$(document).ready(function(){
				$('#error').modal('show');
				});
			  </script>"
		;
	}
	?>

  </body>
</html>

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

if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirmpassword']) && isset($_POST['email'])){

	$username = htmlentities(mysqli_real_escape_string($con, $_POST['username']));
	$password = mysqli_real_escape_string($con, md5($_POST['password']));
	$confirmpassword = mysqli_real_escape_string($con, md5($_POST['confirmpassword']));
	$email = mysqli_real_escape_string($con, $_POST['email']);
	
	if($password != $confirmpassword){
		die("The confirmation password was not equal to the password.");
	}
	
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("The email entered was not correct.");
    }
	
	$result = mysqli_query($con, "SELECT * FROM `users` WHERE `username` = '$username'") or die(mysqli_error($con));
	if(mysqli_num_rows($result) > 0){
		die("This username already exists.");
	}
	
	$result = mysqli_query($con, "SELECT * FROM `users` WHERE `email` = '$email'") or die(mysqli_error($con));
	if(mysqli_num_rows($result) > 0){
		die("This email already exists.");
	}
	
	$ip = mysqli_real_escape_string($con, $_SERVER['REMOTE_ADDR']);
	$date = date('Y-m-d');
	
	mysqli_query($con, "INSERT INTO `users` (`username`, `password`, `email`, `date`, `ip`) VALUES ('$username', '$password', '$email', '$date', '$ip')") or die(mysqli_error($con));
	
	header("Location: login.php?action=registered");
	
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

    <title><?php echo $website;?> - Registration</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet">
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

      <form class="form-signin" action="register.php" method="POST">
        <h2 class="form-signin-heading"><?php echo $website;?></h2>
        <div class="login-wrap">
            <input type="text" id="username" name="username" class="form-control" placeholder="Username" autofocus>
            <input type="password" id="password" name="password" class="form-control" placeholder="Password">
			<input type="password" id="confirmpassword" name="confirmpassword" class="form-control" placeholder="Confirm Password">
			<input type="text" id="email" name="email" class="form-control" placeholder="Email">
            <button class="btn btn-lg btn-login btn-block" type="submit">Register</button>
	  </form>

			<div class="registration">
                Already have an account?&nbsp
                <a class="" href="login.php">
                    Sign In
                </a>
            </div>
			
        </div>

    </div>



    <!-- js placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>

  </body>
</html>

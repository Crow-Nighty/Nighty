<?php

error_reporting (E_ALL ^ E_NOTICE); /* 1st line (recommended) */

ob_start();

if(isset($_POST['website']) && isset($_POST['paypal']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['passwordconf']) && isset($_POST['email']) && isset($_POST['mysqlhost']) && isset($_POST['mysqlusername']) && isset($_POST['mysqlpassword']) && isset($_POST['mysqldatabase'])){

	if($_POST['password'] != $_POST['passwordconf']){
		die('Password confirmation was not equal to the password');
	}
	
	$mysqlhost = trim($_POST['mysqlhost']);
	$mysqlusername = trim($_POST['mysqlusername']);
	$mysqlpassword = trim($_POST['mysqlpassword']);
	$mysqldatabase = trim($_POST['mysqldatabase']);

	if (!$con = mysqli_connect($mysqlhost, $mysqlusername, $mysqlpassword, $mysqldatabase)) {
		die('Database connection failed: ' . mysqli_connect_error());
	}

	$fname = "inc/database.php";
	$fhandle = fopen($fname,"r");
	$content = fread($fhandle,filesize($fname));

	$current = array("databasevariable1", "databasevariable2", "databasevariable3", "databasevariable4");
	$replace = array($mysqlhost, $mysqlusername, $mysqlpassword, $mysqldatabase);

	$content = str_replace($current, $replace, $content);

	$fhandle = fopen($fname,"w");
	fwrite($fhandle,$content);
	fclose($fhandle);
	
	mysqli_query($con, "
		CREATE TABLE IF NOT EXISTS `generators` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  PRIMARY KEY (`id`)
		)
	") or die(mysqli_error($con));

	mysqli_query($con, "
		CREATE TABLE IF NOT EXISTS `news` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `message` mediumtext COLLATE latin1_general_ci NOT NULL,
		  `writer` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `date` datetime NOT NULL,
		  PRIMARY KEY (`id`)
		)
	") or die(mysqli_error($con));
	
	mysqli_query($con, "
		CREATE TABLE IF NOT EXISTS `packages` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `price` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `length` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `accounts` int(11) NOT NULL,
		  `generator` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  PRIMARY KEY (`id`)
		)
	") or die(mysqli_error($con));
	
	mysqli_query($con, "
		CREATE TABLE IF NOT EXISTS `settings` (
		  `website` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `paypal` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `footer` varchar(1000) COLLATE latin1_general_ci NOT NULL DEFAULT 'Created by <a target=_blank href=https://www.github.com/welshman/FreeGen>Welshman</a>',
		  `favicon` varchar(1000) COLLATE latin1_general_ci NOT NULL DEFAULT 'img/favicon.png'
		)
	") or die(mysqli_error($con));
	
	mysqli_query($con, "
		CREATE TABLE IF NOT EXISTS `statistics` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `username` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `generated` int(11) NOT NULL,
		  `date` date NOT NULL,
		  PRIMARY KEY (`id`)
		)
	") or die(mysqli_error($con));
	
	mysqli_query($con, "
		CREATE TABLE IF NOT EXISTS `subscriptions` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `username` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `date` date NOT NULL,
		  `price` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `payment` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `package` int(11) NOT NULL,
		  `expires` date NOT NULL,
		  `txn_id` varchar(1000) COLLATE latin1_general_ci NOT NULL,
		  `active` int(1) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		)
	") or die(mysqli_error($con));
	
	mysqli_query($con, "
		CREATE TABLE IF NOT EXISTS `support` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `from` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `to` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `subject` varchar(1000) COLLATE latin1_general_ci NOT NULL,
		  `message` mediumtext COLLATE latin1_general_ci NOT NULL,
		  `date` date NOT NULL,
		  `read` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		)
	") or die(mysqli_error($con));
	
	mysqli_query($con, "
		CREATE TABLE IF NOT EXISTS `users` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `username` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `password` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `email` varchar(100) COLLATE latin1_general_ci NOT NULL,
		  `rank` int(1) NOT NULL DEFAULT '1',
		  `ip` varchar(20) COLLATE latin1_general_ci NOT NULL,
		  `date` date DEFAULT NULL,
		  `status` int(1) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		)
	") or die(mysqli_error($con));

	$username = mysqli_real_escape_string($con, $_POST['username']);
	$password = md5(mysqli_real_escape_string($con, $_POST['password']));
	$email = mysqli_real_escape_string($con, $_POST['email']);
	$date = date("Y-m-d");
	$ip = $_SERVER['REMOTE_ADDR'];

	mysqli_query($con, "
		INSERT INTO `users` (`username`, `password`, `email`, `rank`, `ip`, `date`) VALUES
		('$username', '$password', '$email', '5', '$ip', '$date')
	") or die(mysqli_error($con));

	$website = mysqli_real_escape_string($con, $_POST['website']);
	$paypal = mysqli_real_escape_string($con, $_POST['paypal']);

	mysqli_query($con, "
		INSERT INTO `settings` (`website`, `paypal`) VALUES
		('$website', '$paypal')
	") or die(mysqli_error($con));

	echo "<script>
	alert('Succesfully Installed!');
	</script>";

	unlink(__FILE__);
	header('Location: login.php');

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
    <link rel="shortcut icon" href="img/favicon.png">

    <title>Generator Source - Installation</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet">
	<!--external css-->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
	<link rel="stylesheet" type="text/css" href="css/jquery.steps.css" />
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
	
</head>

<body>

	  <section class="wrapper">
		  <!-- page start-->
		  <div class="row">
			  <div class="col-lg-12">
			  <!--progress bar start-->
			  <section class="panel">
				  <header class="panel-heading">
					  Installation
				  </header>
				  <div class="panel-body">
					  <form id="wizard-validation-form" action="install.php" method="POST">
						  <div>
							  <h3>Step 1</h3>
							  <section>
								  <center><H2>Website Settings</H2></center>
								  </br>
								  <div class="form-group clearfix">
									  <label class="col-lg-2 control-label">Website name *</label>
									  <div class="col-lg-10">
										  <input class="form-control required" name="website" type="text" placeholder="My Generator">
									  </div>
								  </div>
								  <div class="form-group clearfix">
									  <label class="col-lg-2 control-label">Paypal Address *</label>
									  <div class="col-lg-10">
										  <input class="form-control required" name="paypal" type="email" placeholder="name@domain.com">
									  </div>
								  </div>
								  <div class="form-group clearfix">
									  <label class="col-lg-12 control-label ">(*) Required</label>
								  </div>
							  </section>
							  <h3>Step 2</h3>
							  <section>
								  <center><H2>Admin Account</H2></center>
								  </br>
								  <div class="form-group clearfix">
									  <label class="col-lg-2 control-label">Username *</label>
									  <div class="col-lg-10">
										  <input class="form-control required" name="username" type="text" placeholder="Username">
									  </div>
								  </div>
								  <div class="form-group clearfix">
									  <label class="col-lg-2 control-label">Password *</label>
									  <div class="col-lg-10">
										  <input class="form-control required" name="password" type="password" placeholder="Password">
									  </div>
								  </div>
								  <div class="form-group clearfix">
									  <label class="col-lg-2 control-label">Password Confirmation *</label>
									  <div class="col-lg-10">
										  <input class="form-control required" name="passwordconf" type="password" placeholder="Password Confirmation">
									  </div>
								  </div>
								  <div class="form-group clearfix">
									  <label class="col-lg-2 control-label">Email *</label>
									  <div class="col-lg-10">
										  <input class="form-control required" name="email" type="email" placeholder="name@domain.com">
									  </div>
								  </div>
								  <div class="form-group clearfix">
									  <label class="col-lg-12 control-label ">(*) Required</label>
								  </div>
							  </section>
							  <h3>Step 3</h3>
							  <section>
								  <center><H2>MYSQL Settings</H2></center>
								  </br>
								  <div class="form-group clearfix">
									  <label class="col-lg-2 control-label">MYSQL Host *</label>
									  <div class="col-lg-10">
										  <input class="form-control required" name="mysqlhost" type="text" placeholder="localhost">
									  </div>
								  </div>
								  <div class="form-group clearfix">
									  <label class="col-lg-2 control-label">MYSQL Username *</label>
									  <div class="col-lg-10">
										  <input class="form-control required" name="mysqlusername" type="text" placeholder="root">
									  </div>
								  </div>
								  <div class="form-group clearfix">
									  <label class="col-lg-2 control-label">MYSQL Password *</label>
									  <div class="col-lg-10">
										  <input class="form-control required" name="mysqlpassword" type="password" placeholder="Password">
									  </div>
								  </div>
								  <div class="form-group clearfix">
									  <label class="col-lg-2 control-label">MYSQL Database *</label>
									  <div class="col-lg-10">
										  <input class="form-control required" name="mysqldatabase" type="text" placeholder="User_Database">
									  </div>
								  </div>
								  <div class="form-group clearfix">
									  <label class="col-lg-12 control-label ">(*) Required</label>
								  </div>
							  </section>
						  </div>
					  </form>
				  </div>
			  </section>

		      </div>

		  </div>
	  </div>
	  <!-- page end-->
  </section>



    <!-- js placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.steps.min.js" type="text/javascript"></script>
	<script src="js/jquery.validate.min.js" type="text/javascript"></script>
	<script src="js/jquery.stepy.js"></script>
	
	<script type="text/javascript">
	  $(document).ready(function () {
		  var form = $("#wizard-validation-form");
		  form.validate({
			  errorPlacement: function errorPlacement(error, element) {
				  element.after(error);
			  }
		  });
		  form.children("div").steps({
			  headerTag: "h3",
			  bodyTag: "section",
			  transitionEffect: "slideLeft",
			  onStepChanging: function (event, currentIndex, newIndex) {
				  form.validate().settings.ignore = ":disabled,:hidden";
				  return form.valid();
			  },
			  onFinishing: function (event, currentIndex) {
				  form.validate().settings.ignore = ":disabled";
				  return form.valid();
			  },
			  onFinished: function (event, currentIndex) {
				  document.getElementById("wizard-validation-form").submit();
			  }
		  });


	  });
	</script>

</body>
</html>

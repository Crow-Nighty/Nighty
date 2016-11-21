<?php

error_reporting (E_ALL ^ E_NOTICE); /* 1st line (recommended) */

include "inc/header.php";

if ($_SESSION['rank'] < "5") {
	header('Location: index.php?error=no-admin');
	exit();
}

$profit = 0;

$result = mysqli_query($con, "SELECT * FROM `subscriptions`") or die(mysqli_error($con));
while($row = mysqli_fetch_assoc($result)) {
	$profit = $profit + $row['price'];
}

$profittoday = 0;

$result = mysqli_query($con, "SELECT * FROM `subscriptions` WHERE `date` = '$date'") or die(mysqli_error($con));
while($row = mysqli_fetch_assoc($result)) {
	$profittoday = $profittoday + $row['price'];
}

$result = mysqli_query($con, "SELECT * FROM `subscriptions` WHERE `active` = '1' AND `expires` >= '$date'") or die(mysqli_error($con));
$activesubscriptions = mysqli_num_rows($result);

$result = mysqli_query($con, "SELECT * FROM `users`") or die(mysqli_error($con));
$totalusers = mysqli_num_rows($result);

if (isset($_POST['addgenerator'])){
	$name = mysqli_real_escape_string($con, $_POST['addgenerator']);
	mysqli_query($con, "INSERT INTO `generators` (`name`) VALUES ('$name')") or die(mysqli_error($con));
	
	$result = mysqli_query($con, "SELECT * FROM `generators` WHERE `name` = '$name'") or die(mysqli_error($con));
	while($row = mysqli_fetch_assoc($result)) {
		$accountid = $row['id'];
	}
	
	mysqli_query($con, "CREATE TABLE `generator$accountid` (id INT NOT NULL AUTO_INCREMENT,alt VARCHAR(1000),status INT(1) DEFAULT '1',primary key (id))") or die(mysqli_error($con));
}

if (isset($_GET['deletegenerator'])){
	$id = mysqli_real_escape_string($con, $_GET['deletegenerator']);
	mysqli_query($con, "DROP TABLE `generator$id`") or die(mysqli_error($con));
	mysqli_query($con, "DELETE FROM `generators` WHERE `id` = '$id'") or die(mysqli_error($con));
	echo '
		<script>
			window.history.replaceState("object or string", "Title", "/admin-manage.php");
		</script>
	';
}

if (isset($_POST['editgenerator']) & isset($_POST['generatorid'])){
	$id = mysqli_real_escape_string($con, $_POST['generatorid']);
	$name = mysqli_real_escape_string($con, $_POST['editgenerator']);
	mysqli_query($con, "UPDATE `generators` SET `name` = '$name' WHERE `id` = '$id'") or die(mysqli_error($con));
}

if (isset($_POST['alts']) & isset($_POST['generator'])){
	$id = mysqli_real_escape_string($con, $_POST['generator']);
	mysqli_query($con,"DELETE FROM `generator$id`") or die(mysqli_error($con));
	$values = htmlspecialchars($_POST['alts']);
	$array = explode("\n", $values);
	foreach($array as $line){
		$line = mysqli_real_escape_string($con, $line);
		if (!empty($line)) {
			mysqli_query($con, "INSERT INTO `generator$id` (`alt`) VALUES ('$line')") or die(mysqli_error($con));
		}
	}
}

if (isset($_POST['addpackage']) & isset($_POST['price']) & isset($_POST['generator']) & isset($_POST['length'])){
	$name = mysqli_real_escape_string($con, $_POST['addpackage']);
	$price = mysqli_real_escape_string($con, $_POST['price']);
	$generator = mysqli_real_escape_string($con, $_POST['generator']);
	$max = mysqli_real_escape_string($con, $_POST['max']);
	$length = mysqli_real_escape_string($con, $_POST['length']);
	mysqli_query($con, "INSERT INTO `packages` (`name`, `price`, `length`, `accounts`, `generator`) VALUES ('$name', '$price', '$length', '$max', '$generator')") or die(mysqli_error($con));
}

if (isset($_GET['deletepackage'])){
	$id = mysqli_real_escape_string($con, $_GET['deletepackage']);
	mysqli_query($con, "DELETE FROM `packages` WHERE `id` = '$id'") or die(mysqli_error($con));
	echo '
		<script>
			window.history.replaceState("object or string", "Title", "/admin-manage.php");
		</script>
	';
}

if (isset($_POST['editpackage']) & isset($_POST['packageid']) & isset($_POST['editprice']) & isset($_POST['editgenerator']) & isset($_POST['editlength'])){
	$id = mysqli_real_escape_string($con, $_POST['packageid']);
	$name = mysqli_real_escape_string($con, $_POST['editpackage']);
	$price = mysqli_real_escape_string($con, $_POST['editprice']);
	$generator = mysqli_real_escape_string($con, $_POST['editgenerator']);
	$length = mysqli_real_escape_string($con, $_POST['editlength']);
	$max = mysqli_real_escape_string($con, $_POST['editmax']);
	mysqli_query($con, "UPDATE `packages` SET `name` = '$name' WHERE `id` = '$id'") or die(mysqli_error($con));
	mysqli_query($con, "UPDATE `packages` SET `price` = '$price' WHERE `id` = '$id'") or die(mysqli_error($con));
	mysqli_query($con, "UPDATE `packages` SET `generator` = '$generator' WHERE `id` = '$id'") or die(mysqli_error($con));
	mysqli_query($con, "UPDATE `packages` SET `length` = '$length' WHERE `id` = '$id'") or die(mysqli_error($con));
	mysqli_query($con, "UPDATE `packages` SET `accounts` = '$max' WHERE `id` = '$id'") or die(mysqli_error($con));
}

if (isset($_POST['website']) & isset($_POST['paypal'])){
	$website = mysqli_real_escape_string($con, $_POST['website']);
	$paypal = mysqli_real_escape_string($con, $_POST['paypal']);
	$footer = mysqli_real_escape_string($con, $_POST['footer']);
	$favicon = mysqli_real_escape_string($con, $_POST['favicon']);
	mysqli_query($con, "UPDATE `settings` SET `website` = '$website'") or die(mysqli_error($con));
	mysqli_query($con, "UPDATE `settings` SET `paypal` = '$paypal'") or die(mysqli_error($con));
	mysqli_query($con, "UPDATE `settings` SET `footer` = '$footer'") or die(mysqli_error($con));
	mysqli_query($con, "UPDATE `settings` SET `favicon` = '$favicon'") or die(mysqli_error($con));
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

    <title><?php echo $website;?> - Administration</title>

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

  <body>

  <section id="container" >
      <!--header start-->
      <header class="header white-bg">
            <div class="sidebar-toggle-box">
                <div data-original-title="Toggle Navigation" data-placement="right" class="icon-reorder tooltips"></div>
            </div>
            <!--logo start-->
            <a href="index.php" class="logo"><?php echo $website;?></a>
            <!--logo end-->
            <div class="top-nav ">
                <!--  notification start -->
                <ul class="nav pull-right top-menu">
                    <!-- inbox dropdown start-->
					<?php
						$result = mysqli_query($con, "SELECT * FROM `support` WHERE `to` = '$username' AND `read` = '0' ORDER BY `id`");
						$messages = mysqli_num_rows($result);
							if($messages > 0){
								echo '
										<li id="header_inbox_bar" class="dropdown">
											<a data-toggle="dropdown" class="dropdown-toggle" href="#">
												<i class="icon-envelope-alt"></i>
												<span class="badge bg-important">'.$messages.'</span>
											</a>
											<ul class="dropdown-menu extended inbox">
												<div class="notify-arrow notify-arrow-red"></div>
												<li>
													<p class="red">You have '.$messages.' new messages</p>
												</li>
								';
								while ($row = mysqli_fetch_assoc($result)) {
									echo '
												<li>
													<a href="support.php">
														<span class="subject">
														<span class="from">'.$row['subject'].'</span>
														<span class="time">'.$row['date'].'</span>
														</span>
														<span class="message">
															'.$row['message'].'
														</span>
													</a>
												</li>
											';
								}
								echo '
												<li>
													<a href="support.php">See all messages</a>
												</li>
											</ul>
										</li>
								';
							}else{
							echo '
								<li id="header_inbox_bar" class="dropdown">
									<a data-toggle="dropdown" class="dropdown-toggle" href="#">
										<i class="icon-envelope-alt"></i>
										<span class="badge bg-important">0</span>
									</a>
									<ul class="dropdown-menu extended inbox">
										<li>
											<p class="red">You have '.$messages.' new messages</p>
										</li>
										<li>
											<a href="support.php">See all messages</a>
										</li>
									</ul>
								</li>
							';
							}
					?>			
                    <!-- inbox dropdown end -->
            </div>
        </header>
      <!--header end-->
      <!--sidebar start-->
      <aside>
          <div id="sidebar"  class="nav-collapse ">
              <!-- sidebar menu start-->
              <ul class="sidebar-menu" id="nav-accordion">
                  <li>
                      <a href="index.php">
                          <i class="icon-dashboard"></i>
                          <span>Dashboard</span>
                      </a>
                  </li>
                  <li>
                      <a href="purchase.php">
                          <i class="icon-shopping-cart"></i>
                          <span>Purchase</span>
                      </a>
                  </li>
                  <li>
                      <a href="generator.php">
                          <i class="icon-refresh"></i>
                          <span>Generator</span>
                      </a>
                  </li>
				  <li>
                      <a href="support.php">
                          <i class="icon-envelope"></i>
                          <span>Support</span>
                      </a>
                  </li>
				  <li>
                      <a href="lib/logout.php">
                          <i class="icon-key"></i>
                          <span>Log Out</span>
                      </a>
                  </li>
				  <?php
					if (($_SESSION['rank']) == "5") {
                        echo '
						  <legend style="margin-bottom: 5px;"></legend>
						  <li class="sub-menu">
							  <a class="active" href="javascript:;" >
								  <i class="icon-laptop"></i>
								  <span>Administration</span>
							  </a>
							  <ul class="sub">
								  <li><a  href="admin-manage.php">Manage</a></li>
								  <li><a  href="admin-support.php">Support</a></li>
								  <li><a  href="admin-flagged.php">Flagged</a></li>
								  <li><a  href="admin-news.php">News</a></li>
								  <li><a  href="admin-subscriptions.php">Subscriptions</a></li>
								  <li><a  href="admin-users.php">Users</a></li>
							  </ul>
						  </li>
						';
					}
				  ?>
              </ul>
              <!-- sidebar menu end-->
          </div>
      </aside>
      <!--sidebar end-->
      <!--main content start-->
      <section id="main-content">
          <section class="wrapper">
              <!--state overview start-->
              <div class="row state-overview">
                  <div class="col-lg-3 col-sm-6">
                      <section class="panel">
                          <div class="symbol terques">
                              <i class="icon-user"></i>
                          </div>
                          <div class="value">
                              <h1 class="count">
                                  <?php echo $totalusers;?>
                              </h1>
                              <p>Total Users</p>
                          </div>
                      </section>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                      <section class="panel">
                          <div class="symbol red">
                              <i class="icon-tags"></i>
                          </div>
                          <div class="value">
                              <h1 class=" count2">
                                  <?php echo $activesubscriptions;?>
                              </h1>
                              <p>Active Subscriptions</p>
                          </div>
                      </section>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                      <section class="panel">
                          <div class="symbol yellow">
                              <i class="icon-dollar"></i>
                          </div>
                          <div class="value">
                              <h1 class=" count3">
                                  $<?php echo $profit;?>
                              </h1>
                              <p>Total Profit</p>
                          </div>
                      </section>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                      <section class="panel">
                          <div class="symbol blue">
                              <i class="icon-dollar"></i>
                          </div>
                          <div class="value">
                              <h1 class=" count3">
                                  $<?php echo $profittoday;?>
                              </h1>
                              <p>Today's Profit</p>
                          </div>
                      </section>
                  </div>
              </div>
              <!--state overview end-->

              <div class="row">
				  <div class="col-lg-4">
					  <section class="panel">
						  <div class="panel-body">
							  <div class="task-thumb-details">
								  <h1>Manage Generators</h1>
							  </div>
							  <legend></legend>
							  <div id="collapse">
								<button class="btn btn-info btn-block" data-toggle="collapse" data-target=".addgenerator" data-parent="#collapse"><i class="icon-plus"></i> Add Generator</button></br>
								<form action="admin-manage.php" method="POST">
									<div class="addgenerator sublinks collapse">
										<legend></legend>
										<input name="addgenerator" type="text" class="form-control" placeholder="Name (Ex. Netflix)"></br>
										<button type="submit" class="btn btn-primary btn-block"><i class="icon-plus"></i> Add Generator</button></br>
									</div>
								</form>
							  </div>
							  <legend></legend>
							  <div class="panel-group" id="accordion">
								<?php
								$accountsquery = mysqli_query($con, "SELECT * FROM `generators`") or die(mysqli_error($con));
								while($row = mysqli_fetch_assoc($accountsquery)){
									$generatorid = $row[id];
									$getgeneratorsquery = mysqli_query($con, "SELECT * FROM `generator$generatorid`") or die(mysqli_error($con));
									$generatoramount = mysqli_num_rows($getgeneratorsquery);
									echo '
									  <div class="panel panel-info">
										  <div class="panel-heading">
											  <h4 class="panel-title">
												  <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse'.$row[id].'" aria-expanded="false">'.$row[name].'&nbsp <span class="badge bg-success">'.$generatoramount.'</span></a>
												  <a href="admin-manage.php?deletegenerator='.$row[id].'" class="btn btn-xs btn-danger pull-right"><i class="icon-remove"></i></a>
												  <a class="btn btn-primary btn-xs pull-right" data-toggle="modal" href="#editgenerator" data-generator="'.$row['name'].'" data-generatorid="'.$row['id'].'"><i class="icon-pencil"></i></a>
											  </h4>
										  </div>
										  <div id="collapse'.$row[id].'" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
											  <div class="panel-body" style="background:#F1F2F7;">
												  <form action="admin-manage.php" method="POST">
													<input type="hidden" name="generator" value="'.$row[id].'">
													<textarea name="alts" rows="5" class="form-control" placeholder="username/email:password username/email:password">';
													while($row = mysqli_fetch_assoc($getgeneratorsquery))
													{
														echo $row['alt']."\n";
													}
													echo '</textarea>
													<br>
													<button type="submit" class="btn btn-info btn-large btn-block">Update Alts</button>
												  </form>
											  </div>
										  </div>
									  </div></br>
									  <legend></legend>
									';
								}
								?>
							  </div>
						  </div>
					  </section>
				  </div>
				  <div class="col-lg-4">
					  <section class="panel">
						  <div class="panel-body">
							  <div class="task-thumb-details">
								  <h1>Manage Packages</h1>
							  </div>
							  <legend></legend>
							  <div id="collapse">
								<button class="btn btn-info btn-block" data-toggle="collapse" data-target=".addpackage" data-parent="#collapse"><i class="icon-plus"></i> Add Package</button></br>
								<form action="admin-manage.php" method="POST">
									<div class="addpackage sublinks collapse">
										<legend></legend>
										<input name="addpackage" type="text" class="form-control" placeholder="Name (Ex. Gold Package)"></br>
										<input name="price" type="text" class="form-control" placeholder="Price (Ex. 0.01)"></br>
										<select name="generator" class="form-control">
											<option value="" selected>All Generators</option>
											<?php
												$accountsquery = mysqli_query($con, "SELECT * FROM `generators`") or die(mysqli_error($con));
												while($row = mysqli_fetch_assoc($accountsquery)){
													echo '<option value="'.$row[id].'">'.$row[name].'</option>';
												}
											?>
										</select></br>
										<select name="length" class="form-control">
											<option value="Lifetime" selected>Lifetime</option>
											<option value="1 Day">1 Day</option>
											<option value="3 Days">3 Days</option>
											<option value="1 Week">1 Week</option>
											<option value="1 Month">1 Month</option>
											<option value="2 Months">2 Months</option>
											<option value="3 Months">3 Months</option>
											<option value="4 Months">4 Months</option>
											<option value="5 Months">5 Months</option>
											<option value="6 Months">6 Months</option>
											<option value="7 Months">7 Months</option>
											<option value="8 Months">8 Months</option>
											<option value="9 Months">9 Months</option>
											<option value="10 Months">10 Months</option>
											<option value="11 Months">11 Months</option>
											<option value="12 Months">12 Months</option>
										</select></br>
										<input type="number" name="max" class="form-control" placeholder="Max accounts a day (0 = unlimited)"></br>
										<button type="submit" class="btn btn-primary btn-block"><i class="icon-plus"></i> Add Package</button></br>
									</div>
								</form>
							  </div>
							  <legend></legend>
							  <div class="panel-group" id="accordion">
								<?php
								$packagesquery = mysqli_query($con, "SELECT * FROM `packages`") or die(mysqli_error($con));
								while($row = mysqli_fetch_assoc($packagesquery)){
									echo '
									  <div class="panel panel-info">
										  <div class="panel-heading">
											  <h4 class="panel-title">
												  <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#2collapse'.$row[id].'" aria-expanded="false">'.$row[name].'&nbsp <span class="badge bg-success">$'.$row[price].'</span></a>
												  <a href="admin-manage.php?deletepackage='.$row[id].'" class="btn btn-xs btn-danger pull-right"><i class="icon-remove"></i></a>
												  <a class="btn btn-primary btn-xs pull-right" data-toggle="modal" href="#editpackage" data-package="'.$row['name'].'" data-packageid="'.$row['id'].'" data-price="'.$row['price'].'" data-length="'.$row['length'].'" data-accounts="'.$row['accounts'].'" data-generator="'.$row['generator'].'"><i class="icon-pencil"></i></a>
											  </h4>
										  </div>
										  <div id="2collapse'.$row[id].'" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
											  <div class="panel-body" style="background:#F1F2F7;">
												  <form action="admin-manage.php" method="POST">
													<input type="hidden" name="package" value="'.$row[id].'">
													<br>
													<button type="submit" class="btn btn-info btn-large btn-block"><i class="icon-edit"></i> Edit Package</button>
												  </form>
											  </div>
										  </div>
									  </div></br>
									  <legend></legend>
									';
								}
								?>
							  </div>
						  </div>
					  </section>
				  </div>
				  <div class="col-lg-4">
					  <section class="panel">
						  <div class="panel-body">
							  <div class="task-thumb-details">
								  <h1>Manage Settings</h1>
							  </div>
							  <legend></legend>
							  <?php
								$accountsquery = mysqli_query($con, "SELECT * FROM `settings` LIMIT 1") or die(mysqli_error($con));
								while($row = mysqli_fetch_assoc($accountsquery)){
									echo '
									  <form class="form-horizontal" action="admin-manage.php" method="POST">
										  <div class="form-group">
											  <label for="website" class="col-lg-2 col-sm-2 control-label">Name</label>
											  <div class="col-lg-10">
												  <input type="text" class="form-control" name="website" placeholder="Website Name" value="'.$row['website'].'">
											  </div>
										  </div>
										  <div class="form-group">
											  <label for="paypal" class="col-lg-2 col-sm-2 control-label">Paypal</label>
											  <div class="col-lg-10">
												  <input type="email" class="form-control" name="paypal" placeholder="name@domain.com" value="'.$row['paypal'].'">
											  </div>
										  </div>
										  <div class="form-group">
											  <label for="footer" class="col-lg-2 col-sm-2 control-label">Footer</label>
											  <div class="col-lg-10">
												  <input type="text" class="form-control" name="footer" placeholder="© 2014-2015 | Name Inc."  value="'.$row['footer'].'" disabled>
											  </div>
										  </div>
										  <div class="form-group">
											  <label for="favicon" class="col-lg-2 col-sm-2 control-label">Favicon</label>
											  <div class="col-lg-10">
												  <input type="url" class="form-control" name="favicon" placeholder="http://domain.com/image.jpg"  value="'.$row['favicon'].'">
											  </div>
										  </div>
										  <button type="submit" class="btn btn-info btn-large btn-block"><i class="icon-edit"></i> Update Settings</button>
									  </form>
									';
								}
							  ?>
						  </div>
					  </section>
				  </div>
              </div>

          </section>
		  
		  <!-- Modal -->
		  <div class="modal fade" id="editgenerator" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-sm">
				  <div class="modal-content">
					  <div class="modal-header">
						  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						  <h4 class="modal-title">Edit Generator</h4>
					  </div>
					  <div class="modal-body">
					   <form action="admin-manage.php" method="POST">
					    <input type="hidden" name="generatorid">
						<div class="form-group">
						  <label>Name</label>
						  <input type="text" class="form-control" name="editgenerator">
						</div>
					  </div>
					  <div class="modal-footer">
						<button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
						<button class="btn btn-warning" type="submit"> Update</button>
                      </div>
					   </form>
				  </div>
			  </div>
		  </div>
		  <!-- modal -->
		  
		  <!-- Modal -->
		  <div class="modal fade" id="editpackage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-sm">
				  <div class="modal-content">
					  <div class="modal-header">
						  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						  <h4 class="modal-title">Edit Package</h4>
					  </div>
					  <div class="modal-body">
					   <form action="admin-manage.php" method="POST">
					    <input type="hidden" name="packageid">
						<div class="form-group">
						  <label>Name</label>
						  <input type="text" class="form-control" name="editpackage">
						</div>
						<div class="form-group">
						  <label>Price</label>
						  <input type="text" class="form-control" name="editprice">
						</div>
						<div class="form-group">
							<label>Generator(s)</label>
							<select name="editgenerator" class="form-control">
								<option value="">All Generators</option>
								<?php
									$accountsquery = mysqli_query($con, "SELECT * FROM `generators`") or die(mysqli_error($con));
									while($row = mysqli_fetch_assoc($accountsquery)){
										echo '<option value="'.$row[id].'">'.$row[name].'</option>';
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<label>Length</label>
							<select name="editlength" class="form-control">
								<option value="Lifetime">Lifetime</option>
								<option value="1 Day">1 Day</option>
								<option value="3 Days">3 Days</option>
								<option value="1 Week">1 Week</option>
								<option value="1 Month">1 Month</option>
								<option value="2 Months">2 Months</option>
								<option value="3 Months">3 Months</option>
								<option value="4 Months">4 Months</option>
								<option value="5 Months">5 Months</option>
								<option value="6 Months">6 Months</option>
								<option value="7 Months">7 Months</option>
								<option value="8 Months">8 Months</option>
								<option value="9 Months">9 Months</option>
								<option value="10 Months">10 Months</option>
								<option value="11 Months">11 Months</option>
								<option value="12 Months">12 Months</option>
							</select>
						</div>
						<div class="form-group">
							<label>Max accounts a day</label>
							<input type="number" name="editmax" class="form-control" placeholder="(0 = unlimited)">
						</div>
					  </div>
					  <div class="modal-footer">
						<button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
						<button class="btn btn-warning" type="submit"> Update</button>
                      </div>
					   </form>
				  </div>
			  </div>
		  </div>
		  <!-- modal -->
		  
      </section>
      <!--main content end-->
      <!--footer start-->
      <footer class="site-footer">
          <div class="text-center">
              <?php echo $footer;?>
              <a href="#" class="go-top">
                  <i class="icon-angle-up"></i>
              </a>
          </div>
      </footer>
      <!--footer end-->
  </section>

    <!-- js placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/jquery-1.8.3.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.scrollTo.min.js"></script>
    <script src="js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="js/jquery.customSelect.min.js" ></script>
    <script src="js/respond.min.js" ></script>
	
    <script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>

    <!--common script for all pages-->
    <script src="js/common-scripts.js"></script>
	
	<script>
		$('#editgenerator').on('show.bs.modal', function(e) {
			var generator = $(e.relatedTarget).data('generator');
			var generatorid = $(e.relatedTarget).data('generatorid');
			$(e.currentTarget).find('input[name="editgenerator"]').val(generator);
			$(e.currentTarget).find('input[name="generatorid"]').val(generatorid);
		});
		
		$('#editpackage').on('show.bs.modal', function(e) {
			var package = $(e.relatedTarget).data('package');
			var packageid = $(e.relatedTarget).data('packageid');
			var price = $(e.relatedTarget).data('price');
			var length = $(e.relatedTarget).data('length');
			var accounts = $(e.relatedTarget).data('accounts');
			var generator = $(e.relatedTarget).data('generator');
			$(e.currentTarget).find('input[name="editpackage"]').val(package);
			$(e.currentTarget).find('input[name="packageid"]').val(packageid);
			$(e.currentTarget).find('input[name="editprice"]').val(price);
			$(e.currentTarget).find('input[name="editmax"]').val(accounts);
		});
	</script>

  </body>
</html>

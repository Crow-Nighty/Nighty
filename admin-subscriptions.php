<?php

error_reporting (E_ALL ^ E_NOTICE); /* 1st line (recommended) */

include "inc/header.php";

if ($_SESSION['rank'] < "5") {
	header('Location: index.php?error=no-admin');
	exit();
}

if (isset($_GET['delete'])){
	$id = mysqli_real_escape_string($con, $_GET['delete']);
	mysqli_query($con, "UPDATE `subscriptions` SET `active` = '0' WHERE `id` = '$id'") or die(mysqli_error($con));
	echo '
		<script>
			window.history.replaceState("object or string", "Title", "/admin-subscriptions.php");
		</script>
	';
}

if (isset($_POST['subscriptionid']) && isset($_POST['editpackage']) && isset($_POST['editexpires'])){
	$id = mysqli_real_escape_string($con, $_POST['subscriptionid']);
	$package = mysqli_real_escape_string($con, $_POST['editpackage']);
	$expires = mysqli_real_escape_string($con, $_POST['editexpires']);
	mysqli_query($con, "UPDATE `subscriptions` SET `package` = '$package' WHERE `id` = '$id'") or die(mysqli_error($con));
	mysqli_query($con, "UPDATE `subscriptions` SET `expires` = '$expires' WHERE `id` = '$id'") or die(mysqli_error($con));
}

if (isset($_POST['addsubscription']) && isset($_POST['package'])){
	$user = mysqli_real_escape_string($con, $_POST['addsubscription']);
	$package = mysqli_real_escape_string($con, $_POST['package']);

	$result = mysqli_query($con,"SELECT * FROM `packages` WHERE `id` = '$package'");
	while ($row = mysqli_fetch_array($result)) 
	{
		$length = $row['length'];
	}

	$today = time();

	if($length == "Lifetime"){
		$expires = strtotime("100 years", $today);
	}elseif($length == "1 Day"){
		$expires = strtotime("+1 day", $today);
	}elseif($length == "3 Days"){
		$expires = strtotime("+3 days", $today);
	}elseif($length == "1 Week"){
		$expires = strtotime("+1 week", $today);
	}elseif($length == "1 Month"){
		$expires = strtotime("+1 month", $today);
	}elseif($length == "2 Months"){
		$expires = strtotime("+2 months", $today);
	}elseif($length == "3 Months"){
		$expires = strtotime("+3 months", $today);
	}elseif($length == "4 Months"){
		$expires = strtotime("+4 months", $today);
	}elseif($length == "5 Months"){
		$expires = strtotime("+5 months", $today);
	}elseif($length == "6 Months"){
		$expires = strtotime("+6 months", $today);
	}elseif($length == "7 Months"){
		$expires = strtotime("+7 months", $today);
	}elseif($length == "8 Months"){
		$expires = strtotime("+8 months", $today);
	}elseif($length == "9 Months"){
		$expires = strtotime("+9 months", $today);
	}elseif($length == "10 Months"){
		$expires = strtotime("+10 months", $today);
	}elseif($length == "11 Months"){
		$expires = strtotime("+11 months", $today);
	}elseif($length == "12 Months"){
		$expires = strtotime("+12 months", $today);
	}else{
	}

	$expires = date('Y-m-d', $expires);
	mysqli_query($con, "INSERT INTO `subscriptions` (`username`, `date`, `price`, `payment`, `package`, `expires`) VALUES ('$user', DATE('$date'), '0.00', 'Gift', '$package', '$expires')") or die(mysqli_error($con));
}

$result = mysqli_query($con, "SELECT * FROM `subscriptions`") or die(mysqli_error($con));
$totalsubscriptions = mysqli_num_rows($result);

$result = mysqli_query($con, "SELECT * FROM `subscriptions` WHERE `active` = '1' AND `expires` >= '$date'") or die(mysqli_error($con));
$activesubscriptions = mysqli_num_rows($result);

$result = mysqli_query($con, "SELECT * FROM `subscriptions` WHERE `expires` < '$date'") or die(mysqli_error($con));
$expiredsubscriptions = mysqli_num_rows($result);

$result = mysqli_query($con, "SELECT * FROM `subscriptions` WHERE `date` = '$date'") or die(mysqli_error($con));
$todayssubscriptions = mysqli_num_rows($result);

$result = mysqli_query($con, "SELECT * FROM `subscriptions` WHERE `active` = '0'") or die(mysqli_error($con));
$canceledsubscriptions = mysqli_num_rows($result);
	
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

              <div class="row">
				  <div class="col-lg-9">
					  <section class="panel">
						  <div class="panel-body">
							  <div class="task-thumb-details">
								  <h1>Active Subscriptions</h1>
							  </div>
							  <legend></legend>
								<section class="panel">
								  <table class="table table-striped table-advance table-hover">
								  
									<div id="collapse">

										<button class="btn btn-info btn-large btn-block" data-toggle="collapse" data-target="#addsubscription" data-parent="#collapse"><i class="icon-plus"></i> Add Subscription</button></br>

										<div id="addsubscription" class="sublinks collapse">
											<legend></legend>
											<form action="admin-subscriptions.php" method="POST">
												<input type="text" name="addsubscription" class="form-control" placeholder="Username"></br>
												<select name="package" class="form-control">
												<?php
													$packagesquery = mysqli_query($con, "SELECT * FROM `packages`") or die(mysqli_error($con));
													while($row = mysqli_fetch_assoc($packagesquery)){
														echo '<option value="'.$row[id].'">'.$row[name].'</option>';
													}
												?>
												</select></br>
												<button type="submit" class="btn btn-primary btn-large btn-block"><i class="icon-plus"></i> Add Subscription</button>
											</form>
										</div>
										<legend></legend>
										<input id="filter" type="text" class="form-control" placeholder="Filter..">
									  <thead>
									  <tr>
										  <th><i class="icon-user"></i> Username</th>
										  <th><i class="icon-tag"></i> Package</th>
										  <th><i class="icon-calendar"></i> Expires</th>
										  <th></th>
										  <th></th>
									  </tr>
									  </thead>
									  <tbody class="searchable">
										<?php
										$result = mysqli_query($con, "SELECT * FROM `subscriptions` WHERE `active` = '1' AND `expires` >= '$date'") or die(mysqli_error($con));
										while ($row = mysqli_fetch_array($result)) {
											echo'<tr><td><a href="#">'.$row['username'].'</a></td>';
											$packagequery = mysqli_query($con, "SELECT * FROM `packages` WHERE `id` = '$row[package]'") or die(mysqli_error($con));
											while ($packageinfo = mysqli_fetch_array($packagequery)) {
												echo '<td>' . $packageinfo['name'] . '</td>';
												$package = $packageinfo['name'];
											}
											echo '
												  <td>'.$row['expires'].'</td>
												  <td><a class="btn btn-success btn-xs" data-toggle="modal" href="#info" data-username="'.$row['username'].'" data-package="'.$package.'" data-price="'.$row['price'].'" data-payment="'.$row['payment'].'" data-date="'.$row['date'].'" data-expires="'.$row['expires'].'" data-txn="'.$row['txn'].'"><i class="icon-info"></i>&nbsp More Info</a></td>
												  <td>
													  <a class="btn btn-primary btn-xs" data-toggle="modal" href="#edit" data-username="'.$row['username'].'" data-package="'.$row['package'].'" data-expires="'.$row['expires'].'" data-subscriptionid="'.$row['id'].'"><i class="icon-pencil"></i></a>
													  <a class="btn btn-danger btn-xs" href="admin-subscriptions.php?delete=' . $row['id'] . '"><i class="icon-trash "></i></a>
												  </td>
											  </tr>
											';
										}
										?>
									  </tbody>
								  </table>
							  </section>
						  </div>
					  </section>
				  </div>
				  <div class="col-lg-3">
					  <section class="panel">
						  <div class="panel-body">
							  <div class="task-thumb-details">
								  <h1>Subscription Information</h1>
							  </div>
							  <legend></legend>
								<ul class="nav nav-pills nav-stacked">
                                  <li><a href="#"> <strong><i class="icon-tags"></i></strong>&nbsp Total Subscriptions<span class="label label-primary pull-right r-activity"><?php echo $totalsubscriptions;?></span></a></li>
                                  <li><a href="#"> <strong><i class="icon-ok"></i></strong>&nbsp Active Subscriptions<span class="label label-warning pull-right r-activity"><?php echo $activesubscriptions;?></span></a></li>
								  <li><a href="#"> <strong><i class="icon-remove"></i></strong>&nbsp Expired Subscriptions<span class="label label-success pull-right r-activity"><?php echo $expiredsubscriptions;?></span></a></li>
								  <li><a href="#"> <strong><i class="icon-calendar"></i></strong>&nbsp Today's Subscriptions<span class="label label-info pull-right r-activity"><?php echo $todayssubscriptions;?></span></a></li>
								  <li><a href="#"> <strong><i class="icon-off"></i></strong>&nbsp Canceled Subscriptions<span class="label label-default pull-right r-activity"><?php echo $canceledsubscriptions;?></span></a></li>
								</ul>
						  </div>
					  </section>
				  </div>
              </div>

          </section>
		  
		  <!-- Modal -->
		  <div class="modal fade" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-sm">
				  <div class="modal-content">
					  <div class="modal-header">
						  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						  <h4 class="modal-title">Subscription Info</h4>
					  </div>
					  <div class="modal-body">
						<div class="form-group">
						  <label for="username">Username</label>
						  <input type="text" class="form-control" name="username" disabled>
						</div>
						<div class="form-group">
						  <label for="package">Package</label>
						  <input type="text" class="form-control" name="package" disabled>
						</div>
						<div class="form-group">
						  <label for="price">Price</label>
						  <input type="text" class="form-control" name="price" disabled>
						</div>
						<div class="form-group">
						  <label for="payment">Payment Method</label>
						  <input type="text" class="form-control" name="payment" disabled>
                        </div>
						<div class="form-group">
						  <label for="date">Date</label>
						  <input type="date" class="form-control" name="date" disabled>
                        </div>
						<div class="form-group">
						  <label for="expires">Expires</label>
						  <input type="date" class="form-control" name="expires" disabled>
                        </div>
						<div class="form-group">
						  <label for="txn">Transaction ID</label>
						  <input type="text" class="form-control" name="txn" disabled>
                        </div>
					  </div>
				  </div>
			  </div>
		  </div>
		  <!-- modal -->
		  
		  <!-- Modal -->
		  <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-sm">
				  <div class="modal-content">
					  <div class="modal-header">
						  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						  <h4 class="modal-title">Edit Subscription</h4>
					  </div>
					  <div class="modal-body">
					   <form action="admin-subscriptions.php" method="POST">
					    <input type="hidden" name="subscriptionid">
						<div class="form-group">
						  <label>Username</label>
						  <input type="text" class="form-control" name="editusername" disabled>
						</div>
						<div class="form-group">
						  <label>Package</label>
						  <select class="form-control" name="editpackage">
							<?php
								$packagesquery = mysqli_query($con, "SELECT * FROM `packages`") or die(mysqli_error($con));
								while($row = mysqli_fetch_assoc($packagesquery)){
									echo '<option value="'.$row[id].'">'.$row[name].'</option>';
								}
							?>
						  </select>
						</div>
						<div class="form-group">
						  <label>Expires</label>
						  <input type="date" class="form-control" name="editexpires">
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
		$(document).ready(function () {

			(function ($) {

				$('#filter').keyup(function () {

					var rex = new RegExp($(this).val(), 'i');
					$('.searchable tr').hide();
					$('.searchable tr').filter(function () {
						return rex.test($(this).text());
					}).show();

				})

			}(jQuery));

		});
		
		$('#info').on('show.bs.modal', function(e) {
			var username = $(e.relatedTarget).data('username');
			var package = $(e.relatedTarget).data('package');
			var price = $(e.relatedTarget).data('price');
			var payment = $(e.relatedTarget).data('payment');
			var date = $(e.relatedTarget).data('date');
			var expires = $(e.relatedTarget).data('expires');
			var txn = $(e.relatedTarget).data('txn');
			$(e.currentTarget).find('input[name="username"]').val(username);
			$(e.currentTarget).find('input[name="package"]').val(package);
			$(e.currentTarget).find('input[name="price"]').val(price);
			$(e.currentTarget).find('input[name="payment"]').val(payment);
			$(e.currentTarget).find('input[name="date"]').val(date);
			$(e.currentTarget).find('input[name="expires"]').val(expires);
			$(e.currentTarget).find('input[name="txn"]').val(txn);
		});
		
		$('#edit').on('show.bs.modal', function(e) {
			var editusername = $(e.relatedTarget).data('username');
			var editexpires = $(e.relatedTarget).data('expires');
			var subscriptionid = $(e.relatedTarget).data('subscriptionid');
			var editpackage = $(e.relatedTarget).data('package');
			$(e.currentTarget).find('input[name="editusername"]').val(editusername);
			$(e.currentTarget).find('input[name="editexpires"]').val(editexpires);
			$(e.currentTarget).find('input[name="subscriptionid"]').val(subscriptionid);
		});
	</script>

  </body>
</html>

<?php

error_reporting (E_ALL ^ E_NOTICE); /* 1st line (recommended) */

include "inc/header.php";

if ($_SESSION['rank'] < "5") {
	header('Location: index.php?error=no-admin');
	exit();
}

if (isset($_GET['delete']) && isset($_GET['generator'])){
	$id = mysqli_real_escape_string($con, $_GET['delete']);
	$generatorid = mysqli_real_escape_string($con, $_GET['generator']);
	mysqli_query($con, "UPDATE `generator$generatorid` SET `status` = '0' WHERE `id` = '$id'") or die(mysqli_error($con));
	echo '
		<script>
			window.history.replaceState("object or string", "Title", "/admin-flagged.php");
		</script>
	';
}

$totalalts = 0;
$activealts = 0;
$flaggedalts = 0;
$disabledalts = 0;

$result = mysqli_query($con, "SELECT * FROM `generators`") or die(mysqli_error($con));
while($row = mysqli_fetch_assoc($result)) {
	$generatorid = $row['id'];
	$result2 = mysqli_query($con, "SELECT * FROM `generator$generatorid`") or die(mysqli_error($con));
	$totalalts = $totalalts + mysqli_num_rows($result2);
	$result3 = mysqli_query($con, "SELECT * FROM `generator$generatorid` WHERE `status` != '0'") or die(mysqli_error($con));
	$activealts = $activealts + mysqli_num_rows($result3);
	$result4 = mysqli_query($con, "SELECT * FROM `generator$generatorid` WHERE `status` = '2'") or die(mysqli_error($con));
	$flaggedalts = $flaggedalts + mysqli_num_rows($result4);
	$result5 = mysqli_query($con, "SELECT * FROM `generator$generatorid` WHERE `status` = '0'") or die(mysqli_error($con));
	$disabledalts = $disabledalts + mysqli_num_rows($result5);
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

              <div class="row">
				  <div class="col-lg-9">
					  <section class="panel">
						  <div class="panel-body">
							  <div class="task-thumb-details">
								  <h1>Flagged Alts</h1>
							  </div>
							  <legend></legend>
								<section class="panel">
								  <table class="table table-striped table-advance table-hover">
								  
									<div id="collapse">

										<input id="filter" type="text" class="form-control" placeholder="Filter..">
									  <thead>
									  <tr>
										  <th><i class="icon-repeat"></i> Generator</th>
										  <th><i class="icon-lock"></i> Alt</th>
										  <th></th>
										  <th></th>
									  </tr>
									  </thead>
									  <tbody class="searchable">
										<?php
										$result = mysqli_query($con, "SELECT * FROM `generators`") or die(mysqli_error($con));
										while ($row = mysqli_fetch_array($result)) {
											$generator = $row['name'];
											$generatorid = $row['id'];
											$result2 = mysqli_query($con, "SELECT * FROM `generator$generatorid` WHERE `status` = '2'") or die(mysqli_error($con));
											while ($row2 = mysqli_fetch_array($result2)) {
												echo '<tr>
												  <td>' . $generator . '</td>
												  <td>'.$row2['alt'].'</td>
												  <td><a class="btn btn-info btn-xs" href="#" disabled><i class="icon-check"></i> Test Alt</a></td>
												  <td><a class="btn btn-danger btn-xs" href="admin-flagged.php?delete=' . $row2['id'] . '&generator='.$generatorid.'"><i class="icon-trash "></i></a></td>
											  </tr>
											';
											}
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
								  <h1>Alt Information</h1>
							  </div>
							  <legend></legend>
								<ul class="nav nav-pills nav-stacked">
                                  <li><a href="#"> <strong><i class="icon-lock"></i></strong>&nbsp Total Alts<span class="label label-primary pull-right r-activity"><?php echo $totalalts;?></span></a></li>
                                  <li><a href="#"> <strong><i class="icon-flag"></i></strong>&nbsp Flagged Alts<span class="label label-warning pull-right r-activity"><?php echo $flaggedalts;?></span></a></li>
								  <li><a href="#"> <strong><i class="icon-ok"></i></strong>&nbsp Active Alts<span class="label label-success pull-right r-activity"><?php echo $activealts;?></span></a></li>
								  <li><a href="#"> <strong><i class="icon-remove"></i></strong>&nbsp Disabled Alts<span class="label label-danger pull-right r-activity"><?php echo $disabledalts;?></span></a></li>
								</ul>
						  </div>
					  </section>
				  </div>
              </div>

          </section>
		  
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
	</script>

  </body>
</html>

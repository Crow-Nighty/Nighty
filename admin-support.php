<?php

error_reporting (E_ALL ^ E_NOTICE); /* 1st line (recommended) */

include "inc/header.php";

if ($_SESSION['rank'] < "5") {
	header('Location: index.php?error=no-admin');
	exit();
}

if (isset($_GET['read'])){
	$id = mysqli_real_escape_string($con, $_GET['read']);
	mysqli_query($con, "UPDATE `support` SET `read` = '1' WHERE `id` = '$id'") or die(mysqli_error($con));
	echo '
		<script>
			window.history.replaceState("object or string", "Title", "/admin-support.php");
		</script>
	';
}

if (isset($_GET['delete'])){
	$id = mysqli_real_escape_string($con, $_GET['delete']);
	mysqli_query($con, "DELETE FROM `support` WHERE `id` = '$id'") or die(mysqli_error($con));
	echo '
		<script>
			window.history.replaceState("object or string", "Title", "/admin-support.php");
		</script>
	';
}

if (isset($_POST['reply']) & isset($_POST['id']) & isset($_POST['to']) & isset($_POST['subject'])){
	$reply = mysqli_real_escape_string($con, $_POST['reply']);
	$id = mysqli_real_escape_string($con, $_POST['id']);
	$to = mysqli_real_escape_string($con, $_POST['to']);
	$subject = mysqli_real_escape_string($con, $_POST['subject']);
	mysqli_query($con, "INSERT INTO `support` (`from`, `to`, `subject`, `message`, `date`) VALUES ('Admin', '$to', '$subject', '$reply', DATE('$date'))") or die(mysqli_error($con));
	mysqli_query($con, "UPDATE `support` SET `read` = '1' WHERE `id` = '$id'") or die(mysqli_error($con));
}

$result = mysqli_query($con, "SELECT * FROM `support`") or die(mysqli_error($con));
$totaltickets = mysqli_num_rows($result);

$result = mysqli_query($con, "SELECT * FROM `support` WHERE `to` = 'Admin'") or die(mysqli_error($con));
$receivedtickets = mysqli_num_rows($result);

$result = mysqli_query($con, "SELECT * FROM `support` WHERE `from` = 'Admin'") or die(mysqli_error($con));
$senttickets = mysqli_num_rows($result);

$result = mysqli_query($con, "SELECT * FROM `support` WHERE `read` = '0'") or die(mysqli_error($con));
$unansweredtickets = mysqli_num_rows($result);

if($receivedtickets == 0){
	$replystatus = 0;
}else{
	$replystatus = $senttickets / $receivedtickets * 100;
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
								  <h1>Support Tickets</h1>
							  </div>
							  <legend></legend>
							  <div id="menu">
								<div class="list-group">
								<?php
								$supportquery = mysqli_query($con, "SELECT * FROM `support` WHERE `to` = 'Admin' ORDER BY `date` DESC") or die(mysqli_error());
								while ($row = mysqli_fetch_assoc($supportquery)) {
									echo '
										<a href="#" style="margin-top: 5px;" class="list-group-item ';
									if($row['read'] != "1"){
									echo 'active';
									}
									echo '" data-toggle="collapse" data-target="#message'.$row[id].'" data-parent="#menu">
											<span class="name" style="min-width: 120px;display: inline-block;">'.$row["from"].'</span> <span class="">'.$row["subject"].'</span>
												<span class="badge">'.$row["date"].'</span> 
												<span class="badge"><i class="icon-plus"></i></span>
											</span>
										</a>
										<div id="message'.$row[id].'" class="sublinks collapse" style="background:#F1F2F7;">
											<textarea class="form-control" rows="8" disabled>'.$row[message].'</textarea></br>
											<form method="POST" action="admin-support.php" id="reply" name="reply">
												<textarea name="reply" class="form-control" rows="4"></textarea></br>
												<input type="hidden" name="id" value="'.$row[id].'"/>
												<input type="hidden" name="to" value="'.$row[from].'"/>
												<input type="hidden" name="subject" value="'.$row[subject].'"/>
												<button type="submit" style="margin-left: 5px;width:495px;" class="btn btn-info btn-large">Send Reply</button>
												<div class="btn-group">
													<a style="width:150px;" href="admin-support.php?read='.$row[id].'" class="btn btn-success btn-large">Set Read</a>
													<a style="width:150px;" href="admin-support.php?delete='.$row[id].'" class="btn btn-danger btn-large">Delete</a>
												</div></br></br>
											</form>
										</div>
									';
								}
								?>
								</div>
							  </div>
						  </div>
					  </section>
				  </div>
				  <div class="col-lg-3">
					  <section class="panel">
						  <div class="panel-body">
							  <div class="task-thumb-details">
								  <h1>Support Information</h1>
							  </div>
							  <legend></legend>
								<ul class="nav nav-pills nav-stacked">
                                  <li><a href="#"> <strong><i class="icon-envelope"></i></strong>&nbsp Total Tickets<span class="label label-primary pull-right r-activity"><?php echo $totaltickets;?></span></a></li>
                                  <li><a href="#"> <strong><i class="icon-download"></i></strong>&nbsp Received Tickets<span class="label label-warning pull-right r-activity"><?php echo $receivedtickets;?></span></a></li>
								  <li><a href="#"> <strong><i class="icon-upload"></i></strong>&nbsp Sent Tickets<span class="label label-success pull-right r-activity"><?php echo $senttickets;?></span></a></li>
								  <li><a href="#"> <strong><i class="icon-remove"></i></strong>&nbsp Unanswered Tickets<span class="label label-info pull-right r-activity"><?php echo $unansweredtickets;?></span></a></li>
								</ul></br>
								<legend></legend>
								<center><font size="3"><strong><i class="icon-upload"></i></strong>&nbsp Reply Status</font></center></br>
								<div class="progress progress-striped active progress-sm">
                                  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $replystatus;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $replystatus;?>%">
                                      <span class="sr-only"><?php echo $replystatus;?>% Complete</span>
                                  </div>
                                </div>
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

  </body>
</html>

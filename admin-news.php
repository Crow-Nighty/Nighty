<?php

error_reporting (E_ALL ^ E_NOTICE); /* 1st line (recommended) */

include "inc/header.php";

if ($_SESSION['rank'] < "5") {
	header('Location: index.php?error=no-admin');
	exit();
}

if (isset($_GET['delete'])){
	$id = mysqli_real_escape_string($con, $_GET['delete']);
	mysqli_query($con, "DELETE FROM `news` WHERE `id` = '$id'") or die(mysqli_error($con));
	echo '
		<script>
			window.history.replaceState("object or string", "Title", "/admin-news.php");
		</script>
	';
}

if (isset($_POST['addnews'])){
	$message = mysqli_real_escape_string($con, $_POST['addnews']);
	mysqli_query($con, "INSERT INTO `news` (`message`, `writer`, `date`) VALUES ('$message', '$_SESSION[username]', '$datetime')") or die(mysqli_error($con));
}

if (isset($_POST['newsid']) && isset($_POST['editmessage'])){
	$id = mysqli_real_escape_string($con, $_POST['newsid']);
	$message = mysqli_real_escape_string($con, $_POST['editmessage']);
	mysqli_query($con, "UPDATE `news` SET `message` = '$message' WHERE `id` = '$id'") or die(mysqli_error($con));
}

$result = mysqli_query($con, "SELECT * FROM `news`") or die(mysqli_error($con));
$totalnews = mysqli_num_rows($result);

$result = mysqli_query($con, "SELECT * FROM `news` WHERE DATE(date) = '$date'") or die(mysqli_error($con));
$todaysnews = mysqli_num_rows($result);
	
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
								  <h1>News</h1>
							  </div>
							  <legend></legend>
								<form action="admin-news.php" method="POST">
									<textarea name="addnews" class="form-control" rows="3" placeholder="Type a message here.."></textarea></br>
									<button type="submit" class="btn btn-info btn-block">Add News Message</button></br>
								</form>
								<legend></legend>
								<?php
								$result = mysqli_query($con, "SELECT * FROM `news` ORDER BY `date` DESC") or die(mysqli_error());
								while ($row = mysqli_fetch_assoc($result)) {
									echo '
										<div class="panel-body profile-activity">
										  <div class="activity terques">
											  <span>
												  <i class="icon-bullhorn"></i>
											  </span>
											  <div class="activity-desk">
												  <div class="panel">
													  <div class="panel-body">
														  <div class="arrow"></div>
														  <i class="icon-bell"></i>
														  <h4><a href="#">'.$row['writer'].' </a>&nbsp <small>'.$row['date'].'</small> <a href="admin-news.php?delete=' . $row['id'] . '" class="pull-right"><i class="icon-trash"></i></a>&nbsp<a class="pull-right" data-toggle="modal" href="#edit" data-message="'.$row['message'].'" data-newsid="'.$row['id'].'"><i class="icon-pencil"></i></a></h4>
														  <p>'.$row['message'].'</p>
													  </div>
												  </div>
											  </div>
										  </div>
										</div>
									';
								}
								?>
						  </div>
					  </section>
				  </div>
				  <div class="col-lg-3">
					  <section class="panel">
						  <div class="panel-body">
							  <div class="task-thumb-details">
								  <h1>News Information</h1>
							  </div>
							  <legend></legend>
								<ul class="nav nav-pills nav-stacked">
                                  <li><a href="#"> <strong><i class="icon-bell"></i></strong>&nbsp Total News Messages<span class="label label-primary pull-right r-activity"><?php echo $totalnews;?></span></a></li>
								  <li><a href="#"> <strong><i class="icon-calendar"></i></strong>&nbsp Today's News Messages<span class="label label-warning pull-right r-activity"><?php echo $todaysnews;?></span></a></li></br>
								  <legend></legend>
								</ul>
						  </div>
					  </section>
					  
					  <!-- Modal -->
					  <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						  <div class="modal-dialog modal-sm">
							  <div class="modal-content">
								  <div class="modal-header">
									  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									  <h4 class="modal-title">Edit News Message</h4>
								  </div>
								  <div class="modal-body">
								   <form action="admin-news.php" method="POST">
									<input type="hidden" name="newsid">
									<textarea name="editmessage" class="form-control" rows="5" placeholder="Type a message here.."></textarea>
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
	$('#edit').on('show.bs.modal', function(e) {
		var newsid = $(e.relatedTarget).data('newsid');
		var message = $(e.relatedTarget).data('message');
		$(e.currentTarget).find('input[name="newsid"]').val(newsid);
		$(e.currentTarget).find('textarea[name="editmessage"]').val(message);
	});
	</script>

  </body>
</html>

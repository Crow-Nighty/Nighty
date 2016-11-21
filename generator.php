<?php

include "inc/header.php";

$result = mysqli_query($con, "SELECT * FROM `subscriptions` WHERE `username` = '$username' AND `active` = '1' AND `expires` >= '$date'") or die(mysqli_error($con));
if (mysqli_num_rows($result) < 1 && $_SESSION['rank'] != "5") {
	header('Location: purchase.php');
}else{
	while($row = mysqli_fetch_assoc($result)){
		$package = $row['package'];
		$checkpackage = mysqli_query($con, "SELECT * FROM `packages` WHERE `id` = '$package'") or die(mysqli_error($con));
		while($row1 = mysqli_fetch_assoc($checkpackage)){
			$generator = $row1['generator'];
		}
	}
}

if (isset($_POST['flagalt']) && isset($_POST['generator'])){
	$alt = mysqli_real_escape_string($con, $_POST['flagalt']);
	$generatorid = mysqli_real_escape_string($con, $_POST['generator']);
	mysqli_query($con, "UPDATE `generator$generatorid` SET `status` = '2' WHERE `alt` = '$alt'") or die(mysqli_error($con));
}

$totalalts = 0;

$result = mysqli_query($con, "SELECT * FROM `generators`") or die(mysqli_error($con));
while($row = mysqli_fetch_assoc($result)) {
	$result2 = mysqli_query($con, "SELECT * FROM `generator$row[id]` WHERE `status` != '0'") or die(mysqli_error($con));
	$totalalts = $totalalts + mysqli_num_rows($result2);
}

$result = mysqli_query($con, "SELECT * FROM `generators`") or die(mysqli_error($con));
while($row = mysqli_fetch_assoc($result)) {
	$totalgens = mysqli_num_rows($result);
}

$generatestotal = 0;

$result = mysqli_query($con, "SELECT * FROM `statistics` WHERE `username` = '$username'") or die(mysqli_error($con));
while($row = mysqli_fetch_assoc($result)) {
	$generatestotal = $generatestotal + $row['generated'];
}

$result = mysqli_query($con, "SELECT * FROM `statistics` WHERE `username` = '$username' AND `date` = '$date'") or die(mysqli_error($con));
while($row = mysqli_fetch_assoc($result)) {
	$generatestoday = $row['generated'];
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

    <title><?php echo $website;?> - Generator</title>

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
                      <a class="active" href="generator.php">
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
							  <a href="javascript:;" >
								  <i class="icon-laptop"></i>
								  <span>Administration</span>
							  </a>
							  <ul class="sub">
								  <li><a  href="admin-manage.php">Manage</a></li>
								  <li><a  href="admin-support.php">Support</a></li>
								  <li><a  href="admin-statistics.php">Statistics</a></li>
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
                              <i class="icon-th-list"></i>
                          </div>
                          <div class="value">
                              <h1 class="count">
                                  <?php echo $totalalts;?>
                              </h1>
                              <p>Total Alts</p>
                          </div>
                      </section>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                      <section class="panel">
                          <div class="symbol red">
                              <i class="icon-refresh"></i>
                          </div>
                          <div class="value">
                              <h1 class=" count2">
                                  <?php echo $totalgens;?>
                              </h1>
                              <p>Total Geneators</p>
                          </div>
                      </section>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                      <section class="panel">
                          <div class="symbol yellow">
                              <i class="icon-refresh"></i>
                          </div>
                          <div class="value">
                              <h1 class=" count3">
                                  <?php 
									if(!isset($generatestoday)){
										echo "0";
									}else{
										echo $generatestoday;
									}
								  ?>
                              </h1>
                              <p>Your Generates Today</p>
                          </div>
                      </section>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                      <section class="panel">
                          <div class="symbol blue">
                              <i class="icon-refresh"></i>
                          </div>
                          <div class="value">
                              <h1 class=" count3">
                                  <?php echo $generatestotal;?>
                              </h1>
                              <p>Your Generates Total</p>
                          </div>
                      </section>
                  </div>
              </div>
              <!--state overview end-->
			  
			  <script type="text/javascript">
				function select_all(obj) {
					var text_val=eval(obj);
					text_val.focus();
					text_val.select();
					if (!document.all) return; // IE only
					r = text_val.createTextRange();
					r.execCommand('copy');
				}
			  </script>

              <div class="row">
				<?php
				
					if($generator == "" || $_SESSION['rank'] == 5){
						$generatorquery = "SELECT * FROM `generators`";
					}else{
						$generatorquery = "SELECT * FROM `generators` WHERE `id` = ".$generator;
					}
				
					$result = mysqli_query($con, $generatorquery) or die(mysqli_error($con));
					while ($row = mysqli_fetch_array($result)) {
						echo '
						  <div class="col-lg-4">
							  <!--user info table start-->
							  <section class="panel">
								  <div class="panel-body">
									  <div class="task-thumb-details">
										  <h1>'.$row['name'].' Generator</h1>
									  </div>
									  <legend></legend>
									  <input type="text" id="generator'.$row["id"].'" onclick="select_all(this)" class="text-center form-control" placeholder="username:password"></br>
									  <button id="generate'.$row["id"].'" class="btn btn-info btn-block">Generate</button></br>
									  <legend></legend>
									  <form id="flagform'.$row["id"].'" action="generator.php" method="POST">
										  <input type="hidden" id="flag'.$row["id"].'" name="flagalt" value="">
										  <input type="hidden" name="generator" value="'.$row["id"].'">
										  <div class="btn-group btn-group-justified">
											  <a id="copy'.$row["id"].'" data-clipboard-target="generator'.$row["id"].'" title="Copy" class="btn btn-success">Copy</a>
											  <a href="javascript:void(0);" onclick=$(this).closest("form").submit(); title="Flag as invalid" class="btn btn-danger">Flag as invalid</a>
										  </div>
									  </form>
								  </div>
							  </section>
							  <!--user info table end-->
						  </div>
						';
					}
				?>
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
	
	<script src="js/ZeroClipboard.js" ></script>
	
    <script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>

    <!--common script for all pages-->
    <script src="js/common-scripts.js"></script>
	
	<?php
	
	$result = mysqli_query($con, $generatorquery) or die(mysqli_error($con));
	while ($row = mysqli_fetch_array($result)) {
		echo '
			<script>
			$(document).ready(function(){
				$("#generate'.$row["id"].'").click(function(){
				 $.get("lib/generate.php?generator='.$row["id"].'&username='.$username.'", function(response){
					$("#generator'.$row["id"].'").val(response);
					$("#flag'.$row["id"].'").val(response);
				 });
				});
			});
			
			var client = new ZeroClipboard( document.getElementById("copy'.$row["id"].'") );

			client.on( "ready", function( readyEvent ) {

			  client.on( "aftercopy", function( event ) {
			  } );
			} );

			</script>
		';
	}
	
	?>

  </body>
</html>

<?php
// Start the session
session_start();

//include classes from booking_class.php and sqlconnect.php
include(__DIR__.'/booking_class.php');
include(__DIR__.'/sqlconnect.php');

//Include hashIDs
require_once(__DIR__.'/vendor/autoload.php');
$hashids = new Hashids\Hashids('perfect kitty');

//Check if action is present in URL
if ( ! isset($_GET['action'])) {
	makedie('A required parameter is missing from the booking URL. If this problem persists, please contact Nine Lives.'); }

//Create object for Navigation Bar
$nav = new bookingNav($_GET['action']);

//Create new session to store user form data
$session = new Usr_Session();

//Reset booking session and make all values null when selecting booking slot
(($_GET['action']=='bookingslot') ? session_destroy() : '');

//Create NULL giftcard value until giftcard is applied
$user_giftcard = NULL;
?>

<!DOCTYPE html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9, shrink-to-fit=yes">
    <meta name="description" content="">
    <meta name="author" content="">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>

    <title>Nine Lives Escape Rooms</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <!-- Custom fonts for this template -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/pretty-checkbox/3.0.0/pretty-checkbox.css" rel="stylesheet" type="text/css">
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="css/agency.css" rel="stylesheet">
	<link href="booking.css" rel="stylesheet">
	<script src="booking.js"></script>

  </head>

  <body class="page-top">

    <!--Navigation -->
    <nav class="navbar navbar-expand-lg navbar-booking navbar-dark fixed-top" id="mainNav">
      <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="http://www.ninelivesescape.co.uk/webdev"><img src="logo_transparent2.png" class="testimg"  alt=""></a>
        <div class="navbar-toggler navbar-toggler-right">
			<div class="progress_condensed">
				<?php $nav->smallPagekey(); ?>
			</div>
		</div>
		<div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <?php $nav->largePagekey(); ?>
          </ul>
        </div>
      </div>
    </nav>	
	<?php
		//Load page from $_GET['action']
		require_once(__DIR__.'/'.$_GET['action'].'.php');
	?>	
	</section>
	
<!-- Footer -->
    <footer>
      <div class="container">
        <div class="row">
		<div class="col-3 col-sm-2 col-md-2 col-lg-1 copyright">
			  <?php $nav->cancel(); //Cancel option not available on confirmation page ?>
        </div>
		<div class="col-9 col-sm-10 col-md-3 col-lg-3 copyright">
			  <?php $nav->problembooking(); //Problem Booking not available on confirmation page ?>
        </div>
		<div class="col-md-1 col-lg-2 col-sm-12 col-12" style="margin-top:10px;"></div>
         <div class="col-md-6 col-lg-6 col-12 copyright text-left text-md-right">
				Copyright &copy; Nine Lives Entertainment Ltd. 2018
		 </div>
      </div>
    </footer>

<!-- Problem Booking Modal -->
			<div id="problemBooking" class="modal fade" role="dialog">
			  <div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
				 <div class="modal-header">
				  <h4 class="modal-title">Problem Booking?</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				  </div>
				  <div class="modal-body">
					<p>If our booking system isn't playing nice, it's easiest to give us a call:</p>
					<p style="text-align:center; font-size:22px; padding:20px;"><strong> (+44) 131 605 6364</strong></p>
					<p>We can easily and securely take your booking over the phone.</p>
				</div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
				  </div>
				</div>
			  </div>
			 </div>
			  
		
</body>

</html>
<?php
	//$monthyear for generating valid month and year values from GET
	$monthyear = new monthYearCheck($_GET['month'], $_GET['year']);
	
	//$players for generating valid number of players from GET
	$players = new playersCheck($_GET['players']);
	
	//$month_nav for navigating months
	$month_nav = new calendarNav($monthyear->get_date(), $players->get_players());
?>

	<!-- Calendar -->
	<section style="margin-top:60px;">
	<div class="container">
		<div class="row" style="margin:40px 0px 15px;">
			<div class="col-lg-2 d-none d-lg-block calendar_nav">
					<?php
					//Clone $getdate and minus one month
					$month_nav->previousMonth();
					?>
				</a>
			</div>
			<div class="col-lg-8 col-md-12 text-center">
				<div class="dropdown">
					<?php
					$month_nav->dropdownMonth();
					?>
				</div>
			</div>
			<div class="col-lg-2 d-none d-lg-block calendar_nav text-right">
					<?php
					$month_nav->nextMonth();
					?>
			</div>
		</div>
		<div class="row">
			<div style=" border-bottom: 1px solid black; width:100%; margin-right:30px; margin-left:30px; margin-bottom:42px;"></div>
		</div>
		</div> <!--end container-->
		<div class="container-fluid">
		<?php
			//If date error, deefault to today with error message
			$monthyear->isError();
		
			//Connect to database use Database class
			$conn = new Database();
			$conn->query($monthyear->get_year(), $monthyear->get_month());
			$conn->get_rows();
		?>
		</div>
		<div class="container">
		<div class="row" style="margin:40px 0px 15px;">
			<!-- PHP inserted into calendar navigation to show months -->
			<div class="col-6 calendar_nav">
					<?php
						$month_nav->previousMonth();
					?>
				</a>
			</div>
			<div class="col-6 calendar_nav text-right">
					<?php
						$month_nav->nextMonth();
					?>
			</div>
		</div>
	</section>

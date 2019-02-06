<?php
	//Booking Pages Error handling function
	function makedie($message) {
		echo "<div style='margin:150px auto 0px; width:40%;'><div style='float:left; width:90px; height:100px; font-size:40px;'><i class='fas fa-exclamation-triangle'></i></div><div style='padding:6px;'><strong>Error:</strong> ".$message."</div></div>";
		echo "<div style='margin-top:200px; text-align:center; font-size:12px;'><a href='http://ninelivesescape.co.uk'>Return to Homepage</a></div>";
		die();
	}

	//For navigation at top of page
	class bookingNav {
		var $action;			//$_GET[action] input
		var $allowed;			//array of $_GET[action] page names
		var $stage;				//array of human readable page names
		var $pagekey;			//Page sequence (0 - 3)
		
		function __construct ($action) {		//Define what stage of booking process with action converted to a page key
			$this->allowed = array('bookingslot', 'bookingdetails', 'bookingpayment', 'confirmation', 'apply_giftcard', 'apply_promocode'); //Permissible $_GET actions
			$this->stage = array("Select Time Slot", "Your Details", "Payment", "Confirmation"); 
			$pagekey = array_search($action, $this->allowed);
			($pagekey >= 4 ? $pagekey = 1 : "");
			if($pagekey == -1) {makedie('A required parameter is missing from the booking URL. If this problem persists, please contact Nine Lives.');}
			$this->pagekey = $pagekey;
		}
		function smallPagekey() {		//For sm / mobile devices convert page key to static text
			echo $this->stage[$this->pagekey];
		}
		function largePagekey() {		//For md / desktop devices convert page key to progression
			for($i = 0; $i <= $this->pagekey; $i++) {
				if($i != 0){echo '<li class="nav-item"><div class="fa-arrow"><i class="fa fa-caret-right"></i></div></li>';} //Carets except for first item in list
				echo '<li class="nav-item">';
				echo '<div class="process">'.$this->stage[$i].'</div>';
				echo '</li>';
			}
			for($i = $this->pagekey+1; $i < 4; $i++) {
				echo '<li class="nav-item"><div class="fa-arrow deselect"><i class="fa fa-caret-right"></i></div></li>'; //Add white caret
				echo '<li class="nav-item">';
				echo '<div class="process deselect">'.$this->stage[$i].'</div>';
				echo '</li>';
			}
		}

		function cancel() {
			if($this->pagekey != 3) {
				echo '<a href="http://www.ninelivesescape.co.uk"><i class="fas fa-times" style="padding-right:6px;"></i>Cancel</a>';
			}
		}
		
		function problembooking() {
			if($this->pagekey != 3) {
				echo '<a href="#" data-toggle="modal" data-target="#problemBooking" ><i class="fas fa-exclamation-triangle" style="padding-right:6px;"></i>Problem Booking?</a>';
			}
		}
	}

	//Navigation for previous, current and next months
	class calendarNav {
		var $calendarDate;				//Calendar page month and year
		var $getPlayers;				//No. of players in booking
		var $todayDate;					//Date today
		
		function __construct($getdate, $getplayers) {
			$this->calendarDate = $getdate;
			$this->getPlayers = $getplayers;
			$this->todayDate = new DateTime();
		}
		function previousMonth() {
			$previousDate = clone $this->calendarDate;
			$previousDate->modify('-1 month');
			if($this->todayDate < $this->calendarDate) {
				echo '<a href="booking.php?action=bookingslot&month='.$previousDate->format('m').'&year='.$previousDate->format('Y').'&players='.$this->getPlayers.'" class="deco_none"><i class="fa fa-caret-left padding-wide"></i>'.$previousDate->format('F')." ".$previousDate->format('Y').'</a>';
			}
		}
		function dropdownMonth() {
			//Dropdown button
			echo '<button type="button" class="btn btn-nav dropdown-toggle" data-toggle="dropdown">';
				echo $this->calendarDate->format('F')." ".$this->calendarDate->format('Y');
			echo '</button>';
			
			//Dropdown contents
			echo '<div class="dropdown-menu dropdown-nav" style="position:relative; top:0px;">';
				$date_end = new DateTime('2020-01-01'); //End of months selectable
				$cycle_interval = DateInterval::createFromDateString('1 month');
				$period = new DatePeriod($this->todayDate, $cycle_interval, $date_end);
					foreach ($period as $dt) {
						echo '<a class="dropdown-item" href="booking.php?action=bookingslot&month='.$dt->format('m').'&year='.$dt->format('Y').'&players='.$this->getPlayers.'">'.$dt->format("F").' '.$dt->format("Y").'</a>';
					}
			echo '</div>';
		}
		function nextMonth() {
			$nextDate = clone $this->calendarDate;
			$nextDate->modify('+1 month');
			echo '<a href="booking.php?action=bookingslot&month='.$nextDate->format('m').'&year='.$nextDate->format('Y').'&players='.$this->getPlayers.'" class="deco_none">'.$nextDate->format('F')." ".$nextDate->format('Y').'<i class="fa fa-caret-right padding-wide"></i></a>';
		}
	}
	
	class monthYearCheck {
		var $error; 	//TRUE if no month/year is provided, default to today with error message
		var $clean_month;	//Month value after cleaning
		var $clean_year;	//Year value after cleaning
		var $clean_monthyear;		//Final datetime object after cleaning
		
		function __construct($month, $year) {
			$this->error = NULL;
			date_default_timezone_set("Europe/London"); //Set time zone
			$date_today = new DateTime(); //Get today's date
		
			//In case of attempt to add fake date in system
				if ( ! isset($month) || ! isset($year) || !(ctype_digit($month)) || $month > 12 || $month == 0 || !(ctype_digit($year)) || $year < 2018 || $year > 2030) {
					$month = $date_today->format('m'); //If no date or month set or invalid input default to today
					$year = $date_today->format('Y');
					$this->error = TRUE;
				}
			
			//Make month two digits with leading 0
			$month = sprintf("%02s", $month); 
			
			//If current year but month is too early, make current date
			if ($month < $date_today->format('m') && $year == $date_today->format('Y')) {
				$month = $date_today->format('m');
				$this->error = TRUE;
			}
		
			//Make month and year into separate variables, and a datetime object to be returned
			$this->clean_month = $month;
			$this->clean_year = $year;
			$this->clean_monthyear = date_create_from_format('d/m/Y', "01/".$month."/".$year);
		}
		
		function get_date() {
			return $this->clean_monthyear;
		}
		function get_month() {
			return $this->clean_month;
		}
		function get_year() {
			return $this->clean_year;
		}
		function isError() {
			$message = "<div style='text-align:center; margin-bottom:40px; font-weight:bold; color:darkred;'>An invalid date range was selected. Bookings for the current month are shown by default.</div>";
			return ($this->error == TRUE ? $message : '');
		}
	}
	
	class playersCheck {
		var $players;	//Number of players in booking
		
		function __construct($players) {
			//Check number of players is set, if not error message
			if (! isset($players) ||$players > 6 || $players < 2) {
				makedie("Please select a valid number of players");
			} else {
				$this->players = $players;
			}
		}
		function get_players() {
			return $this->players;
		}
	}
	
	class Usr_Session {
		function resetsession() {
			//Make new usr_array session of null
			$Usr_array = array(
						'Refresh' => 0,
						'First Name' => null,
						'Last Name' => null,
						'Email Address' => null,
						'Email Marketing' => null,
						'Contact Number' => null,
						'Booking ID' => null,
						'Game' => 'Blitz!',
						'Original Price' => null,
						'Discount Total' => 0,
						'Giftcard Code' => null,
						'Promo Code' => null,
						'Total Price' => null
					);
			$_SESSION['Usr_array'] = $Usr_array;
		}
		function update($array_name, $array_value) {
			$_SESSION['Usr_array'][$array_name] = $array_value;
		}
	}
?>
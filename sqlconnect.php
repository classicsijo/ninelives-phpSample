<?php
class Database {
		//For database connection
		private $host;
		private $user;
		private $pass;
		private $db;
		private $sql;
		public $conn;
		public $result;
		
		//For displaying slot availability
		public $row;					//Row from database
		public $date_today;				//Today's date
		public $bookingslots;			//Array of possible booking slots
		public $dateslot;				//Date format of each slot
		public $latest_date;			//Date of row ending at 20:30
		

	public function __construct() {
		$this->db_connect();
		
		date_default_timezone_set("Europe/London"); //Set time zone
		$this->date_today = new DateTime(); //Get today's date
	}

	private function db_connect(){
		/* database configuration settings */
		include('config.php')

		$this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
		
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		return true; 
	}
	
	//Send a query to database
	public function query($year, $month)
	{		
		$this->sql = "SELECT DATE_FORMAT(
						SLDATE, '%Y-%m-%d, %H:%i:%S') AS precisedate,
						DATE_FORMAT(SLDATE, '%W, %D %M') AS easydate,
						DATE_FORMAT(SLDATE, '%W') AS dayofweek,
						SLOT1000,
						SLOT1130,
						SLOT1300,
						SLOT1430,
						SLOT1600,
						SLOT1730,
						SLOT1900,
						SLOT2030,
						SPECIAL_INFO
						FROM Booking_Calendar WHERE SLDATE LIKE '".$year."-".$month."%'";
		
		$this->result = $this->conn->query($this->sql);
	}
	
	public function get_slots() {
		//Array of booking slots						
		$this->bookingslots = array("1000", "1130", "1300", "1430", "1600", "1730", "1900", "2030");
						
		//Cycle through booking slots array to make date_slot for each
		for($x = 0; $x < count($this->bookingslots); $x++) {
			$substring = substr($this->bookingslots[$x], 0, 2).":".substr($this->bookingslots[$x], 2, 2)." ".$this->latest_date->format('d/m/Y');
			$this->dateslot = date_create_from_format('H:i d/m/Y', $substring);
								
			//Identify availability of each slot from booking database
			$this->show_avail($x);
		}
	}
	
	public function get_hashid($encode_dateslot) {
		//Use HashIDs to create anonymous booking link
		$this->hashids = new Hashids\Hashids('perfect kitty'); 
		return $this->hashids->encode($encode_dateslot->format("d"), $encode_dateslot->format("m"), $encode_dateslot->format("y"), $encode_dateslot->format("H"), $encode_dateslot->format("i"), $_GET['players']);
	}
	
	public function show_avail($x) {
		//Add 2 hour buffer to current time to stop immediate bookings
		$date_today_2hr = clone $this->date_today;
		$date_today_2hr->add(new DateInterval('PT2H'));
		
		//Check if booking slot is available in database
		if ($date_today_2hr < $this->dateslot) {
			if ($this->row['SLOT'.$this->bookingslots[$x]] == "Available") {
				//If available, make clickable div using HashIds
				echo "<div role='button' class='book active ".$this->row['dayofweek']."' id='".$this->get_hashid($this->dateslot)."'>".$this->dateslot->format('H:i')."</div>";
			} elseif ($this->row['SLOT'.$this->bookingslots[$x]] == "BLOCKED") {
				//Spacer only if game is BLOCKED
				echo "<div class='book hidden'>".$this->dateslot->format('H:i')."</div>";
			} elseif ($this->row['SLOT'.$this->bookingslots[$x]] == "SOLD") {
				//Inactive div if game is already SOLD
				echo "<div class='book inactive ".$this->row['dayofweek']."'>".$this->dateslot->format('H:i')."</div>";
			}
		} else { //Invisible field to pad out table if slot time has already passed
			echo "<div class='book hidden'>".$this->dateslot->format('H:i')."</div>";
		}
	}
	
	public function get_rows() {
		if($this->result->num_rows > 0) {
			while($this->row = $this->result->fetch_assoc()) {
					$this->latest_date = new DateTime($this->row["precisedate"]); //Get latest date from database
					
					if ($this->latest_date > $this->date_today) {					//Ensure all dates only displayed from today onwards
						//Open new row
						echo "<div class='row' style=''>"; 
						//Open and Close 1st column
						echo "<div class='col-xl-3 col-lg-12 text-xl-right text-center' style='margin-bottom:18px;'>".$this->row["easydate"]."</div>";
						//Open 2nd column
						echo "<div class='col-xl-9 col-lg-12 text-center text-xl-left'>";
						
						//Identify slot times from array
						$this->get_slots();
						
						//Close the 2nd column
						echo "</div>";
						//Close the row
						echo "</div>";
						
						//Spacer at end of week
						if ($this->row['dayofweek'] == "Sunday") {
							echo "<div style='margin-bottom:30px;'></div>";
						}
					}
			}
		} else {
			return makedie("No games are available for this date range");
		}
	}
}
?>
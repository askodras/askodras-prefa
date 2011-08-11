<?php
class Dianomi {
	public $kodikos;
	public $dealer;
	public $kasa1;
	public $metrita1;
	public $kasa2;
	public $metrita2;
	public $kasa3;
	public $metrita3;

	public function __construct() {
		unset($this->kodikos);
		unset($this->dealer);
		unset($this->kasa1);
		unset($this->metrita1);
		unset($this->kasa2);
		unset($this->metrita2);
		unset($this->kasa3);
		unset($this->metrita3);
	}

	public function set_from_dbrow($row) {
		$this->kodikos = $row['κωδικός'];
		$this->dealer = $row['dealer'];
		$this->kasa1 = $row['κάσα1'];
		$this->metrita1 = $row['μετρητά1'];
		$this->kasa2 = $row['κάσα2'];
		$this->metrita2 = $row['μετρητά2'];
		$this->kasa3 = $row['κάσα3'];
		$this->metrita3 = $row['μετρητά3'];
	}

	public function set_from_file($line) {
		$cols = explode("\t", $line);
		if (count($cols) != 8) {
			return(FALSE);
		}

		$nf = 0;
		$this->kodikos = $cols[$nf++];
		$this->dealer = $cols[$nf++];
		$this->kasa1 = $cols[$nf++];
		$this->metrita1 = $cols[$nf++];
		$this->kasa2 = $cols[$nf++];
		$this->metrita2 = $cols[$nf++];
		$this->kasa3 = $cols[$nf++];
		$this->metrita3 = $cols[$nf++];
		return(TRUE);
	}

	public function print_raw_data($fh) {
		Globals::put_line($fh,
			$this->kodikos . "\t" . $this->dealer . "\t" .
			$this->kasa1 . "\t" . $this->metrita1 . "\t" .
			$this->kasa2 . "\t" . $this->metrita2 . "\t" .
			$this->kasa3 . "\t" . $this->metrita3);
	}

	public function json_data($thesi_map) {
		if ($thesi_map === FALSE) {
			die("dianomi::json_data: ασαφής αντιστοίχιση θέσεων");
		}

		print "{k:" . $this->kodikos . ",d:" . $thesi_map[$this->dealer];
		for ($i = 1; $i <= 3; $i++) {
			$kasa = "kasa" . $i;
			$metrita = "metrita" . $i;
			print ",k" . $thesi_map[$i] . ":" . $this->$kasa .
				",m" . $thesi_map[$i] . ":" . $this->$metrita;
		}
		print "}";
	}

	public static function diavase($fh, &$dlist) {
		while ($line = Globals::get_line_end($fh)) {
			$d = new Dianomi;
			if ($d->set_from_file($line)) {
				$dlist[] = $d;
			}
		}
	}

	public static function grapse($fh, &$dlist) {
		Globals::put_line($fh, "@DIANOMI@");
		$n = count($dlist);
		for ($i = 0; $i < $n; $i++) {
			$dlist[$i]->print_raw_data($fh);
		}
		Globals::put_line($fh, "@END@");
	}

	public static function print_json_data($curr, $thesi_map = FALSE, $prev = FALSE) {
		if ($prev === FALSE) {
			self::print_all_json_data($curr, $thesi_map);
			return;
		}

		if ($curr == $prev) {
			return;
		}

		// Κατασκευάζω τα arrays "cdata" και "pdata" που περιέχουν τα
		// δεδομένα των διανομών δεικτοδοτημένα με τους κωδικούς
		// των διανομών.

		$cdata = array();
		$ncurr = count($curr);
		for ($i = 0; $i < $ncurr; $i++) {
			$cdata["d" . $curr[$i]->kodikos] = &$curr[$i];
		}

		$pdata = array();
		$nprev = count($prev);
		for ($i = 0; $i < $nprev; $i++) {
			$pdata["d" . $prev[$i]->kodikos] = &$prev[$i];
		}

		// Διατρέχω τώρα παλαιά και νέα δεδομένα με σκοπό να ελέγξω
		// τις διαφορές και να τις καταχωρήσω στα arrays "new", "mod"
		// και "del".

		$ndif = 0;
		$new = array();
		$mod = array();
		foreach($cdata as $kodikos => $data) {
			if (!array_key_exists($kodikos, $pdata)) {
				$new[] = &$cdata[$kodikos];
				$ndif++;
			}
			elseif ($cdata[$kodikos] != $pdata[$kodikos]) {
				$mod[$kodikos] = &$cdata[$kodikos];
				$ndif++;
			}
		}

		$del = array();
		foreach($pdata as $kodikos => $data) {
			if (!array_key_exists($kodikos, $cdata)) {
				$del[$kodikos] = TRUE;
				$ndif++;
			}
		}

		// Αν οι διαφορές που προέκυψαν μεταξύ παλαιών και νέων δεδομένων
		// είναι περισσότερες από τα ίδια τα δεδομένα, τότε επιστρέφω όλα
		// τα δεδομένα.

		if ($ndif >= $ncurr) {
			self::print_all_json_data($curr, $thesi_map);
			return;
		}

		if (($n = count($del)) > 0) {
			print ",dianomiDel:{";
			$koma = '';
			foreach ($del as $i => $dummy) {
				print $koma; $koma = ",";
				print "'" . $i . "':1";
			}
			print "}";
		}

		if (($n = count($mod)) > 0) {
			print ",dianomiMod:{";
			$koma = '';
			foreach ($mod as $i => $dummy) {
				print $koma; $koma = ",";
				print "'" . $i . "':";
				$mod[$i]->json_data($thesi_map);
			}
			print "}";
		}

		if (($n = count($new)) > 0) {
			print ",dianomiNew:[";
			$koma = '';
			for ($i = 0; $i < $n; $i++) {
				print $koma; $koma = ",";
				$new[$i]->json_data($thesi_map);
			}
			print "]";
		}
	}

	private static function print_all_json_data(&$dlist, $thesi_map) {
		$koma = '';
		$n = count($dlist);
		print ",dianomi:[";
		for ($i = 0; $i < $n; $i++) {
			print $koma; $koma = ",";
			$dlist[$i]->json_data($thesi_map);
		}
		print "]";
	}

	public static function process() {
		global $globals;

		$dianomi = array();

		if ($globals->is_trapezi()) {
			$query = "SELECT * FROM `διανομή` WHERE `τραπέζι` = " .
				$globals->trapezi->kodikos . " ORDER BY `κωδικός`"; 
			$result = $globals->sql_query($query);
			while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$d = new Dianomi;
				$d->set_from_dbrow($row);
				$dianomi[] = $d;
			}
		}

		return $dianomi;
	}
}
?>

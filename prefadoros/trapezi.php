<?php
class Kafenio {
	public static function diavase($fh, &$tlist) {
		while ($line = Globals::get_line_end($fh)) {
			$t = new Trapezi(FALSE);
			if ($t->set_from_file($line)) {
				$tlist[] = $t;
			}
		}
	}

	public static function grapse($fh, &$tlist) {
		Globals::put_line($fh, "@TRAPEZI@");
		$n = count($tlist);
		for ($i = 0; $i < $n; $i++) {
			$tlist[$i]->print_raw_data($fh, FALSE);
		}
		Globals::put_line($fh, "@END@");
	}

	public static function print_json_data($curr, $prev = FALSE) {
		if ($prev === FALSE) {
			self::print_all_json_data($curr);
			return;
		}

		if ($curr == $prev) {
			return;
		}

		// Κατασκευάζω τα arrays "cdata" και "pdata" που περιέχουν τα
		// δεδομένα των τραπεζιών δεικτοδοτημένα με τους κωδικούς των
		// τραπεζιών.

		$cdata = array();
		$ncurr = count($curr);
		for ($i = 0; $i < $ncurr; $i++) {
			$cdata['t' . $curr[$i]->kodikos] = &$curr[$i];
		}

		$pdata = array();
		$nprev = count($prev);
		for ($i = 0; $i < $nprev; $i++) {
			$pdata['t' . $prev[$i]->kodikos] = &$prev[$i];
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
			self::print_all_json_data($curr);
			return;
		}

		if (($n = count($del)) > 0) {
			print ",td:{";
			$koma = '';
			foreach ($del as $i => $dummy) {
				print $koma; $koma = ",";
				print "'" . $i . "':1";
			}
			print "}";
		}

		if (($n = count($mod)) > 0) {
			print ",tm:{";
			$koma = '';
			foreach ($mod as $i => $dummy) {
				print $koma; $koma = ",";
				print "'" . $i . "':";
				$mod[$i]->json_data();
			}
			print "}";
		}

		if (($n = count($new)) > 0) {
			print ",tn:[";
			$koma = '';
			for ($i = 0; $i < $n; $i++) {
				print $koma; $koma = ",";
				$new[$i]->json_data();
			}
			print "]";
		}
	}

	private static function print_all_json_data(&$tlist) {
		print ",t:[";
		$koma = '';
		$n = count($tlist);
		for ($i = 0; $i < $n; $i++) {
			print $koma; $koma = ",";
			$tlist[$i]->json_data();
		}
		print "]";
	}

	public static function process() {
		global $globals;
		static $trapezi = NULL;
		static $etrexe_ts = 0.0;

		$tora_ts = microtime(TRUE);
		if (($tora_ts - $etrexe_ts) <= 1.5) {
			return($trapezi);
		}

		// Για λόγους οικονομίας διαγράφω παλιά τραπέζια μια στις 100 φορές.
		if (mt_rand(0, 100) == 0) {
			self::svise_palia_trapezia();
		}

		$trapezi = array();
		$energos = Prefadoros::energos_pektis();

		$query = "SELECT *, UNIX_TIMESTAMP(`poll`) AS `poll` FROM `trapezi` " .
			"WHERE `telos` IS NULL ORDER BY `kodikos` DESC"; 
		$result = $globals->sql_query($query);
		$now = time();
		while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			if (($now - $row['poll']) >= XRONOS_PEKTIS_IDLE_MAX) {
				continue;
			}

			$t = new Trapezi(FALSE);
			$t->set_from_dbrow($row);
			if ($t->set_energos_pektis($energos)) {
				$trapezi[] = $t;
			}
		}

		$etrexe_ts = microtime(TRUE);
		return($trapezi);
	}

	private static function svise_palia_trapezia() {
		global $globals;

		$query = "SELECT SQL_NO_CACHE `kodikos` FROM `trapezi` WHERE ";

		// Τραπέζια όπου ένας τουλάχιστον παίκτης είναι φευγάτος και
		// δεν έχουν επικοινωνία για πάνω από 5 λεπτά.
		$query .= "(((`pektis1` IS NULL) OR (`pektis2` IS NULL) OR (`pektis3` IS NULL)) " .
			"AND (`poll` < DATE_SUB(NOW(), INTERVAL 5 MINUTE))) ";

		// Τραπέζια που δεν έχουν επικοινωνία για πάνω από μια μέρα.
		$query .= "OR (`poll` < DATE_SUB(NOW(), INTERVAL 1 DAY)) OR ";

		// Τραπέζια που έχουν κάποια ώρα που δημιουργήθηκαν, αλλά κάτι
		// δεν πάει καλά με το polling.
		$query .= "((`poll` IS NULL) AND (`stisimo` < DATE_SUB(NOW(), INTERVAL 15 MINUTE)))";

		$result = @mysqli_query($globals->db, $query);
		if ($result) {
			while ($row = @mysqli_fetch_array($result, MYSQLI_NUM)) {
				Trapezi::diagrafi($row[0]);
			}
		}
	}
}
?>

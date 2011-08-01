<?php
class Permes {
	public $kodikos;
	public $apostoleas;
	public $minima;
	public $dimiourgia;

	public function __construct() {
		$this->kodikos = 0;
		$this->apostoleas = '';
		$this->minima = '';
		$this->dimiourgia = '';
	}

	public function set_from_dbrow($row) {
		$this->kodikos = $row['κωδικός'];
		$this->apostoleas = $row['αποστολέας'];
		$this->minima = preg_replace("/\n/", "&#10;", $row['μήνυμα']);
		$this->dimiourgia = $row['δημιουργία'];
	}

	public function set_from_file($line) {
		$cols = explode("\t", $line);
		if (count($cols) != 4) {
			return(FALSE);
		}

		$nf = 0;
		$this->kodikos = $cols[$nf++];
		$this->apostoleas = $cols[$nf++];
		$this->minima = $cols[$nf++];
		$this->dimiourgia = $cols[$nf++];
		return(TRUE);
	}

	public function print_raw_data($fh) {
		Globals::put_line($fh, $this->kodikos . "\t" .  $this->apostoleas .
			"\t" . $this->minima . "\t" . $this->dimiourgia);
	}

	public function json_data() {
		$minima = preg_replace('/\\\/', '\\\\\\', $this->minima);
		$minima = preg_replace("/'/", "\'", $minima);
		$minima = preg_replace('/"/', '\"', $minima);
		print "{k:" . $this->kodikos . ",a:'" . $this->apostoleas .
			"',m:'" . $minima .  "',d:" . $this->dimiourgia . "}";
	}

	public static function diavase($fh, &$plist) {
		while ($line = Globals::get_line_end($fh)) {
			$p = new Permes();
			if ($p->set_from_file($line)) {
				$plist[] = $p;
			}
		}
	}

	public static function grapse($fh, &$plist) {
		Globals::put_line($fh, "@PERMES@");
		$n = count($plist);
		for ($i = 0; $i < $n; $i++) {
			$plist[$i]->print_raw_data($fh);
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

		$nprev = $prev ? count($prev) : 0;
		$ncurr = count($curr);
		$koma = '';
		$apopio = $nprev;

		// Αν δεν έχουμε μηνύματα προηγούμενης ενημέρωσης ή αυτά
		// ήσαν περισσότερα από τα τωρινά, τότε τα στέλνω όλα.
		if ((!$prev) || ($ncurr < $nprev)) {
			$apopio = 0;
		}
		else {
			// Ελέγχω αν έχουν τροποποιηθεί μηνύματα της προηγούμενης
			// ενημέρωσης. Αν αυτό είναι αλήθεια τα στέλνω όλα.
			for ($i = 0; $i < $nprev; $i++) {
				if ($curr[$i] != $prev[$i]) {
					$apopio = 0;
					break;
				}
			}
		}

		// Θα στείλουμε είτε όλα τα μηνύματα, είτε αυτά που είναι νεότερα
		// από το τελευταίο που είχαμε στείλει.

		if ($apopio <= 0) {
			self::print_all_json_data($curr);
			return;
		}

		$koma = '';
		print ",permesNew:[";
		for ($i = $apopio; $i < $ncurr; $i++) {
			print $koma; $koma = ",";
			$curr[$i]->json_data();
		}
		print "]";
	}

	private static function print_all_json_data(&$plist) {
		$koma = '';
		$n = count($plist);
		print ",permes:[";
		for ($i = 0; $i < $n; $i++) {
			print $koma; $koma = ',';
			$plist[$i]->json_data();
		}
		print "]";
	}

	public static function process() {
		global $globals;
		$slogin = "'" . $globals->asfales($globals->pektis->login) . "'";

		$permes = array();
		$query = "SELECT `κωδικός`, `αποστολέας`, `μήνυμα`, `κατάσταση`, " .
			"UNIX_TIMESTAMP(`δημιουργία`) AS `δημιουργία` " .
			"FROM `μήνυμα` WHERE (`παραλήπτης` LIKE " . $slogin .
			") AND (`κατάσταση` LIKE 'ΝΕΟ') ORDER BY `κωδικός`";
		$result = $globals->sql_query($query);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$p = new Permes;
			$p->set_from_dbrow($row);
			$permes[] = $p;
		}

		return $permes;
	}
}
?>

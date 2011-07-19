<?php
header('Content-type: text/plain; charset=utf-8');
global $no_session;
$no_session = TRUE;
require_once '../lib/standard.php';
require_once '../pektis/pektis.php';
require_once '../prefadoros/prefadoros.php';
set_globals();

global $id;
$id = Globals::perastike_check('id');

Prefadoros::pektis_check();
$globals->pektis->poll_update($id);

global $slogin;
$slogin = "'" . $globals->asfales($globals->pektis->login) . "'";

if (Globals::perastike('freska')) {
	freska_dedomena(torina_dedomena());
	telos_ok();
}

$prev = new Dedomena();
if (!$prev->diavase()) {
	freska_dedomena(torina_dedomena());
	telos_ok();
}

$ekinisi = time();
do {
	check_neotero_id();
	$curr = torina_dedomena();
	if ($curr != $prev) {
		diaforetika_dedomena($curr, $prev);
		telos_ok();
	}

	if ((time() - $ekinisi) > XRONOS_DEDOMENA_MAX) {
		break;
	}
	usleep(XRONOS_DEDOMENA_TIC);
} while (TRUE);

print_epikefalida();
print ",same:true}";
telos_ok();

function torina_dedomena() {
	$curr = new Dedomena();
	$curr->sxesi = process_sxesi();
	$curr->permes = process_permes();
	$curr->trapezi = process_trapezi();
	return($curr);
}

function check_neotero_id() {
	global $globals;
	global $slogin;
	global $id;

	$query = "SELECT `ενημέρωση` FROM `παίκτης` " .
		"WHERE `login` LIKE " . $slogin;
	$result = $globals->sql_query($query);
	$row = mysqli_fetch_array($result, MYSQLI_NUM);
	if (!$row) {
		Globals::fatal('ακαθόριστος παίκτης');
	}

	mysqli_free_result($result);
	if ($row[0] !== $id) {
		print_epikefalida();
		print ",akiro:true}";
		telos_ok();
	}
}

function freska_dedomena($curr) {
	$curr->grapse();
	print_epikefalida();
	print ",freska:true}";
	$curr->json_data();
}

function diaforetika_dedomena($curr, $prev) {
	$curr->grapse();
	print_epikefalida();
	print "}";

	if ($curr->sxesi == $prev->sxesi) {
		print ",sxesi:'same'";
	}
	else {
		$curr->sxesi_json_data();
	}

	if ($curr->permes == $prev->permes) {
		print ",permes:'same'";
	}
	else {
		$curr->permes_json_data($prev->permes);
	}

	if ($curr->trapezi == $prev->trapezi) {
		print ",trapezi:'same'";
	}
	else {
		$curr->trapezi_json_data();
	}
}

function print_epikefalida() {
	global $id;

	header('Content-type: application/json; charset=utf-8');
	print "data:{id:{$id}";
}

class Sxesi {
	public $login;
	public $onoma;
	public $online;
	public $diathesimos;
	public $status;

	public static function is_online($idle) {
		return ($idle < XRONOS_PEKTIS_IDLE_MAX);
	}

	public function __construct() {
		$this->login = '';
		$this->onoma = '';
		$this->online = FALSE;
		$this->diathesimos = FALSE;
		$this->status = '';
	}

	public function set_from_dbrow($row, $energos, $status = '') {
		$this->login = $row['login'];
		$this->onoma = $row['όνομα'];
		$this->online = self::is_online($row['idle']);
		$this->diathesimos = array_key_exists($row['login'], $energos);
		$this->status = $status;
	}

	public function set_from_file($line) {
		$cols = explode("\t", $line);
		if (count($cols) != 5) {
			return(FALSE);
		}

		$this->login = $cols[0];
		$this->onoma = $cols[1];
		$this->online = self::is_online($cols[2]);
		$this->diathesimos = $cols[3];
		$this->status = $cols[4];
		return(TRUE);
	}

	public function print_raw_data($fh) {
		Globals::put_line($fh, $this->login . "\t" . $this->onoma . "\t" .
			($this->online ? 1 : 0) . "\t" .
			($this->diathesimos ? 1 : 0) . "\t" . $this->status);
	}

	public function json_data() {
		if ($this->online) {
			if ($this->diathesimos) {
				$ol = 2;
			}
			else {
				$ol = 1;
			}
		}
		else {
			$ol = 0;
		}

		print "{l:'" . $this->login . "',n:'" . $this->onoma .
			"',o:" . $ol . ",s:'" . $this->status . "'}";
	}

	// Η παρακάτω (static) μέθοδος δημιουργεί λίστα όλων των παικτών
	// που φαίνονται να συμμετέχουν ως παίκτες σε ενεργά τραπέζια.

	public static function energos_pektis() {
		global $globals;
		$pektis = array();
		$query = "SELECT `παίκτης1`, `παίκτης2`, `παίκτης3` " .
			"FROM `τραπέζι` WHERE `τέλος` IS NULL";
		$result = $globals->sql_query($query);
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			for ($i = 0; $i < 3; $i++) {
				$pektis[$row[$i]] = TRUE;
			}
		}

		return($pektis);
	}
}

class Permes {
	public $apostoleas;
	public $minima;
	public $katastasi;
	public $dimiourgia;

	public function __construct() {
		$this->kodikos = 0;
		$this->apostoleas = '';
		$this->minima = '';
		$this->katastasi = '';
		$this->dimiourgia = '';
	}

	public function set_from_dbrow($row) {
		$this->kodikos = $row['κωδικός'];
		$this->apostoleas = $row['αποστολέας'];
		$this->minima = preg_replace("/\n/", "&#10;", $row['μήνυμα']);
		$this->katastasi = $row['κατάσταση'];
		$this->dimiourgia = $row['δημιουργία'];
	}

	public function set_from_file($line) {
		$cols = explode("\t", $line);
		if (count($cols) != 5) {
			return(FALSE);
		}

		$this->kodikos = $cols[0];
		$this->apostoleas = $cols[1];
		$this->minima = $cols[2];
		$this->katastasi = $cols[3];
		$this->dimiourgia = $cols[4];
		return(TRUE);
	}

	public function print_raw_data($fh) {
		Globals::put_line($fh, $this->kodikos . "\t" .
			$this->apostoleas . "\t" . $this->minima . "\t" .
			$this->katastasi . "\t" . $this->dimiourgia);
	}

	public function json_data() {
		$minima = preg_replace('/\\\/', '\\\\\\', $this->minima);
		$minima = preg_replace("/'/", "\'", $minima);
		$minima = preg_replace('/"/', '\"', $minima);
		print "{k:" . $this->kodikos . ",a:'" . $this->apostoleas . "',m:'" . $minima .
			"',s:'" . $this->katastasi . "',d:" . $this->dimiourgia . "}";
	}
}

class Dedomena {
	public $sxesi;
	public $trapezi;

	public function __construct() {
		$this->sxesi = array();
		$this->permes = array();
		$this->trapezi = array();
	}

	public function diavase() {
		$fh = self::open_file('r');
		if (!$fh) {
			return(FALSE);
		}

		while ($line = Globals::get_line($fh)) {
			switch ($line) {
			case '@SXESI@':
				$this->diavase_sxesi($fh);
				break;
			case '@PERMES@':
				$this->diavase_permes($fh);
				break;
			case '@TRAPEZIA@':
				$this->diavase_trapezi($fh);
				break;
			}
		}
		fclose($fh);
		return(TRUE);
	}

	private function diavase_sxesi($fh) {
		while ($line = Globals::get_line($fh)) {
			if ($line === '@END@') {
				return;
			}

			$s = new Sxesi();
			if ($s->set_from_file($line)) {
				$this->sxesi[] = $s;
			}
			else {
				unset($s);
			}
		}
	}

	private function diavase_permes($fh) {
		while ($line = Globals::get_line($fh)) {
			if ($line === '@END@') {
				return;
			}

			$p = new Permes();
			if ($p->set_from_file($line)) {
				$this->permes[] = $p;
			}
			else {
				unset($p);
			}
		}
	}

	private function diavase_trapezi($fh) {
		while ($line = Globals::get_line($fh)) {
			if ($line === '@END@') {
				return;
			}
		}
	}

	public function grapse() {
		$fh = self::open_file('w');
		if (!$fh) {
			Globals::fatal('cannot write data file');
		}

		$this->grapse_sxesi($fh);
		$this->grapse_permes($fh);
		$this->grapse_trapezi($fh);
		fclose($fh);
	}

	private function grapse_sxesi($fh) {
		Globals::put_line($fh, "@SXESI@");
		$n = count($this->sxesi);
		for ($i = 0; $i < $n; $i++) {
			$this->sxesi[$i]->print_raw_data($fh);
		}
		Globals::put_line($fh, "@END@");
	}

	private function grapse_permes($fh) {
		Globals::put_line($fh, "@PERMES@");
		$n = count($this->permes);
		for ($i = 0; $i < $n; $i++) {
			$this->permes[$i]->print_raw_data($fh);
		}
		Globals::put_line($fh, "@END@");
	}

	private function grapse_trapezi($fh) {
		Globals::put_line($fh, "@TRAPEZI@");
		$n = count($this->trapezi);
		for ($i = 0; $i < $n; $i++) {
			$this->trapezi[$i]->print_raw_data($fh);
		}
		Globals::put_line($fh, "@END@");
	}

	private static function open_file($rw) {
		global $globals;

		$fname = '../dedomena/' . $globals->pektis->login;
		$fh = @fopen($fname, $rw);
		return($fh);
	}

	public function json_data() {
		$this->sxesi_json_data();
		$this->permes_json_data();
		$this->trapezi_json_data();
	}

	public function sxesi_json_data() {
		print ",sxesi:[";
		$n = count($this->sxesi);
		for ($i = 0; $i < $n; $i++) {
			if ($i > 0) {
				print ",";
			}
			$this->sxesi[$i]->json_data();
		}
		print "]";
	}

	public function permes_json_data($prev = FALSE) {
		if (!$prev) {
			$nprev = 0;
		}
		else {
			$nprev = count($prev);
		}
		$ncurr = count($this->permes);
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
				if ($this->permes[$i] != $prev[$i]) {
					$apopio = 0;
					break;
				}
			}
		}

		print ",permes";
		if ($apopio > 0) {
			print "New";
		}
		print ":[";
		for ($i = $apopio; $i < $ncurr; $i++) {
			print $koma;
			$koma = ',';
			$this->permes[$i]->json_data();
		}
		print "]";
	}

	public function trapezi_json_data() {
		print ",trapezi:[";
		$n = count($this->trapezi);
		for ($i = 0; $i < $n; $i++) {
			if ($i > 0) {
				print ",";
			}
			$this->trapezi[$i]->json_data();
		}
		print "]";
	}
}

function process_sxesi() {
	global $globals;
	global $slogin;

	$query1 = "SELECT `login`, `όνομα`, (`poll` - NOW()) AS `idle` " .
		"FROM `παίκτης` WHERE 1 ";

	if (Globals::perastike('spat')) {
		$pat = "'%" . $globals->asfales($_REQUEST['spat']) . "%'";
		$query1 .= "AND ((`όνομα` LIKE " . $pat . ") OR " .
			"(`login` LIKE " . $pat . ")) ";
	}

	if (Globals::perastike('skat')) {
		$query1 .= "AND (`idle` < " . XRONOS_PEKTIS_IDLE_MAX . ") ";
	}

	$query2 = " ORDER BY `login`";

	// Δημιουργούμε λίστα όλων των ενεργών παικτών, ώστε να μπορούμε
	// να μαρκάκρουμε τους ενεργόυς παίκτες.
	$energos = Sxesi::energos_pektis();

	// Δημιουργούμε λίστα των παικτών που πρόκειται να επιστραφεί.
	// Η λίστα θα "γεμίσει" με δεδομένα αμέσως μετά.
	$sxesi = array();

	// Πρώτα θα εμφανιστούν οι παίκτες που σχετίζονται ως "φίλοι" με
	// τον παίκτη.
	$query = $query1 . "AND (`login` IN (SELECT `σχετιζόμενος` FROM `σχέση` WHERE " .
		"(`παίκτης` LIKE " . $slogin . ") AND " .
		"(`status` LIKE 'ΦΙΛΟΣ')))" . $query2;
	$result = $globals->sql_query($query);
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$s = new Sxesi;
		$s->set_from_dbrow($row, $energos, 'F');
		$sxesi[] = $s;
	}

	// Αν έχει δοθεί name pattern ή κατάσταση online/available, τότε επιλέγω και
	// μη σχετιζόμενους παίκτες.
	if (Globals::perastike('spat') || Globals::perastike('skat')) {
		$query = $query1 . "AND (`login` NOT IN (SELECT `σχετιζόμενος` FROM `σχέση` WHERE " .
			"(`παίκτης` LIKE " . $slogin . ")))" . $query2;
		$result = $globals->sql_query($query);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$s = new Sxesi;
			$s->set_from_dbrow($row, $energos);
			$sxesi[] = $s;
		}
	}

	// Τέλος, εμφανίζονται οι παίκτες που έχουν "αποκλειστεί" από τον παίκτη.
	$query = $query1 . "AND (`login` IN (SELECT `σχετιζόμενος` FROM `σχέση` WHERE " .
		"(`παίκτης` LIKE " . $slogin . ") AND " .
		"(`status` LIKE 'ΑΠΟΚΛΕΙΣΜΕΝΟΣ')))" . $query2;
	$result = $globals->sql_query($query);
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$s = new Sxesi;
		$s->set_from_dbrow($row, $energos, 'B');
		$sxesi[] = $s;
	}

	return $sxesi;
}

function process_permes() {
	global $globals;
	global $slogin;

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

function process_trapezi() {
	$trapezi = array();
	return($trapezi);
}

function telos_ok() {
	die('@OK');
}
?>
